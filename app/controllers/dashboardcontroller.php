<?php
namespace App\Controllers;

class DashboardController
{
    public function index(): void
    {
        require_auth();
        require_subscription();
        require_shop();
        $shopId = current_shop_id();

        $start = $_GET['from'] ?? date('Y-m-01');
        $end = $_GET['to'] ?? date('Y-m-t');

        $stmt = db()->prepare('SELECT COALESCE(SUM(total), 0) FROM sales WHERE coffee_shop_id = ? AND sold_at BETWEEN ? AND ?');
        $stmt->execute([$shopId, $start, $end]);
        $revenue = (float) $stmt->fetchColumn();

        $stmt = db()->prepare('SELECT COALESCE(SUM(cogs), 0) FROM sales WHERE coffee_shop_id = ? AND sold_at BETWEEN ? AND ?');
        $stmt->execute([$shopId, $start, $end]);
        $cogs = (float) $stmt->fetchColumn();

        $grossProfit = $revenue - $cogs;
        $grossMargin = $revenue > 0 ? ($grossProfit / $revenue) * 100 : 0;

        $stmt = db()->prepare('SELECT COALESCE(SUM(amount), 0) FROM expenses WHERE coffee_shop_id = ? AND spent_at BETWEEN ? AND ?');
        $stmt->execute([$shopId, $start, $end]);
        $expenses = (float) $stmt->fetchColumn();

        $netProfit = $grossProfit - $expenses;
        $profitability = $revenue > 0 ? ($netProfit / $revenue) * 100 : 0;

        view('dashboard', [
            'revenue' => $revenue,
            'cogs' => $cogs,
            'grossProfit' => $grossProfit,
            'grossMargin' => $grossMargin,
            'expenses' => $expenses,
            'netProfit' => $netProfit,
            'profitability' => $profitability,
            'start' => $start,
            'end' => $end,
        ]);
    }
}
