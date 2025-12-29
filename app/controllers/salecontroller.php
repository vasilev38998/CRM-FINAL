<?php
namespace App\Controllers;

class SaleController
{
    public function index(): void
    {
        require_auth();
        require_subscription();
        require_shop();
        $stmt = db()->prepare('SELECT sales.*, products.name AS product_name FROM sales JOIN products ON sales.product_id = products.id WHERE sales.coffee_shop_id = ? ORDER BY sold_at DESC');
        $stmt->execute([current_shop_id()]);
        $sales = $stmt->fetchAll();
        view('sales/index', ['sales' => $sales]);
    }

    public function create(): void
    {
        require_auth();
        require_subscription();
        require_shop();
        $stmt = db()->prepare('SELECT * FROM products WHERE coffee_shop_id = ? ORDER BY name');
        $stmt->execute([current_shop_id()]);
        $products = $stmt->fetchAll();

        if (is_post()) {
            verify_csrf();
            $productId = (int) ($_POST['product_id'] ?? 0);
            $qty = (float) ($_POST['qty'] ?? 0);
            $price = (float) ($_POST['price'] ?? 0);
            $date = sanitize_string($_POST['date'] ?? date('Y-m-d'));

            if ($productId === 0 || $qty <= 0 || $price <= 0) {
                flash('error', 'Заполните все поля продажи.');
                view('sales/create', ['products' => $products]);
                return;
            }

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
                    $updateStmt = db()->prepare('UPDATE ingredients SET stock_qty = ? WHERE id = ?');
                    $updateStmt->execute([$newQty, $ingredient['id']]);
                }
            }

            $stmt = db()->prepare('INSERT INTO sales (coffee_shop_id, product_id, qty, price, total, cogs, sold_at) VALUES (?, ?, ?, ?, ?, ?, ?)');
            $stmt->execute([current_shop_id(), $productId, $qty, $price, $qty * $price, $cogs, $date]);
            audit_log('sales', 'create', ['product_id' => $productId, 'qty' => $qty, 'price' => $price]);
            redirect('index.php?route=sales');
        }

        view('sales/create', ['products' => $products]);
    }
}
