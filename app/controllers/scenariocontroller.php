<?php
namespace App\Controllers;

class ScenarioController
{
    public function index(): void
    {
        require_auth();
        require_subscription();
        require_shop();
        $stmt = db()->prepare('SELECT * FROM scenarios WHERE coffee_shop_id = ? ORDER BY created_at DESC');
        $stmt->execute([current_shop_id()]);
        $scenarios = $stmt->fetchAll();
        view('scenarios/index', ['scenarios' => $scenarios]);
    }

    public function create(): void
    {
        require_auth();
        require_subscription();
        require_shop();
        if (is_post()) {
            verify_csrf();
            $name = sanitize_string($_POST['name'] ?? '');
            $margin = (float) ($_POST['margin_change'] ?? 0);
            $cost = (float) ($_POST['cost_change'] ?? 0);
            $volume = (float) ($_POST['volume_change'] ?? 0);
            if ($name === '') {
                flash('error', 'Укажите название сценария.');
                view('scenarios/create');
                return;
            }
            $stmt = db()->prepare('INSERT INTO scenarios (coffee_shop_id, name, margin_change, cost_change, volume_change, created_at) VALUES (?, ?, ?, ?, ?, NOW())');
            $stmt->execute([current_shop_id(), $name, $margin, $cost, $volume]);
            audit_log('scenarios', 'create', ['name' => $name]);
            redirect('index.php?route=scenarios');
        }
        view('scenarios/create');
    }

    public function report(): void
    {
        require_auth();
        require_subscription();
        require_shop();
        $scenarioId = (int) ($_GET['id'] ?? 0);
        $stmt = db()->prepare('SELECT * FROM scenarios WHERE id = ? AND coffee_shop_id = ?');
        $stmt->execute([$scenarioId, current_shop_id()]);
        $scenario = $stmt->fetch();
        if (!$scenario) {
            flash('error', 'Сценарий не найден.');
            redirect('index.php?route=scenarios');
        }

        $stmt = db()->prepare('SELECT COALESCE(SUM(total), 0) FROM sales WHERE coffee_shop_id = ?');
        $stmt->execute([current_shop_id()]);
        $revenue = (float) $stmt->fetchColumn();

        $stmt = db()->prepare('SELECT COALESCE(SUM(cogs), 0) FROM sales WHERE coffee_shop_id = ?');
        $stmt->execute([current_shop_id()]);
        $cogs = (float) $stmt->fetchColumn();

        $stmt = db()->prepare('SELECT COALESCE(SUM(amount), 0) FROM expenses WHERE coffee_shop_id = ?');
        $stmt->execute([current_shop_id()]);
        $expenses = (float) $stmt->fetchColumn();

        $volumeFactor = 1 + ((float) $scenario['volume_change'] / 100);
        $marginFactor = 1 + ((float) $scenario['margin_change'] / 100);
        $costFactor = 1 + ((float) $scenario['cost_change'] / 100);

        $newRevenue = $revenue * $volumeFactor * $marginFactor;
        $newCogs = $cogs * $volumeFactor * $costFactor;
        $grossProfit = $newRevenue - $newCogs;
        $netProfit = $grossProfit - $expenses;

        view('scenarios/report', [
            'scenario' => $scenario,
            'base' => [
                'revenue' => $revenue,
                'cogs' => $cogs,
                'expenses' => $expenses,
            ],
            'new' => [
                'revenue' => $newRevenue,
                'cogs' => $newCogs,
                'gross_profit' => $grossProfit,
                'net_profit' => $netProfit,
            ],
        ]);
    }
}
