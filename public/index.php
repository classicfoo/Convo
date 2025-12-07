<?php
require_once __DIR__ . '/layout.php';

bootstrap_app();
$user = require_login();

render_header($user, 'Dashboard - Convo');
?>
<div class="space-y-6">
    <div class="rounded bg-white p-6 shadow">
        <h1 class="text-2xl font-semibold text-gray-900">Welcome, <?= htmlspecialchars($user['display_name']) ?>!</h1>
        <p class="mt-2 text-gray-600">This is the beginning of Convo CRM. Authentication is now in place.</p>
        <p class="mt-4 text-sm text-gray-500">Use the navigation to manage users or sign out.</p>
    </div>
</div>
<?php
render_footer();
