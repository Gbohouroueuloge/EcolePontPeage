<?php

use App\Models\Paiement;

$title = "Dashboard";

require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'admin/variables.php';

/** @var PDO */
$pdo = $pdo;

$query = $pdo->prepare("SELECT u.username FROM agent a JOIN users u ON a.user_id = u.id WHERE guichet_id IS NOT NULL AND debut IS NOT NULL AND fin IS NULL");
$query->execute([]);
$agentsCours = $query->fetchAll();

$query = $pdo->prepare("SELECT p.*, g.id AS guichet_id, g.emplacement, v.immatriculation FROM paiement p JOIN guichet g ON p.guichet_id = g.id JOIN vehicule v ON p.vehicule_id = v.id ORDER BY p.created_at DESC");

$query->execute([]);
/** @var Paiement[] */
$paymentsAll = $query->fetchAll(PDO::FETCH_CLASS, Paiement::class);

$revenue = 0;
foreach ($paymentsAll as $payment) {
  $revenue += $payment->montant;
}

$query = $pdo->prepare("SELECT p.*, g.id AS guichet_id, g.emplacement, v.immatriculation FROM paiement p JOIN guichet g ON p.guichet_id = g.id JOIN vehicule v ON p.vehicule_id = v.id ORDER BY p.created_at DESC LIMIT 9");
$query->execute([]);
$activities = $query->fetchAll(PDO::FETCH_CLASS, Paiement::class);


?>

