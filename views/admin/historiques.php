<?php

use App\Models\Guichet;
use App\Models\Paiement;

$title = "Historiques";

require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'admin/variables.php';

$where = "WHERE 1=1";
$par = [];

if (!empty($_GET['q'])) {
  $where .= " AND v.immatriculation LIKE :immatriculation";
  $par['immatriculation'] = '%' . $_GET['q'] . '%'; // recherche partielle
}
if (!empty($_GET['voie'])) {
  $where .= " AND p.guichet_id = :guichet_id";
  $par['guichet_id'] = $_GET['voie'];
}
if (!empty($_GET['type'])) {
  $where .= " AND v.type_vehicule_id = :type_vehicule_id";
  $par['type_vehicule_id'] = $_GET['type'];
}
if (!empty($_GET['paiement'])) {
  $where .= " AND p.mode_paiement = :mode_paiement";
  $par['mode_paiement'] = $_GET['paiement'];
}

/** @var PDO */
$pdo = $pdo;

$query = $pdo->prepare("SELECT * FROM guichet ORDER BY created_at DESC");
$query->execute();
$guichets = $query->fetchAll(PDO::FETCH_CLASS, Guichet::class);

$query = $pdo->prepare("SELECT * FROM typevehicule ORDER BY created_at DESC");
$query->execute();
$typevehicules = $query->fetchAll(PDO::FETCH_OBJ);

$page    = max(1, (int) ($_GET['page'] ?? 1));
$perPage = 8;
$offset  = ($page - 1) * $perPage;

