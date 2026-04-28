<?php
$title = 'Passage';

use App\Models\User;
use App\Models\Agent;
use App\Models\Guichet;
use App\Models\Paiement;

require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'operator/variables.php';

/** @var User   */ $user    = $user;
/** @var Agent  */ $agent   = $agent;
/** @var Guichet*/ $guichet = $guichet;

/* ═══════════════════════════════════════════════════
   TRAITEMENT DU FORMULAIRE
════════════════════════════════════════════════════ */
$form_success = false;
$form_error   = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Vérification de l'état de la voie via l'objet Agent
  if (!$agent->is_en_cours()) {
    $form_error = "Action impossible : La voie est actuellement FERMÉE.";
  } else {
    $immatriculation  = strtoupper(trim($_POST['immatriculation'] ?? ''));
    $type_vehicule_id = (int)($_POST['type_vehicule_id'] ?? 0);
    $mode_paiement    = trim($_POST['mode_paiement'] ?? '');
    $montant          = (float)($_POST['montant'] ?? 0);

    if ($immatriculation && $type_vehicule_id && $mode_paiement && $montant > 0) {
      try {
        $pdo->beginTransaction();

        // 1. Chercher ou créer le véhicule (Table: vehicule)
        $stmtV = $pdo->prepare("SELECT id FROM vehicule WHERE immatriculation = :immat LIMIT 1");
        $stmtV->execute(['immat' => $immatriculation]);
        $vehicule = $stmtV->fetch(PDO::FETCH_OBJ);

        if (!$vehicule) {
          $stmtI = $pdo->prepare(
            "INSERT INTO vehicule (immatriculation, type_vehicule_id) VALUES (:immat, :type)"
          );
          $stmtI->execute(['immat' => $immatriculation, 'type' => $type_vehicule_id]);
          $vehicule_id = (int)$pdo->lastInsertId();
        } else {
          $vehicule_id = (int)$vehicule->id;
          // Optionnel : Mise à jour du type de véhicule si celui-ci a changé
          $stmtU = $pdo->prepare("UPDATE vehicule SET type_vehicule_id = :t WHERE id = :id");
          $stmtU->execute(['t' => $type_vehicule_id, 'id' => $vehicule_id]);
        }

        // 2. Insérer le paiement (Table: paiement)
        // Note: 'is_valide' est à 1 par défaut dans le SQL
        $stmtP = $pdo->prepare(
          "INSERT INTO paiement (vehicule_id, guichet_id, mode_paiement, montant)
                     VALUES (:v, :g, :m, :mt)"
        );
        $stmtP->execute([
          'v'  => $vehicule_id,
          'g'  => $guichet->id,
          'm'  => $mode_paiement,
          'mt' => $montant,
        ]);

        $pdo->commit();
        $form_success = true;
      } catch (\Exception $e) {
        $pdo->rollBack();
        $form_error = "Erreur base de données : " . $e->getMessage();
      }
    } else {
      $form_error = 'Veuillez remplir tous les champs obligatoires.';
    }
  }
}

/* ═══════════════════════════════════════════════════
  RÉCUPÉRATION DES DONNÉES (HISTORIQUE & TYPES)
════════════════════════════════════════════════════ */
// Historique des 10 derniers passages pour ce guichet
$query = $pdo->prepare(
  "SELECT v.immatriculation, p.*, t.libelle
     FROM paiement p
     JOIN vehicule v ON p.vehicule_id = v.id
     JOIN typevehicule t ON v.type_vehicule_id = t.id
     WHERE p.guichet_id = :guichet_id
     ORDER BY p.created_at DESC
     LIMIT 10"
);
$query->execute(['guichet_id' => $guichet->id]);
$passages = $query->fetchAll(PDO::FETCH_CLASS, Paiement::class);

// Liste des types de véhicules pour le formulaire (Table: typevehicule)
$queryTypes = $pdo->query("SELECT * FROM typevehicule ORDER BY price ASC");
$typevehicules = $queryTypes->fetchAll(PDO::FETCH_OBJ);

/* ── Helpers de présentation (Mis à jour selon le SQL) ── */
$modeIcon = fn(string $m) => match (strtolower($m)) {
  'espèces', 'espece' => 'payments',
  'carte'             => 'credit_card',
  'abonnement'        => 'badge',
  'mobile money'      => 'phone_android', // Ajouté selon données SQL
  default             => 'toll',
};

$modeBadgeCss = fn(string $m) => match (strtolower($m)) {
  'espèces', 'espece' => 'bg-brand-success/10 text-brand-success border-brand-success/25',
  'carte'             => 'bg-brand-indigo/10 text-brand-indigo border-brand-indigo/25',
  'abonnement'        => 'bg-secondary-container text-secondary border-secondary/25',
  'mobile money'      => 'bg-amber-100 text-amber-700 border-amber-200',
  default             => 'bg-surface-container text-on-surface border-outline-variant',
};

$modeTopBar = fn(string $m) => match (strtolower($m)) {
  'espèces', 'espece' => 'bg-brand-success',
  'carte'             => 'bg-brand-indigo',
  'abonnement'        => 'bg-secondary',
  'mobile money'      => 'bg-amber-500',
  default             => 'bg-outline-variant',
};
?>

<main class="pt-16 min-h-screen flex flex-col bg-surface">

  <div class="h-14 flex items-center justify-between px-6 md:px-10 text-white shadow-md
        <?= $agent->is_en_cours() ? 'bg-brand-success' : 'bg-error' ?>">
    <div class="flex items-center gap-3">
      <span class="material-symbols-outlined text-2xl" style="font-variation-settings:'FILL' 1;">
        <?= $agent->is_en_cours() ? 'door_open' : 'door_front' ?>
      </span>
      <span class="font-headline font-extrabold text-sm tracking-[0.2em] uppercase">
        <?= $agent->is_en_cours() ? 'Voie Ouverte' : 'Voie Fermée' ?>
      </span>
      <span class="hidden md:inline-flex items-center gap-1.5 ml-3 bg-white/20 px-3 py-0.5 rounded-full text-xs font-mono">
        <span class="w-1.5 h-1.5 rounded-full bg-white <?= $agent->is_en_cours() ? 'animate-pulse' : '' ?>"></span>
        <?= $agent->is_en_cours() ? 'EN SERVICE' : 'HORS SERVICE' ?>
      </span>
    </div>

    <div class="flex items-center gap-3">
      <span class="hidden md:block font-mono text-xs opacity-80 tracking-widest uppercase">
        Sens : <?= htmlspecialchars($guichet->emplacement) ?>
      </span>
      <div class="bg-white/20 border border-white/30 px-3 py-1 rounded-md">
        <span class="font-mono text-xs font-bold tracking-widest">
          Poste #<?= str_pad($guichet->id, 2, '0', STR_PAD_LEFT) ?>
        </span>
      </div>
    </div>
  </div>

  <div class="flex-1 grid grid-cols-1 xl:grid-cols-[400px_1fr]">
    <?php require 'components/asideForm.php' ?>

    <?php require 'components/rightColumn.php' ?>
  </div>
</main>

<?php require_once 'code.php' ?>