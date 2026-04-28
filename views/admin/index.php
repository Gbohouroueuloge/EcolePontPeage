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

<main class="md:ml-64 px-6 pb-12 pt-24 md:px-8">
  <section class="mb-10 flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
    <div>
      <div class="text-[11px] font-bold uppercase tracking-[0.3em] text-secondary">Vue generale</div>
      <h1 class="mt-2 font-headline text-5xl font-black tracking-tight text-primary">Tableau de bord</h1>
      <p class="mt-3 max-w-3xl text-sm leading-7 text-on-surface-variant">
        Revenus, activite des voies, affectations en attente et incidents recents.
      </p>
    </div>

    <div class="flex flex-wrap gap-2">
      <a class="rounded-2xl bg-primary px-4 py-3 text-xs font-bold uppercase tracking-[0.2em] text-white" href="/admin/analytics">Ouvrir analytics</a>

      <a class="rounded-2xl border border-primary/15 px-4 py-3 text-xs font-bold uppercase tracking-[0.2em] text-primary" href="/admin/rapports">Voir rapports</a>
    </div>
  </section>

  <section class="mb-8 grid gap-4 md:grid-cols-2 xl:grid-cols-5">
    <div class="rounded-3xl border border-primary/10 bg-white p-5 shadow-sm">
      <div class="text-[10px] uppercase tracking-[0.2em] text-on-surface-variant">Revenus</div>
      <div class="mt-3 font-mono text-3xl font-bold text-primary"><?= number_format($overview['revenu_total'], 0, ',', ' ') ?></div>
      <div class="mt-2 text-sm text-on-surface-variant">FCFA sur 30 jours</div>
    </div>
    <div class="rounded-3xl border border-primary/10 bg-white p-5 shadow-sm">
      <div class="text-[10px] uppercase tracking-[0.2em] text-on-surface-variant">Passages</div>
      <div class="mt-3 font-mono text-3xl font-bold text-primary"><?= number_format($overview['total_passages'], 0, ',', ' ') ?></div>
      <div class="mt-2 text-sm text-on-surface-variant"><?= $overview['voies_couvertes'] ?> voie(s) utilisee(s)</div>
    </div>
    <div class="rounded-3xl border border-primary/10 bg-white p-5 shadow-sm">
      <div class="text-[10px] uppercase tracking-[0.2em] text-on-surface-variant">Incidents</div>
      <div class="mt-3 font-mono text-3xl font-bold text-primary"><?= number_format($overview['total_incidents'], 0, ',', ' ') ?></div>
      <div class="mt-2 text-sm text-on-surface-variant">sur la periode observee</div>
    </div>
    <div class="rounded-3xl border border-primary/10 bg-white p-5 shadow-sm">
      <div class="text-[10px] uppercase tracking-[0.2em] text-on-surface-variant">En attente</div>
      <div class="mt-3 font-mono text-3xl font-bold text-primary"><?= $userStats['pending_assignments'] ?></div>
      <div class="mt-2 text-sm text-on-surface-variant">operateur(s) a affecter</div>
    </div>
    <div class="rounded-3xl border border-primary/10 bg-white p-5 shadow-sm">
      <div class="text-[10px] uppercase tracking-[0.2em] text-on-surface-variant">Superviseurs</div>
      <div class="mt-3 font-mono text-3xl font-bold text-primary"><?= count($supervisorStats) ?></div>
      <div class="mt-2 text-sm text-on-surface-variant"><?= $overview['agents_actifs'] ?> agent(s) en service</div>
    </div>
  </section>

  <section class="grid gap-8 xl:grid-cols-12">
    <div class="space-y-8 col-span-7">
      <div class="rounded-4xl border border-primary/10 bg-white p-6 shadow-sm">
        <div class="mb-6">
          <div class="text-[11px] font-bold uppercase tracking-[0.24em] text-secondary">Trafic</div>
          <h2 class="mt-2 font-headline text-3xl font-black text-primary">
            Passages des 7 derniers jours
          </h2>
        </div>
        <div class="grid grid-cols-7 gap-3">
          <?php foreach ($dailyTraffic as $i => $point) : ?>
            <?php if ($i >= $days) break ?>
            <?php $height = max(18, (int) round(($point['passages'] / $dailyMax) * 180)); ?>
            <div class="flex flex-col items-center gap-3">
              <span class="font-mono text-[10px] text-on-surface-variant"><?= $point['passages'] ?></span>
              <div class="flex h-52 w-full items-end rounded-[1.25rem] bg-surface-container-low p-2">
                <div class="w-full rounded-2xl bg-[linear-gradient(180deg,#febe49_0%,#000719_100%)]" style="height: <?= $height ?>px"></div>
              </div>
              <span class="text-[10px] font-bold uppercase tracking-[0.2em] text-primary">
                <?= htmlspecialchars($point['label_short']) ?>
              </span>
            </div>
          <?php endforeach; ?>
        </div>
      </div>

      <div class="rounded-4xl border border-primary/10 bg-white p-6 shadow-sm">
        <div class="mb-6">
          <div class="text-[11px] font-bold uppercase tracking-[0.24em] text-secondary">Voies</div>
          <h2 class="mt-2 font-headline text-3xl font-black text-primary">Top guichets par revenus</h2>
        </div>
        <div class="space-y-4">
          <?php foreach ($revenueByGuichet as $guichet) : ?>
            <div class="rounded-3xl bg-surface-container-low p-4">
              <div class="flex items-center justify-between gap-3">
                <div>
                  <div class="font-bold text-primary">Voie <?= $guichet['guichet_id'] ?> - <?= htmlspecialchars($guichet['emplacement']) ?></div>
                  <div class="mt-1 text-xs text-on-surface-variant"><?= number_format($guichet['passages'], 0, ',', ' ') ?> passages</div>
                </div>
                <div class="font-mono text-sm font-bold text-primary"><?= number_format($guichet['revenu'], 0, ',', ' ') ?> FCFA</div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>

    <div class="space-y-8 col-span-5">
      <div class="rounded-4xl border border-primary/10 bg-white p-6 shadow-sm">
        <div class="mb-6">
          <div class="text-[11px] font-bold uppercase tracking-[0.24em] text-secondary">Paiements</div>
          <h2 class="mt-2 font-headline text-3xl font-black text-primary">Modes dominants</h2>
        </div>
        <div class="space-y-4">
          <?php
          $modeTotal = 0.0;
          foreach ($revenueByMode as $mode) {
            $modeTotal += $mode['revenu'];
          }
          ?>
          <?php foreach ($revenueByMode as $mode) : ?>
            <?php $percent = $modeTotal > 0 ? (int) round(($mode['revenu'] / $modeTotal) * 100) : 0; ?>
            <div>
              <div class="mb-1 flex items-center justify-between">
                <span class="text-sm font-semibold text-primary"><?= htmlspecialchars($mode['mode']) ?></span>
                <span class="font-mono text-xs text-on-surface-variant"><?= $percent ?>%</span>
              </div>
              <div class="h-2 rounded-full bg-surface-container-low">
                <div class="h-2 rounded-full bg-primary" style="width: <?= $percent ?>%"></div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>

      <div class="rounded-4xl border border-primary/10 bg-white p-6 shadow-sm">
        <div class="mb-6">
          <div class="text-[11px] font-bold uppercase tracking-[0.24em] text-secondary">Organisation</div>
          <h2 class="mt-2 font-headline text-3xl font-black text-primary">Charges a traiter</h2>
        </div>
        <div class="space-y-4">
          <div class="rounded-3xl bg-surface-container-low p-4">
            <div class="text-[10px] uppercase tracking-[0.2em] text-on-surface-variant">Utilisateurs</div>
            <div class="mt-2 text-sm font-bold text-primary"><?= $userStats['total'] ?> comptes - <?= $userStats['messages_non_lus'] ?> message(s) non lu(s)</div>
          </div>
          <div class="rounded-3xl bg-surface-container-low p-4">
            <div class="text-[10px] uppercase tracking-[0.2em] text-on-surface-variant">Affectations</div>
            <div class="mt-2 text-sm font-bold text-primary"><?= $userStats['pending_assignments'] ?> operateur(s) attendent un guichet</div>
          </div>
          <div class="rounded-3xl bg-surface-container-low p-4">
            <div class="text-[10px] uppercase tracking-[0.2em] text-on-surface-variant">Supervision</div>
            <div class="mt-2 text-sm font-bold text-primary"><?= count($supervisorStats) ?> superviseur(s) actifs sur le reseau</div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="mt-8 rounded-4xl border border-primary/10 bg-white p-6 shadow-sm">
    <div class="mb-6">
      <div class="text-[11px] font-bold uppercase tracking-[0.24em] text-secondary">Activites recentes</div>
      <h2 class="mt-2 font-headline text-3xl font-black text-primary">Paiements et incidents melanges</h2>
    </div>

    <div class="space-y-4">
      <?php foreach ($recentActivity as $activity) : ?>
        <?php $isIncident = $activity['activity_type'] === 'incident'; ?>
        <div class="rounded-3xl border <?= $isIncident ? 'border-error/20 bg-error/5' : 'border-primary/10 bg-surface-container-low' ?> p-4">
          <div class="flex items-start justify-between gap-3">
            <div>
              <div class="font-bold <?= $isIncident ? 'text-error' : 'text-primary' ?>">
                <?= htmlspecialchars($activity['title']) ?>
              </div>
              <div class="mt-1 text-sm text-on-surface-variant uppercase">
                Plaque <?= htmlspecialchars($activity['immatriculation']) ?> - Voie <?= $activity['guichet_id'] ?> <?= htmlspecialchars($activity['emplacement']) ?>
              </div>
              <?php if ($isIncident && $activity['description']) : ?>
                <div class="mt-2 text-sm text-on-surface-variant"><?= htmlspecialchars($activity['description']) ?></div>
              <?php endif; ?>
            </div>
            <div class="text-right">
              <div class="text-xs font-bold uppercase tracking-[0.2em] text-on-surface-variant"><?= date('d/m H:i', strtotime($activity['created_at'])) ?></div>
              <?php if (!$isIncident && $activity['montant'] !== null) : ?>
                <div class="mt-2 font-mono text-sm font-bold text-primary"><?= number_format($activity['montant'], 0, ',', ' ') ?> FCFA</div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </section>
</main>