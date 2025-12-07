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
    $pdo = db();

    $pdo->beginTransaction();

    $pdo->exec(<<<SQL
        CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT NOT NULL UNIQUE,
            password_hash TEXT NOT NULL,
            display_name TEXT NOT NULL,
            role TEXT NOT NULL DEFAULT 'user',
            is_active INTEGER NOT NULL DEFAULT 1,
            created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
        );
    SQL);

    $pdo->exec(<<<SQL
        CREATE TABLE IF NOT EXISTS accounts (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            industry TEXT,
            website TEXT,
            phone TEXT,
            billing_address TEXT,
            owner_user_id INTEGER,
            created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (owner_user_id) REFERENCES users(id) ON DELETE SET NULL
        );
    SQL);

    $pdo->exec(<<<SQL
        CREATE TABLE IF NOT EXISTS stages (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL UNIQUE,
            order_index INTEGER NOT NULL DEFAULT 0,
            is_closed INTEGER NOT NULL DEFAULT 0,
            is_won INTEGER NOT NULL DEFAULT 0
        );
    SQL);

    $pdo->exec(<<<SQL
        CREATE TABLE IF NOT EXISTS opportunities (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            account_id INTEGER NOT NULL,
            owner_user_id INTEGER,
            stage_id INTEGER NOT NULL,
            amount REAL NOT NULL DEFAULT 0,
            close_date TEXT,
            probability REAL,
            next_step TEXT,
            description TEXT,
            created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (account_id) REFERENCES accounts(id) ON DELETE CASCADE,
            FOREIGN KEY (owner_user_id) REFERENCES users(id) ON DELETE SET NULL,
            FOREIGN KEY (stage_id) REFERENCES stages(id) ON DELETE RESTRICT
        );
    SQL);

    $pdo->exec(<<<SQL
        CREATE TABLE IF NOT EXISTS contacts (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            account_id INTEGER NOT NULL,
            first_name TEXT NOT NULL,
            last_name TEXT NOT NULL,
            email TEXT,
            phone TEXT,
            role TEXT,
            created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (account_id) REFERENCES accounts(id) ON DELETE CASCADE
        );
    SQL);

    $pdo->exec(<<<SQL
        CREATE TABLE IF NOT EXISTS activities (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            opportunity_id INTEGER NOT NULL,
            user_id INTEGER,
            type TEXT NOT NULL,
            subject TEXT NOT NULL,
            details TEXT,
            due_date TEXT,
            completed_at TEXT,
            created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (opportunity_id) REFERENCES opportunities(id) ON DELETE CASCADE,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
        );
    SQL);

    $pdo->commit();
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

