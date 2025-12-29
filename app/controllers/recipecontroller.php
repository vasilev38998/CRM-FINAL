<?php
namespace App\Controllers;

class RecipeController
{
    public function index(): void
    {
        require_auth();
        require_subscription();
        require_shop();
        $stmt = db()->prepare('SELECT recipes.*, products.name AS product_name, ingredients.name AS ingredient_name, ingredients.avg_price FROM recipes JOIN products ON recipes.product_id = products.id JOIN ingredients ON recipes.ingredient_id = ingredients.id WHERE recipes.coffee_shop_id = ? ORDER BY products.name');
        $stmt->execute([current_shop_id()]);
        $recipes = $stmt->fetchAll();
        view('recipes/index', ['recipes' => $recipes]);
    }

    public function create(): void
    {
        require_auth();
        require_subscription();
        require_shop();
        $stmt = db()->prepare('SELECT * FROM products WHERE coffee_shop_id = ? ORDER BY name');
        $stmt->execute([current_shop_id()]);
        $products = $stmt->fetchAll();
        $stmt = db()->prepare('SELECT * FROM ingredients WHERE coffee_shop_id = ? ORDER BY name');
        $stmt->execute([current_shop_id()]);
        $ingredients = $stmt->fetchAll();

        if (is_post()) {
            $productId = (int) ($_POST['product_id'] ?? 0);
            $ingredientId = (int) ($_POST['ingredient_id'] ?? 0);
            $qty = (float) ($_POST['qty'] ?? 0);
            if ($productId === 0 || $ingredientId === 0 || $qty <= 0) {
                flash('error', 'Заполните все поля рецепта.');
                view('recipes/create', ['products' => $products, 'ingredients' => $ingredients]);
                return;
            }
            $stmt = db()->prepare('INSERT INTO recipes (coffee_shop_id, product_id, ingredient_id, qty) VALUES (?, ?, ?, ?)');
            $stmt->execute([current_shop_id(), $productId, $ingredientId, $qty]);
            redirect('index.php?route=recipes');
        }
        view('recipes/create', ['products' => $products, 'ingredients' => $ingredients]);
    }
}
