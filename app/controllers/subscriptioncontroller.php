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

        flash('error', 'Не удалось создать платёж. Проверьте настройки Тинькофф.');
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
        $stmt = db()->prepare('SELECT subscriptions.*, plans.name AS plan_name FROM subscriptions JOIN plans ON subscriptions.plan_id = plans.id WHERE subscriptions.user_id = ? ORDER BY subscriptions.end_at DESC');
        $stmt->execute([$user['id']]);
        $subscriptions = $stmt->fetchAll();
        view('subscription/manage', ['subscriptions' => $subscriptions]);
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
        $response = curl_exec($ch);
        curl_close($ch);
        if (!$response) {
            return [];
        }
        $data = json_decode($response, true);
        return is_array($data) ? $data : [];
    }
}
