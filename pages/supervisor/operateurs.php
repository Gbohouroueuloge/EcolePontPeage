<?php

$title = 'Operateurs';

require __DIR__ . '/variables.php';

$placeholders = supervisorPlaceholders($supervisedGuichetIds);

$stmt = $pdo->prepare("
  SELECT a.id AS agent_id, a.debut, a.fin, a.date_assignation, a.guichet_id,
         u.username, u.email,
         g.emplacement,
         COUNT(p.id) AS total_passages,
         COALESCE(SUM(p.montant), 0) AS revenu_total
  FROM agent a
  JOIN users u ON u.id = a.user_id
  JOIN guichet g ON g.id = a.guichet_id
  LEFT JOIN paiement p ON p.guichet_id = a.guichet_id
  WHERE a.guichet_id IN ($placeholders)
  GROUP BY a.id, a.debut, a.fin, a.date_assignation, a.guichet_id, u.username, u.email, g.emplacement
  ORDER BY a.debut IS NULL, a.debut DESC, u.username ASC
");
$stmt->execute($supervisedGuichetIds);
$operators = $stmt->fetchAll(PDO::FETCH_OBJ);

require __DIR__ . '/../../includes/head.php';
require __DIR__ . '/../../layouts/headerSupervisor.php';
?>

<main class="md:ml-72 pt-20 px-6 md:px-8 pb-12">
  <div class="mb-10 mt-4">
    <p class="text-secondary font-mono text-xs font-bold tracking-[0.3em] uppercase mb-2">Equipes</p>
    <h1 class="text-5xl font-headline font-black tracking-tight text-primary">Operateurs suivis</h1>
    <p class="text-on-surface-variant mt-3">Vue consolidee des agents actuellement rattaches a vos voies.</p>
  </div>

  <div class="bg-surface-container-lowest rounded-2xl shadow-sm overflow-hidden">
    <table class="w-full hidden xl:table text-left border-collapse">
      <thead>
        <tr class="bg-surface-container-low">
          <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-500">Agent</th>
          <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-500">Voie</th>
          <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-500">Statut</th>
          <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-500">Debut</th>
          <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-500 text-right">Passages</th>
          <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-500 text-right">Revenus</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-surface-container-low">
        <?php foreach ($operators as $operator) : ?>
          <tr class="hover:bg-surface-container-low/40 transition-colors">
            <td class="px-6 py-4">
              <div class="flex items-center gap-4">
                <div class="h-10 w-10 rounded-full bg-surface-container-high flex items-center justify-center font-bold text-primary">
                  <?= strtoupper(substr($operator->username, 0, 2)) ?>
                </div>
                <div>
                  <div class="font-bold text-primary text-sm uppercase"><?= htmlspecialchars($operator->username) ?></div>
                  <div class="font-mono text-[10px] text-slate-400"><?= htmlspecialchars($operator->email) ?></div>
                </div>
              </div>
            </td>
            <td class="px-6 py-4 text-sm font-semibold text-primary">Voie <?= $operator->guichet_id ?> - <?= htmlspecialchars($operator->emplacement) ?></td>
            <td class="px-6 py-4">
              <span class="px-2 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest <?= $operator->debut && $operator->fin === null ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500' ?>">
                <?= $operator->debut && $operator->fin === null ? 'En service' : 'Hors service' ?>
              </span>
            </td>
            <td class="px-6 py-4 font-mono text-xs text-primary"><?= $operator->debut ? date('d/m/Y H:i', strtotime($operator->debut)) : 'Non demarre' ?></td>
            <td class="px-6 py-4 text-right font-mono text-sm font-bold text-primary"><?= (int)$operator->total_passages ?></td>
            <td class="px-6 py-4 text-right font-mono text-sm font-bold text-primary"><?= number_format((float)$operator->revenu_total, 0, ',', ' ') ?> FCFA</td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <div class="xl:hidden p-4 space-y-4">
      <?php foreach ($operators as $operator) : ?>
        <div class="bg-white rounded-2xl border border-surface-variant p-4">
          <div class="flex items-center justify-between gap-4">
            <div>
              <p class="font-bold text-primary uppercase"><?= htmlspecialchars($operator->username) ?></p>
              <p class="font-mono text-[10px] text-slate-400"><?= htmlspecialchars($operator->email) ?></p>
            </div>
            <span class="px-2 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest <?= $operator->debut && $operator->fin === null ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500' ?>">
              <?= $operator->debut && $operator->fin === null ? 'En service' : 'Repos' ?>
            </span>
          </div>
          <div class="mt-4 space-y-2 text-sm">
            <div class="flex justify-between"><span class="text-on-surface-variant">Voie</span><span class="font-semibold text-primary">#<?= $operator->guichet_id ?> - <?= htmlspecialchars($operator->emplacement) ?></span></div>
            <div class="flex justify-between"><span class="text-on-surface-variant">Debut</span><span class="font-mono text-primary"><?= $operator->debut ? date('d/m H:i', strtotime($operator->debut)) : 'Non demarre' ?></span></div>
            <div class="flex justify-between"><span class="text-on-surface-variant">Passages</span><span class="font-mono text-primary"><?= (int)$operator->total_passages ?></span></div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</main>

<?php require __DIR__ . '/../../layouts/footerAdmin.php'; ?>
</html>
