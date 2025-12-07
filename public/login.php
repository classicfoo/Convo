<?php
require_once __DIR__ . '/layout.php';

bootstrap_app();

$user = current_user();
if ($user !== null) {
    header('Location: /index.php');
    exit;
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf_from_post();

    $username = trim((string) ($_POST['username'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');

    if ($username === '' || $password === '') {
        $errors[] = 'Username and password are required.';
    } else {
        $found = find_user_by_username($username);
        if ($found === null || !password_verify($password, $found['password_hash'])) {
            $errors[] = 'Invalid credentials.';
        } elseif ((int) $found['is_active'] !== 1) {
            $errors[] = 'This account is inactive.';
        } else {
            login_user($found);
            header('Location: /index.php');
            exit;
        }
    }
}

render_header(null, 'Login - Convo');
?>
<div class="grid items-center gap-10 lg:grid-cols-2">
    <div class="space-y-4">
        <p class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-3 py-1 text-xs font-semibold uppercase tracking-wider text-slate-600">Secure workspace</p>
        <h1 class="text-3xl font-semibold text-slate-900">Welcome back</h1>
        <p class="max-w-xl text-lg text-slate-600">Sign in to manage your conversations and keep your team in sync. The refreshed interface keeps things light, simple, and neutral.</p>
        <div class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-white p-4">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl border border-slate-200 bg-slate-50 text-slate-800">C</div>
            <div class="space-y-0.5">
                <div class="text-sm font-semibold text-slate-900">Convo Admin</div>
                <div class="text-sm text-slate-500">Default admin: <span class="font-semibold text-slate-800">admin / admin123</span></div>
            </div>
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-8 shadow-sm">
        <div class="mb-6 space-y-1 text-center">
            <h2 class="text-2xl font-semibold text-slate-900">Sign in</h2>
            <p class="text-sm text-slate-500">Enter your credentials to access Convo</p>
        </div>

        <?php render_errors($errors); ?>

        <form method="POST" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>" />
            <div class="space-y-2">
                <label class="block text-sm font-medium text-slate-700" for="username">Username</label>
                <input class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-slate-900 placeholder:text-slate-400 focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/30" type="text" id="username" name="username" required />
            </div>
            <div class="space-y-2">
                <label class="block text-sm font-medium text-slate-700" for="password">Password</label>
                <input class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-slate-900 placeholder:text-slate-400 focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/30" type="password" id="password" name="password" required />
            </div>
            <button class="w-full rounded-xl bg-brand px-4 py-2.5 text-center text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-600 focus:outline-none focus:ring-2 focus:ring-brand/40" type="submit">Continue</button>
        </form>
    </div>
</div>
<?php
render_footer();
