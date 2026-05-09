<?php

$title = 'Dashboard';

require __DIR__ . '/variables.php';

$placeholders = supervisorPlaceholders($supervisedGuichetIds);

$stmt = $pdo->prepare("
  SELECT COUNT(*) AS total_passages, COALESCE(SUM(montant), 0) AS revenu_total
  FROM paiement
  WHERE guichet_id IN ($placeholders)
");
$stmt->execute($supervisedGuichetIds);
$kpi = $stmt->fetch(PDO::FETCH_OBJ);

$stmt = $pdo->prepare("
  SELECT COUNT(*) 
  FROM incident
  WHERE guichet_id IN ($placeholders)
");
$stmt->execute($supervisedGuichetIds);
$incidentCount = (int)$stmt->fetchColumn();

$stmt = $pdo->prepare("
  SELECT a.*, u.username, g.emplacement
  FROM agent a
  JOIN users u ON u.id = a.user_id
  JOIN guichet g ON g.id = a.guichet_id
  WHERE a.guichet_id IN ($placeholders)
  ORDER BY a.debut DESC, a.updated_at DESC
");
$stmt->execute($supervisedGuichetIds);
$operators = $stmt->fetchAll(PDO::FETCH_OBJ);

$activeOperators = array_values(array_filter($operators, fn($agent) => $agent->debut !== null && $agent->fin === null));

$stmt = $pdo->prepare("
  SELECT p.created_at, p.mode_paiement, p.montant, g.id AS guichet_id, g.emplacement, v.immatriculation
  FROM paiement p
  JOIN guichet g ON g.id = p.guichet_id
  JOIN vehicule v ON v.id = p.vehicule_id
  WHERE p.guichet_id IN ($placeholders)
  ORDER BY p.created_at DESC
  LIMIT 8
");
$stmt->execute($supervisedGuichetIds);
$recentPayments = $stmt->fetchAll(PDO::FETCH_OBJ);

$stmt = $pdo->prepare("
  SELECT g.id, g.emplacement, g.is_active,
         COUNT(p.id) AS nb_passages,
         COALESCE(SUM(p.montant), 0) AS revenu_total,
         MAX(p.created_at) AS last_payment_at
  FROM guichet g
  LEFT JOIN paiement p ON p.guichet_id = g.id
  WHERE g.id IN ($placeholders)
  GROUP BY g.id, g.emplacement, g.is_active
  ORDER BY g.id ASC
");
$stmt->execute($supervisedGuichetIds);
$laneStats = $stmt->fetchAll(PDO::FETCH_OBJ);

$stmt = $pdo->prepare("
  SELECT DATE(created_at) AS jour, COUNT(*) AS total
  FROM paiement
  WHERE guichet_id IN ($placeholders)
    AND created_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
  GROUP BY DATE(created_at)
  ORDER BY jour ASC
");
$stmt->execute($supervisedGuichetIds);
$dailyTrafficRows = $stmt->fetchAll(PDO::FETCH_OBJ);

$trafficMap = [];
foreach ($dailyTrafficRows as $row) {
  $trafficMap[$row->jour] = (int)$row->total;
}

$dailyTraffic = [];
for ($i = 6; $i >= 0; $i--) {
  $date = date('Y-m-d', strtotime("-$i day"));
  $dailyTraffic[] = [
    'label' => date('D', strtotime($date)),
    'total' => $trafficMap[$date] ?? 0,
  ];
}

$maxTraffic = max(array_map(fn($item) => $item['total'], $dailyTraffic)) ?: 1;

require __DIR__ . '/../../includes/head.php';
require __DIR__ . '/../../layouts/headerSupervisor.php';
?>

<main class="md:ml-72 pt-20 px-6 md:px-8 pb-12">
  <div class="mb-10 mt-4">
    <p class="text-secondary font-mono text-xs font-bold tracking-[0.3em] uppercase mb-2">Supervision</p>
    <h1 class="text-5xl md:text-6xl font-headline font-black tracking-tight text-primary">Poste superviseur</h1>
    <p class="text-on-surface-variant font-headline mt-3">
      Vue synthese de <?= htmlspecialchars($supervisor->zone_nominale ?? 'la zone') ?> sur <?= count($supervisedGuichets) ?> voie<?= count($supervisedGuichets) > 1 ? 's' : '' ?>.
    </p>
  </div>

  <section class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">
    <div class="bg-surface-container-lowest p-6 rounded-2xl border-l-4 border-secondary shadow-sm">
      <p class="text-xs font-bold uppercase tracking-widest text-on-surface-variant">Revenus supervises</p>
      <p class="mt-4 font-mono text-4xl font-bold text-primary"><?= number_format((float)$kpi->revenu_total, 0, ',', ' ') ?> FCFA</p>
    </div>
    <div class="bg-surface-container-lowest p-6 rounded-2xl border-l-4 border-primary shadow-sm">
      <p class="text-xs font-bold uppercase tracking-widest text-on-surface-variant">Passages</p>
      <p class="mt-4 font-mono text-4xl font-bold text-primary"><?= number_format((int)$kpi->total_passages, 0, ',', ' ') ?></p>
    </div>
    <div class="bg-surface-container-lowest p-6 rounded-2xl border-l-4 border-tertiary shadow-sm">
      <p class="text-xs font-bold uppercase tracking-widest text-on-surface-variant">Operateurs actifs</p>
      <p class="mt-4 font-mono text-4xl font-bold text-primary"><?= count($activeOperators) ?></p>
    </div>
    <div class="bg-surface-container-lowest p-6 rounded-2xl border-l-4 border-error shadow-sm">
      <p class="text-xs font-bold uppercase tracking-widest text-on-surface-variant">Incidents signales</p>
      <p class="mt-4 font-mono text-4xl font-bold text-error"><?= $incidentCount ?></p>
    </div>
  </section>

  <section class="grid grid-cols-1 xl:grid-cols-12 gap-8 mb-8">
    <div class="xl:col-span-8 flex flex-col gap-8">
      <div class="bg-surface-container-lowest p-8 rounded-2xl shadow-sm">
        <div class="flex items-center justify-between mb-8">
          <div>
            <h2 class="text-2xl font-headline font-bold text-primary">Trafic sur 7 jours</h2>
            <p class="text-sm text-on-surface-variant">Evolution des passages sur votre perimetre.</p>
          </div>
          <a href="/pages/supervisor/historiques.php" class="px-4 py-2 text-xs font-bold bg-primary text-white rounded-lg">Details</a>
        </div>
        <div class="grid h-72 grid-cols-7 items-end gap-3">
          <?php foreach ($dailyTraffic as $point) :
            $height = max(18, (int)round(($point['total'] / $maxTraffic) * 220));
          ?>
            <div class="flex h-full flex-col justify-end gap-3">
              <div class="relative flex-1 rounded-[1.5rem] bg-[linear-gradient(180deg,#f8f2e4_0%,#eef3f9_100%)] px-2 py-3">
                <div class="absolute inset-x-2 bottom-3 rounded-t-[1rem] bg-primary shadow-[0_14px_32px_rgba(0,7,25,0.16)]" style="height: <?= $height ?>px"></div>
              </div>
              <div class="text-center">
                <p class="font-mono text-xs font-bold text-primary"><?= $point['total'] ?></p>
                <p class="text-[10px] uppercase tracking-widest text-on-surface-variant"><?= $point['label'] ?></p>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>

      <div class="bg-primary p-8 rounded-2xl text-white shadow-xl relative overflow-hidden">
        <div class="relative z-10">
          <h2 class="text-2xl font-headline font-bold mb-2">Couverture de la zone</h2>
          <p class="text-slate-300 text-sm mb-6">Surveillance des voies affectees et suivi des derniers encaissements.</p>
          <div class="grid md:grid-cols-2 gap-4">
            <?php foreach ($laneStats as $lane) : ?>
              <div class="rounded-xl border border-white/15 bg-white/5 p-4">
                <div class="flex items-center justify-between mb-3">
                  <p class="text-sm font-bold uppercase tracking-wider">Voie <?= $lane->id ?> - <?= htmlspecialchars($lane->emplacement) ?></p>
                  <span class="text-[10px] px-2 py-1 rounded-full <?= $lane->is_active ? 'bg-emerald-500/20 text-emerald-200' : 'bg-red-500/20 text-red-200' ?>">
                    <?= $lane->is_active ? 'Active' : 'Inactive' ?>
                  </span>
                </div>
                <p class="text-2xl font-mono font-bold"><?= number_format((float)$lane->revenu_total, 0, ',', ' ') ?> <span class="text-sm">FCFA</span></p>
                <p class="mt-2 text-xs text-slate-300"><?= (int)$lane->nb_passages ?> passages<?= $lane->last_payment_at ? ' • dernier a ' . date('H:i', strtotime($lane->last_payment_at)) : '' ?></p>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
        <div class="absolute -right-8 -bottom-8 opacity-10">
          <span class="material-symbols-outlined text-[160px]">monitoring</span>
        </div>
      </div>
    </div>

    <aside class="xl:col-span-4 self-start bg-surface-container-low rounded-2xl p-8 border border-outline-variant/10">
      <div class="flex items-center justify-between mb-8">
        <h2 class="text-xl font-headline font-bold text-primary">Activite recente</h2>
        <span class="text-[10px] font-bold bg-primary text-white px-2 py-1 rounded">LIVE</span>
      </div>

      <div class="space-y-4">
        <?php foreach ($recentPayments as $payment) : ?>
          <div class="border-l-2 border-secondary pl-4 py-1">
            <div class="flex items-center justify-between gap-3">
              <p class="font-bold text-primary"><?= htmlspecialchars($payment->immatriculation) ?></p>
              <p class="font-mono text-[10px] text-on-surface-variant"><?= date('H:i', strtotime($payment->created_at)) ?></p>
            </div>
            <p class="text-xs text-on-surface-variant">Voie <?= $payment->guichet_id ?> - <?= htmlspecialchars($payment->emplacement) ?></p>
            <div class="mt-2 flex items-center justify-between">
              <span class="border px-2 py-1 rounded-full text-[10px] font-bold <?= supervisorBadgeClass($payment->mode_paiement) ?>">
                <?= htmlspecialchars($payment->mode_paiement) ?>
              </span>
              <span class="font-mono text-xs font-bold text-primary"><?= number_format((float)$payment->montant, 0, ',', ' ') ?> FCFA</span>
            </div>
          </div>
        <?php endforeach; ?>

        <?php if (empty($recentPayments)) : ?>
          <p class="text-sm text-on-surface-variant">Aucun encaissement sur le perimetre supervise.</p>
        <?php endif; ?>
      </div>
    </aside>
  </section>
</main>

<?php require __DIR__ . '/../../layouts/footerAdmin.php'; ?>
</html>
