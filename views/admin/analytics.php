<?php

use App\Services\AnalyticsService;
use App\Services\ExportService;

$title = 'Analytics';

require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'admin/variables.php';

$days = 7;

$analyticsService = new AnalyticsService($pdo);
$exportService = new ExportService();

$preset = $_GET['preset'] ?? '30j';
$dateMin = $_GET['date_min'] ?? null;
$dateMax = $_GET['date_max'] ?? null;
$selectedVoies = array_map('intval', $_GET['voies'] ?? []);
$period = $analyticsService->resolvePeriod($preset, $dateMin, $dateMax);

$overview = $analyticsService->getOverview($period['start'], $period['end'], $selectedVoies);
$dailyTraffic = $analyticsService->getDailyTrafficSeries($days, $selectedVoies);
$monthlyRevenue = $analyticsService->getMonthlyRevenueSeries(6, $selectedVoies);
$revenueByMode = $analyticsService->getRevenueByMode($period['start'], $period['end'], $selectedVoies);
$revenueByGuichet = $analyticsService->getRevenueByGuichet($period['start'], $period['end'], $selectedVoies);
$vehicleMix = $analyticsService->getVehicleTypeMix($period['start'], $period['end'], $selectedVoies);
$incidentsByType = $analyticsService->getIncidentsByType($period['start'], $period['end'], $selectedVoies);
$recentActivity = $analyticsService->getRecentActivity(8, $selectedVoies);
$guichets = $adminService->getGuichets();
$queryArgs = $_GET;
unset($queryArgs['export']);
$baseExportUrl = '/admin/analytics';
if ($queryArgs) {
  $baseExportUrl .= '?' . http_build_query($queryArgs);
}

if (!empty($_GET['export'])) {
  $summaryRows = array_map(function (array $row): array {
    return [
      $row['date_key'],
      $row['label'],
      $row['passages'],
      number_format($row['revenu'], 0, ',', ' '),
      $row['incidents'],
    ];
  }, $dailyTraffic);

  if ($_GET['export'] === 'csv') {
    $exportService->downloadCsv(
      'analytics_peage',
      ['Date cle', 'Jour', 'Passages', 'Revenus FCFA', 'Incidents'],
      $summaryRows
    );
  }

  if ($_GET['export'] === 'excel') {
    $exportService->downloadExcel(
      'analytics_peage',
      ['Date cle', 'Jour', 'Passages', 'Revenus FCFA', 'Incidents'],
      $summaryRows
    );
  }

  if ($_GET['export'] === 'json') {
    $exportService->downloadJson('analytics_peage', [
      'period' => [
        'preset' => $period['preset'],
        'label' => $period['label'],
        'start' => $period['start']->format(DATE_ATOM),
        'end' => $period['end']->format(DATE_ATOM),
      ],
      'overview' => $overview,
      'dailyTraffic' => $dailyTraffic,
      'monthlyRevenue' => $monthlyRevenue,
      'revenueByMode' => $revenueByMode,
      'revenueByGuichet' => $revenueByGuichet,
      'vehicleMix' => $vehicleMix,
      'incidentsByType' => $incidentsByType,
    ]);
  }
}

$dailyMax = 1;
foreach ($dailyTraffic as $point) {
  $dailyMax = max($dailyMax, (int) $point['passages']);
}

$monthlyMax = 1;
foreach ($monthlyRevenue as $point) {
  $monthlyMax = max($monthlyMax, (int) $point['revenu']);
}

$modeTotal = 0.0;
foreach ($revenueByMode as $mode) {
  $modeTotal += $mode['revenu'];
}
?>

