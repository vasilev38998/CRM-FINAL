<?php
namespace App\Controllers;

class BackupController
{
    public function index(): void
    {
        require_auth();
        require_admin();
        $dir = __DIR__ . '/../../storage/backups';
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }
        $files = array_values(array_filter(scandir($dir), function ($file) {
            return strpos($file, 'backup_') === 0;
        }));
        rsort($files);
        view('admin/backups', ['files' => $files]);
    }

    public function create(): void
    {
        require_auth();
        require_admin();
        verify_csrf();
        $tables = [
            'users',
            'coffee_shops',
            'shop_users',
            'plans',
            'subscriptions',
            'payments',
            'ingredients',
            'purchases',
            'products',
            'recipes',
            'sales',
            'expenses',
            'cash_transactions',
            'budgets',
            'budget_items',
            'audit_logs',
            'login_attempts',
            'scenarios',
        ];
        $sql = "";
        foreach ($tables as $table) {
            $sql .= "-- Table: {$table}\n";
            $stmt = db()->query('SELECT * FROM ' . $table);
            $rows = $stmt->fetchAll();
            foreach ($rows as $row) {
                $columns = array_map(function ($col) {
                    return '`' . $col . '`';
                }, array_keys($row));
                $values = array_map(function ($value) {
                    if ($value === null) {
                        return 'NULL';
                    }
                    return "'" . addslashes((string) $value) . "'";
                }, array_values($row));
                $sql .= 'INSERT INTO `' . $table . '` (' . implode(',', $columns) . ') VALUES (' . implode(',', $values) . ");\n";
            }
            $sql .= "\n";
        }

        $dir = __DIR__ . '/../../storage/backups';
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }
        $filename = 'backup_' . date('Ymd_His') . '.sql';
        file_put_contents($dir . '/' . $filename, $sql);
        audit_log('backups', 'create', ['file' => $filename]);
        flash('success', 'Бэкап создан: ' . $filename);
        redirect('index.php?route=admin/backups');
    }

    public function download(): void
    {
        require_auth();
        require_admin();
        $file = basename($_GET['file'] ?? '');
        $path = __DIR__ . '/../../storage/backups/' . $file;
        if (!file_exists($path)) {
            flash('error', 'Файл не найден.');
            redirect('index.php?route=admin/backups');
        }
        header('Content-Type: application/sql');
        header('Content-Disposition: attachment; filename="' . $file . '"');
        readfile($path);
        exit;
    }

    public function restore(): void
    {
        require_auth();
        require_admin();
        if (is_post()) {
            verify_csrf();
            if (!isset($_FILES['sql_file']) || $_FILES['sql_file']['error'] !== UPLOAD_ERR_OK) {
                flash('error', 'Ошибка загрузки файла.');
                redirect('index.php?route=admin/backups');
            }
            $sql = file_get_contents($_FILES['sql_file']['tmp_name']);
            $statements = array_filter(array_map('trim', explode(";\n", $sql)));
            foreach ($statements as $statement) {
                db()->exec($statement);
            }
            audit_log('backups', 'restore', ['file' => $_FILES['sql_file']['name']]);
            flash('success', 'Бэкап восстановлен.');
            redirect('index.php?route=admin/backups');
        }
        redirect('index.php?route=admin/backups');
    }
}
