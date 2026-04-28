<?php

use App\Models\Agent;
use App\Models\Guichet;

// Chargement des variables et de la connexion PDO
require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'admin/variables.php';

$title = "Operateurs";
$isOpen = false;

/** @var PDO */
$pdo = $pdo;

/* ═══════════════════════════════════════════════════
   TRAITEMENT DES ACTIONS (ASSIGNATION / REPOS)
════════════════════════════════════════════════════ */

// Assignation d'un agent à une voie (guichet)
if (isset($_GET['voie'])) {
  $agentId = $params['operateur_id'] ?? null;
  $voie    = (int) $_GET['voie'];

  // Mise à jour directe de la table 'agent' selon le schéma pont_peage2
  $stmt = $pdo->prepare("UPDATE agent SET guichet_id = ?, date_assignation = NOW(), debut = NOW() WHERE id = ?");
  $stmt->execute([$voie, $agentId]);

  header("Location: /admin/operateurs");
  exit;
}

// Mise au repos de l'agent (suppression de l'assignation)
if (isset($_GET['delete'])) {
  $agentId = $params['operateur_id'] ?? null;

  // On passe le guichet_id à NULL pour libérer l'agent
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

// Nombre total de passages (table paiement)
$query = $pdo->prepare("SELECT COUNT(id) FROM paiement");
$query->execute();
$Nbrpayment = $query->fetchColumn();

// Récupération de tous les agents pour les compteurs
$queryAll = $pdo->query("
    SELECT a.*, u.username, g.emplacement 
    FROM agent a 
    JOIN users u ON a.user_id = u.id 
    LEFT JOIN guichet g ON a.guichet_id = g.id
");
$agentsAll = $queryAll->fetchAll(PDO::FETCH_CLASS, Agent::class);
$agentsActive = array_filter($agentsAll, fn($a) => $a->guichet_id !== null);

// Liste des guichets pour le menu déroulant
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
    <!-- Header Section with Editorial Typography -->
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

    <!-- Compact Stats Row (Bento Style) -->
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

    <!-- Main Data Grid -->
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

            <!-- Header de la card -->
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

              <!-- Statut -->
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

            <!-- Infos -->
            <div class="px-4 py-3 space-y-2.5">

              <!-- Voie assignée -->
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

              <!-- Transactions -->
              <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Transactions</span>
                <span class="font-mono text-sm font-bold text-primary">0</span>
              </div>

              <!-- Début de service -->
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

            <!-- Actions -->
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

  <!-- Slide-in Side Panel (Agent Details) -->
  <div
    class="fixed <?= $isOpen ? 'flex' : 'hidden' ?> right-0 top-0 h-full w-110 bg-white z-60 shadow-2xl monolith-shadow flex-col translate-x-0 transition-transform duration-300 border-l border-surface-container">
    <!-- Panel Header -->
    <div class="px-8 py-10 border-b border-surface-container-low relative">
      <button class="absolute top-8 right-8 text-slate-400 hover:text-primary">
        <span class="material-symbols-outlined">close</span>
      </button>
      <div class="flex items-start gap-6">
        <div class="w-24 h-24 rounded-lg bg-surface-container border-2 border-secondary-container overflow-hidden p-1">
          <img alt="Detail Agent Jean-Pierre" class="w-full h-full object-cover rounded"
            data-alt="focused detailed portrait of a senior toll bridge operator"
            src="https://lh3.googleusercontent.com/aida-public/AB6AXuAqrlKH7p2trhedP0Fh2Hx59OkMv7ijI9MCS2tIX4kkIHrVlptZZyH1mgU7bwCt26o6s4peTTBde_TN6_q6OeXAqoP_KCia0OQFoJoWx8OhjpB97XRRn1jDZhwE1Yda6cDl8h5qFXdyxjsr3GNzvW4pt_MHmNxY-aM9ZbtW4bKRXRkrKO2vmeIaD_rUoKfQg0on8YnBF1nK3Z3vE8fTGPjZODRoVy7IHWerkdM8Y-tA2uTc2cblJf63W68IFu7bKVSlm16u00gaGNe9" />
        </div>
        <div class="mt-2">
          <span class="text-[10px] font-mono bg-primary text-white px-2 py-0.5 rounded mb-2 inline-block">SENIOR
            OFFICER</span>
          <h3 class="font-['Outfit'] text-2xl font-bold tracking-tight text-primary">Jean-Pierre Dubois</h3>
          <div class="flex items-center gap-2 mt-1">
            <div class="w-1.5 h-1.5 diamond-indicator bg-emerald-500"></div>
            <span class="text-xs font-bold text-emerald-700 uppercase tracking-tighter">Affectation Actuelle: Voie 4
              Nord</span>
          </div>
        </div>
      </div>
    </div>
    <!-- Panel Body -->
    <div class="flex-1 overflow-y-auto p-8 space-y-10">
      <!-- Key Metrics Grid -->
      <div>
        <h4 class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-4 flex items-center gap-2">
          <span class="w-4 h-px bg-slate-300"></span> Performances du mois
        </h4>
        <div class="grid grid-cols-2 gap-4">
          <div class="bg-surface-container-low p-4 rounded-lg border-l-2 border-primary">
            <div class="text-[10px] text-slate-500 font-bold uppercase mb-1">Passages Validés</div>
            <div class="font-mono text-xl font-bold text-primary">12,450</div>
          </div>
          <div class="bg-surface-container-low p-4 rounded-lg border-l-2 border-secondary">
            <div class="text-[10px] text-slate-500 font-bold uppercase mb-1">Anomalies Signalées</div>
            <div class="font-mono text-xl font-bold text-primary">02</div>
          </div>
        </div>
      </div>
      <!-- Recent Shifts List -->
      <div>
        <h4 class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-4 flex items-center gap-2">
          <span class="w-4 h-px bg-slate-300"></span> Historique des gardes
        </h4>
        <div class="space-y-4">
          <div class="flex items-center justify-between py-3 border-b border-surface-container-low">
            <div>
              <div class="text-sm font-bold text-primary">Matin (06:00 - 14:00)</div>
              <div class="text-[10px] text-slate-400 uppercase font-mono">15 Octobre 2023 • LANE_04</div>
            </div>
            <div class="text-right">
              <div class="font-mono text-sm font-bold text-primary">824 p.</div>
              <div class="text-[9px] text-emerald-600 font-bold uppercase">Optimal</div>
            </div>
          </div>
          <div class="flex items-center justify-between py-3 border-b border-surface-container-low">
            <div>
              <div class="text-sm font-bold text-primary">Nuit (22:00 - 06:00)</div>
              <div class="text-[10px] text-slate-400 uppercase font-mono">14 Octobre 2023 • LANE_01</div>
            </div>
            <div class="text-right">
              <div class="font-mono text-sm font-bold text-primary">410 p.</div>
              <div class="text-[9px] text-emerald-600 font-bold uppercase">Optimal</div>
            </div>
          </div>
          <div class="flex items-center justify-between py-3 border-b border-surface-container-low">
            <div>
              <div class="text-sm font-bold text-primary">Après-midi (14:00 - 22:00)</div>
              <div class="text-[10px] text-slate-400 uppercase font-mono">13 Octobre 2023 • LANE_04</div>
            </div>
            <div class="text-right">
              <div class="font-mono text-sm font-bold text-primary">912 p.</div>
              <div class="text-[9px] text-emerald-600 font-bold uppercase">Optimal</div>
            </div>
          </div>
        </div>
      </div>
      <!-- Additional Info -->
      <div class="bg-primary p-6 rounded-xl text-white">
        <div class="flex items-center gap-4 mb-4">
          <span class="material-symbols-outlined text-secondary-container"
            style="font-variation-settings: 'FILL' 1;">workspace_premium</span>
          <span class="text-xs font-bold uppercase tracking-widest">Note d'Assiduité</span>
        </div>
        <div class="font-['Outfit'] text-4xl font-bold mb-2">9.8<span class="text-lg opacity-50">/10</span></div>
        <p class="text-[11px] text-slate-400 font-light leading-relaxed">Jean-Pierre est un agent exemplaire avec un
          taux de présence de 100% sur les 90 derniers jours.</p>
      </div>
    </div>
    <!-- Panel Footer -->
    <div class="p-8 border-t border-surface-container-low bg-surface-container-lowest grid grid-cols-2 gap-4">
      <button
        class="px-6 py-3 border border-surface-variant rounded-lg text-xs font-bold uppercase tracking-widest hover:bg-slate-50 transition-colors">Modifier
        Profil</button>
      <button
        class="px-6 py-3 bg-secondary text-white rounded-lg text-xs font-bold uppercase tracking-widest hover:bg-primary transition-colors">Assigner
        Garde</button>
    </div>
  </div>
</main>
