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
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        colors: {
                            brand: '#0ea5e9',
                            surface: '#ffffff',
                        },
                        fontFamily: {
                            sans: ['Inter', 'ui-sans-serif', 'system-ui', '-apple-system', 'Segoe UI', 'sans-serif'],
                        },
                        boxShadow: {
                            soft: '0 6px 18px rgba(15, 23, 42, 0.04)',
                        },
                    },
                },
            }
        </script>
    </head>
    <body class="min-h-screen bg-surface text-slate-900">
        <header class="sticky top-0 z-10 border-b border-slate-200 bg-white">
            <div class="mx-auto flex max-w-6xl items-center justify-between px-6 py-4">
                <div class="flex items-center gap-3 text-xl font-semibold text-slate-900">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-900">
                        <span class="text-lg">C</span>
                    </div>
                    <div class="leading-tight">
                        <div>Convo</div>
                        <div class="text-xs font-medium text-slate-500">Collaborative CRM</div>
                    </div>
                </div>
                <nav class="flex items-center gap-2 text-sm font-medium text-slate-700">
                    <?php if ($user !== null): ?>
                        <?php $currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); ?>
                        <a href="/index.php" class="inline-flex items-center gap-2 rounded-lg px-3 py-2 transition hover:bg-slate-100 <?= $currentPath === '/index.php' ? 'border border-slate-200 bg-slate-50 text-slate-900' : '' ?>">
                            <span class="h-2 w-2 rounded-full <?= $currentPath === '/index.php' ? 'bg-slate-900' : 'bg-slate-300' ?>"></span>
                            Home
                        </a>
                        <?php if ($is_admin): ?>
                            <a href="/admin/users.php" class="inline-flex items-center gap-2 rounded-lg px-3 py-2 transition hover:bg-slate-100 <?= $currentPath === '/admin/users.php' ? 'border border-slate-200 bg-slate-50 text-slate-900' : '' ?>">
                                <span class="h-2 w-2 rounded-full <?= $currentPath === '/admin/users.php' ? 'bg-slate-900' : 'bg-slate-300' ?>"></span>
                                Users
                            </a>
                        <?php endif; ?>
                        <div class="hidden items-center gap-3 rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 sm:flex">
                            <div class="flex h-8 w-8 items-center justify-center rounded-full border border-slate-200 bg-slate-50 text-xs font-bold text-slate-900">
                                <?= strtoupper(substr($user['display_name'], 0, 2)) ?>
                            </div>
                            <div class="leading-tight">
                                <div><?= htmlspecialchars($user['display_name']) ?></div>
                                <div class="text-xs text-slate-500"><?= htmlspecialchars(ucfirst($user['role'])) ?></div>
                            </div>
                        </div>
                        <a href="/logout.php" class="inline-flex items-center gap-2 rounded-lg border border-slate-200 px-3 py-2 text-slate-700 transition hover:border-slate-300 hover:bg-slate-50">
                            <span class="text-lg">↗</span>
                            Logout
                        </a>
                    <?php else: ?>
                        <a href="/login.php" class="inline-flex items-center gap-2 rounded-lg border border-slate-200 px-3 py-2 text-slate-900 transition hover:bg-slate-50">
                            <span class="h-2 w-2 rounded-full bg-slate-900"></span>
                            Login
                        </a>
                    <?php endif; ?>
                </nav>
            </div>
        </header>
        <main class="mx-auto max-w-6xl px-6 py-10">
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
    <div class="mb-4 flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
        <div class="mt-0.5 flex h-8 w-8 items-center justify-center rounded-full bg-red-100 text-base">&#9888;</div>
        <ul class="space-y-1">
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
    <div class="mb-4 flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-emerald-100 text-base">&#10003;</div>
        <span><?= htmlspecialchars($message) ?></span>
    </div>
    <?php
}
