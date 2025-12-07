<?php
require_once __DIR__ . '/layout.php';

bootstrap_app();
$user = require_login();

render_header($user, 'Accounts - Convo');
$accounts = [
    ['name' => 'Acme Robotics', 'industry' => 'Manufacturing', 'website' => 'acmerobotics.com', 'owner' => 'You'],
    ['name' => 'Northwind Supply', 'industry' => 'Logistics', 'website' => 'northwind.io', 'owner' => 'Team'],
    ['name' => 'Brightline Energy', 'industry' => 'Renewables', 'website' => 'brightline.energy', 'owner' => 'You'],
];
?>
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.08em] text-slate-500">Accounts</p>
            <h1 class="text-2xl font-semibold text-slate-900">Companies anchored in your CRM</h1>
            <p class="mt-2 text-sm text-slate-600">Use accounts as the source of truth for contacts and opportunities.</p>
        </div>
        <div class="flex items-center gap-2">
            <button class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-700 transition hover:-translate-y-0.5 hover:border-slate-300 hover:bg-slate-50" type="button">
                <span class="text-lg">＋</span>
                New account
            </button>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-3">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-[0.08em] text-slate-500">Owners</p>
            <div class="mt-2 text-2xl font-semibold text-slate-900">Shared</div>
            <p class="mt-1 text-sm text-slate-600">Accounts can be assigned while still visible to the team.</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-[0.08em] text-slate-500">Status</p>
            <div class="mt-2 flex items-center gap-2 text-lg font-semibold text-slate-900">
                <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                Healthy
            </div>
            <p class="mt-1 text-sm text-slate-600">Keep details current to help others qualify deals.</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-[0.08em] text-slate-500">Next steps</p>
            <p class="mt-2 text-sm text-slate-600">Attach contacts, log related opportunities, and pin key notes.</p>
        </div>
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="flex items-center justify-between border-b border-slate-200 bg-slate-50 px-4 py-3">
            <div class="text-sm font-semibold text-slate-700">Accounts list</div>
            <div class="flex items-center gap-2 text-xs font-semibold text-slate-600">
                <span class="rounded-full bg-emerald-100 px-3 py-1 text-emerald-700">Search-ready</span>
                <span class="rounded-full bg-slate-200 px-3 py-1 text-slate-700">3 sample records</span>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-white text-left text-xs font-semibold uppercase tracking-[0.08em] text-slate-500">
                    <tr>
                        <th class="px-4 py-3">Name</th>
                        <th class="px-4 py-3">Industry</th>
                        <th class="px-4 py-3">Website</th>
                        <th class="px-4 py-3">Owner</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    <?php foreach ($accounts as $account): ?>
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 font-semibold text-slate-900"><?= htmlspecialchars($account['name']) ?></td>
                            <td class="px-4 py-3 text-slate-700"><?= htmlspecialchars($account['industry']) ?></td>
                            <td class="px-4 py-3 text-slate-700">
                                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700"><?= htmlspecialchars($account['website']) ?></span>
                            </td>
                            <td class="px-4 py-3 text-slate-700"><?= htmlspecialchars($account['owner']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php
render_footer();
