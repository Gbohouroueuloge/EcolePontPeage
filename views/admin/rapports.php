<?php
$title = "Rapports";

use App\ConnectionBDD;

$pdo = ConnectionBDD::getPdo();

/* ═══════════════════════════════════════════════════
   PARAMÈTRES DE PÉRIODE
════════════════════════════════════════════════════ */
$preset  = $_GET['preset'] ?? '30j';
$dateMin = $_GET['date_min'] ?? null;
$dateMax = $_GET['date_max'] ?? null;

$today = new DateTimeImmutable();

switch ($preset) {
  case 'trimestre':
    $start = $today->modify('first day of -2 month')->setTime(0, 0);
    $end   = $today->setTime(23, 59, 59);
    break;
  case 'annee':
    $start = $today->modify('first day of January this year')->setTime(0, 0);
    $end   = $today->setTime(23, 59, 59);
    break;
  case 'custom':
    $start = $dateMin ? new DateTimeImmutable($dateMin . ' 00:00:00') : $today->modify('-30 days');
    $end   = $dateMax ? new DateTimeImmutable($dateMax . ' 23:59:59') : $today->setTime(23, 59, 59);
    break;
  default: // 30j
    $start = $today->modify('-29 days')->setTime(0, 0);
    $end   = $today->setTime(23, 59, 59);
    $preset = '30j';
}

$startStr = $start->format('Y-m-d H:i:s');
$endStr   = $end->format('Y-m-d H:i:s');

/* ── Filtres voies ── */
$voiesSelectionnees = $_GET['voies'] ?? [];  // tableau d'ids guichet

/* ═══════════════════════════════════════════════════
   EXPORT CSV
════════════════════════════════════════════════════ */
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
  $stmtCsv = $pdo->prepare("
    SELECT p.id, p.created_at, v.immatriculation, t.libelle AS type_vehicule,
           g.emplacement AS guichet, p.mode_paiement, p.montant
    FROM paiement p
    JOIN vehicule v     ON p.vehicule_id      = v.id
    JOIN typevehicule t ON v.type_vehicule_id  = t.id
    JOIN guichet g      ON p.guichet_id        = g.id
    WHERE p.created_at BETWEEN :s AND :e
    ORDER BY p.created_at DESC
  ");
  $stmtCsv->execute(['s' => $startStr, 'e' => $endStr]);
  $rows = $stmtCsv->fetchAll(PDO::FETCH_ASSOC);

  header('Content-Type: text/csv; charset=utf-8');
  header('Content-Disposition: attachment; filename="rapport_peage_' . $start->format('Ymd') . '_' . $end->format('Ymd') . '.csv"');
  $out = fopen('php://output', 'w');
  fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM UTF-8
  fputcsv($out, ['ID', 'Date', 'Immatriculation', 'Type Véhicule', 'Guichet', 'Mode Paiement', 'Montant (FCFA)'], ';');
  foreach ($rows as $r) {
    fputcsv($out, [
      $r['id'],
      date('d/m/Y H:i', strtotime($r['created_at'])),
      $r['immatriculation'],
      $r['type_vehicule'],
      $r['guichet'],
      $r['mode_paiement'],
      number_format($r['montant'], 2, ',', ' '),
    ], ';');
  }
  fclose($out);
  exit;
}

/* ═══════════════════════════════════════════════════
   REQUÊTES STATISTIQUES
════════════════════════════════════════════════════ */

/* Condition voies (optionnel) */
$voieClause = '';
$voieParams = [];
if (!empty($voiesSelectionnees)) {
  $placeholders = implode(',', array_fill(0, count($voiesSelectionnees), '?'));
  $voieClause   = " AND p.guichet_id IN ($placeholders)";
  $voieParams   = $voiesSelectionnees;
}

$baseParams = array_merge(['s' => $startStr, 'e' => $endStr], $voieParams);

