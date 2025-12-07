<?php
require_once __DIR__ . '/layout.php';

bootstrap_app();
$user = require_login();

render_header($user, 'Pipeline - Convo');
$lanes = [
    'Prospecting' => [
        ['name' => 'Brightline rollout', 'amount' => '$32,500', 'owner' => 'You'],
    ],
    'Qualification' => [
        ['name' => 'Acme renewal', 'amount' => '$48,000', 'owner' => 'You'],
    ],
    'Proposal' => [
        ['name' => 'Northwind logistics', 'amount' => '$125,000', 'owner' => 'Team'],
    ],
    'Negotiation' => [
        ['name' => 'Evergreen expansion', 'amount' => '$210,000', 'owner' => 'Team'],
    ],
];
?>
<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.08em] text-slate-500">Pipeline</p>
            <h1 class="text-2xl font-semibold text-slate-900">Card-based stage view</h1>
            <p class="mt-2 text-sm text-slate-600">Each lane mirrors the stages from the navigation shell so teams can drag and move deals calmly.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="/opportunities.php" class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-700 transition hover:-translate-y-0.5 hover:border-slate-300 hover:bg-slate-50">
                Back to list
                <span class="text-base">→</span>
            </a>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <?php foreach ($lanes as $stage => $cards): ?>
            <div class="flex flex-col gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="flex items-center justify-between text-sm font-semibold text-slate-700">
                    <?= htmlspecialchars($stage) ?>
                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700"><?= count($cards) ?> deal<?= count($cards) === 1 ? '' : 's' ?></span>
                </div>
                <?php foreach ($cards as $card): ?>
                    <div class="space-y-2 rounded-xl border border-slate-200 bg-white p-3 shadow-sm">
                        <div class="text-sm font-semibold text-slate-900"><?= htmlspecialchars($card['name']) ?></div>
                        <div class="flex items-center justify-between text-xs font-semibold text-slate-600">
                            <span class="rounded-full bg-slate-100 px-3 py-1 text-slate-700"><?= htmlspecialchars($card['amount']) ?></span>
                            <span class="rounded-full bg-emerald-50 px-3 py-1 text-emerald-700">Owner: <?= htmlspecialchars($card['owner']) ?></span>
                        </div>
                        <p class="text-xs text-slate-500">Keep details aligned with Opportunities to stay in sync.</p>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($cards)): ?>
                    <div class="rounded-xl border border-dashed border-slate-200 bg-slate-50 p-3 text-center text-xs text-slate-600">
                        Drop opportunities here to advance.
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php
render_footer();
