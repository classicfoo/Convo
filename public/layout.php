<?php
require_once __DIR__ . '/../src/bootstrap.php';

function render_header(?array $user, string $title = 'Convo CRM'): void
{
    $is_admin = $user !== null && $user['role'] === 'admin';
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title><?= htmlspecialchars($title) ?></title>
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body class="bg-gray-50 text-gray-800">
        <header class="bg-white shadow">
            <div class="mx-auto max-w-4xl px-4 py-4 flex items-center justify-between">
                <div class="text-xl font-semibold text-indigo-600">Convo</div>
                <nav class="flex items-center gap-4 text-sm">
                    <?php if ($user !== null): ?>
                        <a href="/index.php" class="text-gray-700 hover:text-indigo-600">Home</a>
                        <?php if ($is_admin): ?>
                            <a href="/admin/users.php" class="text-gray-700 hover:text-indigo-600">Users</a>
                        <?php endif; ?>
                        <span class="text-gray-500">Signed in as <?= htmlspecialchars($user['display_name']) ?></span>
                        <a href="/logout.php" class="text-red-600 hover:underline">Logout</a>
                    <?php else: ?>
                        <a href="/login.php" class="text-gray-700 hover:text-indigo-600">Login</a>
                    <?php endif; ?>
                </nav>
            </div>
        </header>
        <main class="mx-auto max-w-4xl px-4 py-8">
    <?php
}

function render_footer(): void
{
    ?>
        </main>
    </body>
    </html>
    <?php
}

function render_errors(array $errors): void
{
    if (empty($errors)) {
        return;
    }
    ?>
    <div class="mb-4 rounded border border-red-200 bg-red-50 px-4 py-3 text-red-700">
        <ul class="list-disc pl-5">
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php
}

function render_success(?string $message): void
{
    if ($message === null) {
        return;
    }
    ?>
    <div class="mb-4 rounded border border-green-200 bg-green-50 px-4 py-3 text-green-700">
        <?= htmlspecialchars($message) ?>
    </div>
    <?php
}
