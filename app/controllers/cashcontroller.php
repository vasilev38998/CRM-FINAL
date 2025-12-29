<?php
namespace App\Controllers;

class CashController
{
    public function index(): void
    {
        require_auth();
        require_subscription();
        require_shop();
        $shopId = current_shop_id();
        $start = $_GET['from'] ?? date('Y-m-01');
        $end = $_GET['to'] ?? date('Y-m-t');

        $stmt = db()->prepare('SELECT * FROM cash_transactions WHERE coffee_shop_id = ? AND transacted_at BETWEEN ? AND ? ORDER BY transacted_at DESC');
        $stmt->execute([$shopId, $start, $end]);
        $transactions = $stmt->fetchAll();

        $stmt = db()->prepare('SELECT COALESCE(SUM(amount), 0) FROM cash_transactions WHERE coffee_shop_id = ? AND type = ? AND transacted_at BETWEEN ? AND ?');
        $stmt->execute([$shopId, 'in', $start, $end]);
        $inflow = (float) $stmt->fetchColumn();

        $stmt = db()->prepare('SELECT COALESCE(SUM(amount), 0) FROM cash_transactions WHERE coffee_shop_id = ? AND type = ? AND transacted_at BETWEEN ? AND ?');
        $stmt->execute([$shopId, 'out', $start, $end]);
        $outflow = (float) $stmt->fetchColumn();

        $net = $inflow - $outflow;

        view('cash/index', [
            'transactions' => $transactions,
            'start' => $start,
            'end' => $end,
            'inflow' => $inflow,
            'outflow' => $outflow,
            'net' => $net,
        ]);
    }

    public function create(): void
    {
        require_auth();
        require_subscription();
        require_shop();
        if (is_post()) {
            verify_csrf();
            $type = sanitize_string($_POST['type'] ?? '');
            $source = sanitize_string($_POST['source'] ?? '');
            $category = sanitize_string($_POST['category'] ?? '');
            $amount = (float) ($_POST['amount'] ?? 0);
            $date = sanitize_string($_POST['date'] ?? date('Y-m-d'));
            $note = sanitize_string($_POST['note'] ?? '');

            if (!in_array($type, ['in', 'out'], true) || !in_array($source, ['cash', 'bank'], true) || $category === '' || $amount <= 0) {
                flash('error', 'Заполните все поля движения денежных средств.');
                view('cash/create');
                return;
            }

            $stmt = db()->prepare('INSERT INTO cash_transactions (coffee_shop_id, type, source, category, amount, note, transacted_at) VALUES (?, ?, ?, ?, ?, ?, ?)');
            $stmt->execute([current_shop_id(), $type, $source, $category, $amount, $note, $date]);
            audit_log('cash_transactions', 'create', ['type' => $type, 'source' => $source, 'amount' => $amount]);
            redirect('index.php?route=cash');
        }
        view('cash/create');
    }
}