// Compter le total avec les mêmes filtres
$countQuery = $pdo->prepare("
    SELECT COUNT(*) 
    FROM paiement p 
    JOIN guichet g ON p.guichet_id = g.id 
    JOIN vehicule v ON p.vehicule_id = v.id 
    JOIN typevehicule t ON v.type_vehicule_id = t.id 
    {$where}
");
$countQuery->execute($par);
$total      = $countQuery->fetchColumn();
$totalPages = (int) ceil($total / $perPage);

// Requête principale avec LIMIT + OFFSET
$query = $pdo->prepare("
    SELECT v.id AS vehicule_id, v.*, p.*, g.id AS guichet_id, g.emplacement, t.id AS type_vehicule_id, t.libelle 
    FROM paiement p 
    JOIN guichet g ON p.guichet_id = g.id 
    JOIN vehicule v ON p.vehicule_id = v.id 
    JOIN typevehicule t ON v.type_vehicule_id = t.id 
    {$where}
    ORDER BY p.created_at DESC 
    LIMIT {$perPage} OFFSET {$offset}
");
$query->execute($par);
/** @var Paiement[] */
$passages = $query->fetchAll(PDO::FETCH_CLASS, Paiement::class);

// Construire l'URL de base en conservant les filtres actifs
$queryParams = $_GET;
unset($queryParams['page']); // on gère page séparément
$baseUrl = '?' . http_build_query($queryParams);


// dd($passages);
?>


<main class="md:ml-72 pt-20 px-8 pb-12 relative">
  <div class="flex flex-col">
    <!-- Header -->
    <div class="flex flex-col xl:flex-row justify-between items-center xl:items-end mb-8">
      <div>
        <p class="text-secondary font-mono text-xs font-bold tracking-[0.3em] uppercase mb-2">
          Vue générale
        </p>
        <h2 class="text-6xl font-['Outfit'] font-black tracking-tight text-primary">
          Historique des Passages
        </h2>
        <p class="text-on-surface-variant text-lg mt-4 font-body leading-relaxed">
          Surveillance des flux de transit architectural à
          travers les travées Monolith.
        </p>
      </div>

      <div class="flex gap-4">
        <div class="text-right">
          <span class="block text-[10px] uppercase font-bold text-slate-400 tracking-widest mb-1">Capteurs
            Live</span>
          <div class="flex items-center gap-2 text-primary font-mono font-bold text-lg">
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            4 281 <span class="text-slate-400 text-xs">ACTUEL</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Filters Sticky -->
    <div class="bg-surface/95 backdrop-blur-md py-8">
      <!-- Filter Bar -->
      <form action="" method="get" class="bg-surface-container-low p-4 rounded-xl flex flex-wrap items-center gap-6">

        <!-- Recherche plaque -->
        <div class="flex-1 min-w-50">
          <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1.5 tracking-wider">
            Recherche Plaque
          </label>
          <div class="relative">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm">search</span>
            <input
              class="w-full bg-white border-none rounded-md py-2 pl-10 text-sm font-mono focus:ring-2 focus:ring-secondary-container"
              placeholder="ABC-1234..."
              type="text"
              name="q"
              value="<?= htmlspecialchars($_GET['q'] ?? '') ?>" />
          </div>
        </div>

        <!-- Voie -->
        <div>
          <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1.5 tracking-wider">Voie</label>
          <select class="bg-white border-none rounded-md py-2 px-4 text-sm focus:ring-2 focus:ring-secondary-container" name="voie">
            <option value="">Toutes les voies</option>
            <?php foreach ($guichets as $guichet) : ?>
              <option value="<?= $guichet->id ?>" <?= ($_GET['voie'] ?? '') == $guichet->id ? 'selected' : '' ?>>
                Voie <?= $guichet->id ?> - <?= $guichet->emplacement ?>
              </option>
            <?php endforeach ?>
          </select>
        </div>

        <!-- Catégorie véhicule -->
        <div>
          <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1.5 tracking-wider">Catégorie</label>
          <select class="bg-white border-none rounded-md py-2 px-4 text-sm focus:ring-2 focus:ring-secondary-container" name="type">
            <option value="">Toutes les classes</option>
            <?php foreach ($typevehicules as $type) : ?>
              <option value="<?= $type->id ?>" <?= ($_GET['type'] ?? '') == $type->id ? 'selected' : '' ?>>
                Cat <?= $type->id ?> - <?= $type->libelle ?>
              </option>
            <?php endforeach ?>
          </select>
        </div>

        <!-- Mode de paiement — valeurs alignées sur la DB -->
        <div>
          <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1.5 tracking-wider">Mode de Paiement</label>
          <select class="bg-white border-none rounded-md py-2 px-4 text-sm focus:ring-2 focus:ring-secondary-container" name="paiement">
            <option value="">Tous les paiements</option>
            <option value="Espece" <?= ($_GET['paiement'] ?? '') === 'Espece'      ? 'selected' : '' ?>>Espèces</option>
            <option value="Abonnement" <?= ($_GET['paiement'] ?? '') === 'Abonnement'  ? 'selected' : '' ?>>Abonnement</option>
            <option value="Carte" <?= ($_GET['paiement'] ?? '') === 'Carte'       ? 'selected' : '' ?>>Carte Bancaire</option>
          </select>
        </div>

        <div class="self-end flex gap-2">
          <button type="submit" class="p-2 flex items-center gap-2 bg-primary text-white rounded-md hover:bg-secondary transition-colors">
            <span class="material-symbols-outlined">filter_list</span>
            Filtrer
          </button>
          <!-- Reset -->
          <?php if (!empty(array_filter($_GET ?? []))) : ?>
            <a href="?" class="p-2 flex items-center gap-2 bg-surface-container-high text-slate-600 rounded-md hover:bg-slate-200 transition-colors text-sm font-medium">
              <span class="material-symbols-outlined">close</span>
              Réinitialiser
            </a>
          <?php endif ?>
        </div>

      </form>
    </div>

    <!-- Data Table Section -->
    <div class="flex-1 pb-12">
      <div class="bg-surface-container-lowest rounded-xl overflow-hidden shadow-sm">
        <table class="w-full hidden xl:table text-left border-collapse">
          <thead>
            <tr
              class="bg-surface-container-low text-[10px] font-black uppercase tracking-[0.15em] text-slate-500">
              <th class="px-6 py-5">Details</th>
              <th class="px-6 py-5">Classe Véhicule</th>
              <th class="px-6 py-5">Voie</th>
              <th class="px-6 py-5 text-right">Frais (FCFA)</th>
              <th class="px-6 py-5">Paiement</th>
              <th class="px-6 py-5">Statut</th>
              <th class="px-6 py-5 text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-surface-container">
            <?php foreach ($passages as $passage) : ?>
              <?php require 'cardRowPassage.php' ?>
            <?php endforeach; ?>
          </tbody>
        </table>

        <div class="w-full flex xl:hidden flex-col gap-4">
          <?php foreach ($passages as $passage) : ?>
            <?php require 'cardRowPassageMobile.php' ?>
          <?php endforeach; ?>
        </div>

        <div class="bg-surface-container-low px-8 py-4 flex justify-between items-center">

          <!-- Résumé -->
          <div class="text-[10px] font-black uppercase tracking-widest text-slate-400">
            Affichage de <?= min($offset + $perPage, $total) ?> sur <?= number_format($total, 0, ',', ' ') ?> résultats
          </div>

          <!-- Pagination -->
          <?php if ($totalPages > 1) : ?>
            <div class="flex gap-1 items-center">

              <!-- Première page -->
              <a href="<?= $baseUrl ?>&page=1"
                class="w-8 h-8 flex items-center justify-center rounded <?= $page === 1 ? 'opacity-30 pointer-events-none' : 'hover:bg-surface-container' ?> text-slate-500 border border-transparent transition-colors">
                <span class="material-symbols-outlined text-sm">first_page</span>
              </a>

              <!-- Précédent -->
              <a href="<?= $baseUrl ?>&page=<?= $page - 1 ?>"
                class="w-8 h-8 flex items-center justify-center rounded <?= $page === 1 ? 'opacity-30 pointer-events-none' : 'hover:bg-surface-container' ?> text-slate-500 border border-transparent transition-colors">
                <span class="material-symbols-outlined text-sm">chevron_left</span>
              </a>

              <!-- Page courante -->
              <span class="px-3 h-8 flex items-center justify-center rounded bg-white text-primary border border-surface-container shadow-sm text-xs font-black">
                <?= $page ?> / <?= $totalPages ?>
              </span>

              <!-- Suivant -->
              <a href="<?= $baseUrl ?>&page=<?= $page + 1 ?>"
                class="w-8 h-8 flex items-center justify-center rounded <?= $page === $totalPages ? 'opacity-30 pointer-events-none' : 'hover:bg-surface-container' ?> text-slate-500 border border-transparent transition-colors">
                <span class="material-symbols-outlined text-sm">chevron_right</span>
              </a>

              <!-- Dernière page -->
              <a href="<?= $baseUrl ?>&page=<?= $totalPages ?>"
                class="w-8 h-8 flex items-center justify-center rounded <?= $page === $totalPages ? 'opacity-30 pointer-events-none' : 'hover:bg-surface-container' ?> text-slate-500 border border-transparent transition-colors">
                <span class="material-symbols-outlined text-sm">last_page</span>
              </a>

            </div>
          <?php endif ?>

        </div>
      </div>
    </div>
  </div>

  <!-- Detail Drawer (Slide-in) -->
  <div
    class="fixed hidden right-0 top-0 h-full w-110 bg-white z-60 shadow-2xl monolith-shadow flex flex-col translate-x-0 transition-transform duration-300 border-l border-surface-container">
    <div
      class="flex justify-between items-center px-8 pt-6 border-b border-surface-container-low relative">
      <span class="text-[10px] font-black uppercase tracking-[0.2em] text-secondary">
        Audit de Transaction
      </span>
      <button class="p-2 hover:bg-surface-container rounded-full transition-colors">
        <span class="material-symbols-outlined text-primary">close</span>
      </button>
    </div>

    <div class="flex-1 overflow-y-auto p-8 space-y-10">
      <div class="mb-8">
        <div class="inline-block bg-primary px-4 py-2 rounded-lg mb-4">
          <span class="font-mono text-white font-bold text-2xl tracking-tighter">GX-902-LK</span>
        </div>
        <div class="flex items-baseline gap-2">
          <h3 class="text-2xl font-black font-headline text-primary italic">3 000 FCFA</h3>
          <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Paiement
            Confirmé</span>
        </div>
      </div>

      <!-- Vehicle Detection Image -->
      <div class="relative rounded-xl overflow-hidden aspect-video bg-surface-container mb-8">
        <img alt="Capture de Passage" class="w-full h-full object-cover grayscale contrast-125"
          data-alt="capture de surveillance haute résolution d'une berline grise passant dans une voie de péage en béton de jour avec une mise au point nette sur la plaque d'immatriculation"
          src="https://lh3.googleusercontent.com/aida-public/AB6AXuBg3dMc_YWpOU-NSjXAdPzvabBv5F2r3_KYSedfAc3tGk2XY4Zzqu5B4Zlor8PR6D_awmcM3wWgvvX66CFS-9hme4_MbrzGkDsr-aXRKS54FR9FpXnPwH_7o2TZoRsB4JdrSt6jsMhLhMgAPFfN63aMBzBi1ICPSTCcO8yW8WUma1-qPcoogPxd4Ec0WracDSqcZxEvFcZltPdLNLMZedQsOH-E-Yfp2y0GYDi4NdfAVVvZR31rUja_80gT3q6jBnTQMlFhxuSX5Qv3" />
        <div
          class="absolute top-2 left-2 px-2 py-1 bg-secondary text-white text-[8px] font-black uppercase tracking-widest rounded">
          Confiance OCR : 99,2%</div>
      </div>

      <!-- Timeline -->
      <div class="space-y-8 relative">
        <div class="absolute left-2.75 top-2 bottom-2 w-0.5 bg-surface-container-high"></div>
        <!-- Step 1 -->
        <div class="flex gap-6 relative">
          <div
            class="w-6 h-6 rounded-full bg-emerald-500 flex items-center justify-center z-10 border-4 border-white shadow-sm">
            <span class="material-symbols-outlined text-[10px] text-white font-bold">check</span>
          </div>
          <div>
            <h4 class="text-xs font-black uppercase tracking-wider text-primary">Détection</h4>
            <p class="text-[11px] text-slate-500 font-medium">Capteur #L03-A déclenché</p>
            <span class="text-[9px] font-mono text-slate-400">14:22:05.112</span>
          </div>
        </div>
        <!-- Step 2 -->
        <div class="flex gap-6 relative">
          <div
            class="w-6 h-6 rounded-full bg-emerald-500 flex items-center justify-center z-10 border-4 border-white shadow-sm">
            <span class="material-symbols-outlined text-[10px] text-white font-bold">check</span>
          </div>
          <div>
            <h4 class="text-xs font-black uppercase tracking-wider text-primary">Validation</h4>
            <p class="text-[11px] text-slate-500 font-medium">Tag authentifié (DSRC)</p>
            <span class="text-[9px] font-mono text-slate-400">14:22:05.450</span>
          </div>
        </div>
        <!-- Step 3 -->
        <div class="flex gap-6 relative">
          <div
            class="w-6 h-6 rounded-full bg-emerald-500 flex items-center justify-center z-10 border-4 border-white shadow-sm">
            <span class="material-symbols-outlined text-[10px] text-white font-bold">check</span>
          </div>
          <div>
            <h4 class="text-xs font-black uppercase tracking-wider text-primary">Paiement</h4>
            <p class="text-[11px] text-slate-500 font-medium">Chambre de compensation : Accepté</p>
            <span class="text-[9px] font-mono text-slate-400">14:22:06.012</span>
          </div>
        </div>
        <!-- Step 4 -->
        <div class="flex gap-6 relative">
          <div
            class="w-6 h-6 rounded-full bg-secondary-container flex items-center justify-center z-10 border-4 border-white shadow-sm">
            <span class="w-2 h-2 bg-secondary rounded-full"></span>
          </div>
          <div>
            <h4 class="text-xs font-black uppercase tracking-wider text-primary">Archivé</h4>
            <p class="text-[11px] text-slate-500 font-medium">Chiffré sur Cold Ledger</p>
            <span class="text-[9px] font-mono text-slate-400">Synchronisation en attente...</span>
          </div>
        </div>
      </div>
    </div>

    <div
      class="p-8 border-t border-surface-container-low bg-surface-container-lowest">
      <div
        class="flex justify-between items-center text-[10px] font-bold text-slate-400 uppercase tracking-widest">
        <span>Log Infrastructure</span>
        <span>Nœud : BR-042-S</span>
      </div>
      <button
        class="w-full py-4 mb-3 bg-primary text-white font-bold rounded-lg uppercase tracking-widest text-xs hover:bg-secondary transition-all flex items-center justify-center gap-3 gold-glow-active">
        <span class="material-symbols-outlined text-sm">print</span>
        Télécharger le Reçu
      </button>
      <button
        class="w-full py-3 bg-transparent border-2 border-outline-variant/30 text-primary font-bold rounded-lg uppercase tracking-widest text-xs hover:bg-white transition-all">
        Ouvrir un Ticket de Litige
      </button>
    </div>
  </div>
</main>