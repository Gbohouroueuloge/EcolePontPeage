<?php

$title = 'Incidents';

require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'supervisor/variables.php';

$period = $analyticsService->resolvePeriod($_GET['preset'] ?? '30j', $_GET['date_min'] ?? null, $_GET['date_max'] ?? null);
$incidentsByType = $analyticsService->getIncidentsByType($period['start'], $period['end'], $supervisedGuichetIds);
$incidents = $analyticsService->getDetailedIncidents($period['start'], $period['end'], $supervisedGuichetIds);
?>

<main class="md:ml-64 px-6 pb-12 pt-24 md:px-8">
  <section class="mb-10">
    <div class="text-[11px] font-bold uppercase tracking-[0.3em] text-secondary">Surete</div>
    <h1 class="mt-2 font-headline text-5xl font-black tracking-tight text-primary">Incidents des voies supervisees</h1>
    <p class="mt-3 max-w-3xl text-sm leading-7 text-on-surface-variant">
      Retrouvez les incidents declares sur vos guichets, leur typologie dominante et leur chronologie detaillee.
    </p>
  </section>

  <section class="mb-8 flex flex-wrap gap-3">
    <?php foreach ($incidentsByType as $incident) : ?>
      <div class="rounded-full bg-error/10 px-4 py-2 text-sm font-semibold text-error">
        <?= htmlspecialchars($incident['type']) ?> - <?= $incident['total'] ?>
      </div>
    <?php endforeach; ?>
  </section>

  <section class="rounded-4xl border border-primary/10 bg-white p-6 shadow-sm">
    <div class="mb-6">
      <div class="text-[11px] font-bold uppercase tracking-[0.24em] text-secondary">Journal</div>
      <h2 class="mt-2 font-headline text-3xl font-black text-primary">Historique incident</h2>
    </div>

    <div class="space-y-4">
      <?php foreach ($incidents as $incident) : ?>
        <div class="rounded-3xl border border-error/15 bg-error/5 p-5">
          <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
            <div>
              <div class="font-headline text-xl font-bold text-error"><?= htmlspecialchars($incident['type']) ?></div>
              <div class="mt-2 text-sm text-on-surface-variant">
                Plaque <?= htmlspecialchars($incident['immatriculation']) ?> - Voie <?= $incident['guichet_id'] ?> <?= htmlspecialchars($incident['guichet']) ?>
              </div>
              <?php if ($incident['description']) : ?>
                <div class="mt-3 text-sm leading-7 text-on-surface-variant"><?= htmlspecialchars($incident['description']) ?></div>
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
          Aucun incident sur la periode observee.
        </div>
      <?php endif; ?>
    </div>
  </section>
</main>
