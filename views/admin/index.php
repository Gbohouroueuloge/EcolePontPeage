<?php

use App\Services\AnalyticsService;

$title = 'Dashboard';
require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'admin/variables.php';

$analyticsService = new AnalyticsService($pdo);
$days = 7;
$period = $analyticsService->resolvePeriod('30j');
$overview = $analyticsService->getOverview($period['start'], $period['end']);
$dailyTraffic = $analyticsService->getDailyTrafficSeries($days);
$revenueByMode = $analyticsService->getRevenueByMode($period['start'], $period['end']);
$revenueByGuichet = array_slice($analyticsService->getRevenueByGuichet($period['start'], $period['end']), 0, 5);
$recentActivity = $analyticsService->getRecentActivity(10);
$userStats = $adminService->getUserStats();
$supervisorStats = $adminService->getSupervisorSummary();

$dailyMax = 1;
foreach ($dailyTraffic as $point) {
  $dailyMax = max($dailyMax, (int) $point['passages']);
}
?>

<main class="md:ml-64 px-6 pb-12 pt-24 md:px-8 page-content">
  <section class="mb-10 flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between anim-fade-up">
    <div class="pb-4">
      <div class="text-[11px] font-bold uppercase tracking-[0.3em] text-secondary flex items-center gap-2">
        <span class="material-symbols-outlined text-sm">speed</span>
        Vue générale
      </div>
      <h1 class="mt-2 font-headline header-accent text-5xl font-black tracking-tight text-primary">Tableau de bord</h1>
      <p class="mt-3 max-w-3xl text-sm leading-7 text-on-surface-variant">
        consultation des statistiques et des activités de l'application.
      </p>
    </div>

    <div class="flex flex-wrap gap-3">
      <a class="btn-micro flex items-center gap-2 rounded-2xl bg-primary px-5 py-3 text-xs font-bold uppercase tracking-[0.2em] text-white" href="/admin/analytics">
        <span class="material-symbols-outlined text-lg">monitoring</span>
        Ouvrir analytics
      </a>

      <a class="btn-micro flex items-center gap-2 rounded-2xl border border-primary/15 px-5 py-3 text-xs font-bold uppercase tracking-[0.2em] text-primary" href="/admin/rapports">
        <span class="material-symbols-outlined text-lg">description</span>
        Voir rapports
      </a>
    </div>
  </section>

  <section class="mb-8 grid gap-4 md:grid-cols-2 xl:grid-cols-5">
    <?php
    $stats = [
      ['label' => 'Revenus', 'value' => number_format($overview['revenu_total'], 0, ',', ' ') . ' <span class="text-xs">FCFA</span>', 'sub' => 'sur 30 jours', 'icon' => 'payments', 'delay' => 'delay-50'],
      ['label' => 'Passages', 'value' => number_format($overview['total_passages'], 0, ',', ' '), 'sub' => $overview['voies_couvertes'] . ' voie(s) utilisée(s)', 'icon' => 'directions_car', 'delay' => 'delay-100'],
      ['label' => 'Incidents', 'value' => number_format($overview['total_incidents'], 0, ',', ' '), 'sub' => 'Alertes réseau', 'icon' => 'warning', 'delay' => 'delay-150', 'color' => 'text-error'],
      ['label' => 'En attente', 'value' => $userStats['pending_assignments'], 'sub' => 'Opérateurs à affecter', 'icon' => 'person_add', 'delay' => 'delay-200'],
      ['label' => 'Superviseurs', 'value' => count($supervisorStats), 'sub' => $overview['agents_actifs'] . ' agents actifs', 'icon' => 'badge', 'delay' => 'delay-250'],
    ];
    foreach ($stats as $stat): ?>
      <div class="card-lift anim-scale-in <?= $stat['delay'] ?> rounded-3xl border border-primary/10 bg-white p-5 shadow-sm">
        <div class="flex justify-between items-start">
          <div class="text-[10px] uppercase tracking-[0.2em] text-on-surface-variant"><?= $stat['label'] ?></div>
          <span class="material-symbols-outlined text-primary/30 <?= $stat['color'] ?? '' ?>"><?= $stat['icon'] ?></span>
        </div>
        <div class="mt-3 font-mono text-3xl font-bold text-primary"><?= $stat['value'] ?></div>
        <div class="mt-2 text-sm text-on-surface-variant"><?= $stat['sub'] ?></div>
      </div>
    <?php endforeach; ?>
  </section>

  <section class="grid gap-8 xl:grid-cols-12">
    <div class="space-y-8 col-span-7 anim-fade-up delay-300">
      <div class="rounded-4xl border border-primary/10 bg-white p-6 shadow-sm">
        <div class="mb-8 flex justify-between items-start">
          <div>
            <div class="text-[11px] font-bold uppercase tracking-[0.24em] text-secondary">Trafic</div>
            <h2 class="mt-2 font-headline text-3xl font-black text-primary">Derniers 7 jours</h2>
          </div>
          <span class="material-symbols-outlined text-secondary">query_stats</span>
        </div>
        <div class="grid grid-cols-7 gap-3 h-64 items-end">
          <?php foreach ($dailyTraffic as $i => $point) : ?>
            <?php if ($i >= $days) break; ?>
            <?php $height = max(18, (int) round(($point['passages'] / $dailyMax) * 180)); ?>
            <div class="flex flex-col items-center gap-3 h-full justify-end group">
              <span class="font-mono text-[10px] text-on-surface-variant opacity-0 group-hover:opacity-100 transition-opacity"><?= $point['passages'] ?></span>
              <div class="flex h-52 w-full items-end rounded-[1.25rem] bg-surface-container-low p-1.5 group-hover:bg-surface-container">
                <div class="bar-animate w-full rounded-2xl bg-[linear-gradient(180deg,#febe49_0%,#000719_100%)]" style="height: <?= $height ?>px; animation-delay: <?= $i * 50 ?>ms"></div>
              </div>
              <span class="text-[10px] font-bold uppercase tracking-[0.2em] text-primary">
                <?= htmlspecialchars($point['label_short']) ?>
              </span>
            </div>
          <?php endforeach; ?>
        </div>
      </div>

      <div class="rounded-4xl border border-primary/10 bg-white p-6 shadow-sm">
        <div class="mb-6 flex justify-between items-center">
          <div>
            <div class="text-[11px] font-bold uppercase tracking-[0.24em] text-secondary">Voies</div>
            <h2 class="mt-2 font-headline text-3xl font-black text-primary">Top guichets</h2>
          </div>
          <span class="material-symbols-outlined text-secondary">lanes</span>
        </div>
        <div class="space-y-3">
          <?php foreach ($revenueByGuichet as $index => $guichet) : ?>
            <div class="card-lift anim-slide-right delay-<?= $index * 50 ?> rounded-3xl bg-surface-container-low p-4 flex items-center gap-4">
              <div class="stat-icon flex h-12 w-12 items-center justify-center rounded-2xl bg-white shadow-sm text-primary">
                <span class="material-symbols-outlined">toll</span>
              </div>
              <div class="flex-1 flex items-center justify-between">
                <div>
                  <div class="font-bold text-primary">Voie <?= $guichet['guichet_id'] ?></div>
                  <div class="text-xs text-on-surface-variant"><?= htmlspecialchars($guichet['emplacement']) ?> • <?= number_format($guichet['passages'], 0, ',', ' ') ?> passages</div>
                </div>
                <div class="font-mono text-sm font-bold text-primary"><?= number_format($guichet['revenu'], 0, ',', ' ') ?> <span class="text-[10px]">FCFA</span></div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>

    <div class="space-y-8 col-span-5 anim-fade-up delay-400">
      <div class="rounded-4xl border border-primary/10 bg-white p-6 shadow-sm">
        <div class="mb-6 flex justify-between items-center">
          <h2 class="font-headline text-2xl font-black text-primary">Modes de paiement</h2>
          <span class="material-symbols-outlined text-secondary">account_balance_wallet</span>
        </div>
        <div class="space-y-6">
          <?php
          $modeTotal = array_sum(array_column($revenueByMode, 'revenu'));
          foreach ($revenueByMode as $index => $mode) :
            $percent = $modeTotal > 0 ? (int) round(($mode['revenu'] / $modeTotal) * 100) : 0;
          ?>
            <div class="anim-fade delay-<?= $index * 100 ?>">
              <div class="mb-2 flex items-center justify-between">
                <span class="text-sm font-semibold text-primary flex items-center gap-2">
                  <span class="material-symbols-outlined text-sm"><?= $mode['mode'] == 'Espèces' ? 'payments' : 'contactless' ?></span>
                  <?= htmlspecialchars($mode['mode']) ?>
                </span>
                <span class="font-mono text-xs text-on-surface-variant"><?= $percent ?>%</span>
              </div>
              <div class="h-2 rounded-full bg-surface-container-low overflow-hidden">
                <div class="progress-animate h-2 rounded-full bg-secondary"
                  style="--progress-w: <?= $percent ?>%; animation-delay: <?= 0.5 + ($index * 0.1) ?>s"></div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>

      <div class="rounded-4xl border border-primary/10 bg-white p-6 shadow-sm">
        <div class="mb-6 flex justify-between items-center">
          <h2 class="font-headline text-2xl font-black text-primary">Organisation</h2>
          <span class="material-symbols-outlined text-secondary">hub</span>
        </div>
        <div class="space-y-4">
          <div class="icon-pop rounded-3xl bg-surface-container-low p-4 flex items-center gap-3">
            <span class="material-symbols-outlined text-primary">group</span>
            <div>
              <div class="text-[10px] uppercase tracking-[0.2em] text-on-surface-variant">Utilisateurs</div>
              <div class="text-sm font-bold text-primary"><?= $userStats['total'] ?> comptes • <?= $userStats['messages_non_lus'] ?> messages</div>
            </div>
          </div>
          <div class="icon-pop rounded-3xl bg-surface-container-low p-4 flex items-center gap-3 active-nav-glow">
            <span class="material-symbols-outlined text-secondary">pending_actions</span>
            <div>
              <div class="text-[10px] uppercase tracking-[0.2em] text-on-surface-variant">Affectations</div>
              <div class="text-sm font-bold text-primary"><?= $userStats['pending_assignments'] ?> en attente</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="mt-8 rounded-4xl border border-primary/10 bg-white p-6 shadow-sm anim-fade-up delay-500">
    <div class="mb-8 flex justify-between items-center">
      <div>
        <div class="text-[11px] font-bold uppercase tracking-[0.24em] text-secondary">Flux en temps réel</div>
        <h2 class="mt-2 font-headline text-3xl font-black text-primary">Activités récentes</h2>
      </div>
      <div class="flex items-center gap-2 rounded-full bg-success/10 px-4 py-2 text-brand-success">
        <span class="live-dot h-2 w-2 rounded-full bg-brand-success"></span>
        <span class="text-[10px] font-bold uppercase tracking-widest">Live</span>
      </div>
    </div>

    <div class="space-y-3">
      <?php foreach ($recentActivity as $index => $activity) : ?>
        <?php $isIncident = $activity['activity_type'] === 'incident'; ?>
        <div class="card-lift anim-slide-right delay-<?= $index * 50 ?> rounded-3xl border <?= $isIncident ? 'border-error/20 bg-error/5' : 'border-primary/5 bg-surface-container-low' ?> p-4">
          <div class="flex items-center justify-between gap-4">
            <div class="flex items-center gap-4">
              <div class="flex h-10 w-10 items-center justify-center rounded-xl <?= $isIncident ? 'bg-error text-white' : 'bg-white text-primary shadow-sm' ?>">
                <span class="material-symbols-outlined text-xl"><?= $isIncident ? 'report' : 'check_circle' ?></span>
              </div>
              <div>
                <div class="font-bold <?= $isIncident ? 'text-error' : 'text-primary' ?>">
                  <?= htmlspecialchars($activity['title']) ?>
                </div>
                <div class="mt-1 text-[10px] font-bold text-on-surface-variant uppercase tracking-wider">
                  <span class="mono-data">PLATE: <?= htmlspecialchars($activity['immatriculation']) ?></span> • Voie <?= $activity['guichet_id'] ?>
                </div>
              </div>
            </div>
            <div class="text-right">
              <div class="text-[10px] font-bold text-on-surface-variant"><?= date('H:i', strtotime($activity['created_at'])) ?></div>
              <?php if (!$isIncident && $activity['montant'] !== null) : ?>
                <div class="mt-1 font-mono text-sm font-bold text-primary">+<?= number_format($activity['montant'], 0, ',', ' ') ?></div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </section>
</main>