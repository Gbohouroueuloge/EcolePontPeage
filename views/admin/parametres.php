<?php
$title = "Paramètres";

use App\ConnectionBDD;

$pdo = ConnectionBDD::getPdo();

/* ═══════════════════════════════════════════════════
   ACTIONS POST
════════════════════════════════════════════════════ */
$toast = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'] ?? '';

  /* ── Toggle is_active d'un guichet ── */
  if ($action === 'toggle_guichet') {
    $id        = (int)($_POST['guichet_id'] ?? 0);
    $is_active = (int)($_POST['is_active'] ?? 0); // valeur COURANTE → on inverse
    if ($id) {
      $pdo->prepare("UPDATE guichet SET is_active = ? WHERE id = ?")
        ->execute([!$is_active, $id]);
      $toast = ['type' => 'success', 'msg' => 'Statut de la voie mis à jour.'];
    }
  }

  /* ── Sauvegarde des métadonnées (stockage en session — pas de table config) ── */
  if ($action === 'save_meta') {
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    $_SESSION['config'] = [
      'nom_pont'   => trim($_POST['nom_pont'] ?? 'Pont à Péage'),
      'devise'     => trim($_POST['devise']   ?? 'FCFA (XOF)'),
      'mode_op'    => trim($_POST['mode_op']  ?? 'Commercial Standard'),
    ];
    $toast = ['type' => 'success', 'msg' => 'Configuration enregistrée.'];
  }

  /* ── Réinitialiser tous les guichets (is_active = 1) ── */
  if ($action === 'reset_guichets') {
    $pdo->exec("UPDATE guichet SET is_active = 1");
    $toast = ['type' => 'success', 'msg' => 'Toutes les voies ont été réactivées.'];
  }

  /* Redirect PRG pour éviter le double POST au refresh */
  $qs = $toast ? '?saved=1&msg=' . urlencode($toast['msg']) : '?saved=1';
  header('Location: ' . '/admin/parametres' . $qs);
  exit;
}

/* ── Lire le toast depuis le redirect ── */
if (!empty($_GET['saved'])) {
  $toast = ['type' => 'success', 'msg' => urldecode($_GET['msg'] ?? 'Modifications enregistrées.')];
}

/* ── Config session ── */
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
$config = $_SESSION['config'] ?? [
  'nom_pont' => 'Pont à Péage Monolith Sud',
  'devise'   => 'FCFA (XOF)',
  'mode_op'  => 'Commercial Standard',
];

/* ═══════════════════════════════════════════════════
   DONNÉES
════════════════════════════════════════════════════ */
$guichets = $pdo->query("SELECT * FROM guichet ORDER BY id")->fetchAll(PDO::FETCH_OBJ);