/* KPI globaux */
$stmtKpi = $pdo->prepare("
  SELECT COUNT(*) AS total_passages,
         SUM(p.montant) AS revenu_total,
         AVG(p.montant) AS montant_moyen
  FROM paiement p
  WHERE p.created_at BETWEEN :s AND :e $voieClause
");
$stmtKpi->execute($baseParams);
$kpi = $stmtKpi->fetch(PDO::FETCH_OBJ);

/* KPI incidents */
$stmtInc = $pdo->prepare("
  SELECT COUNT(*) AS total_incidents
  FROM incident i
  WHERE i.created_at BETWEEN :s AND :e
");
$stmtInc->execute(['s' => $startStr, 'e' => $endStr]);
$kpiInc = $stmtInc->fetch(PDO::FETCH_OBJ);

/* Revenus par type de véhicule */
$stmtTypes = $pdo->prepare("
  SELECT t.libelle, COUNT(p.id) AS nb, SUM(p.montant) AS total, AVG(p.montant) AS avg_montant
  FROM paiement p
  JOIN vehicule v     ON p.vehicule_id     = v.id
  JOIN typevehicule t ON v.type_vehicule_id = t.id
  WHERE p.created_at BETWEEN :s AND :e $voieClause
  GROUP BY t.id, t.libelle
  ORDER BY total DESC
");
$stmtTypes->execute($baseParams);
$statTypes = $stmtTypes->fetchAll(PDO::FETCH_OBJ);

/* Revenus par mode de paiement */
$stmtModes = $pdo->prepare("
  SELECT mode_paiement, COUNT(*) AS nb, SUM(montant) AS total
  FROM paiement p
  WHERE p.created_at BETWEEN :s AND :e $voieClause
  GROUP BY mode_paiement
  ORDER BY total DESC
");
$stmtModes->execute($baseParams);
$statModes = $stmtModes->fetchAll(PDO::FETCH_OBJ);

/* Revenus par guichet */
$stmtGuichets = $pdo->prepare("
  SELECT g.emplacement, g.id, COUNT(p.id) AS nb, SUM(p.montant) AS total
  FROM paiement p
  JOIN guichet g ON p.guichet_id = g.id
  WHERE p.created_at BETWEEN :s AND :e $voieClause
  GROUP BY g.id, g.emplacement
  ORDER BY total DESC
");
$stmtGuichets->execute($baseParams);
$statGuichets = $stmtGuichets->fetchAll(PDO::FETCH_OBJ);

/* Revenus par semaine (7 dernières semaines pour le graphe) */
$stmtWeekly = $pdo->prepare("
  SELECT YEARWEEK(created_at, 1) AS semaine,
         MIN(DATE(created_at)) AS debut_semaine,
         SUM(montant) AS total
  FROM paiement
  WHERE created_at BETWEEN :s AND :e
  GROUP BY semaine
  ORDER BY semaine ASC
  LIMIT 8
");
$stmtWeekly->execute(['s' => $startStr, 'e' => $endStr]);
$statWeekly = $stmtWeekly->fetchAll(PDO::FETCH_OBJ);

/* Incidents par type */
$stmtIncTypes = $pdo->prepare("
  SELECT type, COUNT(*) AS nb
  FROM incident
  WHERE created_at BETWEEN :s AND :e
  GROUP BY type
  ORDER BY nb DESC
");
$stmtIncTypes->execute(['s' => $startStr, 'e' => $endStr]);
$statIncTypes = $stmtIncTypes->fetchAll(PDO::FETCH_OBJ);

/* Tous les guichets pour le filtre */
$allGuichets = $pdo->query("SELECT id, emplacement FROM guichet ORDER BY id")->fetchAll(PDO::FETCH_OBJ);

/* ── Helpers ── */
$fcfa     = fn($n) => number_format((float)$n, 0, ',', ' ') . ' FCFA';
$maxWeek  = !empty($statWeekly) ? max(array_map(fn($w) => (float)$w->total, $statWeekly)) : 1;
$maxGuich = !empty($statGuichets) ? max(array_map(fn($g) => (float)$g->total, $statGuichets)) : 1;

$reportId = 'RPT-' . strtoupper(substr(md5($startStr . $endStr), 0, 4)) . '-' . strtoupper(substr(md5($preset), 0, 2));

/* ── Label de période ── */
$labelPeriode = match ($preset) {
  '30j'       => 'Derniers 30 jours',
  'trimestre' => 'Trimestre en cours',
  'annee'     => 'Année ' . $today->format('Y'),
  'custom'    => $start->format('d/m/Y') . ' → ' . $end->format('d/m/Y'),
};

/* Mode paiement → icon */
$modeIcon = fn(string $m) => match (strtolower($m)) {
  'espèces', 'espece', 'especes' => 'payments',
  'carte'                        => 'credit_card',
  'abonnement'                   => 'badge',
  'mobile money'                 => 'smartphone',
  default                        => 'toll',
};
?>

<!-- Overlay -->
<div id="sidebar-overlay"
  class="fixed inset-0 bg-black/20 backdrop-blur-sm z-40 opacity-0 pointer-events-none transition-opacity duration-300"></div>

<!-- Sidebar offcanvas -->
<aside id="sidebar"
  class="fixed top-0 left-0 md:left-60 h-full w-80 bg-surface-container-low z-50 flex flex-col gap-6 p-6 overflow-y-auto shadow-2xl -translate-x-full transition-transform duration-300 ease-in-out">
  <div class="flex items-center justify-between pt-4">
    <h2 class="font-headline font-bold text-primary text-lg">Générateur de Rapport</h2>
    <button id="sidebar-close" class="p-1.5 rounded-md hover:bg-surface-container-high transition-all">
      <span class="material-symbols-outlined text-slate-500 text-xl">close</span>
    </button>
  </div>
  <p class="text-on-surface-variant text-xs leading-relaxed -mt-3">
    Configurez les paramètres pour générer votre analyse détaillée.
  </p>

  <form method="get" id="filter-form" class="flex flex-col gap-6">
    <!-- Type de rapport (statique/visuel pour l'instant) -->
    <section>
      <label class="block text-[10px] font-bold uppercase tracking-widest text-slate-500 mb-3">Type de Rapport</label>
      <div class="flex flex-col gap-2">
        <button type="button"
          class="flex items-center gap-3 p-3 rounded-lg bg-surface-container-lowest shadow-sm border-l-4 border-secondary">
          <span class="material-symbols-outlined text-secondary">analytics</span>
          <span class="text-sm font-semibold text-primary">Revenus &amp; Passages</span>
        </button>
        <button type="button"
          class="flex items-center gap-3 p-3 rounded-lg hover:bg-surface-container-high transition-all text-slate-500">
          <span class="material-symbols-outlined">warning</span>
          <span class="text-sm font-medium">Incidents &amp; Sécurité</span>
        </button>
      </div>
    </section>

    <!-- Période -->
    <section>
      <label class="block text-[10px] font-bold uppercase tracking-widest text-slate-500 mb-3">Période Temporelle</label>
      <div class="grid grid-cols-2 gap-2 mb-3">
        <?php foreach (['30j' => 'Derniers 30j', 'trimestre' => 'Trimestre', 'annee' => 'Année ' . $today->format('Y'), 'custom' => 'Personnalisé'] as $val => $lbl) : ?>
          <button type="submit" name="preset" value="<?= $val ?>"
            class="py-2 px-3 rounded text-xs font-bold transition-all
                   <?= $preset === $val ? 'bg-primary text-white' : 'bg-surface-container-lowest text-primary shadow-sm hover:bg-surface-container-high' ?>">
            <?= $lbl ?>
          </button>
        <?php endforeach; ?>
      </div>
      <div class="flex flex-col gap-2" id="custom-dates" style="<?= $preset !== 'custom' ? 'display:none' : '' ?>">
        <div class="relative">
          <input type="hidden" name="preset" value="custom" />
          <input type="date" name="date_min" value="<?= htmlspecialchars($start->format('Y-m-d')) ?>"
            class="w-full bg-white rounded-md text-xs py-2.5 px-4 shadow-sm text-primary font-mono focus:ring-2 focus:ring-secondary-container" />
        </div>
        <div class="relative">
          <input type="date" name="date_max" value="<?= htmlspecialchars($end->format('Y-m-d')) ?>"
            class="w-full bg-white rounded-md text-xs py-2.5 px-4 shadow-sm text-primary font-mono focus:ring-2 focus:ring-secondary-container" />
        </div>
        <button type="submit" class="bg-primary text-white text-xs font-bold py-2 rounded-lg">Appliquer</button>
      </div>
    </section>

    <!-- Filtrage des voies -->
    <section>
      <label class="block text-[10px] font-bold uppercase tracking-widest text-slate-500 mb-3">Filtrage des Voies</label>
      <div class="space-y-2">
        <label class="flex items-center justify-between p-2 rounded hover:bg-white/50 cursor-pointer">
          <span class="text-xs font-medium text-slate-700">Toutes les voies</span>
          <input type="checkbox" id="all-voies" class="rounded w-4 h-4" <?= empty($voiesSelectionnees) ? 'checked' : '' ?> />
        </label>
        <?php foreach ($allGuichets as $g) : ?>
          <label class="flex items-center justify-between p-2 rounded hover:bg-white/50 cursor-pointer">
            <span class="text-xs font-medium text-slate-700">Voie <?= htmlspecialchars($g->emplacement) ?></span>
            <input type="checkbox" name="voies[]" value="<?= $g->id ?>" class="voie-cb rounded w-4 h-4"
              <?= in_array($g->id, $voiesSelectionnees) ? 'checked' : '' ?> />
          </label>
        <?php endforeach; ?>
      </div>
      <?php if (!empty($voiesSelectionnees)) : ?>
        <button type="submit" class="mt-3 w-full bg-primary text-white text-xs font-bold py-2 rounded-lg">Filtrer</button>
      <?php endif; ?>
    </section>
  </form>

  <!-- Indicateur de couverture -->
  <div class="mt-auto pt-6 border-t border-slate-200">
    <div class="flex justify-between items-end mb-2">
      <span class="text-[10px] font-bold uppercase text-slate-400">Données chargées</span>
      <span class="text-xs font-mono font-bold text-primary"><?= number_format((int)$kpi->total_passages) ?> passages</span>
    </div>
    <div class="h-1 w-full bg-slate-200 rounded-full overflow-hidden">
      <div class="h-full bg-secondary-container rounded-full" style="width: 100%"></div>
    </div>
    <p class="text-[10px] text-slate-400 mt-2 mono-data"><?= $start->format('d/m/Y') ?> → <?= $end->format('d/m/Y') ?></p>
  </div>
</aside>

<main class="md:ml-60 pt-20 relative overflow-hidden">
  <section class="w-full bg-surface-container overflow-y-auto h-[calc(100vh-5rem)] p-8 md:p-12 flex flex-col items-center">

    <!-- Toolbar -->
    <div class="flex w-full max-w-4xl justify-between items-center mb-8 flex-wrap gap-3">
      <div class="flex gap-3 flex-wrap">
        <button id="sidebar-toggle"
          class="bg-surface-container-lowest border border-slate-200 px-4 py-2.5 rounded-lg flex items-center gap-2 text-primary font-bold shadow-sm hover:bg-surface-container-high transition-all">
          <span class="material-symbols-outlined text-lg">tune</span>
          Paramètres
        </button>
        <button onclick="window.print()"
          class="bg-primary text-white px-6 py-2.5 rounded-lg flex items-center gap-2 font-bold transition-transform active:scale-95 shadow-lg shadow-primary/20">
          <span class="material-symbols-outlined text-secondary-container" style="font-variation-settings:'FILL' 1;">picture_as_pdf</span>
          Imprimer / PDF
        </button>
        <a href="?preset=<?= $preset ?>&date_min=<?= $start->format('Y-m-d') ?>&date_max=<?= $end->format('Y-m-d') ?>&export=csv"
          class="bg-transparent border-2 border-primary text-primary px-6 py-2.5 rounded-lg flex items-center gap-2 font-bold hover:bg-primary hover:text-white transition-all">
          <span class="material-symbols-outlined">download</span>
          Exporter CSV
        </a>
      </div>
      <div class="flex items-center gap-2 bg-surface-container-lowest border border-outline-variant/20 px-3 py-2 rounded-lg">
        <span class="material-symbols-outlined text-secondary text-sm" style="font-variation-settings:'FILL' 1;">calendar_month</span>
        <span class="font-mono text-xs font-bold text-primary"><?= $labelPeriode ?></span>
      </div>
    </div>

    <!-- ═══ Document A4 ══════════════════════════════════════════ -->
    <div id="rapport-doc" class="w-full max-w-4xl bg-white shadow-2xl relative p-12 overflow-hidden">

      <!-- Filigrane -->
      <div class="absolute inset-0 flex items-center justify-center pointer-events-none select-none">
        <span class="text-[120px] font-black text-slate-100 rotate-[-30deg] tracking-tighter">APERÇU</span>
      </div>

      <!-- En-tête document -->
      <div class="flex justify-between items-start border-b-2 border-primary pb-8 mb-10">
        <div>
          <div class="flex items-center gap-2 mb-3">
            <div class="w-8 h-8 bg-primary flex items-center justify-center">
              <span class="material-symbols-outlined text-white text-lg">toll</span>
            </div>
            <span class="font-headline font-black text-primary text-xl tracking-tighter">PONT À PÉAGE</span>
          </div>
          <h3 class="text-3xl font-headline font-extrabold text-primary mb-1">Rapport de Performance</h3>
          <p class="text-slate-500 text-sm">Période : <?= $labelPeriode ?> &nbsp;|&nbsp; ID : <?= $reportId ?></p>
        </div>
        <div class="text-right">
          <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Généré le</p>
          <p class="font-mono text-sm text-primary font-bold"><?= $today->format('d/m/Y H:i') ?></p>
        </div>
      </div>

      <!-- KPI Grid -->
      <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-10">
        <div class="bg-surface p-4 border-l-4 border-primary">
          <p class="text-[10px] font-bold text-slate-500 uppercase mb-1">Revenu Total</p>
          <p class="font-mono text-lg font-bold text-primary leading-tight"><?= $fcfa($kpi->revenu_total ?? 0) ?></p>
        </div>
        <div class="bg-surface p-4 border-l-4 border-secondary">
          <p class="text-[10px] font-bold text-slate-500 uppercase mb-1">Passages</p>
          <p class="font-mono text-lg font-bold text-primary leading-tight"><?= number_format((int)$kpi->total_passages) ?></p>
        </div>
        <div class="bg-surface p-4 border-l-4 border-brand-indigo">
          <p class="text-[10px] font-bold text-slate-500 uppercase mb-1">Ticket Moyen</p>
          <p class="font-mono text-lg font-bold text-primary leading-tight"><?= $fcfa($kpi->montant_moyen ?? 0) ?></p>
        </div>
        <div class="bg-surface p-4 border-l-4 border-error">
          <p class="text-[10px] font-bold text-slate-500 uppercase mb-1">Incidents</p>
          <p class="font-mono text-lg font-bold text-primary leading-tight"><?= (int)$kpiInc->total_incidents ?></p>
        </div>
      </div>

      <!-- Graphe revenus hebdomadaires -->
      <?php if (!empty($statWeekly)) : ?>
        <div class="mb-10">
          <h4 class="font-headline font-bold text-sm uppercase tracking-widest text-slate-800 mb-5 flex items-center gap-2">
            <span class="w-2 h-2 bg-secondary rotate-45 inline-block"></span>
            Évolution des Revenus par Semaine
          </h4>
          <div class="h-52 bg-slate-50 rounded border border-dashed border-slate-200 flex items-end justify-around px-4 pb-4 pt-6 relative gap-2">
            <!-- Lignes horizontales -->
            <div class="absolute inset-0 flex flex-col justify-between px-4 py-4 pointer-events-none">
              <?php for ($i = 0; $i < 4; $i++) : ?>
                <div class="border-b border-slate-100 w-full"></div>
              <?php endfor; ?>
            </div>
            <?php foreach ($statWeekly as $w) :
              $pct = $maxWeek > 0 ? round(((float)$w->total / $maxWeek) * 100) : 0;
            ?>
              <div class="flex flex-col items-center gap-1 flex-1 z-10">
                <span class="font-mono text-[9px] text-slate-400 rotate-[-45deg] mb-1 whitespace-nowrap">
                  <?= date('d/m', strtotime($w->debut_semaine)) ?>
                </span>
                <div class="w-full rounded-t-sm bg-primary transition-all" style="height: <?= max($pct, 2) ?>%"></div>
                <span class="font-mono text-[9px] text-slate-500"><?= number_format((float)$w->total / 1000, 0) ?>K</span>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endif; ?>

      <!-- Tableau par type de véhicule -->
      <?php if (!empty($statTypes)) : ?>
        <div class="mb-10">
          <h4 class="font-headline font-bold text-sm uppercase tracking-widest text-slate-800 mb-5 flex items-center gap-2">
            <span class="w-2 h-2 bg-secondary rotate-45 inline-block"></span>
            Détails par Catégorie de Véhicule
          </h4>
          <table class="w-full text-sm">
            <thead class="bg-primary text-white">
              <tr>
                <th class="py-3 px-4 text-left text-[10px] font-bold uppercase tracking-widest">Catégorie</th>
                <th class="py-3 px-4 text-right text-[10px] font-bold uppercase tracking-widest">Volume</th>
                <th class="py-3 px-4 text-right text-[10px] font-bold uppercase tracking-widest">Moy. Ticket</th>
                <th class="py-3 px-4 text-right text-[10px] font-bold uppercase tracking-widest">Total Revenu</th>
              </tr>
            </thead>
            <tbody>
              <?php $totalRevenu = array_sum(array_map(fn($r) => (float)$r->total, $statTypes)); ?>
              <?php foreach ($statTypes as $i => $row) : ?>
                <tr class="border-b border-slate-100 <?= $i % 2 === 1 ? 'bg-slate-50/50' : '' ?>">
                  <td class="py-3 px-4 font-bold text-primary"><?= htmlspecialchars($row->libelle) ?></td>
                  <td class="py-3 px-4 text-right font-mono text-slate-600"><?= number_format((int)$row->nb) ?></td>
                  <td class="py-3 px-4 text-right font-mono text-slate-600"><?= $fcfa($row->avg_montant) ?></td>
                  <td class="py-3 px-4 text-right font-mono font-bold text-primary"><?= $fcfa($row->total) ?></td>
                </tr>
              <?php endforeach; ?>
              <tr class="bg-primary/5 border-t-2 border-primary">
                <td class="py-3 px-4 font-extrabold text-primary uppercase text-[11px] tracking-widest" colspan="3">Total général</td>
                <td class="py-3 px-4 text-right font-mono font-extrabold text-primary"><?= $fcfa($kpi->revenu_total ?? 0) ?></td>
              </tr>
            </tbody>
          </table>
        </div>
      <?php endif; ?>

      <!-- 2 colonnes : modes paiement + revenus par guichet -->
      <div class="grid md:grid-cols-2 gap-8 mb-10">

        <!-- Modes de paiement -->
        <?php if (!empty($statModes)) : ?>
          <div>
            <h4 class="font-headline font-bold text-sm uppercase tracking-widest text-slate-800 mb-4 flex items-center gap-2">
              <span class="w-2 h-2 bg-brand-indigo rotate-45 inline-block"></span>
              Modes de Paiement
            </h4>
            <div class="space-y-3">
              <?php $totalModes = array_sum(array_map(fn($m) => (float)$m->total, $statModes)); ?>
              <?php foreach ($statModes as $mode) :
                $pct = $totalModes > 0 ? round(((float)$mode->total / $totalModes) * 100) : 0;
              ?>
                <div>
                  <div class="flex justify-between items-center mb-1">
                    <div class="flex items-center gap-2">
                      <span class="material-symbols-outlined text-sm text-primary"><?= $modeIcon($mode->mode_paiement) ?></span>
                      <span class="text-xs font-semibold text-primary"><?= htmlspecialchars($mode->mode_paiement) ?></span>
                    </div>
                    <span class="font-mono text-xs text-slate-500"><?= $pct ?>% &nbsp; (<?= number_format((int)$mode->nb) ?>)</span>
                  </div>
                  <div class="h-1.5 bg-slate-100 rounded-full">
                    <div class="h-full bg-primary rounded-full" style="width:<?= $pct ?>%"></div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        <?php endif; ?>

        <!-- Par guichet -->
        <?php if (!empty($statGuichets)) : ?>
          <div>
            <h4 class="font-headline font-bold text-sm uppercase tracking-widest text-slate-800 mb-4 flex items-center gap-2">
              <span class="w-2 h-2 bg-secondary rotate-45 inline-block"></span>
              Revenus par Guichet
            </h4>
            <div class="space-y-3">
              <?php foreach ($statGuichets as $g) :
                $pct = $maxGuich > 0 ? round(((float)$g->total / $maxGuich) * 100) : 0;
              ?>
                <div>
                  <div class="flex justify-between items-center mb-1">
                    <span class="text-xs font-semibold text-primary">Voie <?= htmlspecialchars($g->emplacement) ?></span>
                    <span class="font-mono text-xs text-slate-500"><?= $fcfa($g->total) ?></span>
                  </div>
                  <div class="h-1.5 bg-slate-100 rounded-full">
                    <div class="h-full bg-secondary rounded-full" style="width:<?= $pct ?>%"></div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        <?php endif; ?>
      </div>

      <!-- Incidents par type -->
      <?php if (!empty($statIncTypes)) : ?>
        <div class="mb-10">
          <h4 class="font-headline font-bold text-sm uppercase tracking-widest text-slate-800 mb-4 flex items-center gap-2">
            <span class="w-2 h-2 bg-error rotate-45 inline-block"></span>
            Incidents par Type
          </h4>
          <div class="flex flex-wrap gap-3">
            <?php foreach ($statIncTypes as $inc) : ?>
              <div class="bg-error/5 border border-error/20 px-4 py-2 rounded-lg flex items-center gap-2">
                <span class="font-bold text-error font-mono text-sm"><?= (int)$inc->nb ?></span>
                <span class="text-xs text-on-surface-variant"><?= htmlspecialchars($inc->type) ?></span>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endif; ?>

      <?php if ($kpi->total_passages == 0) : ?>
        <div class="py-20 flex flex-col items-center text-center">
          <span class="material-symbols-outlined text-5xl text-slate-200 mb-4">bar_chart</span>
          <p class="font-headline font-bold text-slate-400">Aucune donnée sur cette période</p>
          <p class="text-xs text-slate-400 mt-1">Essayez d'élargir la plage de dates dans le panneau latéral.</p>
        </div>
      <?php endif; ?>

      <!-- Pied de page document -->
      <div class="flex justify-between items-center text-[10px] text-slate-400 font-bold uppercase tracking-widest border-t border-slate-100 pt-6 mt-4">
        <div>© <?= $today->format('Y') ?> Pont à Péage — TollOps Monolith</div>
        <div class="flex items-center gap-2">
          <span class="w-1 h-1 bg-secondary rounded-full"></span>
          Confidentiel
        </div>
      </div>
    </div>

    <div class="h-16"></div>
  </section>
</main>

<script>
  /* Sidebar */
  const toggle = document.getElementById('sidebar-toggle');
  const sidebar = document.getElementById('sidebar');
  const overlay = document.getElementById('sidebar-overlay');

  const openSidebar = () => {
    sidebar.classList.remove('-translate-x-full');
    overlay.classList.remove('opacity-0', 'pointer-events-none');
  };
  const closeSidebar = () => {
    sidebar.classList.add('-translate-x-full');
    overlay.classList.add('opacity-0', 'pointer-events-none');
  };

  toggle.addEventListener('click', openSidebar);
  overlay.addEventListener('click', closeSidebar);
  document.getElementById('sidebar-close').addEventListener('click', closeSidebar);
  document.addEventListener('keydown', e => e.key === 'Escape' && closeSidebar());

  /* Afficher les dates custom */
  document.querySelectorAll('[name="preset"]').forEach(btn => {
    btn.addEventListener('click', () => {
      document.getElementById('custom-dates').style.display =
        btn.value === 'custom' ? 'flex' : 'none';
      document.getElementById('custom-dates').style.flexDirection = 'column';
    });
  });

  /* Checkbox "Toutes les voies" */
  const allCb = document.getElementById('all-voies');
  const voieCbs = document.querySelectorAll('.voie-cb');
  allCb.addEventListener('change', () => voieCbs.forEach(cb => cb.checked = false));
  voieCbs.forEach(cb => cb.addEventListener('change', () => {
    if (cb.checked) allCb.checked = false;
  }));
</script>