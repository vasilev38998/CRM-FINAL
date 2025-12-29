<?php
$config = require __DIR__ . '/../.env.php';

if (!isset($config['app']['timezone'])) {
    $config['app']['timezone'] = 'Europe/Moscow';
}

date_default_timezone_set($config['app']['timezone']);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

spl_autoload_register(function (string $class): void {
    $prefix = 'App\\';
    if (strpos($class, $prefix) !== 0) {
        return;
    }
    $relative = str_replace('\\', '/', substr($class, strlen($prefix)));
    $path = __DIR__ . '/' . strtolower($relative) . '.php';
    if (file_exists($path)) {
        require $path;
    }
});

function app_config(): array
{
    global $config;
    return $config;
}

function db(): PDO
{
    static $pdo = null;
    if ($pdo) {
        return $pdo;
    }
    $config = app_config();
    $db = $config['db'];
    $dsn = '';
    if ($db['driver'] === 'pgsql') {
        $dsn = sprintf('pgsql:host=%s;port=%s;dbname=%s', $db['host'], $db['port'], $db['database']);
    } else {
        $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=%s', $db['host'], $db['port'], $db['database'], $db['charset']);
    }

    $pdo = new PDO($dsn, $db['username'], $db['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    return $pdo;
}

function view(string $template, array $data = []): void
{
    extract($data);
    $templatePath = __DIR__ . '/views/' . $template . '.php';
    require __DIR__ . '/views/partials/header.php';
    require $templatePath;
    require __DIR__ . '/views/partials/footer.php';
}

function redirect(string $url): void
{
    header('Location: ' . $url);
    exit;
}

function is_post(): bool
{
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function require_auth(): void
{
    if (!current_user()) {
        redirect('index.php?route=auth/login');
    }
}

function flash(string $key, ?string $message = null): ?string
{
    if ($message !== null) {
        $_SESSION['flash'][$key] = $message;
        return null;
    }
    $value = $_SESSION['flash'][$key] ?? null;
    unset($_SESSION['flash'][$key]);
    return $value;
}

function has_active_subscription(int $userId): bool
{
    $stmt = db()->prepare('SELECT COUNT(*) FROM subscriptions WHERE user_id = ? AND status = ? AND end_at >= NOW()');
    $stmt->execute([$userId, 'active']);
    return (int) $stmt->fetchColumn() > 0;
}

function require_subscription(): void
{
    $user = current_user();
    if (!$user) {
        redirect('index.php?route=auth/login');
    }
    if (!has_active_subscription((int) $user['id'])) {
        redirect('index.php?route=subscription/plans');
    }
}

function current_shop_id(): ?int
{
    return $_SESSION['coffee_shop_id'] ?? null;
}

function require_shop(): void
{
    if (!current_shop_id()) {
        redirect('index.php?route=coffee/create');
    }
}

function is_admin(): bool
{
    $user = current_user();
    if (!$user) {
        return false;
    }
    return $user['role'] === 'admin';
}

function require_admin(): void
{
    if (!is_admin()) {
        flash('error', 'Доступ запрещён.');
        redirect('index.php');
    }
}

function base_url(string $path = ''): string
{
    $config = app_config();
    $base = rtrim($config['app']['base_url'], '/');
    return $base . '/' . ltrim($path, '/');
}

function money_format_ru(float $value): string
{
    return number_format($value, 2, ',', ' ') . ' ₽';
}

function csv_read(string $filePath, string $delimiter = ';'): array
{
    $rows = [];
    if (!file_exists($filePath)) {
        return $rows;
    }
    $handle = fopen($filePath, 'r');
    if (!$handle) {
        return $rows;
    }
    while (($data = fgetcsv($handle, 0, $delimiter)) !== false) {
        $rows[] = $data;
    }
    fclose($handle);
    return $rows;
}

function sanitize_string(?string $value): string
{
    return trim((string) $value);
}
