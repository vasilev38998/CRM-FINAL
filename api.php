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
        $planStmt = db()->prepare('SELECT * FROM plans WHERE id = ?');
        $planStmt->execute([$payment['plan_id']]);
        $plan = $planStmt->fetch();
        if ($plan) {
            $start = date('Y-m-d H:i:s');
            $end = date('Y-m-d H:i:s', strtotime('+' . (int) $plan['duration_days'] . ' days'));
            $stmt = db()->prepare('INSERT INTO subscriptions (user_id, plan_id, status, start_at, end_at, created_at) VALUES (?, ?, ?, ?, ?, NOW())');
            $stmt->execute([$payment['user_id'], $plan['id'], 'active', $start, $end]);
        }
    }

    echo 'OK';
    exit;
}

http_response_code(404);
echo 'not found';