<main class="md:ml-72 pt-20 px-8 pb-12">
  <!-- Header -->
  <div class="mb-10 mt-4">
    <p class="text-secondary font-mono text-xs font-bold tracking-[0.3em] uppercase mb-2">
      Vue générale
    </p>
    <h2 class="text-6xl font-['Outfit'] font-black tracking-tight text-primary">
      Tableau de Bord Opérationnel
    </h2>
    <p class="text-on-surface-variant font-headline">
      Surveillance en temps réel du Pont • <?= date('d/m/Y') ?>
    </p>
  </div>

  <!-- KPI Row -->
  <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <!-- Revenue Card -->
    <div
      class="bg-surface-container-lowest p-6 rounded-xl shadow-[0_4px_20px_rgba(0,7,25,0.03)] border-l-4 border-secondary relative overflow-hidden">
      <div class="flex justify-between items-start mb-4">
        <span class="text-xs font-bold font-headline uppercase tracking-wider text-on-surface-variant">Revenus du jour</span>
        <span
          class="material-symbols-outlined text-secondary"
          style="font-variation-settings: 'FILL' 1;">
          payments
        </span>
      </div>
      <div class="font-mono text-[40px] font-bold text-secondary leading-none mb-2">
        <?= number_format($revenue, 0, ',', ' ') ?> FCFA
      </div>

      <!-- Sparkline Placeholder -->
      <div class="mt-4 h-12 w-full flex items-end gap-1">
        <div class="w-full h-[40%] bg-surface-container-high rounded-t-sm"></div>
        <div class="w-full h-[60%] bg-surface-container-high rounded-t-sm"></div>
        <div class="w-full h-[50%] bg-surface-container-high rounded-t-sm"></div>
        <div class="w-full h-[80%] bg-secondary rounded-t-sm"></div>
        <div class="w-full h-[65%] bg-surface-container-high rounded-t-sm"></div>
        <div class="w-full h-[90%] bg-secondary rounded-t-sm"></div>
        <div class="w-full h-full bg-secondary rounded-t-sm"></div>
      </div>
    </div>

    <!-- Passages Card -->
    <div
      class="bg-surface-container-lowest p-6 rounded-xl shadow-[0_4px_20px_rgba(0,7,25,0.03)] border-l-4 border-primary">
      <div class="flex justify-between items-start mb-4">
        <span class="text-xs font-bold font-headline uppercase tracking-wider text-on-surface-variant">Passages</span>
        <span class="material-symbols-outlined text-primary">directions_car</span>
      </div>
      <div class="font-mono text-4xl font-bold text-primary mb-6"><?= count($paymentsAll) ?></div>
      <div class="flex flex-wrap gap-2">
        <?php foreach ($activities as $activity) : ?>
          <span class="px-2 py-1 bg-surface-container text-[10px] font-bold rounded uppercase">
            <?= $activity->immatriculation ?>
          </span>
        <?php endforeach ?>
      </div>
    </div>

    <!-- Agents Card -->
    <div
      class="bg-surface-container-lowest p-6 rounded-xl shadow-[0_4px_20px_rgba(0,7,25,0.03)] border-l-4 border-tertiary">
      <div class="flex justify-between items-start mb-4">
        <span class="text-xs font-bold font-headline uppercase tracking-wider text-on-surface-variant">Agents en service</span>
        <span class="material-symbols-outlined text-tertiary">group</span>
      </div>
      <div class="font-mono text-4xl font-bold text-primary mb-4"><?= count($agentsCours) ?></div>
      <div class="flex -space-x-2 overflow-hidden">
        <?php foreach ($agentsCours as $k => $agent): ?>
          <?php if ($k > 4) break; ?>
          <div
            class="inline-flex items-center justify-center border-2 border-surface-container-high  overflow-hidden h-10 w-10 rounded-full ring-2 bg-surface-container-high ring-white group">
            <span class="text-primary uppercase text-2xl font-black font-mono transition-transform duration-300 group-hover:scale-110" data-icon="person">
              <?= substr($agent['username'], 0, 2) ?>
            </span>
          </div>
        <?php endforeach ?>
        <?php if (count($agentsCours) > 5): ?>
          <div
            class="inline-flex h-10 w-10 rounded-full bg-surface-container-highest items-center justify-center text-[14px] font-bold ring-2 ring-white">
            +<?= count($agentsCours) - 5 ?>
          </div>
        <?php endif ?>
      </div>
    </div>

    <!-- Incidents Card -->
    <!-- <div
      class="bg-surface-container-lowest p-6 rounded-xl shadow-[0_4px_20px_rgba(0,7,25,0.03)] border-l-4 border-error">
      <div class="flex justify-between items-start mb-4">
        <span class="text-xs font-bold font-headline uppercase tracking-wider text-on-surface-variant">Incidents
          actifs</span>
        <span class="material-symbols-outlined text-error" style="font-variation-settings: 'FILL' 1;">warning</span>
      </div>
      <div class="font-mono text-4xl font-bold text-error mb-4">03</div>
      <div class="flex items-center gap-2">
        <div class="w-2 h-2 rounded-full bg-error animate-pulse"></div>
        <span class="text-xs font-bold text-on-surface uppercase tracking-tight">Intervention en cours</span>
      </div>
    </div> -->
  </div>

  <!-- Middle Section: Charts & Activity -->
  <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 mb-8">
    <!-- Left & Center: Charts Stack -->
    <div class="lg:col-span-8 flex flex-col gap-8">
      <!-- Traffic AreaChart -->
      <div class="bg-surface-container-lowest p-8 rounded-2xl shadow-[0_8px_30px_rgba(0,7,25,0.04)]">
        <div class="flex flex-col xl:flex-row items-center justify-between xl:items-end gap-2 mb-8">
          <div>
            <h3 class="text-xl font-outfit font-bold text-primary">Flux de Trafic Hebdomadaire</h3>
            <p class="text-on-surface-variant text-sm">Volume de passages cumulés sur 7 jours</p>
          </div>
          <div class="flex gap-2">
            <button
              class="px-4 py-2 text-xs font-bold border border-outline-variant/30 rounded-md hover:bg-surface-container-low transition-all">Exporter</button>
            <button class="px-4 py-2 text-xs font-bold bg-primary text-white rounded-md">Vue Directe</button>
          </div>
        </div>
        <div class="h-64 relative flex items-end justify-between gap-2">
          <!-- Chart Background Grids -->
          <div class="absolute inset-0 flex flex-col justify-between pointer-events-none opacity-5">
            <div class="border-b border-primary"></div>
            <div class="border-b border-primary"></div>
            <div class="border-b border-primary"></div>
            <div class="border-b border-primary"></div>
          </div>
          <!-- Pseudo Area Chart (CSS Shape) -->
          <div class="flex-1 h-full flex items-end justify-between group">
            <div class="w-full h-full relative">
              <svg class="w-full h-full" preserveaspectratio="none" viewbox="0 0 800 200">
                <defs>
                  <lineargradient id="gradient" x1="0%" x2="0%" y1="0%" y2="100%">
                    <stop offset="0%" stop-color="#000719" stop-opacity="0.2"></stop>
                    <stop offset="100%" stop-color="#000719" stop-opacity="0"></stop>
                  </lineargradient>
                </defs>
                <path d="M0,150 Q100,80 200,120 T400,60 T600,100 T800,40 L800,200 L0,200 Z" fill="url(#gradient)">
                </path>
                <path d="M0,150 Q100,80 200,120 T400,60 T600,100 T800,40" fill="none" stroke="#000719"
                  stroke-width="3"></path>
              </svg>
            </div>
          </div>
        </div>
        <div class="flex justify-between mt-4 px-2">
          <span class="font-mono rotate-90 xl:rotate-0 text-[10px] text-on-surface-variant">LUNDI</span>
          <span class="font-mono rotate-90 xl:rotate-0 text-[10px] text-on-surface-variant">MARDI</span>
          <span class="font-mono rotate-90 xl:rotate-0 text-[10px] text-on-surface-variant">MERCREDI</span>
          <span class="font-mono rotate-90 xl:rotate-0 text-[10px] text-on-surface-variant">JEUDI</span>
          <span class="font-mono rotate-90 xl:rotate-0 text-[10px] text-on-surface-variant">VENDREDI</span>
          <span class="font-mono rotate-90 xl:rotate-0 text-[10px] text-on-surface-variant">SAMEDI</span>
          <span class="font-mono rotate-90 xl:rotate-0 text-[10px] text-on-surface-variant">DIMANCHE</span>
        </div>
      </div>

      <!-- Donut Chart -->
      <div class="md:col-span-5 flex flex-col md:flex-row justify-between items-center bg-surface-container-lowest p-8 rounded-2xl shadow-[0_8px_30px_rgba(0,7,25,0.04)]">
        <div>
          <h3 class="text-lg font-outfit font-bold text-primary mb-6 text-center">Répartition Revenus</h3>
          <div class="relative w-48 h-48 mx-auto flex items-center justify-center">
            <svg class="w-full h-full transform -rotate-90">
              <circle cx="50%" cy="50%" fill="transparent" r="40%" stroke="#e7e2da" stroke-width="20"></circle>
              <circle cx="50%" cy="50%" fill="transparent" r="40%" stroke="#000719" stroke-dasharray="180 300"
                stroke-width="20"></circle>
              <circle cx="50%" cy="50%" fill="transparent" r="40%" stroke="#7e5700" stroke-dasharray="80 300"
                stroke-dashoffset="-180" stroke-width="20"></circle>
            </svg>
            <div class="absolute inset-0 flex flex-col items-center justify-center text-center px-4">
              <span class="font-mono text-xl font-bold">9.3M</span>
              <span class="text-[10px] uppercase font-bold text-on-surface-variant">Total XOF</span>
            </div>
          </div>
        </div>

        <div class="mt-8 space-y-3">
          <div class="flex gap-8 justify-between items-center text-xs">
            <div class="flex items-center gap-2">
              <span class="w-2 h-2 rounded-full bg-primary"></span>
              <span class="font-headline font-semibold">Abonnements</span>
            </div>
            <span class="font-mono font-bold">65%</span>
          </div>
          <div class="flex gap-8 justify-between items-center text-xs">
            <div class="flex items-center gap-2">
              <span class="w-2 h-2 rounded-full bg-secondary"></span>
              <span class="font-headline font-semibold">Tickets Espèces</span>
            </div>
            <span class="font-mono font-bold">28%</span>
          </div>
          <div class="flex gap-8 justify-between items-center text-xs">
            <div class="flex items-center gap-2">
              <span class="w-2 h-2 rounded-full bg-surface-container-highest"></span>
              <span class="font-headline font-semibold">Autres</span>
            </div>
            <span class="font-mono font-bold">7%</span>
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="md:col-span-7 flex flex-col gap-6">
        <div class="bg-primary p-8 rounded-2xl text-white shadow-xl relative overflow-hidden">
          <div class="relative z-10">
            <h3 class="text-xl font-outfit font-bold mb-2">Actions Rapides</h3>
            <p class="text-slate-400 text-sm mb-6">Gestion immédiate des infrastructures.</p>
            <div class="grid md:grid-cols-2 gap-4">
              <button
                class="flex items-center gap-3 p-4 border border-white/20 rounded-xl hover:bg-white/10 transition-all text-left">
                <span class="material-symbols-outlined text-secondary-container">shield</span>
                <span class="text-xs font-bold uppercase tracking-wider">Alerte Sécurité</span>
              </button>
              <button
                class="flex items-center gap-3 p-4 border border-white/20 rounded-xl hover:bg-white/10 transition-all text-left">
                <span class="material-symbols-outlined text-secondary-container">construction</span>
                <span class="text-xs font-bold uppercase tracking-wider">Maintenance</span>
              </button>
              <button
                class="flex items-center gap-3 p-4 border border-white/20 rounded-xl hover:bg-white/10 transition-all text-left">
                <span class="material-symbols-outlined text-secondary-container">credit_card</span>
                <span class="text-xs font-bold uppercase tracking-wider">Terminal TPE</span>
              </button>
              <button
                class="flex items-center gap-3 p-4 border border-white/20 rounded-xl hover:bg-white/10 transition-all text-left">
                <span class="material-symbols-outlined text-secondary-container">support_agent</span>
                <span class="text-xs font-bold uppercase tracking-wider">Appel Poste</span>
              </button>
            </div>
          </div>
          <div class="absolute -right-8 -bottom-8 opacity-10">
            <span class="material-symbols-outlined text-[160px]">architecture</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Right Column: Recent Activity Timeline -->
    <div class="lg:col-span-4 self-start bg-surface-container-low rounded-2xl p-8 border border-outline-variant/10">
      <div class="flex items-center justify-between mb-8">
        <h3 class="text-xl font-outfit font-bold text-primary">Activité Récente</h3>
        <span class="text-[10px] font-bold bg-primary text-white px-2 py-1 rounded">DIRECT</span>
      </div>

      <div class="space-y-6 relative before:content-[''] before:absolute before:left-2.75 before:top-2 before:bottom-2 before:w-px before:bg-outline-variant/30">
        <?php foreach ($activities as $activity): ?>
          <?php require 'components/cards/cardActivity.php'; ?>
        <?php endforeach; ?>
      </div>

      <button
        class="w-full mt-8 py-3 border border-outline-variant/30 rounded-lg text-xs font-bold uppercase tracking-widest text-primary hover:bg-primary hover:text-white transition-all">
        Historique Complet
      </button>
    </div>
  </div>
</main>