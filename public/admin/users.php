<?php
require_once __DIR__ . '/../layout.php';

bootstrap_app();
$user = require_admin();

$errors = [];
$success = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf_from_post();

    $username = trim((string) ($_POST['username'] ?? ''));
    $display_name = trim((string) ($_POST['display_name'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');
    $role = (string) ($_POST['role'] ?? 'user');

    if ($username === '' || $display_name === '' || $password === '') {
        $errors[] = 'All fields are required.';
    } elseif (!in_array($role, ['user', 'admin'], true)) {
        $errors[] = 'Invalid role selection.';
    } elseif (find_user_by_username($username) !== null) {
        $errors[] = 'A user with that username already exists.';
    }

    if (empty($errors)) {
        create_user($username, $password, $display_name, $role);
        $success = 'User created successfully.';
    }
}

$stmt = db()->query('SELECT id, username, display_name, role, is_active, created_at FROM users ORDER BY created_at DESC');
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

render_header($user, 'User Management - Convo');
?>
<div class="space-y-6">
    <div class="rounded bg-white p-6 shadow">
        <h1 class="text-xl font-semibold text-gray-900">Create user</h1>
        <p class="text-sm text-gray-600">Add new teammates to Convo. Remember to share credentials securely.</p>

        <?php render_errors($errors); ?>
        <?php render_success($success); ?>

        <form method="POST" class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>" />
            <div class="md:col-span-1">
                <label class="block text-sm font-medium text-gray-700" for="display_name">Display name</label>
                <input class="mt-1 w-full rounded border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" type="text" id="display_name" name="display_name" required />
            </div>
            <div class="md:col-span-1">
                <label class="block text-sm font-medium text-gray-700" for="username">Username</label>
                <input class="mt-1 w-full rounded border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" type="text" id="username" name="username" required />
            </div>
            <div class="md:col-span-1">
                <label class="block text-sm font-medium text-gray-700" for="password">Temporary password</label>
                <input class="mt-1 w-full rounded border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" type="text" id="password" name="password" required />
            </div>
            <div class="md:col-span-1">
                <label class="block text-sm font-medium text-gray-700" for="role">Role</label>
                <select class="mt-1 w-full rounded border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" id="role" name="role">
                    <option value="user">User</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <div class="md:col-span-2 flex justify-end">
                <button class="rounded bg-indigo-600 px-4 py-2 text-white font-semibold hover:bg-indigo-700" type="submit">Create user</button>
            </div>
        </form>
    </div>

    <div class="rounded bg-white p-6 shadow">
        <h2 class="text-lg font-semibold text-gray-900">Existing users</h2>
        <div class="mt-4 overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left font-medium text-gray-600">Username</th>
                        <th class="px-4 py-2 text-left font-medium text-gray-600">Display name</th>
                        <th class="px-4 py-2 text-left font-medium text-gray-600">Role</th>
                        <th class="px-4 py-2 text-left font-medium text-gray-600">Active</th>
                        <th class="px-4 py-2 text-left font-medium text-gray-600">Created</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php foreach ($users as $row): ?>
                        <tr>
                            <td class="px-4 py-2 text-gray-800"><?= htmlspecialchars($row['username']) ?></td>
                            <td class="px-4 py-2 text-gray-800"><?= htmlspecialchars($row['display_name']) ?></td>
                            <td class="px-4 py-2 text-gray-800"><?= htmlspecialchars(ucfirst($row['role'])) ?></td>
                            <td class="px-4 py-2 text-gray-800"><?= ((int) $row['is_active'] === 1) ? 'Yes' : 'No' ?></td>
                            <td class="px-4 py-2 text-gray-800"><?= htmlspecialchars($row['created_at']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php
render_footer();
?>
