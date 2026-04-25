<?php

use App\Models\TypeVehicule;

$title = "Tarifs";

require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'admin/variables.php';

/** @var PDO */
$pdo = $pdo;

$query = $pdo->prepare("SELECT * FROM typevehicule ORDER BY price");
$query->setFetchMode(PDO::FETCH_CLASS, TypeVehicule::class);
$query->execute();

$types = $query->fetchAll();

$isOpen = false;
$isModified = false;
$typeSelected = null;

if (isset($_GET['new'])) {
  $isOpen = true;
  $isModified = false;
} elseif (!empty($_GET['edit'])) {
  $id = $_GET['edit'];
  $query = $pdo->prepare("SELECT * FROM typevehicule WHERE id = :id");
  $query->setFetchMode(PDO::FETCH_CLASS, TypeVehicule::class);
  $query->execute(['id' => $id]);
  $typeSelected = $query->fetch();

  $isOpen = true;
  $isModified = true;
}

$error = null;
if ($_POST) {
  if (!empty($_POST['libelle']) && !empty($_POST['price'])) {
    $type = new TypeVehicule();
    $type->libelle = $_POST['libelle'];
    $type->price = $_POST['price'];

    if ($isModified) {
      $type->id = $_GET['edit'];
      $type->update();
    } else {
      $type->create();
    }
    header('Location: /admin/tarifs');
    exit();
  }
}

if (!empty($_GET['delete'])) {
  $type = new TypeVehicule();
  $type->id = $_GET['delete'];
  $type->delete();
  header('Location: /admin/tarifs');
  exit();
}

?>

