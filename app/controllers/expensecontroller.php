<?php
namespace App\Controllers;

class ExpenseController
{
    public function index(): void
    {
        require_auth();
        require_subscription();
        require_shop();
        $stmt = db()->prepare('SELECT * FROM expenses WHERE coffee_shop_id = ? ORDER BY spent_at DESC');
        $stmt->execute([current_shop_id()]);
        $expenses = $stmt->fetchAll();
        view('expenses/index', ['expenses' => $expenses]);
    }

    public function create(): void
    {
        require_auth();
        require_subscription();
        require_shop();
        if (is_post()) {
<<<<<<< HEAD
            verify_csrf();
=======
>>>>>>> origin/main
            $category = sanitize_string($_POST['category'] ?? '');
            $amount = (float) ($_POST['amount'] ?? 0);
            $date = sanitize_string($_POST['date'] ?? date('Y-m-d'));
            $note = sanitize_string($_POST['note'] ?? '');
            if ($category === '' || $amount <= 0) {
                flash('error', 'Укажите категорию и сумму.');
                view('expenses/create');
                return;
            }
            $stmt = db()->prepare('INSERT INTO expenses (coffee_shop_id, category, amount, note, spent_at) VALUES (?, ?, ?, ?, ?)');
            $stmt->execute([current_shop_id(), $category, $amount, $note, $date]);
<<<<<<< HEAD
            audit_log('expenses', 'create', ['category' => $category, 'amount' => $amount]);
=======
>>>>>>> origin/main
            redirect('index.php?route=expenses');
        }
        view('expenses/create');
    }
}
