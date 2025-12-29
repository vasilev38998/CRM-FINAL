<?php
namespace App\Controllers;

class ProductController
{
    public function index(): void
    {
        require_auth();
        require_subscription();
        require_shop();
        $stmt = db()->prepare('SELECT * FROM products WHERE coffee_shop_id = ? ORDER BY name');
        $stmt->execute([current_shop_id()]);
        $products = $stmt->fetchAll();
        view('products/index', ['products' => $products]);
    }

    public function create(): void
    {
        require_auth();
        require_subscription();
        require_shop();
        if (is_post()) {
            $name = sanitize_string($_POST['name'] ?? '');
            $price = (float) ($_POST['price'] ?? 0);
            if ($name === '' || $price <= 0) {
                flash('error', 'Заполните название и цену продажи.');
                view('products/create');
                return;
            }
            $stmt = db()->prepare('INSERT INTO products (coffee_shop_id, name, price_sell, created_at) VALUES (?, ?, ?, NOW())');
            $stmt->execute([current_shop_id(), $name, $price]);
            redirect('index.php?route=products');
        }
        view('products/create');
    }

    public function pricing(): void
    {
        require_auth();
        require_subscription();
        require_shop();
        $margin = (float) ($_GET['margin'] ?? 60);
        $stmt = db()->prepare('SELECT * FROM products WHERE coffee_shop_id = ? ORDER BY name');
        $stmt->execute([current_shop_id()]);
        $products = $stmt->fetchAll();
        $result = [];
        foreach ($products as $product) {
            $stmt = db()->prepare('SELECT recipes.qty, ingredients.avg_price FROM recipes JOIN ingredients ON recipes.ingredient_id = ingredients.id WHERE recipes.coffee_shop_id = ? AND recipes.product_id = ?');
            $stmt->execute([current_shop_id(), $product['id']]);
            $items = $stmt->fetchAll();
            $cost = 0.0;
            foreach ($items as $item) {
                $cost += (float) $item['qty'] * (float) $item['avg_price'];
            }
            $recommended = $margin > 0 ? $cost / (1 - ($margin / 100)) : $cost;
            $result[] = [
                'name' => $product['name'],
                'cost' => $cost,
                'current_price' => (float) $product['price_sell'],
                'recommended_price' => $recommended,
                'margin' => $margin,
            ];
        }
        view('products/pricing', ['items' => $result, 'margin' => $margin]);
    }
}
