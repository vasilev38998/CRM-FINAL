<?php
namespace App\Controllers;

class BudgetController
{
    public function index(): void
    {
        require_auth();
        require_subscription();
        require_shop();
        $stmt = db()->prepare('SELECT * FROM budgets WHERE coffee_shop_id = ? ORDER BY period_start DESC');
        $stmt->execute([current_shop_id()]);
        $budgets = $stmt->fetchAll();
        view('budgets/index', ['budgets' => $budgets]);
    }

    public function create(): void
    {
        require_auth();
        require_subscription();
        require_shop();
        if (is_post()) {
            $name = sanitize_string($_POST['name'] ?? '');
            $start = sanitize_string($_POST['start'] ?? '');
            $end = sanitize_string($_POST['end'] ?? '');
            if ($name === '' || $start === '' || $end === '') {
                flash('error', 'Заполните все поля бюджета.');
                view('budgets/create');
                return;
            }
            $stmt = db()->prepare('INSERT INTO budgets (coffee_shop_id, name, period_start, period_end, created_at) VALUES (?, ?, ?, ?, NOW())');
            $stmt->execute([current_shop_id(), $name, $start, $end]);
            $budgetId = (int) db()->lastInsertId();
            redirect('index.php?route=budgets/edit&id=' . $budgetId);
        }
        view('budgets/create');
    }

    public function edit(): void
    {
        require_auth();
        require_subscription();
        require_shop();
        $budgetId = (int) ($_GET['id'] ?? 0);
        $stmt = db()->prepare('SELECT * FROM budgets WHERE id = ? AND coffee_shop_id = ?');
        $stmt->execute([$budgetId, current_shop_id()]);
        $budget = $stmt->fetch();
        if (!$budget) {
            flash('error', 'Бюджет не найден.');
            redirect('index.php?route=budgets');
        }
        if (is_post()) {
            $category = sanitize_string($_POST['category'] ?? '');
            $amount = (float) ($_POST['amount'] ?? 0);
            $type = sanitize_string($_POST['type'] ?? '');
            if ($category === '' || $amount <= 0 || !in_array($type, ['income', 'expense'], true)) {
                flash('error', 'Заполните статью и сумму.');
                view('budgets/edit', ['budget' => $budget, 'items' => $this->loadItems($budgetId)]);
                return;
            }
            $stmt = db()->prepare('INSERT INTO budget_items (budget_id, category, amount_plan, type) VALUES (?, ?, ?, ?)');
            $stmt->execute([$budgetId, $category, $amount, $type]);
            redirect('index.php?route=budgets/edit&id=' . $budgetId);
        }
        $items = $this->loadItems($budgetId);
        view('budgets/edit', ['budget' => $budget, 'items' => $items]);
    }

    public function report(): void
    {
        require_auth();
        require_subscription();
        require_shop();
        $budgetId = (int) ($_GET['id'] ?? 0);
        $stmt = db()->prepare('SELECT * FROM budgets WHERE id = ? AND coffee_shop_id = ?');
        $stmt->execute([$budgetId, current_shop_id()]);
        $budget = $stmt->fetch();
        if (!$budget) {
            flash('error', 'Бюджет не найден.');
            redirect('index.php?route=budgets');
        }
        $items = $this->loadItems($budgetId);
        $report = [];
        foreach ($items as $item) {
            $fact = 0.0;
            if ($item['type'] === 'expense') {
                $stmt = db()->prepare('SELECT COALESCE(SUM(amount), 0) FROM expenses WHERE coffee_shop_id = ? AND category = ? AND spent_at BETWEEN ? AND ?');
                $stmt->execute([current_shop_id(), $item['category'], $budget['period_start'], $budget['period_end']]);
                $fact = (float) $stmt->fetchColumn();
            } else {
                $stmt = db()->prepare('SELECT COALESCE(SUM(total), 0) FROM sales WHERE coffee_shop_id = ? AND sold_at BETWEEN ? AND ?');
                $stmt->execute([current_shop_id(), $budget['period_start'], $budget['period_end']]);
                $fact = (float) $stmt->fetchColumn();
            }
            $diff = $fact - (float) $item['amount_plan'];
            $percent = (float) $item['amount_plan'] > 0 ? ($diff / (float) $item['amount_plan']) * 100 : 0;
            $report[] = [
                'category' => $item['category'],
                'type' => $item['type'],
                'plan' => (float) $item['amount_plan'],
                'fact' => $fact,
                'diff' => $diff,
                'percent' => $percent,
            ];
        }
        view('budgets/report', ['budget' => $budget, 'report' => $report]);
    }

    private function loadItems(int $budgetId): array
    {
        $stmt = db()->prepare('SELECT * FROM budget_items WHERE budget_id = ? ORDER BY type, category');
        $stmt->execute([$budgetId]);
        return $stmt->fetchAll();
    }
}
