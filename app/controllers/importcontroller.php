<?php
namespace App\Controllers;

class ImportController
{
    public function purchases(): void
    {
        $this->handleImport('purchases');
    }

    public function sales(): void
    {
        $this->handleImport('sales');
    }

    public function expenses(): void
    {
        $this->handleImport('expenses');
    }

    private function handleImport(string $type): void
    {
        require_auth();
        require_subscription();
        require_shop();

        $errors = [];
        $rows = [];

        if (is_post()) {
            verify_csrf();
            $action = $_POST['action'] ?? 'preview';
            if ($action === 'preview' && isset($_FILES['csv_file'])) {
                $file = $_FILES['csv_file'];
                if ($file['error'] === UPLOAD_ERR_OK) {
                    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                    $rows = tabular_read($file['tmp_name'], $extension);
                    [$rows, $errors] = $this->validateRows($type, $rows);
                    $_SESSION['import'][$type] = $rows;
                } else {
                    $errors[] = 'Ошибка загрузки файла.';
                }
            }

            if ($action === 'import') {
                $rows = $_SESSION['import'][$type] ?? [];
                if (!$rows) {
                    $errors[] = 'Нет данных для импорта. Сначала выполните предпросмотр.';
                } else {
                    $this->importRows($type, $rows);
                    unset($_SESSION['import'][$type]);
                    audit_log('imports', 'import', ['type' => $type, 'rows' => count($rows)]);
                    flash('success', 'Импорт выполнен.');
                    redirect('index.php?route=' . $type);
                }
            }
        }

        view('import/' . $type, [
            'rows' => $rows,
            'errors' => $errors,
        ]);
    }

    private function validateRows(string $type, array $rows): array
    {
        $errors = [];
        $filtered = [];
        foreach ($rows as $index => $row) {
            if ($index === 0 && $this->isHeaderRow($row)) {
                continue;
            }
            if ($type === 'purchases') {
                if (count($row) < 4) {
                    $errors[] = 'Строка ' . ($index + 1) . ': недостаточно колонок.';
                    continue;
                }
                $ingredientName = sanitize_string($row[0]);
                $qty = (float) $row[1];
                $price = (float) $row[2];
                $date = sanitize_string($row[3]);
                if ($ingredientName === '' || $qty <= 0 || $price <= 0) {
                    $errors[] = 'Строка ' . ($index + 1) . ': заполните название, количество и цену.';
                    continue;
                }
                $ingredientId = $this->findIngredientId($ingredientName);
                if (!$ingredientId) {
                    $errors[] = 'Строка ' . ($index + 1) . ': ингредиент не найден (' . $ingredientName . ').';
                    continue;
                }
                $filtered[] = [
                    'ingredient_id' => $ingredientId,
                    'ingredient_name' => $ingredientName,
                    'qty' => $qty,
                    'price' => $price,
                    'date' => $date ?: date('Y-m-d'),
                ];
            }
            if ($type === 'sales') {
                if (count($row) < 4) {
                    $errors[] = 'Строка ' . ($index + 1) . ': недостаточно колонок.';
                    continue;
                }
                $productName = sanitize_string($row[0]);
                $qty = (float) $row[1];
                $price = (float) $row[2];
                $date = sanitize_string($row[3]);
                if ($productName === '' || $qty <= 0 || $price <= 0) {
                    $errors[] = 'Строка ' . ($index + 1) . ': заполните название, количество и цену.';
                    continue;
                }
                $productId = $this->findProductId($productName);
                if (!$productId) {
                    $errors[] = 'Строка ' . ($index + 1) . ': напиток не найден (' . $productName . ').';
                    continue;
                }
                $filtered[] = [
                    'product_id' => $productId,
                    'product_name' => $productName,
                    'qty' => $qty,
                    'price' => $price,
                    'date' => $date ?: date('Y-m-d'),
                ];
            }
            if ($type === 'expenses') {
                if (count($row) < 3) {
                    $errors[] = 'Строка ' . ($index + 1) . ': недостаточно колонок.';
                    continue;
                }
                $category = sanitize_string($row[0]);
                $amount = (float) $row[1];
                $date = sanitize_string($row[2]);
                $note = sanitize_string($row[3] ?? '');
                if ($category === '' || $amount <= 0) {
                    $errors[] = 'Строка ' . ($index + 1) . ': заполните категорию и сумму.';
                    continue;
                }
                $filtered[] = [
                    'category' => $category,
                    'amount' => $amount,
                    'date' => $date ?: date('Y-m-d'),
                    'note' => $note,
                ];
            }
        }
        return [$filtered, $errors];
    }

