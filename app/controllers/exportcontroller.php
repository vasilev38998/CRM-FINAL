<?php
namespace App\Controllers;

class ExportController
{
    public function purchases(): void
    {
        $this->export('purchases');
    }

    public function sales(): void
    {
        $this->export('sales');
    }

    public function expenses(): void
    {
        $this->export('expenses');
    }

    private function export(string $type): void
    {
        require_auth();
        require_subscription();
        require_shop();
        $headers = [];
        $rows = [];

        if ($type === 'purchases') {
            $headers = ['Ингредиент', 'Количество', 'Цена', 'Сумма', 'Дата'];
            $stmt = db()->prepare('SELECT purchases.*, ingredients.name AS ingredient_name FROM purchases JOIN ingredients ON purchases.ingredient_id = ingredients.id WHERE purchases.coffee_shop_id = ? ORDER BY purchased_at DESC');
            $stmt->execute([current_shop_id()]);
            foreach ($stmt->fetchAll() as $row) {
                $rows[] = [
                    $row['ingredient_name'],
                    $row['qty'],
                    $row['price'],
                    $row['total'],
                    $row['purchased_at'],
                ];
            }
        }

        if ($type === 'sales') {
            $headers = ['Напиток', 'Количество', 'Цена', 'Выручка', 'COGS', 'Дата'];
            $stmt = db()->prepare('SELECT sales.*, products.name AS product_name FROM sales JOIN products ON sales.product_id = products.id WHERE sales.coffee_shop_id = ? ORDER BY sold_at DESC');
            $stmt->execute([current_shop_id()]);
            foreach ($stmt->fetchAll() as $row) {
                $rows[] = [
                    $row['product_name'],
                    $row['qty'],
                    $row['price'],
                    $row['total'],
                    $row['cogs'],
                    $row['sold_at'],
                ];
            }
        }

        if ($type === 'expenses') {
            $headers = ['Категория', 'Сумма', 'Дата', 'Комментарий'];
            $stmt = db()->prepare('SELECT * FROM expenses WHERE coffee_shop_id = ? ORDER BY spent_at DESC');
            $stmt->execute([current_shop_id()]);
            foreach ($stmt->fetchAll() as $row) {
                $rows[] = [
                    $row['category'],
                    $row['amount'],
                    $row['spent_at'],
                    $row['note'],
                ];
            }
        }

        $content = export_excel_xml($headers, $rows);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="' . $type . '_export.xls"');
        echo $content;
        exit;
    }
}