<main class="md:ml-64 px-6 pb-12 pt-24 md:px-8">
  <section class="mb-10 flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
    <div>
      <div class="text-[11px] font-bold uppercase tracking-[0.3em] text-secondary">Decisionnel</div>
      <h1 class="mt-2 font-headline text-5xl font-black tracking-tight text-primary">Analytics temps reel</h1>
      <p class="mt-3 max-w-3xl text-sm leading-7 text-on-surface-variant">
        Données analytiques sur les passages et les revenus au cours du temps.
      </p>
    </div>

    <div class="flex flex-wrap gap-2">
      <a class="rounded-2xl bg-primary px-4 py-3 text-xs font-bold uppercase tracking-[0.2em] text-white" href="<?= $baseExportUrl . ($queryArgs ? '&' : '?') ?>export=csv">Export CSV</a>
      <a class="rounded-2xl border border-primary/15 px-4 py-3 text-xs font-bold uppercase tracking-[0.2em] text-primary" href="<?= $baseExportUrl . ($queryArgs ? '&' : '?') ?>export=json">Export JSON</a>
      <a class="rounded-2xl border border-primary/15 px-4 py-3 text-xs font-bold uppercase tracking-[0.2em] text-primary" href="<?= $baseExportUrl . ($queryArgs ? '&' : '?') ?>export=excel">Export Excel</a>
    </div>
  </section>

  <section class="mb-8 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
    <div class="rounded-3xl border border-primary/10 bg-white p-5 shadow-sm">
      <div class="text-[10px] uppercase tracking-[0.2em] text-on-surface-variant">Revenus</div>
      <div class="mt-3 font-mono text-3xl font-bold text-primary"><?= number_format($overview['revenu_total'], 0, ',', ' ') ?></div>
      <div class="mt-2 text-sm text-on-surface-variant">FCFA sur <?= htmlspecialchars($period['label']) ?></div>
    </div>
    <div class="rounded-3xl border border-primary/10 bg-white p-5 shadow-sm">
      <div class="text-[10px] uppercase tracking-[0.2em] text-on-surface-variant">Passages</div>
      <div class="mt-3 font-mono text-3xl font-bold text-primary"><?= number_format($overview['total_passages'], 0, ',', ' ') ?></div>
      <div class="mt-2 text-sm text-on-surface-variant"><?= $overview['voies_couvertes'] ?> voie(s) observee(s)</div>
    </div>
    <div class="rounded-3xl border border-primary/10 bg-white p-5 shadow-sm">
      <div class="text-[10px] uppercase tracking-[0.2em] text-on-surface-variant">Ticket moyen</div>
      <div class="mt-3 font-mono text-3xl font-bold text-primary"><?= number_format($overview['ticket_moyen'], 0, ',', ' ') ?></div>
      <div class="mt-2 text-sm text-on-surface-variant">FCFA par passage</div>
    </div>
    <div class="rounded-3xl border border-primary/10 bg-white p-5 shadow-sm">
      <div class="text-[10px] uppercase tracking-[0.2em] text-on-surface-variant">Incidents</div>
      <div class="mt-3 font-mono text-3xl font-bold text-primary"><?= number_format($overview['total_incidents'], 0, ',', ' ') ?></div>
      <div class="mt-2 text-sm text-on-surface-variant"><?= $overview['agents_actifs'] ?> agent(s) en service</div>
    </div>
  </section>

  <section class="grid gap-8 xl:grid-cols-12">
    <div class="space-y-8 col-span-7">
      <div class="rounded-4xl border border-primary/10 bg-white p-6 shadow-sm">
        <div class="mb-6">
          <div class="text-[11px] font-bold uppercase tracking-[0.24em] text-secondary">Flux journalier</div>
          <h2 class="mt-2 font-headline text-3xl font-black text-primary">Passages des 7 derniers jours</h2>
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
              <span class="text-[10px] font-bold uppercase tracking-[0.2em] text-primary"><?= htmlspecialchars($point['label_short']) ?></span>
            </div>
          <?php endforeach; ?>
        </div>
      </div>

      <div class="rounded-4xl border border-primary/10 bg-white p-6 shadow-sm">
        <div class="mb-6">
          <div class="text-[11px] font-bold uppercase tracking-[0.24em] text-secondary">Projection</div>
          <h2 class="mt-2 font-headline text-3xl font-black text-primary">Revenus mensuels</h2>
        </div>
        <div class="grid grid-cols-6 gap-3">
          <?php foreach ($monthlyRevenue as $point) : ?>
            <?php $height = max(16, (int) round(($point['revenu'] / $monthlyMax) * 170)); ?>
            <div class="flex flex-col items-center gap-3">
              <span class="font-mono text-[10px] text-on-surface-variant"><?= number_format($point['revenu'] / 1000, 0) ?>k</span>
              <div class="flex h-48 w-full items-end rounded-[1.25rem] bg-surface-container-low p-2">
                <div class="w-full rounded-2xl bg-[linear-gradient(180deg,#3D3A8C_0%,#000719_100%)]" style="height: <?= $height ?>px"></div>
              </div>
              <span class="text-[10px] font-bold uppercase tracking-[0.2em] text-primary"><?= htmlspecialchars($point['label']) ?></span>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>

    <div class="space-y-8 col-span-5">
      <div class="rounded-4xl border border-primary/10 bg-white p-6 shadow-sm">
        <div class="mb-6">
          <div class="text-[11px] font-bold uppercase tracking-[0.24em] text-secondary">Paiements</div>
          <h2 class="mt-2 font-headline text-3xl font-black text-primary">Repartition par mode</h2>
        </div>
        <div class="space-y-4">
          <?php foreach ($revenueByMode as $mode) : ?>
            <?php $percent = $modeTotal > 0 ? (int) round(($mode['revenu'] / $modeTotal) * 100) : 0; ?>
            <div>
              <div class="mb-1 flex items-center justify-between">
                <span class="text-sm font-semibold text-primary"><?= htmlspecialchars($mode['mode']) ?></span>
                <span class="font-mono text-xs text-on-surface-variant"><?= $percent ?>% - <?= number_format($mode['revenu'], 0, ',', ' ') ?> FCFA</span>
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
          <div class="text-[11px] font-bold uppercase tracking-[0.24em] text-secondary">Incidents</div>
          <h2 class="mt-2 font-headline text-3xl font-black text-primary">Typologie</h2>
        </div>
        <div class="flex flex-wrap gap-3">
          <?php foreach ($incidentsByType as $incident) : ?>
            <div class="rounded-full bg-error/10 px-4 py-2 text-sm font-semibold text-error">
              <?= htmlspecialchars($incident['type']) ?> - <?= $incident['total'] ?>
            </div>
          <?php endforeach; ?>
          <?php if (!$incidentsByType) : ?>
            <div class="text-sm text-on-surface-variant">Aucun incident sur la periode selectionnee.</div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </section>

  <section class="mt-8 grid gap-8 xl:grid-cols-2">
    <div class="rounded-4xl border border-primary/10 bg-white p-6 shadow-sm">
      <div class="mb-6">
        <div class="text-[11px] font-bold uppercase tracking-[0.24em] text-secondary">Mix vehicules</div>
        <h2 class="mt-2 font-headline text-3xl font-black text-primary">Categorie et ticket moyen</h2>
      </div>
      <div class="space-y-4">
        <?php foreach ($vehicleMix as $type) : ?>
          <div class="rounded-3xl bg-surface-container-low p-4">
            <div class="flex items-center justify-between gap-3">
              <div>
                <div class="font-bold text-primary"><?= htmlspecialchars($type['libelle']) ?></div>
                <div class="mt-1 text-xs text-on-surface-variant"><?= number_format($type['passages'], 0, ',', ' ') ?> passages</div>
              </div>
              <div class="text-right">
                <div class="font-mono text-sm font-bold text-primary"><?= number_format($type['revenu'], 0, ',', ' ') ?> FCFA</div>
                <div class="mt-1 text-xs text-on-surface-variant">Ticket moyen <?= number_format($type['ticket_moyen'], 0, ',', ' ') ?></div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="rounded-4xl border border-primary/10 bg-white p-6 shadow-sm">
      <div class="mb-6">
        <div class="text-[11px] font-bold uppercase tracking-[0.24em] text-secondary">Voies</div>
        <h2 class="mt-2 font-headline text-3xl font-black text-primary">Top guichets</h2>
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

  <section class="mt-8">
    <div class="rounded-4xl border border-primary/10 bg-white p-6 shadow-sm">
      <div class="mb-6">
        <div class="text-[11px] font-bold uppercase tracking-[0.24em] text-secondary">Activite</div>
        <h2 class="mt-2 font-headline text-3xl font-black text-primary">Chronologie recente</h2>
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
                <div class="mt-1 uppercase text-sm text-on-surface-variant">
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
    </div>
  </section>
</main>