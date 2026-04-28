<?php

use App\Services\ExportService;

$title = 'Rapports';

require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'supervisor/variables.php';

$exportService = new ExportService();
$period = $analyticsService->resolvePeriod($_GET['preset'] ?? '30j', $_GET['date_min'] ?? null, $_GET['date_max'] ?? null);
$overview = $analyticsService->getOverview($period['start'], $period['end'], $supervisedGuichetIds);
$transactions = $analyticsService->getDetailedTransactions($period['start'], $period['end'], $supervisedGuichetIds);
$incidents = $analyticsService->getDetailedIncidents($period['start'], $period['end'], $supervisedGuichetIds);
$queryArgs = $_GET;
unset($queryArgs['export']);
$baseExportUrl = '/superviseur/rapports';
if ($queryArgs) {
  $baseExportUrl .= '?' . http_build_query($queryArgs);
}

if (!empty($_GET['export'])) {
  $headers = ['Date', 'Immatriculation', 'Type vehicule', 'Voie', 'Paiement', 'Montant'];
  $rows = array_map(function (array $row): array {
    return [
      date('d/m/Y H:i', strtotime($row['created_at'])),
      $row['immatriculation'],
      $row['type_vehicule'],
      'Voie ' . $row['guichet_id'] . ' ' . $row['guichet'],
      $row['mode_paiement'],
      number_format($row['montant'], 0, ',', ' ') . ' FCFA',
    ];
  }, $transactions);

  if ($_GET['export'] === 'csv') {
    $exportService->downloadCsv('rapport_superviseur', $headers, $rows);
  }

  if ($_GET['export'] === 'excel') {
    $exportService->downloadExcel('rapport_superviseur', $headers, $rows);
  }

  if ($_GET['export'] === 'json') {
    $exportService->downloadJson('rapport_superviseur', [
      'period' => [
        'label' => $period['label'],
        'start' => $period['start']->format(DATE_ATOM),
        'end' => $period['end']->format(DATE_ATOM),
      ],
      'overview' => $overview,
      'transactions' => $transactions,
      'incidents' => $incidents,
    ]);
  }
}
?>

<main class="md:ml-64 px-6 pb-12 pt-24 md:px-8">
  <section class="mb-10 flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
    <div>
      <div class="text-[11px] font-bold uppercase tracking-[0.3em] text-secondary">Reporting</div>
      <h1 class="mt-2 font-headline text-5xl font-black tracking-tight text-primary">Rapports superviseur</h1>
      <p class="mt-3 max-w-3xl text-sm leading-7 text-on-surface-variant">
        Exportez les passages et incidents de votre perimetre pour partage terrain, point journalier et suivi de performance.
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

  <section class="grid gap-8 xl:grid-cols-[1.15fr_0.85fr]">
    <div class="rounded-4xl border border-primary/10 bg-white p-6 shadow-sm">
      <div class="mb-6">
        <div class="text-[11px] font-bold uppercase tracking-[0.24em] text-secondary">Transactions</div>
        <h2 class="mt-2 font-headline text-3xl font-black text-primary">Derniers passages exportables</h2>
      </div>

      <div class="space-y-4">
        <?php foreach (array_slice($transactions, 0, 10) as $row) : ?>
          <div class="rounded-3xl bg-surface-container-low p-4">
            <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
              <div>
                <div class="font-headline text-xl font-bold text-primary"><?= htmlspecialchars($row['immatriculation']) ?></div>
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

    <div class="rounded-4xl border border-primary/10 bg-white p-6 shadow-sm">
      <div class="mb-6">
        <div class="text-[11px] font-bold uppercase tracking-[0.24em] text-secondary">Incidents</div>
        <h2 class="mt-2 font-headline text-3xl font-black text-primary">Synthese a partager</h2>
      </div>

      <div class="space-y-4">
        <?php foreach (array_slice($incidents, 0, 8) as $incident) : ?>
          <div class="rounded-3xl border border-error/15 bg-error/5 p-4">
            <div class="font-headline text-xl font-bold text-error"><?= htmlspecialchars($incident['type']) ?></div>
            <div class="mt-2 text-sm text-on-surface-variant">Plaque <?= htmlspecialchars($incident['immatriculation']) ?> - Voie <?= $incident['guichet_id'] ?> <?= htmlspecialchars($incident['guichet']) ?></div>
            <div class="mt-2 text-xs text-on-surface-variant"><?= date('d/m/Y H:i', strtotime($incident['created_at'])) ?></div>
          </div>
        <?php endforeach; ?>

        <?php if (!$incidents) : ?>
          <div class="rounded-3xl bg-surface-container-low p-8 text-center text-on-surface-variant">
            Aucun incident sur cette periode.
          </div>
        <?php endif; ?>
      </div>
    </div>
  </section>
</main>
