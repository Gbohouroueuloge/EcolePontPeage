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

$iconVehicule = [
  "Moto" => "motorcycle",
  "Voiture" => "directions_car",
  "Van/SUV" => "airport_shuttle",
  "Poids Lourd" => "local_shipping",
];

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

<main class="md:ml-64 px-6 pb-12 pt-24 md:px-8 bg-background min-h-screen">
  <section class="mb-10 flex flex-col gap-6 xl:flex-row xl:items-end xl:justify-between anim-fade-up">
    <div class="pb-4">
      <div class="text-[11px] font-bold uppercase tracking-[0.3em] text-secondary flex items-center gap-2">
        <span class="material-symbols-outlined text-sm">monitoring</span> Analyse des données
      </div>
      <h1 class="mt-2 font-headline header-accent text-5xl font-black tracking-tight text-primary">Analytics</h1>
      <p class="mt-3 max-w-3xl text-sm leading-7 text-on-surface-variant">
        Rapports détaillés sur le trafic, les performances financières.
      </p>
    </div>

    <div class="flex flex-wrap gap-2">
      <a class="rounded-2xl bg-primary px-4 py-3 text-xs font-bold uppercase tracking-[0.2em] text-white" href="<?= $baseExportUrl . ($queryArgs ? '&' : '?') ?>export=csv">Export CSV</a>
      <a class="rounded-2xl border border-primary/15 px-4 py-3 text-xs font-bold uppercase tracking-[0.2em] text-primary" href="<?= $baseExportUrl . ($queryArgs ? '&' : '?') ?>export=json">Export JSON</a>
      <a class="rounded-2xl border border-primary/15 px-4 py-3 text-xs font-bold uppercase tracking-[0.2em] text-primary" href="<?= $baseExportUrl . ($queryArgs ? '&' : '?') ?>export=excel">Export Excel</a>
    </div>
  </section>

  <section class="mb-8 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
    <?php
    $stats = [
      ['label' => 'Chiffre d\'affaires', 'value' => number_format($overview['revenu_total'], 0, ',', ' ') . ' <span class="text-xs">FCFA</span>', 'icon' => 'payments', 'delay' => 'delay-0'],
      ['label' => 'Volume Trafic', 'value' => number_format($overview['total_passages'], 0, ',', ' '), 'icon' => 'directions_car', 'delay' => 'delay-100'],
      ['label' => 'Moyenne / Jour', 'value' => number_format($overview['total_passages'] / 30, 1, ',', ' '), 'icon' => 'calendar_today', 'delay' => 'delay-150'],
      ['label' => 'Taux d\'Incidents', 'value' => number_format($overview['total_incidents'], 0), 'icon' => 'warning', 'delay' => 'delay-200'],
    ];
    foreach ($stats as $stat): ?>
      <div class="card-lift <?= $stat['delay'] ?> anim-scale-in rounded-3xl border border-primary/10 bg-white p-6 shadow-sm">
        <div class="flex items-center justify-between">
          <span class="stat-icon flex h-12 w-12 items-center justify-center rounded-2xl bg-surface-container-low text-primary">
            <span class="material-symbols-outlined"><?= $stat['icon'] ?></span>
          </span>
        </div>
        <div class="mt-4 text-[10px] font-bold uppercase tracking-[0.2em] text-on-surface-variant"><?= $stat['label'] ?></div>
        <div class="mt-1 font-mono text-3xl font-bold text-primary"><?= $stat['value'] ?></div>
      </div>
    <?php endforeach; ?>
  </section>

  <section class="grid gap-8 xl:grid-cols-12">
    <div class="space-y-8 col-span-7">
      <div class="rounded-4xl border border-primary/10 bg-white p-6 shadow-sm">
        <div class="mb-8 flex justify-between items-start">
          <div>
            <div class="text-[11px] font-bold uppercase tracking-[0.24em] text-secondary">Flux journalier</div>
            <h2 class="mt-2 font-headline text-3xl font-black text-primary">Passages des 7 derniers jours</h2>
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
        <div class="mb-8 flex justify-between items-start">
          <div>
            <div class="text-[11px] font-bold uppercase tracking-[0.24em] text-secondary">Projection</div>
            <h2 class="mt-2 font-headline text-3xl font-black text-primary">Revenus mensuels</h2>
          </div>
          <span class="material-symbols-outlined text-indigo-500">query_stats</span>
        </div>
        <div class="grid grid-cols-6 gap-3 items-end">
          <?php foreach ($monthlyRevenue as $point) : ?>
            <?php $height = max(16, (int) round(($point['revenu'] / $monthlyMax) * 170)); ?>
            <div class="flex flex-col items-center gap-3 h-full justify-end group">
              <span class="font-mono text-[10px] text-on-surface-variant opacity-0 group-hover:opacity-100 transition-opacity"><?= number_format($point['revenu'] / 1000, 0) ?>k</span>
              <div class="flex h-48 w-full items-end rounded-[1.25rem] bg-surface-container-low p-2">
                <div class="bar-animate w-full rounded-2xl bg-[linear-gradient(180deg,#3D3A8C_0%,#000719_100%)]" style="height: <?= $height ?>px" animation-delay: <?= $i * 50 ?>ms></div>
              </div>
              <span class="text-[10px] font-bold uppercase tracking-[0.2em] text-primary"><?= htmlspecialchars($point['label']) ?></span>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>

    <div class="space-y-8 col-span-5">
      <div class="anim-fade-up delay-200 rounded-4xl border border-primary/10 bg-white p-6 shadow-sm">
        <h3 class="mb-6 font-headline text-xl font-bold text-primary flex items-center justify-between gap-2">
          <span>Modes de paiement</span>
          <span class="material-symbols-outlined text-secondary">account_balance_wallet</span>
        </h3>
        <div class="space-y-6">
          <?php
          $modeTotal = array_sum(array_column($revenueByMode, 'revenu'));
          foreach ($revenueByMode as $idx => $m):
            $p = $modeTotal > 0 ? ($m['revenu'] / $modeTotal) * 100 : 0;
          ?>
            <div class="anim-fade delay-<?= $idx * 150 ?>">
              <div class="mb-2 flex items-center justify-between text-xs font-bold">
                <span class="text-primary uppercase tracking-wider"><?= $m['mode'] ?></span>
                <span class="text-secondary"><?= round($p) ?>%</span>
              </div>
              <div class="h-2 rounded-full bg-surface-container-low overflow-hidden">
                <div class="progress-animate h-full rounded-full bg-secondary"
                  style="--progress-w: <?= $p ?>%; animation-delay: <?= 0.5 + ($idx * 0.1) ?>s"></div>
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
          <div class="rounded-3xl bg-surface-container-low p-4 group">
            <div class="flex items-center justify-between gap-3">
              <div>
                <div class="material-symbols-outlined group-hover:scale-110 transition-all group-hover:text-secondary text-on-surface-variant">
                  <?= $iconVehicule[$type['libelle']] ?? "commute" ?>
                </div>
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
  </section>

  <section
    class="mt-8 rounded-4xl border border-primary/10 bg-white p-6 shadow-sm anim-fade-up delay-500">
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