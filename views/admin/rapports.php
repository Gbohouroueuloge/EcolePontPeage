<?php

use App\Services\AnalyticsService;
use App\Services\ExportService;

$title = 'Rapports';

require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'admin/variables.php';

$analyticsService = new AnalyticsService($pdo);
$exportService = new ExportService();

$preset = $_GET['preset'] ?? '30j';
$dateMin = $_GET['date_min'] ?? null;
$dateMax = $_GET['date_max'] ?? null;
$selectedVoies = array_map('intval', $_GET['voies'] ?? []);
$period = $analyticsService->resolvePeriod($preset, $dateMin, $dateMax);

$overview = $analyticsService->getOverview($period['start'], $period['end'], $selectedVoies);
$transactions = $analyticsService->getDetailedTransactions($period['start'], $period['end'], $selectedVoies);
$incidents = $analyticsService->getDetailedIncidents($period['start'], $period['end'], $selectedVoies);
$revenueByMode = $analyticsService->getRevenueByMode($period['start'], $period['end'], $selectedVoies);
$revenueByGuichet = $analyticsService->getRevenueByGuichet($period['start'], $period['end'], $selectedVoies);
$vehicleMix = $analyticsService->getVehicleTypeMix($period['start'], $period['end'], $selectedVoies);
$guichets = $adminService->getGuichets();
$queryArgs = $_GET;
unset($queryArgs['export']);
$baseExportUrl = '/admin/rapports';
if ($queryArgs) {
  $baseExportUrl .= '?' . http_build_query($queryArgs);
}

if (!empty($_GET['export'])) {
  $headers = ['Date', 'Immatriculation', 'Type vehicule', 'Voie', 'Mode paiement', 'Montant', 'Valide'];
  $rows = array_map(function (array $row): array {
    return [
      date('d/m/Y H:i', strtotime($row['created_at'])),
      $row['immatriculation'],
      $row['type_vehicule'],
      'Voie ' . $row['guichet_id'] . ' ' . $row['guichet'],
      $row['mode_paiement'],
      number_format($row['montant'], 0, ',', ' ') . ' FCFA',
      $row['is_valide'] ? 'Oui' : 'Non',
    ];
  }, $transactions);

  if ($_GET['export'] === 'csv') {
    $exportService->downloadCsv('rapport_admin', $headers, $rows);
  }

  if ($_GET['export'] === 'excel') {
    $exportService->downloadExcel('rapport_admin', $headers, $rows);
  }

  if ($_GET['export'] === 'json') {
    $exportService->downloadJson('rapport_admin', [
      'period' => [
        'preset' => $period['preset'],
        'label' => $period['label'],
        'start' => $period['start']->format(DATE_ATOM),
        'end' => $period['end']->format(DATE_ATOM),
      ],
      'overview' => $overview,
      'transactions' => $transactions,
      'incidents' => $incidents,
      'revenueByMode' => $revenueByMode,
      'revenueByGuichet' => $revenueByGuichet,
      'vehicleMix' => $vehicleMix,
    ]);
  }
}
?>

