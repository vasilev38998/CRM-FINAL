<?php
require __DIR__ . '/app/bootstrap.php';

$route = $_GET['route'] ?? '';

if ($route === 'payment/webhook') {
    $payload = json_decode(file_get_contents('php://input'), true) ?? [];
    $terminalKey = $payload['TerminalKey'] ?? '';
    $status = $payload['Status'] ?? '';
    $paymentId = $payload['PaymentId'] ?? '';
    $orderId = $payload['OrderId'] ?? '';

    $config = app_config();
    if ($terminalKey !== ($config['tinkoff']['terminal_key'] ?? '')) {
        http_response_code(400);
        echo 'bad terminal';
        exit;
    }

    $stmt = db()->prepare('SELECT * FROM payments WHERE order_id = ?');
    $stmt->execute([$orderId]);
    $payment = $stmt->fetch();
    if (!$payment) {
        http_response_code(404);
        echo 'payment not found';
        exit;
    }

    $stmt = db()->prepare('UPDATE payments SET status = ?, payment_id = ? WHERE id = ?');
    $stmt->execute([$status, $paymentId, $payment['id']]);

    if (in_array($status, ['CONFIRMED', 'AUTHORIZED', 'DONE'], true)) {
        if (!empty($payment['processed_at'])) {
            echo 'OK';
            exit;
        }
        $planStmt = db()->prepare('SELECT * FROM plans WHERE id = ?');
        $planStmt->execute([$payment['plan_id']]);
        $plan = $planStmt->fetch();
        if ($plan) {
            $stmt = db()->prepare('SELECT * FROM subscriptions WHERE user_id = ? AND status = ? AND end_at >= NOW() ORDER BY end_at DESC LIMIT 1');
            $stmt->execute([$payment['user_id'], 'active']);
            $active = $stmt->fetch();
            if ($active) {
                $newEnd = date('Y-m-d H:i:s', strtotime($active['end_at'] . ' +' . (int) $plan['duration_days'] . ' days'));
                $stmt = db()->prepare('UPDATE subscriptions SET end_at = ?, plan_id = ? WHERE id = ?');
                $stmt->execute([$newEnd, $plan['id'], $active['id']]);
            } else {
                $start = date('Y-m-d H:i:s');
                $end = date('Y-m-d H:i:s', strtotime('+' . (int) $plan['duration_days'] . ' days'));
                $stmt = db()->prepare('INSERT INTO subscriptions (user_id, plan_id, status, start_at, end_at, created_at) VALUES (?, ?, ?, ?, ?, NOW())');
                $stmt->execute([$payment['user_id'], $plan['id'], 'active', $start, $end]);
            }
            db()->prepare('UPDATE payments SET processed_at = NOW() WHERE id = ?')->execute([$payment['id']]);
        }
    }

    echo 'OK';
    exit;
}

if ($route === 'pos/sales') {
    $token = $_SERVER['HTTP_X_API_TOKEN'] ?? ($_GET['token'] ?? '');
    if ($token === '') {
        http_response_code(401);
        echo 'token required';
        exit;
    }
    $stmt = db()->prepare('SELECT * FROM api_tokens WHERE token = ?');
    $stmt->execute([$token]);
    $apiToken = $stmt->fetch();
    if (!$apiToken) {
        http_response_code(403);
        echo 'invalid token';
        exit;
    }
    $payload = json_decode(file_get_contents('php://input'), true) ?? [];
    $shopId = (int) ($payload['coffee_shop_id'] ?? 0);
    $items = $payload['items'] ?? [];
    if ($shopId === 0 || !$items) {
        http_response_code(400);
        echo 'invalid payload';
        exit;
    }
    foreach ($items as $item) {
        $productName = sanitize_string($item['product_name'] ?? '');
        $qty = (float) ($item['qty'] ?? 0);
        $price = (float) ($item['price'] ?? 0);
        $date = sanitize_string($item['date'] ?? date('Y-m-d'));
        if ($productName === '' || $qty <= 0 || $price <= 0) {
            continue;
        }
        $stmt = db()->prepare('SELECT * FROM products WHERE coffee_shop_id = ? AND name = ?');
        $stmt->execute([$shopId, $productName]);
        $product = $stmt->fetch();
        if (!$product) {
            continue;
        }
        $stmt = db()->prepare('SELECT * FROM recipes WHERE coffee_shop_id = ? AND product_id = ?');
        $stmt->execute([$shopId, $product['id']]);
        $recipeItems = $stmt->fetchAll();
        $cogs = 0.0;
        foreach ($recipeItems as $recipeItem) {
            $ingredientStmt = db()->prepare('SELECT * FROM ingredients WHERE id = ?');
            $ingredientStmt->execute([$recipeItem['ingredient_id']]);
            $ingredient = $ingredientStmt->fetch();
            if ($ingredient) {
                $cost = $recipeItem['qty'] * $qty * (float) $ingredient['avg_price'];
                $cogs += $cost;
                $newQty = (float) $ingredient['stock_qty'] - ($recipeItem['qty'] * $qty);
                db()->prepare('UPDATE ingredients SET stock_qty = ? WHERE id = ?')
                    ->execute([$newQty, $ingredient['id']]);
            }
        }
        db()->prepare('INSERT INTO sales (coffee_shop_id, product_id, qty, price, total, cogs, sold_at) VALUES (?, ?, ?, ?, ?, ?, ?)')
            ->execute([$shopId, $product['id'], $qty, $price, $qty * $price, $cogs, $date]);
    }
    echo 'OK';
    exit;
}

http_response_code(404);
echo 'not found';
