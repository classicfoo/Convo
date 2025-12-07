<?php
require_once __DIR__ . '/layout.php';

bootstrap_app();
$user = require_login();

render_header($user, 'Dashboard - Convo');
?>
<div class="space-y-8">
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white">
        <div class="border-b border-slate-200 bg-white px-6 py-6">
            <p class="text-sm uppercase tracking-[0.08em] text-slate-500">Welcome back</p>
            <h1 class="mt-2 text-3xl font-semibold leading-snug text-slate-900">Hi <?= htmlspecialchars($user['display_name']) ?>, here’s your Convo workspace</h1>
            <p class="mt-3 text-sm text-slate-600">Everything is now tuned for a bright, neutral dashboard.</p>
        </div>
        <div class="grid gap-6 px-6 py-6 md:grid-cols-3">
            <div class="rounded-xl border border-slate-100 bg-white p-4">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Access</p>
                <div class="mt-2 flex items-center justify-between">
                    <div class="text-lg font-semibold text-slate-900">Signed in</div>
                    <span class="rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">Active</span>
                </div>
                <p class="mt-2 text-sm text-slate-500">You’re securely authenticated.</p>
            </div>
            <div class="rounded-xl border border-slate-100 bg-white p-4">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Role</p>
                <div class="mt-2 flex items-center justify-between">
                    <div class="text-lg font-semibold text-slate-900"><?= htmlspecialchars(ucfirst($user['role'])) ?></div>
                    <span class="rounded-full border border-slate-200 bg-slate-50 px-3 py-1 text-xs font-semibold text-slate-700"><?= htmlspecialchars($user['username']) ?></span>
                </div>
                <p class="mt-2 text-sm text-slate-500">Navigate using the header to manage your workspace.</p>
            </div>
            <div class="rounded-xl border border-slate-100 bg-white p-4">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Next steps</p>
                <div class="mt-2 space-y-2 text-sm text-slate-600">
                    <p>• Explore the Users area to invite teammates.</p>
                    <p>• Keep credentials safe—log out when you’re done.</p>
                    <p>• Customize Convo as your CRM evolves.</p>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
render_footer();
