<?php

use App\Models\Agent;
use App\Models\Guichet;
use App\Services\AdminService;

// Chargement des variables et de la connexion PDO
require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'admin/variables.php';

$title = "Operateurs";
$isOpen = false;

/** @var PDO */
$pdo = $pdo;

/* ═══════════════════════════════════════════════════
   TRAITEMENT DES ACTIONS (ASSIGNATION / REPOS)
════════════════════════════════════════════════════ */

if (isset($_GET['voie'])) {
  $agentId = $params['operateur_id'] ?? null;
  $voie    = (int) $_GET['voie'];

  // Mise à jour directe de la table 'agent' selon le schéma pont_peage2
  $stmt = $pdo->prepare("UPDATE agent SET guichet_id = ?, date_assignation = NOW(), debut = NOW() WHERE id = ?");
  $stmt->execute([$voie, $agentId]);

  header("Location: /admin/operateurs");
  exit;
}

if (isset($_GET['delete'])) {
  $agentId = $params['operateur_id'] ?? null;

  $stmt = $pdo->prepare("UPDATE agent SET guichet_id = NULL, date_assignation = NULL, debut = NULL WHERE id = ?");
  $stmt->execute([$agentId]);

  header("Location: /admin/operateurs");
  exit;
}

/* ═══════════════════════════════════════════════════
  FILTRES ET STATISTIQUES
════════════════════════════════════════════════════ */

$where = "";
$isActive = 'tous';
if (!empty($_GET['filtre'])) {
  $filtre = $_GET['filtre'];
  switch ($filtre) {
    case 'service':
      $where = "WHERE a.guichet_id IS NOT NULL";
      $isActive = 'service';
      break;
    case 'repos':
      $where = "WHERE a.guichet_id IS NULL";
      $isActive = 'repos';
      break;
  }
}

$query = $pdo->prepare("SELECT COUNT(id) FROM paiement");
$query->execute();
$Nbrpayment = $query->fetchColumn();

