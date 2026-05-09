<?php

require __DIR__ . '/variables.php';

$title = 'Dashboard';

$todayRevenueStmt = $pdo->query("
  SELECT COALESCE(SUM(montant), 0)
  FROM paiement
  WHERE DATE(created_at) = CURDATE()
");

$todayRevenue = (float)$todayRevenueStmt->fetchColumn();

$todayPassages = (int)$pdo->query("SELECT COUNT(*) FROM paiement WHERE DATE(created_at) = CURDATE()")->fetchColumn();
$activeOperators = (int)$pdo->query("SELECT COUNT(*) FROM agent WHERE guichet_id IS NOT NULL AND debut IS NOT NULL AND fin IS NULL")->fetchColumn();
$activeLanes = (int)$pdo->query("SELECT COUNT(*) FROM guichet WHERE is_active = 1")->fetchColumn();
$incidentCount = (int)$pdo->query("SELECT COUNT(*) FROM incident")->fetchColumn();
$unreadMessages = (int)$pdo->query("SELECT COUNT(*) FROM admin_notifications WHERE is_read = 0")->fetchColumn();
$supervisorCount = (int)$pdo->query("SELECT COUNT(*) FROM superviseur")->fetchColumn();

$waitingOperators = (int)$pdo->query("
  SELECT COUNT(*)
  FROM users u
  LEFT JOIN agent a ON a.user_id = u.id
  WHERE u.role = 'operateur' AND (a.id IS NULL OR a.guichet_id IS NULL)
")->fetchColumn();

$waitingSupervisorsRows = $pdo->query("
  SELECT u.id
  FROM users u
  LEFT JOIN superviseur s ON s.user_id = u.id
  LEFT JOIN superviseur_guichet sg ON sg.superviseur_id = s.id
  WHERE u.role = 'superviseur'
  GROUP BY u.id
  HAVING COUNT(sg.guichet_id) = 0
")->fetchAll(PDO::FETCH_OBJ);
$waitingSupervisors = count($waitingSupervisorsRows);

$recentPaymentsStmt = $pdo->query("
  SELECT p.created_at, p.mode_paiement, p.montant, g.id AS guichet_id, g.emplacement, v.immatriculation
  FROM paiement p
  JOIN guichet g ON g.id = p.guichet_id
  JOIN vehicule v ON v.id = p.vehicule_id
  ORDER BY p.created_at DESC
  LIMIT 8
");
$recentPayments = $recentPaymentsStmt->fetchAll(PDO::FETCH_OBJ);

$laneStatsStmt = $pdo->query("
  SELECT g.id, g.emplacement, g.is_active,
         COUNT(p.id) AS total_passages,
         COALESCE(SUM(p.montant), 0) AS revenu_total
  FROM guichet g
  LEFT JOIN paiement p ON p.guichet_id = g.id
  GROUP BY g.id, g.emplacement, g.is_active
  ORDER BY g.id ASC
");
$laneStats = $laneStatsStmt->fetchAll(PDO::FETCH_OBJ);

$trafficRows = $pdo->query("
  SELECT DATE(created_at) AS jour, COUNT(*) AS total
  FROM paiement
  WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
  GROUP BY DATE(created_at)
  ORDER BY jour ASC
")->fetchAll(PDO::FETCH_OBJ);

$trafficMap = [];
foreach ($trafficRows as $row) {
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

$recentNotifications = $pdo->query("
  SELECT *
  FROM admin_notifications
  ORDER BY created_at DESC
  LIMIT 5
")->fetchAll(PDO::FETCH_OBJ);
?>

<!DOCTYPE html>
<html lang="fr">
<?php require __DIR__ . '/../../includes/head.php'; ?>
<?php require __DIR__ . '/../../layouts/headerAdmin.php'; ?>

<main class="md:ml-72 pt-20 px-6 md:px-8 pb-12">
  <div class="mt-4 mb-10 flex flex-col xl:flex-row xl:items-end xl:justify-between gap-6">
    <div>
      <p class="text-secondary font-mono text-xs font-bold tracking-[0.3em] uppercase mb-2">Vue generale</p>
      <h1 class="text-5xl md:text-6xl font-headline font-black tracking-tight text-primary">Dashboard</h1>
      <p class="text-on-surface-variant font-headline mt-3">
        Tableau de bord d'exploitation du pont pour le <?= date('d/m/Y') ?>.
      </p>
    </div>
    <div class="flex flex-wrap gap-3">
      <a href="/pages/admin/messages.php" class="rounded-xl border border-outline-variant/20 bg-surface-container-lowest px-4 py-3 text-xs font-bold uppercase tracking-[0.22em] text-primary">
        <?= $unreadMessages ?> message<?= $unreadMessages > 1 ? 's' : '' ?> non lu<?= $unreadMessages > 1 ? 's' : '' ?>
      </a>
      <a href="/pages/admin/parametres.php" class="rounded-xl bg-primary px-4 py-3 text-xs font-bold uppercase tracking-[0.22em] text-white">
        Parametres
      </a>
    </div>
  </div>

  <section class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">
    <div class="bg-surface-container-lowest p-6 rounded-2xl border-l-4 border-secondary shadow-sm">
      <p class="text-xs font-bold uppercase tracking-widest text-on-surface-variant">Revenus du jour</p>
      <p class="mt-4 font-mono text-4xl font-bold text-primary"><?= number_format($todayRevenue, 0, ',', ' ') ?> FCFA</p>
      <p class="mt-2 text-xs text-on-surface-variant"><?= number_format($todayPassages) ?> passages aujourd'hui</p>
    </div>
    <div class="bg-surface-container-lowest p-6 rounded-2xl border-l-4 border-primary shadow-sm">
      <p class="text-xs font-bold uppercase tracking-widest text-on-surface-variant">Agents actifs</p>
      <p class="mt-4 font-mono text-4xl font-bold text-primary"><?= $activeOperators ?></p>
      <p class="mt-2 text-xs text-on-surface-variant"><?= $activeLanes ?> voie<?= $activeLanes > 1 ? 's' : '' ?> ouverte<?= $activeLanes > 1 ? 's' : '' ?></p>
    </div>
    <div class="bg-surface-container-lowest p-6 rounded-2xl border-l-4 border-tertiary shadow-sm">
      <p class="text-xs font-bold uppercase tracking-widest text-on-surface-variant">Superviseurs</p>
      <p class="mt-4 font-mono text-4xl font-bold text-primary"><?= $supervisorCount ?></p>
      <p class="mt-2 text-xs text-on-surface-variant"><?= $waitingSupervisors ?> en attente d'affectation</p>
    </div>
    <div class="bg-surface-container-lowest p-6 rounded-2xl border-l-4 border-error shadow-sm">
      <p class="text-xs font-bold uppercase tracking-widest text-on-surface-variant">Incidents et attentes</p>
      <p class="mt-4 font-mono text-4xl font-bold text-error"><?= $incidentCount ?></p>
      <p class="mt-2 text-xs text-on-surface-variant"><?= $waitingOperators ?> agent<?= $waitingOperators > 1 ? 's' : '' ?> en attente</p>
    </div>
  </section>

  <section class="grid grid-cols-1 xl:grid-cols-12 gap-8 mb-8">
    <div class="xl:col-span-8 flex flex-col gap-8">
      <div class="bg-surface-container-lowest rounded-2xl p-8 shadow-sm">
        <div class="flex items-center justify-between mb-8">
          <div>
            <h2 class="text-2xl font-headline font-bold text-primary">Trafic sur 7 jours</h2>
            <p class="text-sm text-on-surface-variant">Lecture rapide de la charge vehicule sur la semaine glissante.</p>
          </div>
          <a href="/pages/admin/historiques.php" class="rounded-lg bg-primary px-4 py-2 text-xs font-bold uppercase tracking-[0.22em] text-white">Voir tout</a>
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

      <div class="bg-primary rounded-2xl p-8 text-white shadow-xl relative overflow-hidden">
        <div class="relative z-10">
          <div class="flex items-center justify-between gap-4 mb-6">
            <div>
              <h2 class="text-2xl font-headline font-bold">Occupation des voies</h2>
              <p class="text-sm text-slate-300">Synthese de performance par poste de peage.</p>
            </div>
            <a href="/pages/admin/operateurs.php" class="rounded-lg border border-white/15 px-4 py-2 text-xs font-bold uppercase tracking-[0.22em]">
              Gerer les affectations
            </a>
          </div>
          <div class="grid md:grid-cols-2 gap-4">
            <?php foreach ($laneStats as $lane) : ?>
              <div class="rounded-xl border border-white/15 bg-white/5 p-4">
                <div class="flex items-center justify-between mb-3">
                  <p class="text-sm font-bold uppercase tracking-wider">Voie <?= $lane->id ?> - <?= htmlspecialchars($lane->emplacement) ?></p>
                  <span class="text-[10px] px-2 py-1 rounded-full <?= $lane->is_active ? 'bg-emerald-500/20 text-emerald-200' : 'bg-red-500/20 text-red-200' ?>">
                    <?= $lane->is_active ? 'Active' : 'Fermee' ?>
                  </span>
                </div>
                <p class="text-2xl font-mono font-bold"><?= number_format((float)$lane->revenu_total, 0, ',', ' ') ?> <span class="text-sm">FCFA</span></p>
                <p class="mt-2 text-xs text-slate-300"><?= (int)$lane->total_passages ?> passages cumules</p>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
        <div class="absolute -right-8 -bottom-8 opacity-10">
          <span class="material-symbols-outlined text-[160px]">toll</span>
        </div>
      </div>
    </div>

    <aside class="xl:col-span-4 flex flex-col gap-8">
      <div class="bg-surface-container-low rounded-2xl p-8 border border-outline-variant/10">
        <div class="flex items-center justify-between mb-6">
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
              <p class="mt-1 font-mono text-xs font-bold text-primary"><?= number_format((float)$payment->montant, 0, ',', ' ') ?> FCFA • <?= htmlspecialchars($payment->mode_paiement) ?></p>
            </div>
          <?php endforeach; ?>
        </div>
      </div>

      <div class="bg-surface-container-lowest rounded-2xl p-8 shadow-sm border border-outline-variant/10">
        <div class="flex items-center justify-between mb-6">
          <h2 class="text-xl font-headline font-bold text-primary">Messages admin</h2>
          <a href="/pages/admin/messages.php" class="text-xs font-bold uppercase tracking-[0.22em] text-secondary">Ouvrir</a>
        </div>
        <div class="space-y-4">
          <?php foreach ($recentNotifications as $notification) : ?>
            <div class="rounded-xl border px-4 py-3 <?= $notification->is_read ? 'border-outline-variant/15 bg-surface-container-low' : 'border-secondary/20 bg-secondary/5' ?>">
              <div class="flex items-center justify-between gap-3">
                <p class="text-sm font-bold text-primary"><?= htmlspecialchars($notification->title) ?></p>
                <?php if (!$notification->is_read) : ?>
                  <span class="h-2 w-2 rounded-full bg-secondary"></span>
                <?php endif; ?>
              </div>
              <p class="mt-2 text-xs leading-6 text-on-surface-variant"><?= htmlspecialchars($notification->message) ?></p>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </aside>
  </section>
</main>

<?php require __DIR__ . '/../../layouts/footerAdmin.php'; ?>
</html>
