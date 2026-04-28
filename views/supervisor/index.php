<?php

$title = 'Dashboard Superviseur';

require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'supervisor/variables.php';

$period = $analyticsService->resolvePeriod('30j');
$overview = $analyticsService->getOverview($period['start'], $period['end'], $supervisedGuichetIds);
$dailyTraffic = $analyticsService->getDailyTrafficSeries(7, $supervisedGuichetIds);
$recentActivity = $analyticsService->getRecentActivity(8, $supervisedGuichetIds);
$revenueByGuichet = $analyticsService->getRevenueByGuichet($period['start'], $period['end'], $supervisedGuichetIds);
$dailyMax = 1;
foreach ($dailyTraffic as $point) {
  $dailyMax = max($dailyMax, (int) $point['passages']);
}
?>

<main class="md:ml-64 px-6 pb-12 pt-24 md:px-8">
  <section class="mb-10 flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
    <div>
      <div class="text-[11px] font-bold uppercase tracking-[0.3em] text-secondary">Supervision terrain</div>
      <h1 class="mt-2 font-headline text-5xl font-black tracking-tight text-primary">Vue superviseur</h1>
      <p class="mt-3 max-w-3xl text-sm leading-7 text-on-surface-variant">
        Consolidez les voies que vous pilotez, suivez les incidents et gardez un oeil sur la charge quotidienne de vos equipes.
      </p>
    </div>

    <div class="rounded-3xl border border-primary/10 bg-white/80 px-5 py-4 shadow-sm">
      <div class="text-[10px] uppercase tracking-[0.2em] text-on-surface-variant">Zone</div>
      <div class="mt-2 text-lg font-bold text-primary"><?= htmlspecialchars($supervisorProfile->zone_nominale ?? 'Non definie') ?></div>
    </div>
  </section>

  <section class="mb-8 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
    <div class="rounded-3xl border border-primary/10 bg-white p-5 shadow-sm">
      <div class="text-[10px] uppercase tracking-[0.2em] text-on-surface-variant">Voies</div>
      <div class="mt-3 font-mono text-3xl font-bold text-primary"><?= count($supervisedGuichets) ?></div>
    </div>
    <div class="rounded-3xl border border-primary/10 bg-white p-5 shadow-sm">
      <div class="text-[10px] uppercase tracking-[0.2em] text-on-surface-variant">Passages</div>
      <div class="mt-3 font-mono text-3xl font-bold text-primary"><?= number_format($overview['total_passages'], 0, ',', ' ') ?></div>
    </div>
    <div class="rounded-3xl border border-primary/10 bg-white p-5 shadow-sm">
      <div class="text-[10px] uppercase tracking-[0.2em] text-on-surface-variant">Revenus</div>
      <div class="mt-3 font-mono text-3xl font-bold text-primary"><?= number_format($overview['revenu_total'], 0, ',', ' ') ?></div>
      <div class="mt-2 text-sm text-on-surface-variant">FCFA</div>
    </div>
    <div class="rounded-3xl border border-primary/10 bg-white p-5 shadow-sm">
      <div class="text-[10px] uppercase tracking-[0.2em] text-on-surface-variant">Incidents</div>
      <div class="mt-3 font-mono text-3xl font-bold text-primary"><?= number_format($overview['total_incidents'], 0, ',', ' ') ?></div>
    </div>
  </section>

  <section class="grid gap-8 xl:grid-cols-[1.2fr_0.8fr]">
    <div class="rounded-4xl border border-primary/10 bg-white p-6 shadow-sm">
      <div class="mb-6">
        <div class="text-[11px] font-bold uppercase tracking-[0.24em] text-secondary">Charge hebdomadaire</div>
        <h2 class="mt-2 font-headline text-3xl font-black text-primary">Passages sur les voies supervisees</h2>
      </div>

      <div class="grid grid-cols-7 gap-3">
        <?php foreach ($dailyTraffic as $point) : ?>
          <?php $height = max(18, (int) round(($point['passages'] / $dailyMax) * 180)); ?>
          <div class="flex flex-col items-center gap-3">
            <span class="font-mono text-[10px] text-on-surface-variant"><?= $point['passages'] ?></span>
            <div class="flex h-52 w-full items-end rounded-[1.25rem] bg-surface-container-low p-2">
              <div class="w-full rounded-2xl bg-[linear-gradient(180deg,#3D3A8C_0%,#000719_100%)]" style="height: <?= $height ?>px"></div>
            </div>
            <span class="text-[10px] font-bold uppercase tracking-[0.2em] text-primary"><?= htmlspecialchars($point['label_short']) ?></span>
          </div>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="rounded-4xl border border-primary/10 bg-white p-6 shadow-sm">
      <div class="mb-6">
        <div class="text-[11px] font-bold uppercase tracking-[0.24em] text-secondary">Voies</div>
        <h2 class="mt-2 font-headline text-3xl font-black text-primary">Performance par guichet</h2>
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
  </section>

  <section class="mt-8 rounded-4xl border border-primary/10 bg-white p-6 shadow-sm">
    <div class="mb-6">
      <div class="text-[11px] font-bold uppercase tracking-[0.24em] text-secondary">Terrain</div>
      <h2 class="mt-2 font-headline text-3xl font-black text-primary">Chronologie recente</h2>
    </div>
    <div class="space-y-4">
      <?php foreach ($recentActivity as $activity) : ?>
        <?php $isIncident = $activity['activity_type'] === 'incident'; ?>
        <div class="rounded-3xl border <?= $isIncident ? 'border-error/20 bg-error/5' : 'border-primary/10 bg-surface-container-low' ?> p-4">
          <div class="flex items-start justify-between gap-3">
            <div>
              <div class="font-bold <?= $isIncident ? 'text-error' : 'text-primary' ?>"><?= htmlspecialchars($activity['title']) ?></div>
              <div class="mt-1 text-sm text-on-surface-variant">
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
