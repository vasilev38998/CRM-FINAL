<?php
namespace App\Controllers;

class ShopUserController
{
    public function index(): void
    {
        require_auth();
        require_subscription();
        require_shop();
        require_shop_role(['owner']);
        $stmt = db()->prepare('SELECT shop_users.*, users.email, users.name FROM shop_users JOIN users ON shop_users.user_id = users.id WHERE shop_users.coffee_shop_id = ?');
        $stmt->execute([current_shop_id()]);
        $users = $stmt->fetchAll();
        view('coffee/users', ['users' => $users]);
    }

    public function add(): void
    {
        require_auth();
        require_subscription();
        require_shop();
        require_shop_role(['owner']);
        if (is_post()) {
            verify_csrf();
            $email = sanitize_string($_POST['email'] ?? '');
            $role = sanitize_string($_POST['role'] ?? '');
            if ($email === '' || !in_array($role, ['owner', 'manager', 'accountant'], true)) {
                flash('error', 'Заполните email и роль.');
                redirect('index.php?route=coffee/users');
            }
            $stmt = db()->prepare('SELECT id FROM users WHERE email = ?');
            $stmt->execute([$email]);
            $userId = $stmt->fetchColumn();
            if (!$userId) {
                flash('error', 'Пользователь не найден.');
                redirect('index.php?route=coffee/users');
            }
            db()->prepare('INSERT IGNORE INTO shop_users (coffee_shop_id, user_id, role, created_at) VALUES (?, ?, ?, NOW())')
                ->execute([current_shop_id(), $userId, $role]);
            audit_log('shop_users', 'add', ['email' => $email, 'role' => $role]);
            redirect('index.php?route=coffee/users');
        }
        redirect('index.php?route=coffee/users');
    }
}
