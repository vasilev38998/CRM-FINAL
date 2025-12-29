<?php
namespace App\Controllers;

class AdminController
{
    public function plans(): void
    {
        require_auth();
        require_admin();
        $plans = db()->query('SELECT * FROM plans ORDER BY price ASC')->fetchAll();
        view('admin/plans', ['plans' => $plans]);
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
