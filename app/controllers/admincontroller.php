<?php
namespace App\Controllers;

class AdminController
{
    public function stats(): void
    {
        require_auth();
        require_admin();
        $totalUsers = (int) db()->query('SELECT COUNT(*) FROM users')->fetchColumn();
        $totalShops = (int) db()->query('SELECT COUNT(*) FROM coffee_shops')->fetchColumn();
        $totalSubscriptions = (int) db()->query('SELECT COUNT(*) FROM subscriptions')->fetchColumn();
        $activeSubscriptions = (int) db()->query("SELECT COUNT(*) FROM subscriptions WHERE status = 'active' AND end_at >= NOW()")->fetchColumn();
        $paymentsTotal = (float) db()->query('SELECT COALESCE(SUM(amount), 0) FROM payments')->fetchColumn();

        $stmt = db()->query('SELECT id, name, email, created_at FROM users ORDER BY created_at DESC LIMIT 10');
        $recentUsers = $stmt->fetchAll();

        $stmt = db()->query('SELECT subscriptions.*, users.email, plans.name AS plan_name FROM subscriptions JOIN users ON subscriptions.user_id = users.id JOIN plans ON subscriptions.plan_id = plans.id ORDER BY subscriptions.created_at DESC LIMIT 50');
        $subscriptions = $stmt->fetchAll();

        view('admin/stats', [
            'totalUsers' => $totalUsers,
            'totalShops' => $totalShops,
            'totalSubscriptions' => $totalSubscriptions,
            'activeSubscriptions' => $activeSubscriptions,
            'paymentsTotal' => $paymentsTotal,
            'recentUsers' => $recentUsers,
            'subscriptions' => $subscriptions,
        ]);
    }

    public function plans(): void
    {
        require_auth();
        require_admin();
        $plans = db()->query('SELECT * FROM plans ORDER BY price ASC')->fetchAll();
        view('admin/plans', ['plans' => $plans]);
    }

    public function tokens(): void
    {
        require_auth();
        require_admin();
        if (is_post()) {
            verify_csrf();
            $label = sanitize_string($_POST['label'] ?? '');
            if ($label === '') {
                flash('error', 'Укажите название токена.');
                redirect('index.php?route=admin/tokens');
            }
            $token = bin2hex(random_bytes(16));
            $stmt = db()->prepare('INSERT INTO api_tokens (token, label, created_at) VALUES (?, ?, NOW())');
            $stmt->execute([$token, $label]);
            flash('success', 'Токен создан: ' . $token);
            redirect('index.php?route=admin/tokens');
        }
        $tokens = db()->query('SELECT * FROM api_tokens ORDER BY created_at DESC')->fetchAll();
        view('admin/tokens', ['tokens' => $tokens]);
    }

    public function audit(): void
    {
        require_auth();
        require_admin();
        $stmt = db()->query('SELECT audit_logs.*, users.email FROM audit_logs LEFT JOIN users ON audit_logs.user_id = users.id ORDER BY audit_logs.created_at DESC LIMIT 200');
        $logs = $stmt->fetchAll();
        view('admin/audit', ['logs' => $logs]);
    }

    public function system(): void
    {
        require_auth();
        require_admin();
        $dbOk = false;
        try {
            db()->query('SELECT 1');
            $dbOk = true;
        } catch (Exception $e) {
            $dbOk = false;
            log_event('DB status error', ['error' => $e->getMessage()]);
        }
        $status = [
            'php_version' => PHP_VERSION,
            'db_status' => $dbOk ? 'OK' : 'Ошибка',
            'uploads_writable' => is_writable(__DIR__ . '/../../uploads'),
            'storage_writable' => is_writable(__DIR__ . '/../../storage'),
        ];
        view('admin/system', ['status' => $status]);
    }

    public function editPlan(): void
    {
        require_auth();
        require_admin();
        $planId = (int) ($_GET['id'] ?? 0);
        $plan = null;
        if ($planId) {
            $stmt = db()->prepare('SELECT * FROM plans WHERE id = ?');
            $stmt->execute([$planId]);
            $plan = $stmt->fetch();
        }
        if (is_post()) {
            verify_csrf();
            $name = sanitize_string($_POST['name'] ?? '');
            $price = (float) ($_POST['price'] ?? 0);
            $duration = (int) ($_POST['duration_days'] ?? 0);
            $description = sanitize_string($_POST['description'] ?? '');
            if ($name === '' || $price <= 0 || $duration <= 0) {
                flash('error', 'Заполните все поля.');
                view('admin/plan_form', ['plan' => $plan]);
                return;
            }
            if ($plan) {
                $stmt = db()->prepare('UPDATE plans SET name = ?, price = ?, duration_days = ?, description = ? WHERE id = ?');
                $stmt->execute([$name, $price, $duration, $description, $plan['id']]);
            } else {
                $stmt = db()->prepare('INSERT INTO plans (name, price, duration_days, description) VALUES (?, ?, ?, ?)');
                $stmt->execute([$name, $price, $duration, $description]);
            }
            redirect('index.php?route=admin/plans');
        }
        view('admin/plan_form', ['plan' => $plan]);
    }
}