$queryAll = $pdo->query("
    SELECT a.*, u.username, g.emplacement 
    FROM agent a 
    JOIN users u ON a.user_id = u.id 
    LEFT JOIN guichet g ON a.guichet_id = g.id
");
$agentsAll = $queryAll->fetchAll(PDO::FETCH_CLASS, Agent::class);
$agentsActive = array_filter($agentsAll, fn($a) => $a->guichet_id !== null);

$queryG = $pdo->query("SELECT * FROM guichet ORDER BY id ASC");
$guichets = $queryG->fetchAll(PDO::FETCH_CLASS, Guichet::class);

/* ═══════════════════════════════════════════════════
  PAGINATION ET LISTE DES OPÉRATEURS
════════════════════════════════════════════════════ */

$perPage = 8;
$currentPage = max(1, (int)($_GET['page'] ?? 1));

$countQuery = $pdo->query("SELECT COUNT(id) FROM agent as a {$where}");
$total = (int)$countQuery->fetchColumn();
$pages = ceil($total / $perPage);
$offset = ($currentPage - 1) * $perPage;

$query = $pdo->prepare("
    SELECT 
        a.id AS agent_real_id, 
        a.*, 
        u.username, 
        g.id AS guichet_real_id, 
        g.slug,
        g.emplacement 
    FROM agent a 
    JOIN users u ON a.user_id = u.id 
    LEFT JOIN guichet g ON a.guichet_id = g.id
    {$where}
    ORDER BY a.created_at DESC 
    LIMIT :limit OFFSET :offset
");
$query->bindValue('limit', $perPage, PDO::PARAM_INT);
$query->bindValue('offset', $offset, PDO::PARAM_INT);
$query->execute();

$operateurs = $query->fetchAll(PDO::FETCH_CLASS, Agent::class);

$filtres_labels = ['tous' => 'Tous', 'service' => 'Service', 'repos' => 'Repos'];
?>

<main class="md:ml-72 pt-20 px-8 pb-12 relative">
  <div>
    <!-- Header -->
    <div class="flex flex-col lg:flex-row justify-between items-center lg:items-end gap-2 mb-10">
      <div>
        <p class="text-secondary font-mono text-xs font-bold tracking-[0.3em] uppercase mb-2">
          Système
        </p>
        <h2 class="text-6xl font-['Outfit'] font-black tracking-tight text-primary">
          Gestion des Opérateurs
        </h2>
      </div>

      <a href="/admin/utilisateurs" class="px-6 py-3 bg-primary text-white rounded-lg text-xs font-bold uppercase tracking-widest hover:bg-primary-container transition-colors">
        Ajouter un operateur
      </a>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mb-10">
      <div class="bg-surface-container-lowest p-6 rounded-xl monolith-shadow border-l-4 border-primary">
        <div class="text-slate-500 text-xs font-bold uppercase tracking-widest mb-1">Total Staff</div>
        <div class="font-mono text-3xl font-bold text-primary"><?= count($agentsAll) ?></div>
      </div>
      <div class="bg-surface-container-lowest p-6 rounded-xl monolith-shadow border-l-4 border-secondary-container">
        <div class="text-slate-500 text-xs font-bold uppercase tracking-widest mb-1">Postes Actifs</div>
        <div class="font-mono text-3xl font-bold text-primary"><?= count($agentsActive) ?></div>
      </div>
      <div class="bg-surface-container-lowest p-6 rounded-xl monolith-shadow border-l-4 border-emerald-500">
        <div class="text-slate-500 text-xs font-bold uppercase tracking-widest mb-1">Taux d'Efficacité</div>
        <div class="font-mono text-3xl font-bold text-primary">98.2<span class="text-sm font-body">%</span></div>
      </div>
      <div class="bg-surface-container-lowest p-6 rounded-xl monolith-shadow border-l-4 border-on-tertiary-container">
        <div class="text-slate-500 text-xs font-bold uppercase tracking-widest mb-1">Passages</div>
        <div class="font-mono text-3xl font-bold text-primary">
          <?= $Nbrpayment ?>
        </div>
      </div>
    </div>

    <!-- Main -->
    <div class="bg-surface-container-lowest rounded-xl shadow-sm overflow-hidden">
      <div class="flex bg-surface-container-low p-1 mb-4 rounded-lg w-fit">
        <?php foreach ($filtres_labels as $key => $value) : ?>
          <a href="?filtre=<?= $key ?>" class="px-4 py-2 text-xs font-bold rounded-md <?= $isActive == $key ? 'bg-primary text-white' : 'text-slate-500 hover:text-primary' ?>">
            <?= $value ?>
          </a>
        <?php endforeach ?>
      </div>

      <table class="w-full hidden xl:table text-left border-collapse">
        <thead>
          <tr class="bg-surface-container-low">
            <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-500">Agent Details</th>
            <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-500">Status</th>
            <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-500">Voie Assignée</th>
            <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-500">Début Service</th>
            <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-500">Actions</th>
          </tr>
        </thead>

        <tbody class="divide-y divide-surface-container-low">
          <?php foreach ($operateurs as $op) : ?>
            <tr class="hover:bg-surface-container-low/30 transition-colors">
              <td class="px-6 py-4">
                <div class="flex items-center gap-4">
                  <div class="h-10 w-10 rounded-full bg-surface-container-high flex items-center justify-center font-bold text-primary">
                    <?= strtoupper(substr($op->username, 0, 2)) ?>
                  </div>
                  <div>
                    <div class="font-bold text-primary text-sm uppercase"><?= htmlspecialchars($op->username) ?></div>
                    <div class="font-mono text-[10px] text-slate-400">ID-AGENT-<?= $op->agent_real_id ?></div>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4">
                <?php if ($op->guichet_id) : ?>
                  <span class="flex items-center gap-2 text-[10px] font-bold text-emerald-700 uppercase">
                    <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span> En Service
                  </span>
                <?php else : ?>
                  <span class="text-[10px] font-bold text-slate-500 uppercase">En Repos</span>
                <?php endif ?>
              </td>
              <td class="px-6 py-4">
                <span class="px-2 py-1 rounded text-[10px] font-mono <?= $op->guichet_id ? 'bg-primary text-white' : 'bg-slate-100 text-slate-400' ?>">
                  <?= $op->emplacement ?? '—' ?>
                </span>
              </td>
              <td class="px-6 py-4 text-xs text-slate-500 font-mono">
                <?= $op->debut ? date('d/m H:i', strtotime($op->debut)) : '—' ?>
              </td>
              <td class="px-6 py-4">
                <div class="flex items-center gap-2">
                  <a href="?view=<?= $op->user_id ?>" class="p-1 text-primary hover:bg-primary/10 rounded">
                    <span class="material-symbols-outlined text-sm">visibility</span>
                  </a>

                  <select onchange="window.location.href = 'operateurs/<?= $op->username ?>-<?= $op->agent_real_id ?>?voie=' + this.value"
                    class="text-xs border rounded px-2 py-1 focus:ring-2 focus:ring-primary">
                    <option value="" disabled <?= !$op->guichet_id ? 'selected' : '' ?>>Assigner...</option>
                    <?php foreach ($guichets as $g) : ?>
                      <option value="<?= $g->id ?>" <?= $op->guichet_id == $g->id ? 'selected' : '' ?>>
                        Voie <?= $g->id ?> - <?= $g->emplacement ?>
                      </option>
                    <?php endforeach ?>
                  </select>

                  <?php if ($op->guichet_id) : ?>
                    <a href="operateurs/<?= $op->username ?>-<?= $op->agent_real_id ?>?delete" class="p-1 text-error hover:bg-error/10 rounded">
                      <span class="material-symbols-outlined text-sm">delete</span>
                    </a>
                  <?php endif ?>
                </div>
              </td>
            </tr>
          <?php endforeach ?>
        </tbody>
      </table>

      <div class="w-full flex xl:hidden flex-col gap-4">
        <?php foreach ($operateurs as $agent) : ?>
          <div class="bg-white rounded-2xl border border-surface-variant shadow-sm overflow-hidden mb-3">
            <div class="flex items-center justify-between px-4 py-3 bg-surface-container-low/40 border-b border-surface-variant">
              <div class="flex items-center gap-3">
                <div class="inline-flex items-center justify-center border-2 border-surface-container-high overflow-hidden h-10 w-10 rounded-full bg-surface-container-high ring-2 ring-white">
                  <span class="text-primary uppercase text-lg font-black font-mono">
                    <?= substr($agent->username, 0, 2) ?>
                  </span>
                </div>
                <div>
                  <div class="font-bold text-primary text-sm uppercase"><?= $agent->username ?></div>
                  <div class="font-mono text-[10px] text-slate-400">ID-PRT-<?= $agent->id ?></div>
                </div>
              </div>

              <?php if ($agent->is_en_cours()) : ?>
                <div class="flex items-center gap-1.5 bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-200">
                  <div class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></div>
                  <span class="text-[10px] font-bold text-emerald-700 uppercase">En Service</span>
                </div>
              <?php else : ?>
                <div class="flex items-center gap-1.5 bg-slate-50 px-2.5 py-1 rounded-full border border-slate-200">
                  <div class="w-1.5 h-1.5 rounded-full bg-slate-300"></div>
                  <span class="text-[10px] font-bold text-slate-500 uppercase">Repos</span>
                </div>
              <?php endif ?>
            </div>

            <div class="px-4 py-3 space-y-2.5">
              <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Voie</span>
                <?php if ($agent->is_en_cours()) : ?>
                  <span class="bg-primary-container uppercase text-white px-2 py-1 rounded text-[10px] font-mono tracking-tighter">
                    VOIE_<?= $agent->guichet_real_id ?>_<?= $agent->emplacement ?>
                  </span>
                <?php else : ?>
                  <span class="bg-surface-container-high text-primary px-2 py-1 rounded text-[10px] font-mono tracking-tighter">—</span>
                <?php endif ?>
              </div>

              <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Transactions</span>
                <span class="font-mono text-sm font-bold text-primary">0</span>
              </div>

              <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Début</span>
                <?php if ($agent->getDateDebut()) : ?>
                  <span class="bg-primary-container uppercase text-white px-2 py-1 rounded text-[10px] font-mono tracking-tighter">
                    <?= $agent->getDateDebut()->format('d/m/Y H:i') ?>
                  </span>
                <?php else : ?>
                  <span class="bg-surface-container-high text-primary px-2 py-1 rounded text-[10px] font-mono tracking-tighter">—</span>
                <?php endif ?>
              </div>
            </div>

            <div class="px-4 py-3 border-t border-surface-variant bg-surface-container-low/20">
              <div class="flex items-center gap-2">
                <select
                  name="voie"
                  onchange="window.location.href = '/admin/operateurs/<?= $agent->username ?>-<?= $agent->agent_real_id ?>?voie=' + this.value"
                  class="flex-1 text-xs bg-white rounded-lg border border-surface-variant px-2 py-1.5 text-primary font-semibold shadow-sm hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all cursor-pointer">
                  <option value="" disabled <?= $agent->guichet_real_id === null ? 'selected' : '' ?>>Choisir une voie...</option>
                  <?php foreach ($guichets as $guichet) : ?>
                    <option value="<?= $guichet->id ?>" <?= $agent->guichet_real_id == $guichet->id ? 'selected' : '' ?>>
                      Voie <?= $guichet->id ?> - <?= $guichet->emplacement ?>
                    </option>
                  <?php endforeach ?>
                </select>

                <?php if ($agent->guichet_real_id !== null) : ?>
                  <a
                    href="operateurs/<?= $agent->username ?>-<?= $agent->agent_real_id ?>?delete"
                    class="p-1.5 bg-error-container text-error rounded-lg border border-surface-variant hover:bg-error/35 cursor-pointer transition-colors">
                    <span class="material-symbols-outlined text-sm">delete</span>
                  </a>
                <?php endif ?>
              </div>
            </div>
          </div>
        <?php endforeach ?>
      </div>

      <div class="flex items-center justify-between mt-6 px-4">
        <div class="text-xs font-mono text-on-surface-variant uppercase tracking-widest">
          Page <?= $currentPage ?> sur <?= $pages ?>
        </div>

        <div class="flex gap-2">
          <?php if ($currentPage > 1): ?>
            <a href="?page=<?= $currentPage - 1 ?>"
              class="flex items-center gap-1 px-4 py-2 bg-surface-container border border-outline-variant rounded-xl text-xs font-bold uppercase hover:bg-surface-container-high transition-colors">
              <span class="material-symbols-outlined text-sm">chevron_left</span> Précédent
            </a>
          <?php endif; ?>

          <?php if ($currentPage < $pages): ?>
            <a href="?page=<?= $currentPage + 1 ?>"
              class="flex items-center gap-1 px-4 py-2 bg-surface-container border border-outline-variant rounded-xl text-xs font-bold uppercase hover:bg-surface-container-high transition-colors">
              Suivant <span class="material-symbols-outlined text-sm">chevron_right</span>
            </a>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</main>