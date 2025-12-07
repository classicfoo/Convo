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
<div class="max-w-md mx-auto">
    <div class="mb-6 text-center">
        <h1 class="text-2xl font-semibold text-gray-900">Sign in to Convo</h1>
        <p class="text-sm text-gray-500">Default admin: admin / admin123</p>
    </div>

    <?php render_errors($errors); ?>

    <form method="POST" class="space-y-4 bg-white p-6 shadow rounded">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>" />
        <div>
            <label class="block text-sm font-medium text-gray-700" for="username">Username</label>
            <input class="mt-1 w-full rounded border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" type="text" id="username" name="username" required />
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700" for="password">Password</label>
            <input class="mt-1 w-full rounded border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" type="password" id="password" name="password" required />
        </div>
        <button class="w-full rounded bg-indigo-600 px-4 py-2 text-white font-semibold hover:bg-indigo-700" type="submit">Sign in</button>
    </form>
</div>
<?php
render_footer();
