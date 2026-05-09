<?php
$title = 'Mon Dashboard';

require __DIR__ . '/variables.php';

if (isset($_GET['close'])) {
  $pdo->prepare("UPDATE agent SET fin = NOW() WHERE id = :id")->execute(['id' => $agent->id]);

  http_response_code(301);
  header("Location : /pages/operator/");
  exit();
}

if (isset($_GET['open'])) {
  $pdo->prepare("UPDATE agent SET debut = NOW(), fin = NULL WHERE id = :id")->execute(['id' => $agent->id]);

  http_response_code(301);
  header("Location : /pages/operator/shift.php");
  exit();
}

if (isset($_GET['logout'])) {
  logout();

  http_response_code(301);
  header("Location : /");
  exit();
}

$query = $pdo->prepare("SELECT v.id AS vehicule_id, v.immatriculation, p.*, t.id AS type_vehicule_id, t.libelle 
  FROM paiement p 
  JOIN vehicule v ON p.vehicule_id = v.id 
  JOIN typevehicule t ON v.type_vehicule_id = t.id 
  WHERE p.guichet_id = :guichet_id
  ORDER BY p.created_at DESC 
  LIMIT 8");

$query->execute(['guichet_id' => $guichet->id]);
$passages = $query->fetchAll(PDO::FETCH_OBJ);

$montant = 0;
foreach ($passages as $passage) {
  $montant += $passage->montant;
}

$query = $pdo->prepare("SELECT COUNT(id) FROM incident");

$query->execute([]);
$incidents = $query->fetchColumn();

$debut = $agent->debut ? new DateTime($agent->debut) : null;
$fin = $agent->fin ? new DateTime($agent->fin) : null;

function isEnCours($agt)
{
  $debut = $agt->debut ? new DateTime($agt->debut) : null;
  $fin = $agt->fin ? new DateTime($agt->fin) : null;

  
  if ($agt->guichet_id === null || $debut === null) {
    return false;
  }

  return $debut->getTimestamp() <= time() && ($fin === null || $fin->getTimestamp() >= time());
}
?>

<?php require __DIR__ . '/../../layouts/headerOperator.php'; ?>

<main class="pt-24 px-4 md:px-8 mb-24 max-w-7xl mx-auto">
  
  <section
    class="mb-8 rounded-xl overflow-hidden bg-tertiary-container relative p-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
    <div class="flex items-center gap-4">
      <div class="p-3 bg-brand-indigo rounded-lg text-white">
        <span class="material-symbols-outlined text-3xl">schedule</span>
      </div>
      <div>
        <h2 class="text-white font-headline font-bold text-lg leading-tight">
          Heure Actuel : <?= date('H\h:i') ?>
        </h2>
        <p class="text-on-tertiary-container text-sm font-medium tracking-wide">
          DÉBUTÉ À <?= $debut->format('H\h:i') ?> • <?= isEnCours($agent) ? 'EN COURS' : 'FINI À ' . $fin->format('H\h:i')  ?>
        </p>
      </div>
    </div>
    <div class="flex gap-2">
      <span
        class="px-3 py-1 bg-secondary-container text-on-secondary-container rounded-md text-xs font-bold font-headline flex items-center gap-1">
        <span class="material-symbols-outlined text-sm <?= isEnCours($agent) ? 'animate-pulse' : '' ?>" style="font-variation-settings: 'FILL' 1">fiber_manual_record</span>
        <?= isEnCours($agent) ? 'EN COURS' : 'FINI' ?>
      </span>
      <span class="px-3 py-1 bg-brand-indigo text-white rounded-md text-xs font-bold text-center font-headline">
        VOIE #<?= $guichet->id ?>
      </span>
    </div>
  </section>

  
  <div class="grid grid-cols-1 md:grid-cols-3 gap-12 mb-12">
    <div class="bg-surface-container-lowest p-6 rounded-xl ghost-border flex flex-col gap-2">
      <div class="flex justify-between items-start">
        <span
          class="text-sm font-semibold text-on-surface-variant font-headline uppercase tracking-wider">Passages</span>
        <span class="material-symbols-outlined text-secondary">directions_car</span>
      </div>
      <div class="mono-data text-4xl font-bold text-secondary">
        <?= count($passages) ?>
      </div>
    </div>

    <div class="bg-surface-container-lowest p-6 rounded-xl ghost-border flex flex-col gap-2">
      <div class="flex justify-between items-start">
        <span
          class="text-sm font-semibold text-on-surface-variant font-headline uppercase tracking-wider">Encaissé (FCFA)</span>
        <span class="material-symbols-outlined text-brand-indigo">payments</span>
      </div>
      <div class="mono-data text-4xl font-bold text-brand-indigo">
        <?= number_format($montant, 0, ',', ' ') ?>
      </div>
    </div>

    
    <div class="bg-surface-container-lowest p-6 rounded-xl ghost-border flex flex-col gap-2">
      <div class="flex justify-between items-start">
        <span
          class="text-sm font-semibold text-on-surface-variant font-headline uppercase tracking-wider">Incidents</span>
        <span class="material-symbols-outlined text-error">report_problem</span>
      </div>
      <div class="mono-data text-4xl font-bold text-error">
        <?= $incidents ?>
      </div>
    </div>
  </div>

  
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
    
    <div class="lg:col-span-2 space-y-6">
      <div class="flex items-center justify-between">
        <h3 class="font-headline font-extrabold text-2xl tracking-tight text-primary">Historique de passage
        </h3>
      </div>
      <div class="bg-surface-container-lowest rounded-xl overflow-hidden ghost-border">
        <table class="w-full text-left border-collapse">
          <thead class="bg-surface-container-low">
            <tr>
              <th class="px-6 py-4 text-xs font-bold text-on-surface-variant uppercase font-headline">
                Date</th>
              <th class="px-6 py-4 text-xs font-bold text-on-surface-variant uppercase font-headline">
                immatriculation</th>
              <th class="px-6 py-4 text-xs font-bold text-on-surface-variant uppercase font-headline">
                Catégorie</th>
              <th class="px-6 py-4 text-xs font-bold text-on-surface-variant uppercase font-headline">
                Paiement</th>
              <th
                class="px-6 py-4 text-xs font-bold text-on-surface-variant uppercase font-headline text-right">
                Montant (FCFA)</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-surface-container">
            <?php foreach ($passages as $passage) : ?>
              <?php require 'components/cards/cardRowPassage.php' ?>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

    
    <div class="space-y-6">
      <h3 class="font-headline font-extrabold text-2xl tracking-tight text-primary">Mon Profil</h3>
      <div
        class="bg-surface-container-lowest p-8 rounded-xl ghost-border flex flex-col items-center text-center">
        <div class="relative mb-4">
          <div
            class="relative inline-flex group cursor-pointer">
            <div class="flex items-center justify-center w-24 h-24 rounded-full overflow-hidden border-2 border-surface-container-high bg-secondary-container shadow-sm transition-all duration-300 group-hover:shadow-md">
              <span class="text-primary uppercase text-5xl font-black font-mono transition-transform duration-300 group-hover:scale-110" data-icon="person">
                <?= substr($agent->username, 0, 2) ?>
              </span>
            </div>
          </div>

          <div class="absolute -bottom-3 -right-2 p-1.5 bg-brand-indigo text-white rounded-full">
            <span class="material-symbols-outlined text-sm">edit</span>
          </div>
        </div>
        <h4 class="font-headline font-bold text-xl text-primary">
          <?= $agent->username ?>
        </h4>
        <div class="w-full mt-8 space-y-4 text-left">
          <div class="space-y-1">
            <label
              class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest">
              Identifiant Agent
            </label>
            <div
              class="p-3 bg-surface-container-low rounded-lg mono-data text-sm font-bold text-primary">
              #<?= $agent->id ?>
            </div>
          </div>
          <div class="space-y-1">
            <label class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest">Email
              Professionnel</label>
            <div class="p-3 bg-surface-container-low rounded-lg text-sm text-on-surface">
              <?= $agent->email ?>
            </div>
          </div>
        </div>
      </div>

      
      <div class="flex flex-col gap-3">
        <?php if (isEnCours($agent)) : ?>
          <a
            href="?close"
            class="w-full flex items-center justify-center py-4 rounded-xl border-2 border-primary text-primary font-headline font-bold text-sm tracking-wide hover:bg-primary hover:text-white transition-all">
            Fermer la voie
          </a>
        <?php else : ?>
          <a
            href="?open"
            class="w-full flex items-center justify-center py-4 rounded-xl border-2 border-on-secondary text-on-secondary font-headline font-bold text-sm tracking-wide bg-secondary-container hover:bg-primary transition-all">
            Ouvrir la voie
          </a>
        <?php endif; ?>
        <a
          href="?logout"
          class="w-full flex items-center justify-center py-4 rounded-xl bg-[#FF6B6B] text-white font-headline font-bold text-sm tracking-wide shadow-lg hover:brightness-95 transition-all">
          Se déconnecter
        </a>
      </div>
    </div>
  </div>
</main>

<?php require __DIR__ . '/../../layouts/footerAdmin.php'; ?>