    private function importRows(string $type, array $rows): void
    {
        if ($type === 'purchases') {
            foreach ($rows as $row) {
                $ingredientId = $row['ingredient_id'];
                $qty = $row['qty'];
                $price = $row['price'];
                $date = $row['date'];
                $stmt = db()->prepare('SELECT * FROM ingredients WHERE id = ?');
                $stmt->execute([$ingredientId]);
                $ingredient = $stmt->fetch();
                if (!$ingredient) {
                    continue;
                }
                $oldQty = (float) $ingredient['stock_qty'];
                $oldPrice = (float) $ingredient['avg_price'];
                $newQty = $oldQty + $qty;
                $newAvg = $newQty > 0 ? ($oldQty * $oldPrice + $qty * $price) / $newQty : $price;

                db()->prepare('INSERT INTO purchases (coffee_shop_id, ingredient_id, qty, price, total, purchased_at) VALUES (?, ?, ?, ?, ?, ?)')
                    ->execute([current_shop_id(), $ingredientId, $qty, $price, $qty * $price, $date]);
                db()->prepare('UPDATE ingredients SET stock_qty = ?, avg_price = ? WHERE id = ?')
                    ->execute([$newQty, $newAvg, $ingredientId]);
            }
        }
        if ($type === 'sales') {
            foreach ($rows as $row) {
                $productId = $row['product_id'];
                $qty = $row['qty'];
                $price = $row['price'];
                $date = $row['date'];
                $stmt = db()->prepare('SELECT * FROM recipes WHERE coffee_shop_id = ? AND product_id = ?');
                $stmt->execute([current_shop_id(), $productId]);
                $recipeItems = $stmt->fetchAll();
                $cogs = 0.0;
                foreach ($recipeItems as $item) {
                    $ingredientStmt = db()->prepare('SELECT * FROM ingredients WHERE id = ?');
                    $ingredientStmt->execute([$item['ingredient_id']]);
                    $ingredient = $ingredientStmt->fetch();
                    if ($ingredient) {
                        $cost = $item['qty'] * $qty * (float) $ingredient['avg_price'];
                        $cogs += $cost;
                        $newQty = (float) $ingredient['stock_qty'] - ($item['qty'] * $qty);
                        db()->prepare('UPDATE ingredients SET stock_qty = ? WHERE id = ?')
                            ->execute([$newQty, $ingredient['id']]);
                    }
                }
                db()->prepare('INSERT INTO sales (coffee_shop_id, product_id, qty, price, total, cogs, sold_at) VALUES (?, ?, ?, ?, ?, ?, ?)')
                    ->execute([current_shop_id(), $productId, $qty, $price, $qty * $price, $cogs, $date]);
            }
        }
        if ($type === 'expenses') {
            foreach ($rows as $row) {
                $category = $row['category'];
                $amount = $row['amount'];
                $date = $row['date'];
                $note = $row['note'];
                db()->prepare('INSERT INTO expenses (coffee_shop_id, category, amount, note, spent_at) VALUES (?, ?, ?, ?, ?)')
                    ->execute([current_shop_id(), $category, $amount, $note, $date]);
            }
        }
    }

    private function findIngredientId(string $name): ?int
    {
        $stmt = db()->prepare('SELECT id FROM ingredients WHERE coffee_shop_id = ? AND name = ?');
        $stmt->execute([current_shop_id(), $name]);
        $id = $stmt->fetchColumn();
        return $id ? (int) $id : null;
    }

    private function findProductId(string $name): ?int
    {
        $stmt = db()->prepare('SELECT id FROM products WHERE coffee_shop_id = ? AND name = ?');
        $stmt->execute([current_shop_id(), $name]);
        $id = $stmt->fetchColumn();
        return $id ? (int) $id : null;
    }

    private function isHeaderRow(array $row): bool
    {
        $rowText = strtolower(implode(';', $row));
        return strpos($rowText, 'название') !== false || strpos($rowText, 'категория') !== false;
    }
}
