<?php
namespace App\Controllers;

class CoffeeShopController
{
    public function create(): void
    {
        require_auth();
        if (is_post()) {
<<<<<<< HEAD
            verify_csrf();
=======
>>>>>>> origin/main
            $name = sanitize_string($_POST['name'] ?? '');
            $city = sanitize_string($_POST['city'] ?? '');
            if ($name === '') {
                flash('error', 'Введите название кофейни.');
                view('coffee/create');
                return;
            }
            $stmt = db()->prepare('INSERT INTO coffee_shops (user_id, name, city, created_at) VALUES (?, ?, ?, NOW())');
            $stmt->execute([current_user()['id'], $name, $city]);
<<<<<<< HEAD
            $shopId = (int) db()->lastInsertId();
            db()->prepare('INSERT INTO shop_users (coffee_shop_id, user_id, role, created_at) VALUES (?, ?, ?, NOW())')
                ->execute([$shopId, current_user()['id'], 'owner']);
            $_SESSION['coffee_shop_id'] = $shopId;
            audit_log('coffee_shops', 'create', ['name' => $name, 'city' => $city]);
=======
            $_SESSION['coffee_shop_id'] = (int) db()->lastInsertId();
>>>>>>> origin/main
            redirect('index.php?route=dashboard');
        }
        view('coffee/create');
    }

    public function select(): void
    {
        require_auth();
<<<<<<< HEAD
        $stmt = db()->prepare('SELECT coffee_shops.* FROM coffee_shops JOIN shop_users ON coffee_shops.id = shop_users.coffee_shop_id WHERE shop_users.user_id = ? ORDER BY coffee_shops.created_at DESC');
        $stmt->execute([current_user()['id']]);
        $shops = $stmt->fetchAll();
        if (is_post()) {
            verify_csrf();
=======
        $stmt = db()->prepare('SELECT * FROM coffee_shops WHERE user_id = ? ORDER BY created_at DESC');
        $stmt->execute([current_user()['id']]);
        $shops = $stmt->fetchAll();
        if (is_post()) {
>>>>>>> origin/main
            $shopId = (int) ($_POST['coffee_shop_id'] ?? 0);
            foreach ($shops as $shop) {
                if ((int) $shop['id'] === $shopId) {
                    $_SESSION['coffee_shop_id'] = $shopId;
                    redirect('index.php?route=dashboard');
                }
            }
            flash('error', 'Кофейня не найдена.');
        }
        view('coffee/select', ['shops' => $shops]);
    }
}