<main class="md:ml-72 pt-20 px-8 pb-12 relative">
  <div>
    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row justify-between items-center lg:items-end gap-2 mb-16">
      <div>
        <p class="text-secondary font-mono text-xs font-bold tracking-[0.3em] uppercase mb-2">
          Gestion
        </p>
        <h2 class="text-6xl font-['Outfit'] font-black tracking-tight text-primary">Grille Tarifaire</h2>
      </div>

      <div class="flex gap-4">
        <a
          href="/admin/historiques"
          class="px-6 py-3 ghost-border text-primary font-bold rounded-lg hover:bg-surface-container-low transition-all">
          Historique
        </a>
        <a
          href="?new"
          class="px-6 py-3 bg-primary text-white font-bold rounded-lg hover:bg-secondary transition-all flex items-center gap-2">
          <span class="material-symbols-outlined text-sm">add</span>
          Nouvelle Catégorie
        </a>
      </div>
    </div>

    <!-- Vehicle Categories Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-20">
      <?php foreach ($types as $type) : ?>
        <div class="bg-surface-container-lowest p-8 rounded-xl ghost-border hover:shadow-xl hover:shadow-primary/5 transition-all group relative overflow-hidden">
          <div class="absolute top-0 left-0 w-1 h-full bg-primary-container"></div>
          <a href="?edit=<?= $type->id ?>" class="after:absolute after:inset-0 z-0">
            <span class="material-symbols-outlined text-[64px] text-primary mb-8 block transition-transform group-hover:scale-110">
              <?= $type->getIcon() ?>
            </span>
            <h3 class="font-headline font-bold text-lg mb-1"><?= $type->libelle ?></h3>
            <p class="text-on-surface-variant text-xs mb-6 uppercase tracking-widest">
              classe <?= $type->id ?>
            </p>
            <div class="flex items-baseline gap-1 mb-6">
              <span class="font-mono text-2xl font-bold text-secondary">
                <?= $type->getPrice() ?>
              </span>
              <small class="text-secondary font-bold text-sm">FCFA</small>
            </div>
          </a>

          <div class="hidden group-hover:block absolute top-4 right-4 z-10">
            <a
              onclick="return confirm('Voulez-vous vraiment supprimer cet élément ?')"
              href="?delete=<?= $type->id ?>"
              class="bg-error/10 text-error p-2 rounded-lg hover:bg-error hover:text-white transition-colors flex items-center justify-center">
              <span class="material-symbols-outlined">delete</span>
            </a>
          </div>

          <div class="bg-surface-container-low inline-flex items-center gap-2 px-3 py-1 rounded-full relative z-0">
            <span class="w-1.5 h-1.5 bg-secondary diamond-indicator"></span>
            <span class="text-[10px] font-mono font-bold text-on-surface-variant">
              Valide depuis <?= $type->getCreatedAt()->format('d/m/Y') ?>
            </span>
          </div>
        </div>
      <?php endforeach ?>

      <a
        href="?new"
        class="bg-surface-container-lowest p-8 rounded-xl ghost-border hover:shadow-xl hover:shadow-primary/5 transition-all group relative overflow-hidden border-2 border-dashed border-outline-variant/30 flex flex-col items-center justify-center text-center">
        <span class="material-symbols-outlined text-[48px] text-outline-variant mb-4"
          data-icon="add_circle">add_circle</span>
        <p class="text-on-surface-variant font-bold text-sm">Ajouter<br />Catégorie</p>
      </a>
    </div>

    <!-- Subscription Section -->
    <section class="mb-24">
      <div class="flex items-center gap-4 mb-10">
        <h3 class="text-3xl font-['Outfit'] font-bold text-primary">Abonnements Pass-Bridge</h3>
        <div class="h-px flex-1 bg-surface-container-high"></div>
      </div>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
        <!-- Monthly Card -->
        <div class="bg-surface-container-lowest p-10 rounded-xl ghost-border relative overflow-hidden">
          <div class="flex justify-between items-start mb-10">
            <div>
              <h4 class="text-2xl font-bold mb-2">Mensuel Illimité</h4>
              <p class="text-on-surface-variant max-w-xs">Accès total 24/7 pour un véhicule enregistré
                sur le pont.</p>
            </div>
            <div class="bg-surface-container-low p-4 rounded-lg">
              <span class="material-symbols-outlined text-primary"
                data-icon="calendar_month">calendar_month</span>
            </div>
          </div>
          <div class="flex items-baseline gap-2 mb-10">
            <span class="font-mono text-5xl font-black text-primary">45 000</span>
            <span class="font-headline font-bold text-primary text-xl">FCFA / mois</span>
          </div>
          <ul class="space-y-4 mb-10">
            <li class="flex items-center gap-3 text-sm">
              <span class="material-symbols-outlined text-green-600 text-sm" data-icon="check_circle"
                style="font-variation-settings: 'FILL' 1;">check_circle</span>
              Passage prioritaire (Ligne Or)
            </li>
            <li class="flex items-center gap-3 text-sm">
              <span class="material-symbols-outlined text-green-600 text-sm" data-icon="check_circle"
                style="font-variation-settings: 'FILL' 1;">check_circle</span>
              Support technique 24h/24
            </li>
          </ul>
          <button
            class="w-full py-4 border-2 border-primary text-primary font-bold rounded-lg hover:bg-primary hover:text-white transition-all uppercase tracking-widest text-xs">Modifier
            l'Offre</button>
        </div>
        <!-- Annual Card (Recommended) -->
        <div
          class="bg-surface-container-lowest p-10 rounded-xl border-2 border-secondary gold-glow relative overflow-hidden">
          <div class="absolute top-6 right-6">
            <span
              class="bg-secondary text-white text-[10px] font-black uppercase tracking-[0.2em] px-4 py-1 rounded-full">Recommandé</span>
          </div>
          <div class="flex justify-between items-start mb-10">
            <div>
              <h4 class="text-2xl font-bold mb-2">Annuel Signature</h4>
              <p class="text-on-surface-variant max-w-xs">Optimisation fiscale pour les entreprises et
                les résidents.</p>
            </div>
            <div class="bg-secondary/10 p-4 rounded-lg">
              <span class="material-symbols-outlined text-secondary" data-icon="workspace_premium"
                style="font-variation-settings: 'FILL' 1;">workspace_premium</span>
            </div>
          </div>
          <div class="flex items-baseline gap-2 mb-10">
            <span class="font-mono text-5xl font-black text-primary">450 000</span>
            <span class="font-headline font-bold text-primary text-xl">FCFA / an</span>
            <span class="text-secondary text-sm font-bold ml-2">(-20% d'économie)</span>
          </div>
          <ul class="space-y-4 mb-10">
            <li class="flex items-center gap-3 text-sm">
              <span class="material-symbols-outlined text-secondary text-sm" data-icon="verified"
                style="font-variation-settings: 'FILL' 1;">verified</span>
              Multi-véhicules (Jusqu'à 3)
            </li>
            <li class="flex items-center gap-3 text-sm">
              <span class="material-symbols-outlined text-secondary text-sm" data-icon="verified"
                style="font-variation-settings: 'FILL' 1;">verified</span>
              Accès Lounge Administrateur
            </li>
          </ul>
          <button
            class="w-full py-4 bg-primary text-white font-bold rounded-lg hover:bg-secondary transition-all uppercase tracking-widest text-xs shadow-lg shadow-primary/20">Éditer
            Configuration</button>
        </div>
      </div>
    </section>
  </div>

  <?php if ($isOpen): ?>
    <div
      class="fixed right-0 top-0 h-full w-110 bg-white z-60 shadow-2xl monolith-shadow flex flex-col translate-x-0 transition-transform duration-300 border-l border-surface-container">
      <div class="px-8 py-10 border-b border-surface-container-low relative">
        <div class="flex justify-between items-center">
          <div class="flex items-center gap-4">
            <div class="w-10 h-10 bg-primary-container flex items-center justify-center rounded">
              <span class="material-symbols-outlined text-white" data-icon="edit">edit</span>
            </div>
            <h3 class="text-2xl font-['Outfit'] font-bold">
              <?= $isModified ? 'Modification' : 'Création' ?> de Tarif</h3>
          </div>
          <a
            href="/admin/tarifs"
            class="w-10 h-10 flex items-center justify-center hover:bg-surface-container rounded-full transition-colors">
            <span class="material-symbols-outlined">close</span>
          </a>
        </div>
      </div>

      <form class="flex-1 overflow-y-auto p-8 space-y-8" method="post">
        <div class="hidden">
          <label
            class="block text-xs font-black uppercase tracking-widest text-on-surface-variant mb-3">Sélection Catégorie</label>
          <div class="grid grid-cols-3 gap-4">
            <?php foreach ($types as $type) : ?>
              <button
                class="border-2 border-primary bg-primary text-white p-2 rounded-lg flex flex-col items-center gap-2"
                type="button">
                <span class="material-symbols-outlined" data-icon="directions_car"><?= $type->getIcon() ?></span>
                <span class="text-xs font-bold"><?= $type->libelle ?></span>
              </button>
            <?php endforeach; ?>
          </div>
        </div>

        <div>
          <label
            class="block text-xs font-black uppercase tracking-widest text-on-surface-variant mb-3">
            Nom Catégorie
          </label>
          <div class="relative">
            <input
              class="w-full bg-surface-container-low border-0 border-l-4 border-secondary p-4 font-mono text-lg font-bold focus:ring-0 focus:bg-surface-container-high transition-colors"
              type="text" name="libelle" placeholder="Entrez le nom de la categorie"
              value="<?= $isModified ? $_POST['libelle'] ?? $typeSelected->libelle : '' ?>" />
          </div>
        </div>

        <div>
          <label
            class="block text-xs font-black uppercase tracking-widest text-on-surface-variant mb-3">Tarif (FCFA)</label>
          <div class="relative">
            <input
              class="w-full bg-surface-container-low border-0 border-l-4 border-secondary p-4 font-mono text-3xl font-bold focus:ring-0 focus:bg-surface-container-high transition-colors"
              type="text" name="price" placeholder="Entrez le tarif"
              value="<?= $isModified ? $_POST['price'] ?? $typeSelected->price : '' ?>" />
            <div class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 font-bold text-xs">
              XOF
            </div>
          </div>
        </div>

        <div class="p-6 bg-surface-container rounded-lg border-l-4 border-secondary/50">
          <div class="flex gap-4">
            <span class="material-symbols-outlined text-secondary" data-icon="info">info</span>
            <p class="text-xs text-on-surface-variant leading-relaxed">Cette modification entraînera une
              notification automatique à tous les abonnés « Mensuel Illimité » concernés par ce changement
              de classe.</p>
          </div>
        </div>

        <div class="pt-4 flex flex-col gap-4">
          <button
            type="submit"
            class="w-full py-4 bg-primary text-white font-bold rounded-lg hover:bg-secondary transition-all uppercase tracking-widest text-sm">
            Enregistrer
          </button>
          <button
            type="reset"
            class="w-full py-4 ghost-border text-on-surface-variant font-bold rounded-lg hover:bg-error/20 transition-all uppercase tracking-widest text-sm">Annuler</button>
        </div>
      </form>
    </div>
  <?php endif; ?>
</main>