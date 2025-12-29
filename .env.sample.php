<?php
return [
    'app' => [
        'name' => 'CoffeeFin',
        'base_url' => 'https://example.com',
        'timezone' => 'Europe/Moscow',
        'debug' => true,
        'admin_email' => 'admin@example.com',
    ],
    'db' => [
        'driver' => 'mysql', // mysql | pgsql
        'host' => 'localhost',
        'port' => '3306',
        'database' => 'coffee_fin',
        'username' => 'db_user',
        'password' => 'db_password',
        'charset' => 'utf8mb4',
    ],
    'tinkoff' => [
        'terminal_key' => 'YOUR_TERMINAL_KEY',
        'secret_key' => 'YOUR_SECRET_KEY',
        'notification_url' => 'https://example.com/api.php?route=payment/webhook',
        'success_url' => 'https://example.com/index.php?route=subscription/success',
        'fail_url' => 'https://example.com/index.php?route=subscription/fail',
        'receipt' => [
            'taxation' => 'usn_income', // osn | usn_income | usn_income_outcome | envd | esn | patent
            'tax' => 'none', // none | vat0 | vat10 | vat20 | vat110 | vat120
            'payment_method' => 'full_prepayment', // full_prepayment | prepayment | advance | full_payment | partial_payment | credit | credit_payment
            'payment_object' => 'service', // commodity | service | work | payment | another
            'email' => 'receipt@example.com',
            'phone' => '',
        ],
    ],
];
