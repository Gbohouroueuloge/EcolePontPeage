<?php
$title = 'Mon Shift';

use App\Components\Notification;
use App\Models\User;
use App\Models\Agent;
use App\Models\Guichet;

require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'operator/variables.php';

/** @var User */
$user = $user;

/** @var Agent */
$agent = $agent;

/** @var Guichet */
$guichet = $guichet;

if (isset($_GET['close'])) {
  $pdo->prepare("UPDATE agent SET fin = NOW() WHERE id = :id")->execute(['id' => $agent->id]);

  http_response_code(301);
  header("Location : /operator/$params[username]-$params[id]");
  exit();
}

if (isset($_GET['open'])) {
  $pdo->prepare("UPDATE agent SET debut = NOW(), fin = NULL WHERE id = :id")->execute(['id' => $agent->id]);

  http_response_code(301);
  header("Location : /operator/$params[username]-$params[id]/mon-shift");
  exit();
}

if (isset($_GET['logout'])) {
  $auth->logout();

  http_response_code(301);
  header("Location : /");
  exit();
}

?>

<main class="pt-24 px-4 md:px-8 mb-24 max-w-7xl mx-auto">
  <!-- Shift Info Banner -->
  <section
    class="mb-8 rounded-xl overflow-hidden bg-tertiary-container relative p-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
    <div class="flex items-center gap-4">
      <div class="p-3 bg-brand-indigo rounded-lg text-white">
        <span class="material-symbols-outlined text-3xl">schedule</span>
      </div>
      <div>
        <h2 class="text-white font-headline font-bold text-lg leading-tight">
          Shift Actuel : <?= date('H\h:i') ?>
        </h2>
        <p class="text-on-tertiary-container text-sm font-medium tracking-wide">
          DÉBUTÉ À <?= $agent->getDateDebut()->format('H\h:i') ?> • <?= $agent->is_en_cours() ? 'EN COURS' : 'FINI À ' . $agent->getDateFin()->format('H\h:i')  ?>
        </p>
      </div>
    </div>
    <div class="flex gap-2">
      <span
        class="px-3 py-1 bg-secondary-container text-on-secondary-container rounded-md text-xs font-bold font-headline flex items-center gap-1">
        <span class="material-symbols-outlined text-sm <?= $agent->is_en_cours() ? 'animate-pulse' : '' ?>" style="font-variation-settings: 'FILL' 1">fiber_manual_record</span>
        <?= $agent->is_en_cours() ? 'EN COURS' : 'FINI' ?>
      </span>
      <span class="px-3 py-1 bg-brand-indigo text-white rounded-md text-xs font-bold text-center font-headline">
        VOIE #<?= $guichet->id ?>
      </span>
    </div>
  </section>

  <!-- KPI Cards Grid -->
  <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
    <!-- Passages Card -->
    <div class="bg-surface-container-lowest p-6 rounded-xl ghost-border flex flex-col gap-2">
      <div class="flex justify-between items-start">
        <span
          class="text-sm font-semibold text-on-surface-variant font-headline uppercase tracking-wider">Passages</span>
        <span class="material-symbols-outlined text-brand-indigo">directions_car</span>
      </div>
      <div class="mono-data text-4xl font-bold text-primary">1,284</div>
      <div class="flex items-center gap-1 text-xs text-secondary font-bold">
        <span class="material-symbols-outlined text-xs">trending_up</span>
        +12% vs hier
      </div>
    </div>
    <!-- Revenue Card -->
    <div class="bg-surface-container-lowest p-6 rounded-xl ghost-border flex flex-col gap-2">
      <div class="flex justify-between items-start">
        <span
          class="text-sm font-semibold text-on-surface-variant font-headline uppercase tracking-wider">Encaissé
          (FCFA)</span>
        <span class="material-symbols-outlined text-brand-indigo">payments</span>
      </div>
      <div class="mono-data text-4xl font-bold text-primary">642,000</div>
      <div class="flex items-center gap-1 text-xs text-secondary font-bold">
        <span class="material-symbols-outlined text-xs">account_balance_wallet</span>
        Espèces: 400k | Tag: 242k
      </div>
    </div>
    <!-- Incidents Card -->
    <div class="bg-surface-container-lowest p-6 rounded-xl ghost-border flex flex-col gap-2">
      <div class="flex justify-between items-start">
        <span
          class="text-sm font-semibold text-on-surface-variant font-headline uppercase tracking-wider">Incidents</span>
        <span class="material-symbols-outlined text-brand-indigo">report_problem</span>
      </div>
      <div class="mono-data text-4xl font-bold text-primary">03</div>
      <div class="flex items-center gap-1 text-xs text-error font-bold">
        <span class="material-symbols-outlined text-xs">warning</span>
        2 Non-paiements résolus
      </div>
    </div>
  </div>

  <!-- Two Column Layout: Table & Profile -->
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
    <!-- Compact History Table -->
    <div class="lg:col-span-2 space-y-6">
      <div class="flex items-center justify-between">
        <h3 class="font-headline font-extrabold text-2xl tracking-tight text-primary">Historique de passage
        </h3>
        <button class="text-sm font-bold text-secondary flex items-center gap-1">
          VOIR TOUT <span class="material-symbols-outlined text-sm">arrow_forward</span>
        </button>
      </div>
      <div class="bg-surface-container-lowest rounded-xl overflow-hidden ghost-border">
        <table class="w-full text-left border-collapse">
          <thead class="bg-surface-container-low">
            <tr>
              <th class="px-6 py-4 text-xs font-bold text-on-surface-variant uppercase font-headline">
                Heure</th>
              <th class="px-6 py-4 text-xs font-bold text-on-surface-variant uppercase font-headline">
                Véhicule</th>
              <th class="px-6 py-4 text-xs font-bold text-on-surface-variant uppercase font-headline">
                Cat.</th>
              <th class="px-6 py-4 text-xs font-bold text-on-surface-variant uppercase font-headline">
                Paiement</th>
              <th
                class="px-6 py-4 text-xs font-bold text-on-surface-variant uppercase font-headline text-right">
                Montant</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-surface-container">
            <tr class="hover:bg-surface-container-low transition-colors">
              <td class="px-6 py-4 mono-data text-sm">10:42:15</td>
              <td class="px-6 py-4">
                <div
                  class="inline-flex items-center px-3 py-1 rounded bg-surface-container-highest border-l-2 border-primary-container">
                  <span class="mono-data font-bold text-sm tracking-wider">AA-123-BB</span>
                </div>
              </td>
              <td class="px-6 py-4 text-sm font-medium">C1</td>
              <td class="px-6 py-4">
                <span
                  class="flex items-center gap-1 text-xs font-bold text-on-tertiary-fixed-variant">
                  <span class="material-symbols-outlined text-sm">contactless</span>
                  TAG
                </span>
              </td>
              <td class="px-6 py-4 text-right mono-data font-bold text-primary">500</td>
            </tr>
            <tr class="hover:bg-surface-container-low transition-colors">
              <td class="px-6 py-4 mono-data text-sm">10:41:02</td>
              <td class="px-6 py-4">
                <div
                  class="inline-flex items-center px-3 py-1 rounded bg-surface-container-highest border-l-2 border-primary-container">
                  <span class="mono-data font-bold text-sm tracking-wider">CK-908-XZ</span>
                </div>
              </td>
              <td class="px-6 py-4 text-sm font-medium">C2</td>
              <td class="px-6 py-4">
                <span class="flex items-center gap-1 text-xs font-bold text-secondary">
                  <span class="material-symbols-outlined text-sm">payments</span>
                  CASH
                </span>
              </td>
              <td class="px-6 py-4 text-right mono-data font-bold text-primary">1,500</td>
            </tr>
            <tr class="hover:bg-surface-container-low transition-colors">
              <td class="px-6 py-4 mono-data text-sm">10:38:44</td>
              <td class="px-6 py-4">
                <div
                  class="inline-flex items-center px-3 py-1 rounded bg-surface-container-highest border-l-2 border-primary-container">
                  <span class="mono-data font-bold text-sm tracking-wider">LT-442-DD</span>
                </div>
              </td>
              <td class="px-6 py-4 text-sm font-medium">C1</td>
              <td class="px-6 py-4">
                <span
                  class="flex items-center gap-1 text-xs font-bold text-on-tertiary-fixed-variant">
                  <span class="material-symbols-outlined text-sm">contactless</span>
                  TAG
                </span>
              </td>
              <td class="px-6 py-4 text-right mono-data font-bold text-primary">500</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Profile Section -->
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

      <!-- Action Buttons -->
      <div class="flex flex-col gap-3">
        <?php if ($agent->is_en_cours()) : ?>
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