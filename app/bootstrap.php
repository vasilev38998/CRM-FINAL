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

<<<<<<< HEAD
function log_event(string $message, array $context = []): void
{
    $logPath = __DIR__ . '/../storage/app.log';
    $entry = [
        'time' => date('Y-m-d H:i:s'),
        'message' => $message,
        'context' => $context,
    ];
    @file_put_contents($logPath, json_encode($entry, JSON_UNESCAPED_UNICODE) . PHP_EOL, FILE_APPEND);
}

=======
>>>>>>> origin/main
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

<<<<<<< HEAD
function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrf_token()) . '">';
}

function verify_csrf(): void
{
    if (!is_post()) {
        return;
    }
    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        flash('error', 'Ошибка безопасности. Обновите страницу и попробуйте снова.');
        redirect($_SERVER['HTTP_REFERER'] ?? 'index.php');
    }
}

=======
>>>>>>> origin/main
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

<<<<<<< HEAD
function user_ip(): string
{
    return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
}

function audit_log(string $entity, string $action, array $payload = []): void
{
    $user = current_user();
    $shopId = current_shop_id();
    $stmt = db()->prepare('INSERT INTO audit_logs (user_id, coffee_shop_id, entity, action, payload, created_at) VALUES (?, ?, ?, ?, ?, NOW())');
    $stmt->execute([
        $user['id'] ?? null,
        $shopId,
        $entity,
        $action,
        json_encode($payload, JSON_UNESCAPED_UNICODE),
    ]);
}

=======
>>>>>>> origin/main
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

<<<<<<< HEAD
function user_shop_role(int $shopId, int $userId): ?string
{
    $stmt = db()->prepare('SELECT role FROM shop_users WHERE coffee_shop_id = ? AND user_id = ?');
    $stmt->execute([$shopId, $userId]);
    $role = $stmt->fetchColumn();
    return $role ? (string) $role : null;
}

function require_shop_role(array $roles): void
{
    $user = current_user();
    if (!$user) {
        redirect('index.php?route=auth/login');
    }
    $shopId = current_shop_id();
    $role = $shopId ? user_shop_role($shopId, (int) $user['id']) : null;
    if (!$role || !in_array($role, $roles, true)) {
        flash('error', 'Недостаточно прав доступа.');
        redirect('index.php?route=dashboard');
    }
}

function require_shop(): void
{
    $shopId = current_shop_id();
    if (!$shopId) {
        redirect('index.php?route=coffee/create');
    }
    $user = current_user();
    if (!$user) {
        redirect('index.php?route=auth/login');
    }
    if (!user_shop_role($shopId, (int) $user['id'])) {
        flash('error', 'Нет доступа к выбранной кофейне.');
        redirect('index.php?route=coffee/select');
    }
=======
function require_shop(): void
{
    if (!current_shop_id()) {
        redirect('index.php?route=coffee/create');
    }
>>>>>>> origin/main
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

<<<<<<< HEAD
function xlsx_read(string $filePath): array
{
    $rows = [];
    if (!class_exists('ZipArchive')) {
        return $rows;
    }
    $zip = new ZipArchive();
    if ($zip->open($filePath) !== true) {
        return $rows;
    }
    $sharedStrings = [];
    $sharedXml = $zip->getFromName('xl/sharedStrings.xml');
    if ($sharedXml) {
        $xml = simplexml_load_string($sharedXml);
        if ($xml && isset($xml->si)) {
            foreach ($xml->si as $si) {
                $sharedStrings[] = (string) $si->t;
            }
        }
    }
    $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
    if ($sheetXml) {
        $xml = simplexml_load_string($sheetXml);
        if ($xml && isset($xml->sheetData->row)) {
            foreach ($xml->sheetData->row as $row) {
                $rowData = [];
                foreach ($row->c as $c) {
                    $value = (string) $c->v;
                    $type = (string) $c['t'];
                    if ($type === 's') {
                        $idx = (int) $value;
                        $value = $sharedStrings[$idx] ?? '';
                    }
                    $rowData[] = $value;
                }
                $rows[] = $rowData;
            }
        }
    }
    $zip->close();
    return $rows;
}

function tabular_read(string $filePath, string $extension): array
{
    if ($extension === 'xlsx') {
        return xlsx_read($filePath);
    }
    return csv_read($filePath);
}

function export_excel_xml(array $headers, array $rows): string
{
    $xml = '<?xml version="1.0"?>';
    $xml .= '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" ';
    $xml .= 'xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">';
    $xml .= '<Worksheet ss:Name="Sheet1"><Table>';
    $xml .= '<Row>';
    foreach ($headers as $header) {
        $xml .= '<Cell><Data ss:Type="String">' . htmlspecialchars((string) $header) . '</Data></Cell>';
    }
    $xml .= '</Row>';
    foreach ($rows as $row) {
        $xml .= '<Row>';
        foreach ($row as $cell) {
            $type = is_numeric($cell) ? 'Number' : 'String';
            $xml .= '<Cell><Data ss:Type="' . $type . '">' . htmlspecialchars((string) $cell) . '</Data></Cell>';
        }
        $xml .= '</Row>';
    }
    $xml .= '</Table></Worksheet></Workbook>';
    return $xml;
}

=======
>>>>>>> origin/main
function sanitize_string(?string $value): string
{
    return trim((string) $value);
}
