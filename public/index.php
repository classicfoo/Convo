<?php
require_once __DIR__ . '/layout.php';

bootstrap_app();
$user = require_login();

render_header($user, 'Dashboard - Convo');
?>
<div class="space-y-8">
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-subtle">
        <div class="border-b border-slate-200 bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 px-6 py-6 text-white">
            <p class="text-xs font-semibold uppercase tracking-[0.08em] text-slate-300">Workspace overview</p>
            <h1 class="mt-2 text-3xl font-semibold leading-snug">Welcome back, <?= htmlspecialchars($user['display_name']) ?></h1>
            <p class="mt-3 max-w-3xl text-sm text-slate-300">Navigate with the top bar to jump between Accounts, Opportunities, and the Pipeline. This dashboard keeps the tone minimal and calm so you can focus on deals.</p>
            <div class="mt-4 flex flex-wrap gap-3 text-xs font-semibold text-slate-900">
                <a href="/accounts.php" class="inline-flex items-center gap-2 rounded-full bg-white/90 px-3 py-2 text-slate-900 shadow-sm transition hover:-translate-y-0.5 hover:bg-white">
                    <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                    View accounts
                </a>
                <a href="/opportunities.php" class="inline-flex items-center gap-2 rounded-full bg-white/90 px-3 py-2 text-slate-900 shadow-sm transition hover:-translate-y-0.5 hover:bg-white">
                    <span class="h-2 w-2 rounded-full bg-blue-500"></span>
                    Review opportunities
                </a>
                <a href="/pipeline.php" class="inline-flex items-center gap-2 rounded-full bg-white/90 px-3 py-2 text-slate-900 shadow-sm transition hover:-translate-y-0.5 hover:bg-white">
                    <span class="h-2 w-2 rounded-full bg-amber-500"></span>
                    Check pipeline
                </a>
            </div>
        </div>
        <div class="grid gap-6 px-6 py-6 md:grid-cols-3">
            <div class="rounded-xl border border-slate-100 bg-white p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Your access</p>
                <div class="mt-3 flex items-center justify-between">
                    <div class="text-lg font-semibold text-slate-900">Signed in</div>
                    <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">Active</span>
                </div>
                <p class="mt-2 text-sm text-slate-500">Session is active with workspace navigation above.</p>
            </div>
            <div class="rounded-xl border border-slate-100 bg-white p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Role</p>
                <div class="mt-3 flex items-center justify-between">
                    <div class="text-lg font-semibold text-slate-900"><?= htmlspecialchars(ucfirst($user['role'])) ?></div>
                    <span class="rounded-full bg-slate-900 px-3 py-1 text-xs font-semibold text-white"><?= htmlspecialchars($user['username']) ?></span>
                </div>
                <p class="mt-2 text-sm text-slate-500">Use Admin to manage teammates if you have permissions.</p>
            </div>
            <div class="rounded-xl border border-slate-100 bg-white p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Next steps</p>
                <div class="mt-3 space-y-2 text-sm text-slate-600">
                    <p>• Capture a new account to anchor contacts.</p>
                    <p>• Log opportunities and set close dates.</p>
                    <p>• Move deals across the pipeline as you work.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.08em] text-slate-500">Quick navigation</p>
                    <h2 class="text-xl font-semibold text-slate-900">Key CRM areas</h2>
                </div>
                <a href="/pipeline.php" class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-700 transition hover:-translate-y-0.5 hover:border-slate-300 hover:bg-slate-50">
                    View pipeline
                    <span class="text-lg">→</span>
                </a>
            </div>
            <div class="grid gap-4 md:grid-cols-3">
                <a href="/accounts.php" class="group rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition hover:-translate-y-1 hover:shadow-subtle">
                    <div class="flex items-center justify-between text-sm font-semibold text-slate-700">
                        Accounts
                        <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">Start</span>
                    </div>
                    <p class="mt-3 text-sm text-slate-500">Keep companies organized with owners and context.</p>
                    <div class="mt-4 flex items-center gap-2 text-xs font-semibold text-emerald-700 opacity-0 transition group-hover:opacity-100">
                        Go to Accounts <span class="text-base">→</span>
                    </div>
                </a>
                <a href="/opportunities.php" class="group rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition hover:-translate-y-1 hover:shadow-subtle">
                    <div class="flex items-center justify-between text-sm font-semibold text-slate-700">
                        Opportunities
                        <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">Plan</span>
                    </div>
                    <p class="mt-3 text-sm text-slate-500">Track deals with owners, stages, and close dates.</p>
                    <div class="mt-4 flex items-center gap-2 text-xs font-semibold text-blue-700 opacity-0 transition group-hover:opacity-100">
                        Go to Opportunities <span class="text-base">→</span>
                    </div>
                </a>
                <a href="/pipeline.php" class="group rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition hover:-translate-y-1 hover:shadow-subtle">
                    <div class="flex items-center justify-between text-sm font-semibold text-slate-700">
                        Pipeline
                        <span class="rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-700">Move</span>
                    </div>
                    <p class="mt-3 text-sm text-slate-500">Watch stages progress in a calm, card-based lane view.</p>
                    <div class="mt-4 flex items-center gap-2 text-xs font-semibold text-amber-700 opacity-0 transition group-hover:opacity-100">
                        Go to Pipeline <span class="text-base">→</span>
                    </div>
                </a>
            </div>
        </div>

        <div class="space-y-4">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between text-sm font-semibold text-slate-700">
                    Activity hints
                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">Calm mode</span>
                </div>
                <ul class="mt-4 space-y-3 text-sm text-slate-600">
                    <li class="flex items-start gap-2">
                        <span class="mt-1 h-2 w-2 rounded-full bg-emerald-500"></span>
                        Add notes to opportunities to keep next steps visible.
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="mt-1 h-2 w-2 rounded-full bg-blue-500"></span>
                        Group deals by stage in the pipeline to stay organized.
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="mt-1 h-2 w-2 rounded-full bg-amber-500"></span>
                        Keep accounts fresh with updated contacts and owners.
                    </li>
                </ul>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-[0.08em] text-slate-500">Support</p>
                <p class="mt-2 text-sm text-slate-600">Need another user? Use the Admin link in the header to manage seats. All other work happens through the navigation shell.</p>
            </div>
        </div>
    </div>
</div>
<?php
render_footer();
