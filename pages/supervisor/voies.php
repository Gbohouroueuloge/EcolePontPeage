<?php

$title = 'Voies';

require __DIR__ . '/variables.php';

$placeholders = supervisorPlaceholders($supervisedGuichetIds);

$stmt = $pdo->prepare("
  SELECT g.id, g.slug, g.emplacement, g.is_active,
         COUNT(p.id) AS total_passages,
         COALESCE(SUM(p.montant), 0) AS revenu_total,
         MAX(p.created_at) AS last_payment_at,
         u.username AS operateur_nom,
         a.debut,
         a.fin
  FROM guichet g
  LEFT JOIN paiement p ON p.guichet_id = g.id
  LEFT JOIN agent a ON a.guichet_id = g.id AND a.fin IS NULL
  LEFT JOIN users u ON u.id = a.user_id
  WHERE g.id IN ($placeholders)
  GROUP BY g.id, g.slug, g.emplacement, g.is_active, u.username, a.debut, a.fin
  ORDER BY g.id ASC
");
$stmt->execute($supervisedGuichetIds);
$lanes = $stmt->fetchAll(PDO::FETCH_OBJ);

require __DIR__ . '/../../includes/head.php';
require __DIR__ . '/../../layouts/headerSupervisor.php';
?>

<main class="md:ml-72 pt-20 px-6 md:px-8 pb-12">
  <div class="mb-10 mt-4">
    <p class="text-secondary font-mono text-xs font-bold tracking-[0.3em] uppercase mb-2">Terrain</p>
    <h1 class="text-5xl font-headline font-black tracking-tight text-primary">Etat des voies</h1>
    <p class="text-on-surface-variant mt-3">Toutes les voies affectees a votre supervision avec leur statut d’exploitation.</p>
  </div>

  <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
    <?php foreach ($lanes as $lane) : ?>
      <article class="bg-surface-container-lowest rounded-2xl p-7 shadow-sm border border-outline-variant/10">
        <div class="flex items-start justify-between gap-4 mb-6">
          <div>
            <p class="text-[10px] font-bold uppercase tracking-widest text-on-surface-variant">Voie <?= $lane->id ?></p>
            <h2 class="text-3xl font-headline font-black text-primary"><?= htmlspecialchars($lane->emplacement) ?></h2>
          </div>
          <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest <?= $lane->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700' ?>">
            <?= $lane->is_active ? 'Disponible' : 'Fermee' ?>
          </span>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-6">
          <div class="bg-surface-container-low rounded-xl p-4">
            <p class="text-[10px] font-bold uppercase tracking-widest text-on-surface-variant">Revenus</p>
            <p class="mt-2 font-mono text-xl font-bold text-primary"><?= number_format((float)$lane->revenu_total, 0, ',', ' ') ?> FCFA</p>
          </div>
          <div class="bg-surface-container-low rounded-xl p-4">
            <p class="text-[10px] font-bold uppercase tracking-widest text-on-surface-variant">Passages</p>
            <p class="mt-2 font-mono text-xl font-bold text-primary"><?= (int)$lane->total_passages ?></p>
          </div>
        </div>

        <div class="space-y-3 text-sm">
          <div class="flex items-center justify-between gap-4">
            <span class="text-on-surface-variant">Operateur en service</span>
            <span class="font-bold text-primary"><?= htmlspecialchars($lane->operateur_nom ?? 'Aucun operateur actif') ?></span>
          </div>
          <div class="flex items-center justify-between gap-4">
            <span class="text-on-surface-variant">Prise de poste</span>
            <span class="font-mono text-primary"><?= $lane->debut ? date('d/m/Y H:i', strtotime($lane->debut)) : 'Non demarree' ?></span>
          </div>
          <div class="flex items-center justify-between gap-4">
            <span class="text-on-surface-variant">Dernier paiement</span>
            <span class="font-mono text-primary"><?= $lane->last_payment_at ? date('d/m/Y H:i', strtotime($lane->last_payment_at)) : 'Aucun' ?></span>
          </div>
        </div>
      </article>
    <?php endforeach; ?>
  </div>
</main>

<?php require __DIR__ . '/../../layouts/footerAdmin.php'; ?>
</html>