function seed_default_stages(): void
{
    $existing = db()->query('SELECT COUNT(*) FROM stages')->fetchColumn();
    if ((int) $existing > 0) {
        return;
    }

    $defaults = [
        ['Prospecting', 1, 0, 0],
        ['Qualification', 2, 0, 0],
        ['Proposal', 3, 0, 0],
        ['Negotiation', 4, 0, 0],
        ['Closed Won', 5, 1, 1],
        ['Closed Lost', 6, 1, 0],
    ];

    $stmt = db()->prepare('INSERT INTO stages (name, order_index, is_closed, is_won) VALUES (:name, :order_index, :is_closed, :is_won)');
    foreach ($defaults as [$name, $order, $closed, $won]) {
        $stmt->execute([
            ':name' => $name,
            ':order_index' => $order,
            ':is_closed' => $closed,
            ':is_won' => $won,
        ]);
    }
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

function create_account(array $data): int
{
    $stmt = db()->prepare('INSERT INTO accounts (name, industry, website, phone, billing_address, owner_user_id) VALUES (:name, :industry, :website, :phone, :billing_address, :owner_user_id)');
    $stmt->execute([
        ':name' => $data['name'],
        ':industry' => $data['industry'] ?? null,
        ':website' => $data['website'] ?? null,
        ':phone' => $data['phone'] ?? null,
        ':billing_address' => $data['billing_address'] ?? null,
        ':owner_user_id' => $data['owner_user_id'] ?? null,
    ]);

    return (int) db()->lastInsertId();
}

function get_account(int $id): ?array
{
    $stmt = db()->prepare('SELECT * FROM accounts WHERE id = :id');
    $stmt->execute([':id' => $id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    return $row === false ? null : $row;
}

function list_accounts(): array
{
    $stmt = db()->query('SELECT * FROM accounts ORDER BY name');

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function update_account(int $id, array $data): bool
{
    $stmt = db()->prepare('UPDATE accounts SET name = :name, industry = :industry, website = :website, phone = :phone, billing_address = :billing_address, owner_user_id = :owner_user_id, updated_at = CURRENT_TIMESTAMP WHERE id = :id');

    return $stmt->execute([
        ':name' => $data['name'],
        ':industry' => $data['industry'] ?? null,
        ':website' => $data['website'] ?? null,
        ':phone' => $data['phone'] ?? null,
        ':billing_address' => $data['billing_address'] ?? null,
        ':owner_user_id' => $data['owner_user_id'] ?? null,
        ':id' => $id,
    ]);
}

function delete_account(int $id): bool
{
    $stmt = db()->prepare('DELETE FROM accounts WHERE id = :id');

    return $stmt->execute([':id' => $id]);
}

function create_contact(array $data): int
{
    $stmt = db()->prepare('INSERT INTO contacts (account_id, first_name, last_name, email, phone, role) VALUES (:account_id, :first_name, :last_name, :email, :phone, :role)');
    $stmt->execute([
        ':account_id' => $data['account_id'],
        ':first_name' => $data['first_name'],
        ':last_name' => $data['last_name'],
        ':email' => $data['email'] ?? null,
        ':phone' => $data['phone'] ?? null,
        ':role' => $data['role'] ?? null,
    ]);

    return (int) db()->lastInsertId();
}

function get_contact(int $id): ?array
{
    $stmt = db()->prepare('SELECT * FROM contacts WHERE id = :id');
    $stmt->execute([':id' => $id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    return $row === false ? null : $row;
}

function list_contacts_by_account(int $account_id): array
{
    $stmt = db()->prepare('SELECT * FROM contacts WHERE account_id = :account_id ORDER BY last_name, first_name');
    $stmt->execute([':account_id' => $account_id]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function update_contact(int $id, array $data): bool
{
    $stmt = db()->prepare('UPDATE contacts SET first_name = :first_name, last_name = :last_name, email = :email, phone = :phone, role = :role, updated_at = CURRENT_TIMESTAMP WHERE id = :id');

    return $stmt->execute([
        ':first_name' => $data['first_name'],
        ':last_name' => $data['last_name'],
        ':email' => $data['email'] ?? null,
        ':phone' => $data['phone'] ?? null,
        ':role' => $data['role'] ?? null,
        ':id' => $id,
    ]);
}

function delete_contact(int $id): bool
{
    $stmt = db()->prepare('DELETE FROM contacts WHERE id = :id');

    return $stmt->execute([':id' => $id]);
}

function create_stage(string $name, int $order_index, bool $is_closed, bool $is_won): int
{
    $stmt = db()->prepare('INSERT INTO stages (name, order_index, is_closed, is_won) VALUES (:name, :order_index, :is_closed, :is_won)');
    $stmt->execute([
        ':name' => $name,
        ':order_index' => $order_index,
        ':is_closed' => $is_closed ? 1 : 0,
        ':is_won' => $is_won ? 1 : 0,
    ]);

    return (int) db()->lastInsertId();
}

function get_stage(int $id): ?array
{
    $stmt = db()->prepare('SELECT * FROM stages WHERE id = :id');
    $stmt->execute([':id' => $id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    return $row === false ? null : $row;
}

function list_stages(): array
{
    $stmt = db()->query('SELECT * FROM stages ORDER BY order_index');

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function update_stage(int $id, array $data): bool
{
    $stmt = db()->prepare('UPDATE stages SET name = :name, order_index = :order_index, is_closed = :is_closed, is_won = :is_won WHERE id = :id');

    return $stmt->execute([
        ':name' => $data['name'],
        ':order_index' => $data['order_index'],
        ':is_closed' => $data['is_closed'] ? 1 : 0,
        ':is_won' => $data['is_won'] ? 1 : 0,
        ':id' => $id,
    ]);
}

function delete_stage(int $id): bool
{
    $stmt = db()->prepare('DELETE FROM stages WHERE id = :id');

    return $stmt->execute([':id' => $id]);
}

function create_opportunity(array $data): int
{
    $stmt = db()->prepare('INSERT INTO opportunities (name, account_id, owner_user_id, stage_id, amount, close_date, probability, next_step, description) VALUES (:name, :account_id, :owner_user_id, :stage_id, :amount, :close_date, :probability, :next_step, :description)');
    $stmt->execute([
        ':name' => $data['name'],
        ':account_id' => $data['account_id'],
        ':owner_user_id' => $data['owner_user_id'] ?? null,
        ':stage_id' => $data['stage_id'],
        ':amount' => $data['amount'] ?? 0,
        ':close_date' => $data['close_date'] ?? null,
        ':probability' => $data['probability'] ?? null,
        ':next_step' => $data['next_step'] ?? null,
        ':description' => $data['description'] ?? null,
    ]);

    return (int) db()->lastInsertId();
}

function get_opportunity(int $id): ?array
{
    $stmt = db()->prepare('SELECT * FROM opportunities WHERE id = :id');
    $stmt->execute([':id' => $id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    return $row === false ? null : $row;
}

function list_opportunities(): array
{
    $stmt = db()->query('SELECT * FROM opportunities ORDER BY updated_at DESC');

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function update_opportunity(int $id, array $data): bool
{
    $stmt = db()->prepare('UPDATE opportunities SET name = :name, account_id = :account_id, owner_user_id = :owner_user_id, stage_id = :stage_id, amount = :amount, close_date = :close_date, probability = :probability, next_step = :next_step, description = :description, updated_at = CURRENT_TIMESTAMP WHERE id = :id');

    return $stmt->execute([
        ':name' => $data['name'],
        ':account_id' => $data['account_id'],
        ':owner_user_id' => $data['owner_user_id'] ?? null,
        ':stage_id' => $data['stage_id'],
        ':amount' => $data['amount'] ?? 0,
        ':close_date' => $data['close_date'] ?? null,
        ':probability' => $data['probability'] ?? null,
        ':next_step' => $data['next_step'] ?? null,
        ':description' => $data['description'] ?? null,
        ':id' => $id,
    ]);
}

function delete_opportunity(int $id): bool
{
    $stmt = db()->prepare('DELETE FROM opportunities WHERE id = :id');

    return $stmt->execute([':id' => $id]);
}

function create_activity(array $data): int
{
    $stmt = db()->prepare('INSERT INTO activities (opportunity_id, user_id, type, subject, details, due_date, completed_at) VALUES (:opportunity_id, :user_id, :type, :subject, :details, :due_date, :completed_at)');
    $stmt->execute([
        ':opportunity_id' => $data['opportunity_id'],
        ':user_id' => $data['user_id'] ?? null,
        ':type' => $data['type'],
        ':subject' => $data['subject'],
        ':details' => $data['details'] ?? null,
        ':due_date' => $data['due_date'] ?? null,
        ':completed_at' => $data['completed_at'] ?? null,
    ]);

    return (int) db()->lastInsertId();
}

function get_activity(int $id): ?array
{
    $stmt = db()->prepare('SELECT * FROM activities WHERE id = :id');
    $stmt->execute([':id' => $id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    return $row === false ? null : $row;
}

function list_activities_for_opportunity(int $opportunity_id): array
{
    $stmt = db()->prepare('SELECT * FROM activities WHERE opportunity_id = :opportunity_id ORDER BY created_at DESC');
    $stmt->execute([':opportunity_id' => $opportunity_id]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function update_activity(int $id, array $data): bool
{
    $stmt = db()->prepare('UPDATE activities SET type = :type, subject = :subject, details = :details, due_date = :due_date, completed_at = :completed_at WHERE id = :id');

    return $stmt->execute([
        ':type' => $data['type'],
        ':subject' => $data['subject'],
        ':details' => $data['details'] ?? null,
        ':due_date' => $data['due_date'] ?? null,
        ':completed_at' => $data['completed_at'] ?? null,
        ':id' => $id,
    ]);
}

function delete_activity(int $id): bool
{
    $stmt = db()->prepare('DELETE FROM activities WHERE id = :id');

    return $stmt->execute([':id' => $id]);
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
    seed_default_stages();
}
