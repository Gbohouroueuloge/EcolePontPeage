<?php

$title = 'Incidents';

require __DIR__ . '/variables.php';

$where = "WHERE i.guichet_id IN (" . supervisorPlaceholders($supervisedGuichetIds) . ")";
$params = $supervisedGuichetIds;

if (!empty($_GET['type'])) {
  $where .= " AND i.type = ?";
  $params[] = $_GET['type'];
}

$stmt = $pdo->prepare("
  SELECT i.*, g.emplacement, v.immatriculation
  FROM incident i
  JOIN guichet g ON g.id = i.guichet_id
  JOIN vehicule v ON v.id = i.vehicule_id
  $where
  ORDER BY i.created_at DESC
");
$stmt->execute($params);
$incidents = $stmt->fetchAll(PDO::FETCH_OBJ);

$stmt = $pdo->prepare("
  SELECT type, COUNT(*) AS total
  FROM incident i
  WHERE i.guichet_id IN (" . supervisorPlaceholders($supervisedGuichetIds) . ")
  GROUP BY type
  ORDER BY total DESC
");
$stmt->execute($supervisedGuichetIds);
$incidentTypes = $stmt->fetchAll(PDO::FETCH_OBJ);

require __DIR__ . '/../../includes/head.php';
require __DIR__ . '/../../layouts/headerSupervisor.php';
?>

<main class="md:ml-72 pt-20 px-6 md:px-8 pb-12">
  <div class="mb-10 mt-4 flex flex-col xl:flex-row xl:items-end xl:justify-between gap-6">
    <div>
      <p class="text-secondary font-mono text-xs font-bold tracking-[0.3em] uppercase mb-2">Alerte</p>
      <h1 class="text-5xl font-headline font-black tracking-tight text-primary">Incidents de zone</h1>
      <p class="text-on-surface-variant mt-3">Tous les signalements remontes depuis les voies sous votre responsabilite.</p>
    </div>
    <form method="get" class="flex items-end gap-3">
      <div>
        <label class="block text-[10px] font-bold uppercase tracking-widest text-slate-500 mb-2">Type</label>
        <select name="type" class="bg-white border border-outline-variant/30 rounded-lg px-4 py-3 text-sm">
          <option value="">Tous les types</option>
          <?php foreach ($incidentTypes as $type) : ?>
            <option value="<?= htmlspecialchars($type->type) ?>" <?= ($_GET['type'] ?? '') === $type->type ? 'selected' : '' ?>>
              <?= htmlspecialchars($type->type) ?> (<?= (int)$type->total ?>)
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <button type="submit" class="bg-primary text-white px-5 py-3 rounded-lg text-xs font-bold uppercase tracking-widest">Filtrer</button>
      <?php if (!empty($_GET['type'])) : ?>
        <a href="incidents.php" class="px-4 py-3 rounded-lg bg-surface-container-high text-primary text-xs font-bold uppercase tracking-widest">Reset</a>
      <?php endif; ?>
    </form>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <?php foreach ($incidentTypes as $type) : ?>
      <div class="bg-surface-container-lowest rounded-2xl p-5 border border-outline-variant/10">
        <p class="text-[10px] font-bold uppercase tracking-widest text-on-surface-variant"><?= htmlspecialchars($type->type) ?></p>
        <p class="mt-3 font-mono text-3xl font-bold text-error"><?= (int)$type->total ?></p>
      </div>
    <?php endforeach; ?>
  </div>

  <div class="space-y-4">
    <?php foreach ($incidents as $incident) : ?>
      <article class="bg-surface-container-lowest rounded-2xl p-6 shadow-sm border-l-4 border-error">
        <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
          <div>
            <div class="flex items-center gap-3 flex-wrap">
              <h2 class="text-xl font-headline font-bold text-primary"><?= htmlspecialchars($incident->immatriculation) ?></h2>
              <span class="px-2 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest bg-error/10 text-error"><?= htmlspecialchars($incident->type) ?></span>
              <span class="px-2 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest bg-surface-container-high text-primary">Voie <?= $incident->guichet_id ?> - <?= htmlspecialchars($incident->emplacement) ?></span>
            </div>
            <p class="mt-3 text-sm text-on-surface-variant"><?= nl2br(htmlspecialchars($incident->description ?: 'Aucune description complementaire.')) ?></p>
          </div>
          <div class="text-right">
            <p class="font-mono text-sm font-bold text-primary"><?= date('d/m/Y H:i', strtotime($incident->created_at)) ?></p>
            <?php if ($incident->url_image) : ?>
              <a class="inline-block mt-3 text-xs font-bold text-secondary" href="<?= htmlspecialchars($incident->url_image) ?>" target="_blank">Voir l'image</a>
            <?php endif; ?>
          </div>
        </div>
      </article>
    <?php endforeach; ?>

    <?php if (empty($incidents)) : ?>
      <div class="bg-surface-container-lowest rounded-2xl p-10 text-center text-on-surface-variant">
        Aucun incident ne correspond aux filtres actuels.
      </div>
    <?php endif; ?>
  </div>
</main>

<?php require __DIR__ . '/../../layouts/footerAdmin.php'; ?>
</html>
