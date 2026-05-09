<?php

$title = 'Historiques';

require __DIR__ . '/variables.php';

$where = "WHERE p.guichet_id IN (" . supervisorPlaceholders($supervisedGuichetIds) . ")";
$params = $supervisedGuichetIds;

if (!empty($_GET['q'])) {
  $where .= " AND v.immatriculation LIKE ?";
  $params[] = '%' . $_GET['q'] . '%';
}
if (!empty($_GET['voie']) && in_array((int)$_GET['voie'], $supervisedGuichetIds, true)) {
  $where .= " AND p.guichet_id = ?";
  $params[] = (int)$_GET['voie'];
}
if (!empty($_GET['paiement'])) {
  $where .= " AND p.mode_paiement = ?";
  $params[] = $_GET['paiement'];
}

$stmt = $pdo->prepare("
  SELECT COUNT(*)
  FROM paiement p
  JOIN vehicule v ON v.id = p.vehicule_id
  $where
");
$stmt->execute($params);
$total = (int)$stmt->fetchColumn();

$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 10;
$offset = ($page - 1) * $perPage;
$totalPages = max(1, (int)ceil($total / $perPage));

$stmt = $pdo->prepare("
  SELECT p.*, g.emplacement, v.immatriculation, t.libelle
  FROM paiement p
  JOIN guichet g ON g.id = p.guichet_id
  JOIN vehicule v ON v.id = p.vehicule_id
  JOIN typevehicule t ON t.id = v.type_vehicule_id
  $where
  ORDER BY p.created_at DESC
  LIMIT $perPage OFFSET $offset
");
$stmt->execute($params);
$payments = $stmt->fetchAll(PDO::FETCH_OBJ);

$baseParams = $_GET;
unset($baseParams['page']);
$baseUrl = '?' . http_build_query($baseParams);

require __DIR__ . '/../../includes/head.php';
require __DIR__ . '/../../layouts/headerSupervisor.php';
?>

<main class="md:ml-72 pt-20 px-6 md:px-8 pb-12">
  <div class="mb-10 mt-4 flex flex-col xl:flex-row xl:items-end xl:justify-between gap-6">
    <div>
      <p class="text-secondary font-mono text-xs font-bold tracking-[0.3em] uppercase mb-2">Controle</p>
      <h1 class="text-5xl font-headline font-black tracking-tight text-primary">Historique des passages</h1>
      <p class="text-on-surface-variant mt-3">Recherche rapide sur les transactions des voies que vous supervisez.</p>
    </div>
    <div class="text-right">
      <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Volume</p>
      <p class="font-mono text-2xl font-bold text-primary"><?= number_format($total, 0, ',', ' ') ?> passages</p>
    </div>
  </div>

  <form method="get" class="bg-surface-container-low p-4 rounded-2xl flex flex-wrap gap-4 mb-8">
    <input type="text" name="q" value="<?= htmlspecialchars($_GET['q'] ?? '') ?>" placeholder="Immatriculation"
      class="flex-1 min-w-52 bg-white rounded-lg px-4 py-3 text-sm border border-outline-variant/20">

    <select name="voie" class="bg-white rounded-lg px-4 py-3 text-sm border border-outline-variant/20">
      <option value="">Toutes les voies</option>
      <?php foreach ($supervisedGuichets as $lane) : ?>
        <option value="<?= $lane->id ?>" <?= ($_GET['voie'] ?? '') == $lane->id ? 'selected' : '' ?>>
          Voie <?= $lane->id ?> - <?= htmlspecialchars($lane->emplacement) ?>
        </option>
      <?php endforeach; ?>
    </select>

    <select name="paiement" class="bg-white rounded-lg px-4 py-3 text-sm border border-outline-variant/20">
      <option value="">Tous les paiements</option>
      <?php foreach (['Espece', 'Carte', 'Abonnement', 'Mobile Money'] as $mode) : ?>
        <option value="<?= $mode ?>" <?= ($_GET['paiement'] ?? '') === $mode ? 'selected' : '' ?>><?= $mode ?></option>
      <?php endforeach; ?>
    </select>

    <button type="submit" class="bg-primary text-white px-5 py-3 rounded-lg text-xs font-bold uppercase tracking-widest">Filtrer</button>
    <?php if (!empty(array_filter($_GET))) : ?>
      <a href="historiques.php" class="bg-surface-container-high text-primary px-5 py-3 rounded-lg text-xs font-bold uppercase tracking-widest">Reset</a>
    <?php endif; ?>
  </form>

  <div class="bg-surface-container-lowest rounded-2xl shadow-sm overflow-hidden">
    <table class="w-full hidden xl:table text-left border-collapse">
      <thead>
        <tr class="bg-surface-container-low">
          <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-500">Immatriculation</th>
          <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-500">Categorie</th>
          <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-500">Voie</th>
          <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-500">Paiement</th>
          <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-500 text-right">Montant</th>
          <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-500">Date</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-surface-container-low">
        <?php foreach ($payments as $payment) : ?>
          <tr class="hover:bg-surface-container-low/40">
            <td class="px-6 py-4 font-bold text-primary"><?= htmlspecialchars($payment->immatriculation) ?></td>
            <td class="px-6 py-4 text-sm text-primary"><?= htmlspecialchars($payment->libelle) ?></td>
            <td class="px-6 py-4 text-sm text-primary">Voie <?= $payment->guichet_id ?> - <?= htmlspecialchars($payment->emplacement) ?></td>
            <td class="px-6 py-4">
              <span class="border px-2 py-1 rounded-full text-[10px] font-bold <?= supervisorBadgeClass($payment->mode_paiement) ?>">
                <?= htmlspecialchars($payment->mode_paiement) ?>
              </span>
            </td>
            <td class="px-6 py-4 text-right font-mono font-bold text-primary"><?= number_format((float)$payment->montant, 0, ',', ' ') ?> FCFA</td>
            <td class="px-6 py-4 font-mono text-xs text-primary"><?= date('d/m/Y H:i', strtotime($payment->created_at)) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <div class="xl:hidden p-4 space-y-4">
      <?php foreach ($payments as $payment) : ?>
        <div class="bg-white rounded-2xl border border-surface-variant p-4">
          <div class="flex items-center justify-between gap-3">
            <p class="font-bold text-primary"><?= htmlspecialchars($payment->immatriculation) ?></p>
            <p class="font-mono text-[10px] text-slate-400"><?= date('d/m H:i', strtotime($payment->created_at)) ?></p>
          </div>
          <p class="mt-2 text-sm text-on-surface-variant"><?= htmlspecialchars($payment->libelle) ?> • Voie <?= $payment->guichet_id ?> - <?= htmlspecialchars($payment->emplacement) ?></p>
          <div class="mt-3 flex items-center justify-between">
            <span class="border px-2 py-1 rounded-full text-[10px] font-bold <?= supervisorBadgeClass($payment->mode_paiement) ?>">
              <?= htmlspecialchars($payment->mode_paiement) ?>
            </span>
            <span class="font-mono text-sm font-bold text-primary"><?= number_format((float)$payment->montant, 0, ',', ' ') ?> FCFA</span>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <div class="px-6 py-4 bg-surface-container-low flex items-center justify-between">
      <p class="text-[10px] font-bold uppercase tracking-widest text-on-surface-variant">Page <?= $page ?> / <?= $totalPages ?></p>
      <div class="flex items-center gap-2">
        <a href="<?= $baseUrl ?>&page=<?= max(1, $page - 1) ?>" class="px-4 py-2 rounded-lg border border-outline-variant/20 text-xs font-bold <?= $page <= 1 ? 'pointer-events-none opacity-40' : 'hover:bg-surface-container-high' ?>">Prec.</a>
        <a href="<?= $baseUrl ?>&page=<?= min($totalPages, $page + 1) ?>" class="px-4 py-2 rounded-lg border border-outline-variant/20 text-xs font-bold <?= $page >= $totalPages ? 'pointer-events-none opacity-40' : 'hover:bg-surface-container-high' ?>">Suiv.</a>
      </div>
    </div>
  </div>
</main>

<?php require __DIR__ . '/../../layouts/footerAdmin.php'; ?>
</html>
