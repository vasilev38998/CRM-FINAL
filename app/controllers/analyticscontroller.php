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

    public function network(): void
    {
        require_auth();
        require_subscription();
        $user = current_user();
        $start = $_GET['from'] ?? date('Y-m-01');
        $end = $_GET['to'] ?? date('Y-m-t');

        $stmt = db()->prepare('SELECT coffee_shop_id AS id FROM shop_users WHERE user_id = ?');
        $stmt->execute([$user['id']]);
        $shopIds = array_column($stmt->fetchAll(), 'id');
        if (!$shopIds) {
            flash('error', 'Добавьте хотя бы одну кофейню.');
            redirect('index.php?route=coffee/create');
        }

        $placeholders = implode(',', array_fill(0, count($shopIds), '?'));

        $stmt = db()->prepare('SELECT COALESCE(SUM(total), 0) FROM sales WHERE coffee_shop_id IN (' . $placeholders . ') AND sold_at BETWEEN ? AND ?');
        $stmt->execute(array_merge($shopIds, [$start, $end]));
        $revenue = (float) $stmt->fetchColumn();

        $stmt = db()->prepare('SELECT COALESCE(SUM(cogs), 0) FROM sales WHERE coffee_shop_id IN (' . $placeholders . ') AND sold_at BETWEEN ? AND ?');
        $stmt->execute(array_merge($shopIds, [$start, $end]));
        $cogs = (float) $stmt->fetchColumn();

        $grossProfit = $revenue - $cogs;
        $grossMargin = $revenue > 0 ? ($grossProfit / $revenue) * 100 : 0;

        $stmt = db()->prepare('SELECT COALESCE(SUM(amount), 0) FROM expenses WHERE coffee_shop_id IN (' . $placeholders . ') AND spent_at BETWEEN ? AND ?');
        $stmt->execute(array_merge($shopIds, [$start, $end]));
        $expenses = (float) $stmt->fetchColumn();

        $netProfit = $grossProfit - $expenses;
        $profitability = $revenue > 0 ? ($netProfit / $revenue) * 100 : 0;

        view('analytics/network', [
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

    public function abc(): void
    {
        require_auth();
        require_subscription();
        require_shop();
        $stmt = db()->prepare('SELECT products.name, SUM(sales.total) AS revenue FROM sales JOIN products ON sales.product_id = products.id WHERE sales.coffee_shop_id = ? GROUP BY products.name ORDER BY revenue DESC');
        $stmt->execute([current_shop_id()]);
        $rows = $stmt->fetchAll();
        $total = 0.0;
        foreach ($rows as $row) {
            $total += (float) $row['revenue'];
        }
        $result = [];
        $cum = 0.0;
        foreach ($rows as $row) {
            $revenue = (float) $row['revenue'];
            $share = $total > 0 ? ($revenue / $total) * 100 : 0;
            $cum += $share;
            $abc = $cum <= 80 ? 'A' : ($cum <= 95 ? 'B' : 'C');
            $result[] = [
                'name' => $row['name'],
                'revenue' => $revenue,
                'share' => $share,
                'abc' => $abc,
            ];
        }

        $xyz = $this->calculateXyz();
        foreach ($result as &$item) {
            $item['xyz'] = $xyz[$item['name']] ?? 'Z';
        }

        view('analytics/abc', ['items' => $result]);
    }

    public function seasonality(): void
    {
        require_auth();
        require_subscription();
        require_shop();
        $stmt = db()->prepare(\"SELECT DATE_FORMAT(sold_at, '%Y-%m') AS period, SUM(total) AS revenue FROM sales WHERE coffee_shop_id = ? GROUP BY period ORDER BY period\");
        $stmt->execute([current_shop_id()]);
        $rows = $stmt->fetchAll();
        view('analytics/seasonality', ['rows' => $rows]);
    }

    private function calculateXyz(): array
    {
        $stmt = db()->prepare(\"SELECT products.name, DATE_FORMAT(sales.sold_at, '%Y-%m') AS period, SUM(sales.total) AS revenue FROM sales JOIN products ON sales.product_id = products.id WHERE sales.coffee_shop_id = ? GROUP BY products.name, period ORDER BY products.name, period\");
        $stmt->execute([current_shop_id()]);
        $rows = $stmt->fetchAll();
        $data = [];
        foreach ($rows as $row) {
            $data[$row['name']][] = (float) $row['revenue'];
        }
        $result = [];
        foreach ($data as $name => $values) {
            $mean = array_sum($values) / max(count($values), 1);
            $variance = 0.0;
            foreach ($values as $value) {
                $variance += pow($value - $mean, 2);
            }
            $std = count($values) > 0 ? sqrt($variance / count($values)) : 0;
            $cv = $mean > 0 ? $std / $mean : 1;
            $result[$name] = $cv <= 0.1 ? 'X' : ($cv <= 0.25 ? 'Y' : 'Z');
        }
        return $result;
    }
}