/* Statistiques légères par guichet */
$stmtStats = $pdo->query("
  SELECT guichet_id,
         COUNT(*) AS nb_passages,
         ROUND(COUNT(*) * 100.0 / NULLIF((SELECT COUNT(*) FROM paiement), 0), 1) AS pct
  FROM paiement
  GROUP BY guichet_id
");
$statsGuichet = [];
foreach ($stmtStats->fetchAll(PDO::FETCH_OBJ) as $s) {
  $statsGuichet[$s->guichet_id] = $s;
}

/* Agent actuellement en service sur chaque guichet */
$stmtAgents = $pdo->query("
  SELECT a.guichet_id, u.username
  FROM agent a
  JOIN users u ON a.user_id = u.id
  WHERE a.fin IS NULL AND a.guichet_id IS NOT NULL
");
$agentsGuichet = [];
foreach ($stmtAgents->fetchAll(PDO::FETCH_OBJ) as $a) {
  $agentsGuichet[$a->guichet_id] = $a->username;
}

/* Total incidents ouverts */
$totalIncidents = (int)$pdo->query("SELECT COUNT(*) FROM incident")->fetchColumn();
?>

<?php if ($toast) : ?>
  <!-- Toast flottant -->
  <div id="toast"
    class="fixed bottom-10 right-10 bg-primary-container text-white py-4 px-6 rounded-xl shadow-2xl gold-glow flex items-center gap-4 z-50 anim-fade-in-up">
    <div class="bg-secondary p-1.5 rounded-full flex items-center justify-center">
      <span class="material-symbols-outlined text-white text-sm" style="font-variation-settings:'FILL' 1;">check</span>
    </div>
    <div>
      <p class="text-sm font-bold"><?= htmlspecialchars($toast['msg']) ?></p>
      <p class="text-[10px] text-slate-400 font-mono">REF : CFG-<?= strtoupper(substr(md5(time()), 0, 8)) ?></p>
    </div>
    <button onclick="document.getElementById('toast').remove()" class="ml-4 text-slate-400 hover:text-white transition-colors">
      <span class="material-symbols-outlined text-lg">close</span>
    </button>
  </div>
  <script>
    setTimeout(() => document.getElementById('toast')?.remove(), 4000);
  </script>
<?php endif; ?>

<main class="md:ml-60 pt-20 px-6 md:px-10 pb-12 relative">
  <!-- En-tête -->
  <div class="flex flex-col xl:flex-row justify-between items-start xl:items-end gap-2 mb-12">
    <div>
      <p class="text-secondary font-mono text-xs font-bold tracking-[0.3em] uppercase mb-2">Système</p>
      <h2 class="text-5xl font-headline font-black tracking-tight text-primary">Configuration Système</h2>
      <p class="text-on-surface-variant text-lg mt-3 font-body leading-relaxed">
        Ajustez le cœur numérique de l'infrastructure du pont.
      </p>
    </div>
    <div class="flex items-center gap-3 pb-2">
      <span class="material-symbols-outlined text-brand-success text-sm" style="font-variation-settings:'FILL' 1;">check_circle</span>
      <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">Base de données active</span>
    </div>
  </div>

  <div class="grid grid-cols-12 gap-8 items-start">

    <!-- ══ Section 1 : Guichets ════════════════════════════════════ -->
    <section class="col-span-12 xl:col-span-8 bg-surface-container-lowest shadow-sm rounded-xl p-8 border border-outline-variant/10 relative overflow-hidden">
      <div class="absolute top-0 left-0 w-2 h-full bg-primary-container"></div>

      <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-3">
        <div>
          <h3 class="text-xl font-bold text-primary">Guichets &amp; Voies</h3>
          <p class="text-sm text-on-surface-variant mt-0.5">Gérer la disponibilité des barrières physiques</p>
        </div>
        <form method="post">
          <input type="hidden" name="action" value="reset_guichets" />
          <button type="submit"
            class="text-primary font-bold text-xs flex items-center gap-2 hover:text-secondary transition-colors border border-primary/20 px-3 py-2 rounded-lg hover:border-secondary">
            <span class="material-symbols-outlined text-sm">refresh</span>
            TOUT RÉACTIVER
          </button>
        </form>
      </div>

      <div class="grid md:grid-cols-2 gap-4">
        <?php foreach ($guichets as $g) :
          $stat    = $statsGuichet[$g->id] ?? null;
          $operateur = $agentsGuichet[$g->id] ?? null;
          $label   = 'Voie ' . strtoupper(substr(str_replace('voie_0', 'V', $g->slug), 0, 3));
        ?>
          <div class="bg-surface-container-low p-5 rounded-lg border border-transparent hover:border-secondary-container transition-all group relative">
            <div class="flex justify-between items-start mb-4">
              <div class="bg-primary-container w-10 h-10 flex items-center justify-center rounded-md shrink-0">
                <span class="mono-data text-white font-bold text-sm">V<?= $g->id ?></span>
              </div>

              <!-- Toggle is_active -->
              <form method="post">
                <input type="hidden" name="action" value="toggle_guichet" />
                <input type="hidden" name="guichet_id" value="<?= $g->id ?>" />
                <input type="hidden" name="is_active" value="<?= (int)$g->is_active ?>" />
                <label class="relative inline-flex items-center cursor-pointer">
                  <input type="checkbox" class="sr-only peer" <?= $g->is_active ? 'checked' : '' ?>
                    onchange="this.closest('form').submit()" />
                  <div class="w-11 h-6 bg-slate-200 rounded-full peer
                  peer-checked:after:translate-x-full peer-checked:after:border-white
                  after:content-[''] after:absolute after:top-0.5 after:left-0.5
                  after:bg-white after:border-gray-300 after:border after:rounded-full
                  after:h-5 after:w-5 after:transition-all peer-checked:bg-secondary"></div>
                </label>
              </form>
            </div>

            <h4 class="font-bold text-primary mb-1"><?= htmlspecialchars($g->emplacement) ?></h4>

            <div class="flex items-center gap-2 mb-2">
              <?php if ($g->is_active) : ?>
                <div class="w-2 h-2 bg-secondary rotate-45"></div>
                <span class="mono-data text-xs text-secondary font-bold">
                  <?= $stat ? $stat->pct . '% trafic' : 'ACTIF' ?>
                </span>
              <?php else : ?>
                <div class="w-2 h-2 bg-slate-300 rotate-45"></div>
                <span class="mono-data text-xs text-slate-400 font-bold uppercase tracking-widest">Désactivé</span>
              <?php endif; ?>
            </div>

            <?php if ($operateur) : ?>
              <div class="flex items-center gap-1 mt-1">
                <span class="material-symbols-outlined text-xs text-brand-indigo">person</span>
                <span class="text-[11px] text-brand-indigo font-mono"><?= htmlspecialchars($operateur) ?></span>
              </div>
            <?php elseif ($g->is_active) : ?>
              <span class="text-[11px] text-slate-400 font-mono">Aucun opérateur en service</span>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      </div>
    </section>

    <!-- ══ Section 2 : Protocoles de paiement ═════════════════════ -->
    <section class="col-span-12 xl:col-span-4 bg-primary shadow-sm rounded-xl p-8 text-white relative overflow-hidden">
      <div class="absolute -right-12 -top-12 w-48 h-48 bg-secondary/10 rounded-full blur-3xl pointer-events-none"></div>

      <div class="mb-8">
        <h3 class="text-xl font-bold font-headline tracking-tight">Protocoles de Paiement</h3>
        <p class="text-xs text-slate-400 mt-1 uppercase tracking-widest">Modes acceptés</p>
      </div>

      <?php
      $modes = [
        ['icon' => 'credit_card',  'label' => 'Traitement par Carte',  'sub' => 'VISA / MASTERCARD',    'on' => true],
        ['icon' => 'payments',     'label' => 'Espèces',               'sub' => 'MANUEL OPÉRATEUR',     'on' => true],
        ['icon' => 'smartphone',   'label' => 'Mobile Money',          'sub' => 'ORANGE / MTN / MOOV',  'on' => true],
        ['icon' => 'badge',        'label' => 'Abonnement',            'sub' => 'CARTE FIDÉLITÉ',        'on' => true],
      ];
      ?>
      <div class="space-y-5">
        <?php foreach ($modes as $m) : ?>
          <div class="flex items-center justify-between group">
            <div class="flex items-center gap-4">
              <div class="w-10 h-10 bg-white/5 rounded-lg flex items-center justify-center">
                <span class="material-symbols-outlined text-secondary"><?= $m['icon'] ?></span>
              </div>
              <div>
                <p class="text-sm font-bold"><?= $m['label'] ?></p>
                <p class="text-[10px] text-slate-500 font-mono"><?= $m['sub'] ?></p>
              </div>
            </div>
            <label class="relative inline-flex items-center cursor-pointer">
              <input type="checkbox" class="sr-only peer" <?= $m['on'] ? 'checked' : '' ?> />
              <div class="w-9 h-5 bg-white/10 rounded-full peer
              peer-checked:after:translate-x-4 peer-checked:after:border-white
              after:content-[''] after:absolute after:top-0.5 after:left-0.5
              after:bg-white after:border after:rounded-full
              after:h-4 after:w-4 after:transition-all peer-checked:bg-secondary"></div>
            </label>
          </div>
        <?php endforeach; ?>
      </div>

      <!-- Alerte incidents -->
      <?php if ($totalIncidents > 0) : ?>
        <div class="mt-8 bg-white/5 border border-white/10 rounded-lg p-4 flex items-center gap-3">
          <span class="material-symbols-outlined text-secondary text-lg" style="font-variation-settings:'FILL' 1;">warning</span>
          <div>
            <p class="text-xs font-bold text-white"><?= $totalIncidents ?> incident<?= $totalIncidents > 1 ? 's' : '' ?> enregistré<?= $totalIncidents > 1 ? 's' : '' ?></p>
            <p class="text-[10px] text-slate-400 mt-0.5">À consulter dans la section rapports</p>
          </div>
        </div>
      <?php endif; ?>

      <div class="mt-8 pt-6 border-t border-white/10">
        <p class="text-[10px] text-slate-400 uppercase tracking-widest mb-3">Guichets actifs</p>
        <div class="bg-white/5 p-4 rounded-lg flex items-center justify-between border border-white/5">
          <span class="font-mono font-bold text-sm">
            <?= count(array_filter($guichets, fn($g) => $g->is_active)) ?> / <?= count($guichets) ?> VOIES
          </span>
          <span class="w-2 h-2 bg-secondary rounded-full animate-pulse"></span>
        </div>
      </div>
    </section>

    <!-- ══ Section 3 : Métadonnées ════════════════════════════════ -->
    <section class="col-span-12 xl:col-span-7 bg-surface-container-low shadow-sm rounded-xl p-10 border border-outline-variant/10">
      <h3 class="text-2xl font-bold text-primary font-headline mb-8">Métadonnées &amp; Configuration</h3>

      <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="action" value="save_meta" />

        <div class="grid grid-cols-2 gap-x-10 gap-y-8">
          <div class="col-span-2">
            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Nom de l'Identité du Pont</label>
            <input type="text" name="nom_pont" value="<?= htmlspecialchars($config['nom_pont']) ?>"
              class="w-full bg-white border border-outline-variant/30 rounded-md px-4 py-3
                     focus:border-secondary-container focus:ring-0 text-sm font-semibold transition-all" />
          </div>
          <div>
            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Devise Principale</label>
            <div class="relative">
              <select name="devise"
                class="w-full bg-white border border-outline-variant/30 rounded-md px-4 py-3
                       appearance-none focus:border-secondary-container focus:ring-0 text-sm font-semibold font-mono">
                <?php foreach (['FCFA (XOF)', 'EUR (€)', 'USD ($)', 'GBP (£)'] as $d) : ?>
                  <option <?= $config['devise'] === $d ? 'selected' : '' ?>><?= $d ?></option>
                <?php endforeach; ?>
              </select>
              <span class="material-symbols-outlined absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">expand_more</span>
            </div>
          </div>
          <div>
            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Mode de Fonctionnement</label>
            <div class="relative">
              <select name="mode_op"
                class="w-full bg-white border border-outline-variant/30 rounded-md px-4 py-3
                       appearance-none focus:border-secondary-container focus:ring-0 text-sm font-semibold">
                <?php foreach (['Commercial Standard', 'Équilibrage Charge de Pointe', 'VIP / Priorité Uniquement'] as $m) : ?>
                  <option <?= $config['mode_op'] === $m ? 'selected' : '' ?>><?= $m ?></option>
                <?php endforeach; ?>
              </select>
              <span class="material-symbols-outlined absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">expand_more</span>
            </div>
          </div>

          <!-- Logo upload (visuel, pas de table pour stocker) -->
          <div class="col-span-2">
            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Logo Global (SVG / PNG)</label>
            <label for="logo_upload"
              class="border-2 border-dashed border-outline-variant/40 rounded-lg p-8 flex flex-col
                     items-center justify-center bg-white/50 hover:bg-white hover:border-secondary
                     transition-all cursor-pointer group">
              <span class="material-symbols-outlined text-4xl text-slate-300 group-hover:text-secondary mb-3 transition-colors">cloud_upload</span>
              <p id="logo-label" class="text-xs font-bold text-slate-500 group-hover:text-primary transition-colors">GLISSER &amp; DÉPOSER LOGO VECTORIEL</p>
              <p class="text-[10px] text-slate-400 mt-1">SVG ou PNG haute résolution (min. 1024px)</p>
            </label>
            <input type="file" id="logo_upload" name="logo" accept="image/svg+xml,image/png" class="hidden"
              onchange="document.getElementById('logo-label').textContent = this.files[0]?.name ?? 'GLISSER & DÉPOSER LOGO VECTORIEL'" />
          </div>
        </div>

        <div class="mt-8 flex justify-end">
          <button type="submit"
            class="bg-primary text-on-primary font-label font-semibold py-3 px-8 rounded-lg
                   hover:bg-secondary-container hover:text-primary transition-all duration-300
                   active:scale-[0.98] gold-glow uppercase tracking-widest text-xs flex items-center gap-2">
            <span class="material-symbols-outlined text-sm" style="font-variation-settings:'FILL' 1;">save</span>
            Enregistrer
          </button>
        </div>
      </form>
    </section>

    <!-- ══ Section 4 : Intégrité système ═════════════════════════ -->
    <section class="col-span-12 xl:col-span-5 space-y-6">

      <!-- Backup card -->
      <div class="bg-surface-container-highest shadow-sm rounded-xl p-8 border border-outline-variant/20">
        <div class="flex items-center gap-4 mb-6">
          <div class="w-12 h-12 bg-white rounded-lg flex items-center justify-center shadow-sm">
            <span class="material-symbols-outlined text-primary">database</span>
          </div>
          <div>
            <h3 class="font-bold text-primary">Intégrité du Système</h3>
            <p class="text-xs text-on-surface-variant">Dernière sauvegarde : Aujourd'hui, 04:00</p>
          </div>
        </div>

        <div class="space-y-4 mb-8">
          <div class="flex justify-between items-center text-sm">
            <span class="text-slate-600 font-medium">Planification Auto.</span>
            <span class="mono-data font-bold text-primary">TOUTES LES 6H</span>
          </div>
          <div class="flex justify-between items-center text-sm">
            <span class="text-slate-600 font-medium">Sync. Hors Site</span>
            <div class="flex items-center gap-2">
              <div class="w-1.5 h-1.5 bg-brand-success rounded-full"></div>
              <span class="mono-data font-bold text-primary">ACTIF</span>
            </div>
          </div>
          <div class="flex justify-between items-center text-sm">
            <span class="text-slate-600 font-medium">Total passages (BDD)</span>
            <span class="mono-data font-bold text-primary">
              <?= number_format((int)$pdo->query("SELECT COUNT(*) FROM paiement")->fetchColumn()) ?>
            </span>
          </div>
          <div class="flex justify-between items-center text-sm">
            <span class="text-slate-600 font-medium">Total véhicules</span>
            <span class="mono-data font-bold text-primary">
              <?= number_format((int)$pdo->query("SELECT COUNT(*) FROM vehicule")->fetchColumn()) ?>
            </span>
          </div>
        </div>

        <div class="grid grid-cols-2 gap-3">
          <button onclick="alert('Synchronisation déclenchée.')"
            class="bg-primary text-white py-3 rounded-md font-bold text-xs hover:opacity-90 transition-all flex items-center justify-center gap-2">
            <span class="material-symbols-outlined text-sm">backup</span>
            SYNC. MANUELLE
          </button>
          <button onclick="alert('Archivage des logs.')"
            class="bg-transparent border border-primary text-primary py-3 rounded-md font-bold text-xs hover:bg-slate-50 transition-all">
            ARCHIVE LOGS
          </button>
        </div>
      </div>

      <!-- Alerte sécurité -->
      <div class="bg-error-container/20 border-l-4 border-error p-6 rounded-lg flex items-start gap-4">
        <span class="material-symbols-outlined text-error" style="font-variation-settings:'FILL' 1;">warning</span>
        <div>
          <p class="text-xs font-bold text-error uppercase tracking-widest mb-1">Application de la Sécurité</p>
          <p class="text-sm text-on-error-container">
            Vérifiez régulièrement les mises à jour du contrôleur de voies.
            <?php if ($totalIncidents > 0) echo $totalIncidents . ' incident(s) non résolu(s) à traiter.'; ?>
          </p>
        </div>
      </div>
    </section>

  </div>
</main>