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
<div class="space-y-8">
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-subtle">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 bg-slate-50 px-6 py-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">User onboarding</p>
                <h1 class="text-xl font-semibold text-slate-900">Create a new teammate</h1>
                <p class="text-sm text-slate-600">Invite collaborators with a neutral, modern experience.</p>
            </div>
            <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">Admin mode</span>
        </div>

        <div class="px-6 py-6">
            <?php render_errors($errors); ?>
            <?php render_success($success); ?>

            <form method="POST" class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>" />
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-slate-700" for="display_name">Display name</label>
                    <input class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-slate-900 shadow-inner focus:border-brand focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand/30" type="text" id="display_name" name="display_name" required />
                </div>
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-slate-700" for="username">Username</label>
                    <input class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-slate-900 shadow-inner focus:border-brand focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand/30" type="text" id="username" name="username" required />
                </div>
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-slate-700" for="password">Temporary password</label>
                    <input class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-slate-900 shadow-inner focus:border-brand focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand/30" type="text" id="password" name="password" required />
                </div>
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-slate-700" for="role">Role</label>
                    <select class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-slate-900 shadow-inner focus:border-brand focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand/30" id="role" name="role">
                        <option value="user">User</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div class="md:col-span-2 flex justify-end">
                    <button class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white shadow-subtle transition hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-brand/40" type="submit">
                        <span class="text-lg">＋</span>
                        Create user
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-subtle">
        <div class="flex items-center justify-between border-b border-slate-200 bg-slate-50 px-6 py-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Directory</p>
                <h2 class="text-lg font-semibold text-slate-900">Existing users</h2>
            </div>
            <div class="rounded-full bg-slate-900 px-3 py-1 text-xs font-semibold text-white">Total: <?= count($users) ?></div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-slate-600">Username</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-600">Display name</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-600">Role</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-600">Active</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-600">Created</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 bg-white">
                    <?php foreach ($users as $row): ?>
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 font-semibold text-slate-900"><?= htmlspecialchars($row['username']) ?></td>
                            <td class="px-4 py-3 text-slate-700"><?= htmlspecialchars($row['display_name']) ?></td>
                            <td class="px-4 py-3 text-slate-700">
                                <span class="rounded-full border px-3 py-1 text-xs font-semibold <?= $row['role'] === 'admin' ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 'border-slate-200 bg-slate-50 text-slate-700' ?>"><?= htmlspecialchars(ucfirst($row['role'])) ?></span>
                            </td>
                            <td class="px-4 py-3 text-slate-700">
                                <span class="rounded-full px-3 py-1 text-xs font-semibold <?= ((int) $row['is_active'] === 1) ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' ?>"><?= ((int) $row['is_active'] === 1) ? 'Active' : 'Inactive' ?></span>
                            </td>
                            <td class="px-4 py-3 text-slate-500"><?= htmlspecialchars($row['created_at']) ?></td>
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
