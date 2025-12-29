<?php
namespace App\Controllers;

class CoffeeShopController
{
    public function create(): void
    {
        require_auth();
        if (is_post()) {
            $name = sanitize_string($_POST['name'] ?? '');
            $city = sanitize_string($_POST['city'] ?? '');
            if ($name === '') {
                flash('error', 'Введите название кофейни.');
                view('coffee/create');
                return;
            }
            $stmt = db()->prepare('INSERT INTO coffee_shops (user_id, name, city, created_at) VALUES (?, ?, ?, NOW())');
            $stmt->execute([current_user()['id'], $name, $city]);
            $_SESSION['coffee_shop_id'] = (int) db()->lastInsertId();
            redirect('index.php?route=dashboard');
        }
        view('coffee/create');
    }

    public function select(): void
    {
        require_auth();
        $stmt = db()->prepare('SELECT * FROM coffee_shops WHERE user_id = ? ORDER BY created_at DESC');
        $stmt->execute([current_user()['id']]);
        $shops = $stmt->fetchAll();
        if (is_post()) {
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
