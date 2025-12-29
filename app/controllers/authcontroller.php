<?php
namespace App\Controllers;

class AuthController
{
    public function login(): void
    {
        if (is_post()) {
            $email = sanitize_string($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $stmt = db()->prepare('SELECT * FROM users WHERE email = ?');
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            if ($user && password_verify($password, $user['password_hash'])) {
                $_SESSION['user'] = $user;
                redirect('index.php?route=dashboard');
            }
            flash('error', 'Неверный email или пароль.');
        }
        view('auth/login');
    }

    public function register(): void
    {
        if (is_post()) {
            $name = sanitize_string($_POST['name'] ?? '');
            $email = sanitize_string($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            if ($name === '' || $email === '' || $password === '') {
                flash('error', 'Заполните все поля.');
                view('auth/register');
                return;
            }
            $stmt = db()->prepare('SELECT COUNT(*) FROM users WHERE email = ?');
            $stmt->execute([$email]);
            if ((int) $stmt->fetchColumn() > 0) {
                flash('error', 'Пользователь с таким email уже существует.');
                view('auth/register');
                return;
            }
            $role = 'user';
            $config = app_config();
            if (isset($config['app']['admin_email']) && $config['app']['admin_email'] === $email) {
                $role = 'admin';
            }
            $stmt = db()->prepare('INSERT INTO users (name, email, password_hash, role, created_at) VALUES (?, ?, ?, ?, NOW())');
            $stmt->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT), $role]);
            $userId = (int) db()->lastInsertId();
            $stmt = db()->prepare('SELECT * FROM users WHERE id = ?');
            $stmt->execute([$userId]);
            $_SESSION['user'] = $stmt->fetch();
            redirect('index.php?route=coffee/create');
        }
        view('auth/register');
    }

    public function logout(): void
    {
        session_destroy();
        redirect('index.php');
    }

    public function forgot(): void
    {
        if (is_post()) {
            $email = sanitize_string($_POST['email'] ?? '');
            $stmt = db()->prepare('SELECT * FROM users WHERE email = ?');
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            if ($user) {
                $token = bin2hex(random_bytes(16));
                $stmt = db()->prepare('INSERT INTO password_resets (user_id, token, created_at) VALUES (?, ?, NOW())');
                $stmt->execute([$user['id'], $token]);
                $resetLink = 'index.php?route=auth/reset&token=' . $token;
                flash('success', 'Ссылка для сброса сформирована: ' . $resetLink);
            } else {
                flash('error', 'Email не найден.');
            }
        }
        view('auth/forgot');
    }

    public function reset(): void
    {
        $token = sanitize_string($_GET['token'] ?? '');
        if ($token === '') {
            flash('error', 'Некорректный токен.');
            redirect('index.php?route=auth/forgot');
        }
        if (is_post()) {
            $password = $_POST['password'] ?? '';
            if ($password === '') {
                flash('error', 'Введите новый пароль.');
                view('auth/reset', ['token' => $token]);
                return;
            }
            $stmt = db()->prepare('SELECT * FROM password_resets WHERE token = ?');
            $stmt->execute([$token]);
            $reset = $stmt->fetch();
            if (!$reset) {
                flash('error', 'Токен не найден.');
                redirect('index.php?route=auth/forgot');
            }
            $stmt = db()->prepare('UPDATE users SET password_hash = ? WHERE id = ?');
            $stmt->execute([password_hash($password, PASSWORD_DEFAULT), $reset['user_id']]);
            db()->prepare('DELETE FROM password_resets WHERE token = ?')->execute([$token]);
            flash('success', 'Пароль обновлён. Войдите заново.');
            redirect('index.php?route=auth/login');
        }
        view('auth/reset', ['token' => $token]);
    }
}
