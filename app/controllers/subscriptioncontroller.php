<?php
namespace App\Controllers;

class SubscriptionController
{
    public function plans(): void
    {
        $plans = db()->query('SELECT * FROM plans ORDER BY price ASC')->fetchAll();
        view('subscription/plans', ['plans' => $plans]);
    }

    public function pay(): void
    {
        require_auth();
        $planId = (int) ($_GET['plan_id'] ?? 0);
        $stmt = db()->prepare('SELECT * FROM plans WHERE id = ?');
        $stmt->execute([$planId]);
        $plan = $stmt->fetch();
        if (!$plan) {
            flash('error', 'Тариф не найден.');
            redirect('index.php?route=subscription/plans');
        }

        $user = current_user();
        $orderId = 'order_' . $user['id'] . '_' . time();
        $amount = (int) round($plan['price'] * 100);

        $config = app_config();
        $payload = [
            'TerminalKey' => $config['tinkoff']['terminal_key'],
            'Amount' => $amount,
            'OrderId' => $orderId,
            'Description' => 'Подписка ' . $plan['name'],
            'SuccessURL' => $config['tinkoff']['success_url'],
            'FailURL' => $config['tinkoff']['fail_url'],
            'NotificationURL' => $config['tinkoff']['notification_url'],
            'PayType' => 'O',
        ];

        $payload['Token'] = $this->makeToken($payload, $config['tinkoff']['secret_key']);

        $response = $this->sendRequest('https://securepay.tinkoff.ru/v2/Init', $payload);
        $this->logPaymentAttempt($orderId, $payload, $response);

        $stmt = db()->prepare('INSERT INTO payments (user_id, plan_id, amount, status, provider, order_id, payment_id, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())');
        $stmt->execute([
            $user['id'],
            $plan['id'],
            $plan['price'],
            $response['Status'] ?? 'NEW',
            'tinkoff',
            $orderId,
            $response['PaymentId'] ?? null,
        ]);

        if (!empty($response['PaymentURL'])) {
            redirect($response['PaymentURL']);
        }

        $errorMessage = $this->buildPaymentErrorMessage($response);
        flash('error', $errorMessage);
        redirect('index.php?route=subscription/plans');
    }

    public function success(): void
    {
        view('subscription/success');
    }

    public function fail(): void
    {
        view('subscription/fail');
    }

    public function manage(): void
    {
        require_auth();
        $user = current_user();
        $stmt = db()->prepare('SELECT subscriptions.*, plans.name AS plan_name FROM subscriptions JOIN plans ON subscriptions.plan_id = plans.id WHERE subscriptions.user_id = ? ORDER BY subscriptions.end_at DESC LIMIT 1');
        $stmt->execute([$user['id']]);
        $subscription = $stmt->fetch();
        view('subscription/manage', ['subscription' => $subscription]);
    }

    private function makeToken(array $data, string $secret): string
    {
        $data['Password'] = $secret;
        ksort($data);
        $values = '';
        foreach ($data as $value) {
            if (is_array($value)) {
                continue;
            }
            $values .= $value;
        }
        return hash('sha256', $values);
    }

    private function sendRequest(string $url, array $payload): array
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        $response = curl_exec($ch);
        $curlError = curl_error($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if (!$response) {
            return [
                '_error' => $curlError ?: 'Пустой ответ от сервера оплаты.',
                '_http_code' => $httpCode,
            ];
        }
        $data = json_decode($response, true);
        if (!is_array($data)) {
            return [
                '_error' => 'Некорректный ответ от сервера оплаты.',
                '_http_code' => $httpCode,
                '_raw' => $response,
            ];
        }
        $data['_http_code'] = $httpCode;
        return $data;
    }

    private function buildPaymentErrorMessage(array $response): string
    {
        if (!empty($response['_error'])) {
            return 'Не удалось создать платёж. Ошибка соединения: ' . $response['_error'];
        }
        if (!empty($response['Message'])) {
            $code = $response['ErrorCode'] ?? 'N/A';
            return 'Не удалось создать платёж. Код ошибки: ' . $code . '. ' . $response['Message'];
        }
        if (!empty($response['Details'])) {
            return 'Не удалось создать платёж. ' . $response['Details'];
        }
        return 'Не удалось создать платёж. Проверьте настройки Тинькофф.';
    }

    private function logPaymentAttempt(string $orderId, array $payload, array $response): void
    {
        $logPath = __DIR__ . '/../../storage/payments.log';
        $entry = [
            'time' => date('Y-m-d H:i:s'),
            'order_id' => $orderId,
            'payload' => [
                'TerminalKey' => $payload['TerminalKey'] ?? null,
                'Amount' => $payload['Amount'] ?? null,
                'OrderId' => $payload['OrderId'] ?? null,
                'Description' => $payload['Description'] ?? null,
                'SuccessURL' => $payload['SuccessURL'] ?? null,
                'FailURL' => $payload['FailURL'] ?? null,
                'NotificationURL' => $payload['NotificationURL'] ?? null,
            ],
            'response' => $response,
        ];
        @file_put_contents($logPath, json_encode($entry, JSON_UNESCAPED_UNICODE) . PHP_EOL, FILE_APPEND);
    }
}
