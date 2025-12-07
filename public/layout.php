<?php
require_once __DIR__ . '/../src/bootstrap.php';

function render_header(?array $user, string $title = 'Convo CRM'): void
{
    $is_admin = $user !== null && $user['role'] === 'admin';
    $navItems = [
        ['label' => 'Dashboard', 'href' => '/index.php'],
        ['label' => 'Accounts', 'href' => '/accounts.php'],
        ['label' => 'Opportunities', 'href' => '/opportunities.php'],
        ['label' => 'Pipeline', 'href' => '/pipeline.php'],
    ];
    $currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/';
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
                            brand: '#10a37f',
                            surface: '#f6f8fa',
                        },
                        fontFamily: {
                            sans: ['Inter', 'ui-sans-serif', 'system-ui', '-apple-system', 'Segoe UI', 'sans-serif'],
                        },
                        boxShadow: {
                            subtle: '0 12px 30px rgba(15, 23, 42, 0.08)',
                        },
                    },
                },
            }
        </script>
    </head>
    <body class="min-h-screen bg-surface text-slate-900">
        <header class="sticky top-0 z-10 border-b border-slate-200 bg-white/90 backdrop-blur">
            <div class="mx-auto flex max-w-6xl items-center justify-between gap-6 px-6 py-4">
                <div class="flex items-center gap-3 text-xl font-semibold text-slate-900">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-900 text-white shadow-subtle">
                        <span class="text-lg">C</span>
                    </div>
                    <div class="leading-tight">
                        <div>Convo</div>
                        <div class="text-xs font-medium text-slate-500">Collaborative CRM</div>
                    </div>
                </div>

                <?php if ($user !== null): ?>
                    <nav class="hidden items-center gap-1 rounded-full border border-slate-200 bg-white px-1 py-1 text-sm font-medium text-slate-700 shadow-sm lg:flex">
                        <?php foreach ($navItems as $item):
                            $isActive = $currentPath === $item['href'];
                            ?>
                            <a href="<?= $item['href'] ?>"
                               class="inline-flex items-center gap-2 rounded-full px-4 py-2 transition <?= $isActive ? 'bg-slate-900 text-white shadow-subtle' : 'hover:bg-slate-100' ?>">
                                <span class="h-2 w-2 rounded-full <?= $isActive ? 'bg-white' : 'bg-slate-400' ?>"></span>
                                <?= htmlspecialchars($item['label']) ?>
                            </a>
                        <?php endforeach; ?>
                    </nav>
                <?php endif; ?>

                <div class="flex flex-1 items-center justify-end gap-3">
                    <?php if ($user !== null): ?>
                        <div class="hidden items-center gap-3 rounded-full border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-700 shadow-sm md:flex">
                            <div class="flex h-9 w-9 items-center justify-center rounded-full bg-slate-900 text-xs font-bold text-white">
                                <?= strtoupper(substr($user['display_name'], 0, 2)) ?>
                            </div>
                            <div class="leading-tight">
                                <div><?= htmlspecialchars($user['display_name']) ?></div>
                                <div class="text-xs text-slate-500"><?= htmlspecialchars(ucfirst($user['role'])) ?></div>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <?php if ($is_admin): ?>
                                <a href="/admin/users.php" class="inline-flex items-center gap-2 rounded-lg border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-700 transition hover:border-slate-300 hover:bg-slate-50">
                                    <span class="text-lg">⚙️</span>
                                    Admin
                                </a>
                            <?php endif; ?>
                            <a href="/logout.php" class="inline-flex items-center gap-2 rounded-lg bg-slate-900 px-3 py-2 text-sm font-semibold text-white shadow-subtle transition hover:bg-slate-800">
                                <span class="text-lg">↗</span>
                                Logout
                            </a>
                        </div>
                    <?php else: ?>
                        <a href="/login.php" class="inline-flex items-center gap-2 rounded-lg bg-slate-900 px-3 py-2 text-sm font-semibold text-white shadow-subtle transition hover:bg-slate-800">
                            <span class="h-2 w-2 rounded-full bg-white"></span>
                            Login
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php if ($user !== null): ?>
                <div class="border-t border-slate-200 bg-white/80">
                    <div class="mx-auto flex max-w-6xl items-center justify-between px-6 py-3 text-sm text-slate-600">
                        <div class="flex items-center gap-3">
                            <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">In workspace</span>
                            <span class="hidden sm:inline">Use the top navigation to jump between CRM areas.</span>
                        </div>
                        <div class="hidden items-center gap-2 sm:flex">
                            <span class="h-2 w-2 animate-pulse rounded-full bg-emerald-500"></span>
                            <span class="text-xs font-semibold text-emerald-700">Session active</span>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </header>
        <main class="mx-auto max-w-6xl px-6 py-10 space-y-6">
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
    <div class="mb-4 flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 shadow-sm">
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
    <div class="mb-4 flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 shadow-sm">
        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-emerald-100 text-base">&#10003;</div>
        <span><?= htmlspecialchars($message) ?></span>
    </div>
    <?php
}
