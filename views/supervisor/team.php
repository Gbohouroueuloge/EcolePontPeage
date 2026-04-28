<?php

$title = 'Equipe';

require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'supervisor/variables.php';

$operatorRows = [];
if ($supervisedGuichetIds) {
  $placeholders = implode(', ', array_fill(0, count($supervisedGuichetIds), '?'));
  $stmt = $pdo->prepare("
    SELECT
      u.username,
      u.email,
      a.debut,
      a.fin,
      g.id AS guichet_id,
      g.emplacement
    FROM agent a
    JOIN users u ON u.id = a.user_id
    JOIN guichet g ON g.id = a.guichet_id
    WHERE u.role = 'operateur'
      AND a.guichet_id IN ({$placeholders})
    ORDER BY g.id ASC, u.username ASC
  ");
  $stmt->execute($supervisedGuichetIds);
  $operatorRows = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$pendingOperators = $pdo->query("
  SELECT u.username, u.email
  FROM users u
  LEFT JOIN agent a ON a.user_id = u.id
  WHERE u.role = 'operateur'
    AND (a.guichet_id IS NULL OR a.guichet_id = 0)
  ORDER BY u.created_at DESC
  LIMIT 8
")->fetchAll(PDO::FETCH_ASSOC);
?>

<main class="md:ml-64 px-6 pb-12 pt-24 md:px-8">
  <section class="mb-10">
    <div class="text-[11px] font-bold uppercase tracking-[0.3em] text-secondary">Equipe</div>
    <h1 class="mt-2 font-headline text-5xl font-black tracking-tight text-primary">Operateurs sous supervision</h1>
    <p class="mt-3 max-w-3xl text-sm leading-7 text-on-surface-variant">
      Visualisez les operateurs presents sur vos voies ainsi que les comptes encore en attente d affectation dans la plateforme.
    </p>
  </section>

  <section class="grid gap-8 xl:grid-cols-[1.1fr_0.9fr]">
    <div class="rounded-4xl border border-primary/10 bg-white p-6 shadow-sm">
      <div class="mb-6">
        <div class="text-[11px] font-bold uppercase tracking-[0.24em] text-secondary">Voies suivies</div>
        <h2 class="mt-2 font-headline text-3xl font-black text-primary">Equipe en poste</h2>
      </div>

      <div class="space-y-4">
        <?php foreach ($operatorRows as $operator) : ?>
          <?php $active = $operator['fin'] === null; ?>
          <div class="rounded-3xl border border-outline-variant/15 bg-surface-container-low p-5">
            <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
              <div>
                <div class="flex flex-wrap items-center gap-3">
                  <div class="font-headline text-xl font-bold text-primary"><?= htmlspecialchars($operator['username']) ?></div>
                  <span class="rounded-full px-3 py-1 text-[10px] font-black uppercase tracking-[0.2em] <?= $active ? 'bg-brand-success/15 text-brand-success' : 'bg-error/10 text-error' ?>">
                    <?= $active ? 'En service' : 'Pause' ?>
                  </span>
                </div>
                <div class="mt-2 text-sm text-on-surface-variant"><?= htmlspecialchars($operator['email']) ?></div>
                <div class="mt-2 text-sm font-semibold text-primary">Voie <?= $operator['guichet_id'] ?> - <?= htmlspecialchars($operator['emplacement']) ?></div>
              </div>
              <div class="rounded-2xl bg-white px-4 py-3 text-sm text-primary shadow-sm">
                Debut:
                <span class="font-mono font-bold">
                  <?= $operator['debut'] ? date('d/m H:i', strtotime($operator['debut'])) : 'Non demarre' ?>
                </span>
              </div>
            </div>
          </div>
        <?php endforeach; ?>

        <?php if (!$operatorRows) : ?>
          <div class="rounded-3xl bg-surface-container-low p-8 text-center text-on-surface-variant">
            Aucun operateur actuellement rattache a vos guichets.
          </div>
        <?php endif; ?>
      </div>
    </div>

    <div class="rounded-4xl border border-primary/10 bg-white p-6 shadow-sm">
      <div class="mb-6">
        <div class="text-[11px] font-bold uppercase tracking-[0.24em] text-secondary">Affectations</div>
        <h2 class="mt-2 font-headline text-3xl font-black text-primary">Comptes en attente</h2>
      </div>

      <div class="space-y-4">
        <?php foreach ($pendingOperators as $operator) : ?>
          <div class="rounded-3xl border border-secondary-container/20 bg-secondary-container/10 p-5">
            <div class="font-headline text-xl font-bold text-primary"><?= htmlspecialchars($operator['username']) ?></div>
            <div class="mt-2 text-sm text-on-surface-variant"><?= htmlspecialchars($operator['email']) ?></div>
          </div>
        <?php endforeach; ?>

        <?php if (!$pendingOperators) : ?>
          <div class="rounded-3xl bg-surface-container-low p-8 text-center text-on-surface-variant">
            Aucun compte operateur en attente actuellement.
          </div>
        <?php endif; ?>
      </div>
    </div>
  </section>
</main>
