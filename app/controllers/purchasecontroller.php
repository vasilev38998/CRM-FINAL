<?php
namespace App\Controllers;

class PurchaseController
{
    public function index(): void
    {
        require_auth();
        require_subscription();
        require_shop();
        $stmt = db()->prepare('SELECT purchases.*, ingredients.name AS ingredient_name FROM purchases JOIN ingredients ON purchases.ingredient_id = ingredients.id WHERE purchases.coffee_shop_id = ? ORDER BY purchased_at DESC');
        $stmt->execute([current_shop_id()]);
        $purchases = $stmt->fetchAll();
        view('purchases/index', ['purchases' => $purchases]);
    }

    public function create(): void
    {
        require_auth();
        require_subscription();
        require_shop();
        $stmt = db()->prepare('SELECT * FROM ingredients WHERE coffee_shop_id = ? ORDER BY name');
        $stmt->execute([current_shop_id()]);
        $ingredients = $stmt->fetchAll();
        if (is_post()) {
            verify_csrf();
            $ingredientId = (int) ($_POST['ingredient_id'] ?? 0);
            $qty = (float) ($_POST['qty'] ?? 0);
            $price = (float) ($_POST['price'] ?? 0);
            $date = sanitize_string($_POST['date'] ?? date('Y-m-d'));
            if ($ingredientId === 0 || $qty <= 0 || $price <= 0) {
                flash('error', 'Укажите ингредиент, количество и цену.');
                view('purchases/create', ['ingredients' => $ingredients]);
                return;
            }
            $stmt = db()->prepare('SELECT * FROM ingredients WHERE id = ? AND coffee_shop_id = ?');
            $stmt->execute([$ingredientId, current_shop_id()]);
            $ingredient = $stmt->fetch();
            if (!$ingredient) {
                flash('error', 'Ингредиент не найден.');
                view('purchases/create', ['ingredients' => $ingredients]);
                return;
            }
            $oldQty = (float) $ingredient['stock_qty'];
            $oldPrice = (float) $ingredient['avg_price'];
            $newQty = $oldQty + $qty;
            $newAvg = $newQty > 0 ? ($oldQty * $oldPrice + $qty * $price) / $newQty : $price;

            $stmt = db()->prepare('INSERT INTO purchases (coffee_shop_id, ingredient_id, qty, price, total, purchased_at) VALUES (?, ?, ?, ?, ?, ?)');
            $stmt->execute([current_shop_id(), $ingredientId, $qty, $price, $qty * $price, $date]);

            $stmt = db()->prepare('UPDATE ingredients SET stock_qty = ?, avg_price = ? WHERE id = ?');
            $stmt->execute([$newQty, $newAvg, $ingredientId]);
            audit_log('purchases', 'create', ['ingredient_id' => $ingredientId, 'qty' => $qty, 'price' => $price]);
            redirect('index.php?route=purchases');
        }
        view('purchases/create', ['ingredients' => $ingredients]);
    }
}