<main class="md:ml-64 px-6 pb-12 pt-24 md:px-8">
  <section class="mb-10 flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
    <div>
      <div class="text-[11px] font-bold uppercase tracking-[0.3em] text-secondary">Reporting</div>
      <h1 class="mt-2 font-headline text-5xl font-black tracking-tight text-primary">Rapports administrateur</h1>
      <p class="mt-3 max-w-3xl text-sm leading-7 text-on-surface-variant">
        Rapports analytiques sur les transactions et les incidents au cours du temps.
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
      <div class="text-[10px] uppercase tracking-[0.2em] text-on-surface-variant">Periode</div>
      <div class="mt-3 text-lg font-bold text-primary"><?= htmlspecialchars($period['label']) ?></div>
    </div>
    <div class="rounded-3xl border border-primary/10 bg-white p-5 shadow-sm">
      <div class="text-[10px] uppercase tracking-[0.2em] text-on-surface-variant">Revenus</div>
      <div class="mt-3 font-mono text-3xl font-bold text-primary"><?= number_format($overview['revenu_total'], 0, ',', ' ') ?></div>
      <div class="mt-2 text-sm text-on-surface-variant">FCFA</div>
    </div>
    <div class="rounded-3xl border border-primary/10 bg-white p-5 shadow-sm">
      <div class="text-[10px] uppercase tracking-[0.2em] text-on-surface-variant">Passages</div>
      <div class="mt-3 font-mono text-3xl font-bold text-primary"><?= number_format($overview['total_passages'], 0, ',', ' ') ?></div>
    </div>
    <div class="rounded-3xl border border-primary/10 bg-white p-5 shadow-sm">
      <div class="text-[10px] uppercase tracking-[0.2em] text-on-surface-variant">Incidents</div>
      <div class="mt-3 font-mono text-3xl font-bold text-primary"><?= number_format($overview['total_incidents'], 0, ',', ' ') ?></div>
    </div>
  </section>

  <section class="grid gap-8 xl:grid-cols-[0.8fr_1.2fr]">
    <div class="rounded-4xl border border-primary/10 bg-white p-6 shadow-sm">
      <div class="mb-6">
        <div class="text-[11px] font-bold uppercase tracking-[0.24em] text-secondary">Modes</div>
        <h2 class="mt-2 font-headline text-3xl font-black text-primary">Paiements observes</h2>
      </div>
      <div class="space-y-4">
        <?php foreach ($revenueByMode as $mode) : ?>
          <div class="rounded-3xl bg-surface-container-low p-4">
            <div class="flex items-center justify-between gap-3">
              <div class="font-bold text-primary"><?= htmlspecialchars($mode['mode']) ?></div>
              <div class="font-mono text-sm font-bold text-primary"><?= number_format($mode['revenu'], 0, ',', ' ') ?> FCFA</div>
            </div>
            <div class="mt-2 text-xs text-on-surface-variant"><?= number_format($mode['passages'], 0, ',', ' ') ?> passage(s)</div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="rounded-4xl border border-primary/10 bg-white p-6 shadow-sm">
      <div class="mb-6">
        <div class="text-[11px] font-bold uppercase tracking-[0.24em] text-secondary">Vehicules</div>
        <h2 class="mt-2 font-headline text-3xl font-black text-primary">Mix de trafic</h2>
      </div>
      <div class="space-y-4">
        <?php foreach ($vehicleMix as $type) : ?>
          <div class="rounded-3xl bg-surface-container-low p-4">
            <div class="flex items-center justify-between gap-3">
              <div class="font-bold text-primary"><?= htmlspecialchars($type['libelle']) ?></div>
              <div class="font-mono text-sm font-bold text-primary"><?= number_format($type['revenu'], 0, ',', ' ') ?> FCFA</div>
            </div>
            <div class="mt-2 text-xs text-on-surface-variant"><?= number_format($type['passages'], 0, ',', ' ') ?> passage(s) - ticket moyen <?= number_format($type['ticket_moyen'], 0, ',', ' ') ?></div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <section class="mt-8 rounded-4xl border border-primary/10 bg-white p-6 shadow-sm">
    <div class="mb-6">
      <div class="text-[11px] font-bold uppercase tracking-[0.24em] text-secondary">Incidents</div>
      <h2 class="mt-2 font-headline text-3xl font-black text-primary">Derniers signalements</h2>
    </div>
    <div class="space-y-4">
      <?php foreach (array_slice($incidents, 0, 8) as $incident) : ?>
        <div class="rounded-3xl border border-error/15 bg-error/5 p-4">
          <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
            <div>
              <div class="font-headline text-xl font-bold text-error"><?= htmlspecialchars($incident['type']) ?></div>
              <div class="mt-2 uppercase text-sm text-on-surface-variant">Plaque <?= htmlspecialchars($incident['immatriculation']) ?> - Voie <?= $incident['guichet_id'] ?> <?= htmlspecialchars($incident['guichet']) ?></div>
              <?php if ($incident['description']) : ?>
                <div class="mt-2 text-sm text-on-surface-variant"><?= htmlspecialchars($incident['description']) ?></div>
              <?php endif; ?>
            </div>
            <div class="rounded-2xl bg-white px-4 py-3 text-sm font-mono font-bold text-primary shadow-sm">
              <?= date('d/m/Y H:i', strtotime($incident['created_at'])) ?>
            </div>
          </div>
        </div>
      <?php endforeach; ?>

      <?php if (!$incidents) : ?>
        <div class="rounded-3xl bg-surface-container-low p-8 text-center text-on-surface-variant">
          Aucun incident sur la periode choisie.
        </div>
      <?php endif; ?>
    </div>
  </section>

  <section class="mt-8">
    <div class="rounded-4xl border border-primary/10 bg-white p-6 shadow-sm">
      <div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
          <div class="text-[11px] font-bold uppercase tracking-[0.24em] text-secondary">Transactions</div>
          <h2 class="mt-2 font-headline text-3xl font-black text-primary">Tableau des passages</h2>
        </div>

        <form class="flex flex-wrap gap-3" method="get">
          <select class="rounded-2xl border border-outline-variant/30 bg-surface-container-low px-4 py-3 text-sm text-primary" name="preset">
            <option value="30j" <?= $period['preset'] === '30j' ? 'selected' : '' ?>>30 jours</option>
            <option value="7j" <?= $period['preset'] === '7j' ? 'selected' : '' ?>>7 jours</option>
            <option value="trimestre" <?= $period['preset'] === 'trimestre' ? 'selected' : '' ?>>Trimestre</option>
            <option value="annee" <?= $period['preset'] === 'annee' ? 'selected' : '' ?>>Annee</option>
          </select>
          <button class="rounded-2xl bg-primary px-4 py-3 text-xs font-bold uppercase tracking-[0.2em] text-white" type="submit">Appliquer</button>
        </form>
      </div>

      <div class="space-y-4">
        <?php foreach (array_slice($transactions, 0, 12) as $row) : ?>
          <div class="rounded-3xl bg-surface-container-low p-4">
            <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
              <div>
                <div class="font-headline uppercase text-xl font-bold text-primary"><?= htmlspecialchars($row['immatriculation']) ?></div>
                <div class="mt-2 text-sm text-on-surface-variant">
                  <?= htmlspecialchars($row['type_vehicule']) ?> - Voie <?= $row['guichet_id'] ?> <?= htmlspecialchars($row['guichet']) ?>
                </div>
              </div>
              <div class="text-right">
                <div class="font-mono text-sm font-bold text-primary"><?= number_format($row['montant'], 0, ',', ' ') ?> FCFA</div>
                <div class="mt-1 text-xs text-on-surface-variant"><?= htmlspecialchars($row['mode_paiement']) ?> - <?= date('d/m/Y H:i', strtotime($row['created_at'])) ?></div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
</main>