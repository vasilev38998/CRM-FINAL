<?php
namespace App\Controllers;

class IngredientController
{
    public function index(): void
    {
        require_auth();
        require_subscription();
        require_shop();
        $stmt = db()->prepare('SELECT * FROM ingredients WHERE coffee_shop_id = ? ORDER BY name');
        $stmt->execute([current_shop_id()]);
        $ingredients = $stmt->fetchAll();
        view('ingredients/index', ['ingredients' => $ingredients]);
    }

    public function create(): void
    {
        require_auth();
        require_subscription();
        require_shop();
        if (is_post()) {
            $name = sanitize_string($_POST['name'] ?? '');
            $unit = sanitize_string($_POST['unit'] ?? '');
            $qty = (float) ($_POST['qty'] ?? 0);
            $price = (float) ($_POST['price'] ?? 0);
            if ($name === '' || $unit === '') {
                flash('error', 'Заполните название и единицу измерения.');
                view('ingredients/create');
                return;
            }
            $stmt = db()->prepare('INSERT INTO ingredients (coffee_shop_id, name, unit, stock_qty, avg_price, created_at) VALUES (?, ?, ?, ?, ?, NOW())');
            $stmt->execute([current_shop_id(), $name, $unit, $qty, $price]);
            redirect('index.php?route=ingredients');
        }
        view('ingredients/create');
    }
}
