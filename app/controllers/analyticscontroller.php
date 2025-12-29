<?php
namespace App\Controllers;

class AnalyticsController
{
    public function pnl(): void
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

        $breakEven = $grossMargin > 0 ? $expenses / ($grossMargin / 100) : 0;

        $stmt = db()->prepare('SELECT products.name, SUM(sales.total) AS revenue, SUM(sales.cogs) AS cogs FROM sales JOIN products ON sales.product_id = products.id WHERE sales.coffee_shop_id = ? AND sales.sold_at BETWEEN ? AND ? GROUP BY products.name');
        $stmt->execute([$shopId, $start, $end]);
        $productMargins = [];
        foreach ($stmt->fetchAll() as $row) {
            $rev = (float) $row['revenue'];
            $c = (float) $row['cogs'];
            $profit = $rev - $c;
            $margin = $rev > 0 ? ($profit / $rev) * 100 : 0;
            $productMargins[] = [
                'name' => $row['name'],
                'revenue' => $rev,
                'cogs' => $c,
                'profit' => $profit,
                'margin' => $margin,
            ];
        }

        view('analytics/pnl', [
            'revenue' => $revenue,
            'cogs' => $cogs,
            'grossProfit' => $grossProfit,
            'grossMargin' => $grossMargin,
            'expenses' => $expenses,
            'netProfit' => $netProfit,
            'profitability' => $profitability,
            'breakEven' => $breakEven,
            'productMargins' => $productMargins,
            'start' => $start,
            'end' => $end,
        ]);
    }
}
