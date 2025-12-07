<?php
require_once __DIR__ . '/layout.php';

bootstrap_app();
$user = require_login();

render_header($user, 'Opportunities - Convo');
$opportunities = [
    ['name' => 'Renewal with Acme Robotics', 'stage' => 'Qualification', 'amount' => '$48,000', 'close_date' => '2024-07-18'],
    ['name' => 'Logistics optimization - Northwind', 'stage' => 'Proposal', 'amount' => '$125,000', 'close_date' => '2024-08-02'],
    ['name' => 'Pilot rollout - Brightline Energy', 'stage' => 'Prospecting', 'amount' => '$32,500', 'close_date' => '2024-07-30'],
];
?>
<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.08em] text-slate-500">Opportunities</p>
            <h1 class="text-2xl font-semibold text-slate-900">Deals moving through your pipeline</h1>
            <p class="mt-2 text-sm text-slate-600">Filter by stage or owner, and use the pipeline to move cards forward.</p>
        </div>
        <div class="flex items-center gap-2">
            <button class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-700 transition hover:-translate-y-0.5 hover:border-slate-300 hover:bg-slate-50" type="button">
                <span class="text-lg">＋</span>
                New opportunity
            </button>
        </div>
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-700">
            <div class="flex items-center gap-2">
                <span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700">Stage filter</span>
                <span class="rounded-full bg-slate-200 px-3 py-1 text-xs font-semibold text-slate-700">All owners</span>
            </div>
            <a href="/pipeline.php" class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 transition hover:-translate-y-0.5 hover:border-slate-300 hover:bg-slate-50">
                Open pipeline
                <span class="text-base">→</span>
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-white text-left text-xs font-semibold uppercase tracking-[0.08em] text-slate-500">
                    <tr>
                        <th class="px-4 py-3">Name</th>
                        <th class="px-4 py-3">Stage</th>
                        <th class="px-4 py-3">Amount</th>
                        <th class="px-4 py-3">Close date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    <?php foreach ($opportunities as $opportunity): ?>
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 font-semibold text-slate-900"><?= htmlspecialchars($opportunity['name']) ?></td>
                            <td class="px-4 py-3">
                                <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700"><?= htmlspecialchars($opportunity['stage']) ?></span>
                            </td>
                            <td class="px-4 py-3 text-slate-700"><?= htmlspecialchars($opportunity['amount']) ?></td>
                            <td class="px-4 py-3 text-slate-700"><?= htmlspecialchars($opportunity['close_date']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php
render_footer();
