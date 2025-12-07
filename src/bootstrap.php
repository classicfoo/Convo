<?php
declare(strict_types=1);

session_name('convo_session');
session_set_cookie_params([
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();

define('CONVO_DB_PATH', __DIR__ . '/../data/database.sqlite');

function db(): PDO
{
    static $pdo;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $pdo = new PDO('sqlite:' . CONVO_DB_PATH);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec('PRAGMA foreign_keys = ON');

    return $pdo;
}

function ensure_schema(): void
{
    $sql = <<<SQL
        CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT NOT NULL UNIQUE,
            password_hash TEXT NOT NULL,
            display_name TEXT NOT NULL,
            role TEXT NOT NULL DEFAULT 'user',
            is_active INTEGER NOT NULL DEFAULT 1,
            created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
        );
    SQL;

    db()->exec($sql);
}

function seed_admin_user(): void
{
    $count = (int) db()->query('SELECT COUNT(*) FROM users')->fetchColumn();
    if ($count > 0) {
        return;
    }

    $username = 'admin';
    $password = getenv('CONVO_ADMIN_PASSWORD') ?: 'admin123';
    $hash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = db()->prepare('INSERT INTO users (username, password_hash, display_name, role, is_active) VALUES (:username, :hash, :display_name, :role, 1)');
    $stmt->execute([
        ':username' => $username,
        ':hash' => $hash,
        ':display_name' => 'Administrator',
        ':role' => 'admin',
    ]);
}

function current_user(): ?array
{
    if (!isset($_SESSION['user_id'])) {
        return null;
    }

    $stmt = db()->prepare('SELECT * FROM users WHERE id = :id');
    $stmt->execute([':id' => $_SESSION['user_id']]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row === false || (int) $row['is_active'] !== 1) {
        logout_user();
        return null;
    }

    return $row;
}

function require_login(): array
{
    $user = current_user();
    if ($user === null) {
        header('Location: /login.php');
        exit;
    }

    return $user;
}

function require_admin(): array
{
    $user = require_login();
    if ($user['role'] !== 'admin') {
        http_response_code(403);
        echo 'Forbidden: admin access required.';
        exit;
    }

    return $user;
}

function find_user_by_username(string $username): ?array
{
    $stmt = db()->prepare('SELECT * FROM users WHERE username = :username');
    $stmt->execute([':username' => $username]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row === false) {
        return null;
    }

    return $row;
}

function create_user(string $username, string $password, string $display_name, string $role): int
{
    $stmt = db()->prepare('INSERT INTO users (username, password_hash, display_name, role) VALUES (:username, :hash, :display_name, :role)');
    $stmt->execute([
        ':username' => $username,
        ':hash' => password_hash($password, PASSWORD_DEFAULT),
        ':display_name' => $display_name,
        ':role' => $role,
    ]);

    return (int) db()->lastInsertId();
}

function login_user(array $user): void
{
    session_regenerate_id(true);
    $_SESSION['user_id'] = $user['id'];
}

function logout_user(): void
{
    $_SESSION = [];
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_destroy();
    }
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
    }

    return $_SESSION['csrf_token'];
}

function verify_csrf_from_post(): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return;
    }

    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals(csrf_token(), $token)) {
        http_response_code(400);
        echo 'Invalid CSRF token.';
        exit;
    }
}

function bootstrap_app(): void
{
    ensure_schema();
    seed_admin_user();
}
