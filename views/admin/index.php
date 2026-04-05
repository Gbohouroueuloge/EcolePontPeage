<?php
$title = "Dashboard";

$notifications = [
  ['title' => "Paiement Validé - Plaque [AA-456-ZZ]", 'text' => "14:42:05 • 8 200 FCFA • Cabine 04", 'type' => "payments", 'color' => ["bg-secondary-container", "text-primary"]],
  ['title' => "Refus de Passage - Badge Invalide", 'text' => "14:39:12 • Voie Express 02", 'type' => "report", 'color' => ["bg-error-container", "text-error"]],
  ['title' => "Agent Jean D. en Pause", 'text' => "14:35:00 • Relève Secteur Sud", 'type' => "person", 'color' => ["bg-surface-container-highest", "text-primary"]],
  ['title' => "Check-up Capteur Laser L04", 'text' => "14:28:10 • Statut: Opérationnel", 'type' => "visibility", 'color' => ["bg-surface-container-highest", "text-primary"]],
];

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
      Surveillance en temps réel du Pont de la Monolithe • 24 Octobre 2023
    </p>
  </div>

  <!-- KPI Row -->
  <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
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
      <div class="font-mono text-[40px] font-bold text-secondary leading-none mb-2">9 367 055 FCFA</div>
      <div class="flex items-center gap-2 text-green-600 font-headline font-bold text-sm">
        <span class="material-symbols-outlined text-sm">trending_up</span>
        +12.4% vs hier
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
      <div class="font-mono text-4xl font-bold text-primary mb-6">4 892</div>
      <div class="flex flex-wrap gap-2">
        <span class="px-2 py-1 bg-surface-container text-[10px] font-bold rounded uppercase">Vl: 3.2k</span>
        <span class="px-2 py-1 bg-surface-container text-[10px] font-bold rounded uppercase">Pl: 1.1k</span>
        <span class="px-2 py-1 bg-surface-container text-[10px] font-bold rounded uppercase">Moto: 592</span>
      </div>
    </div>

    <!-- Agents Card -->
    <div
      class="bg-surface-container-lowest p-6 rounded-xl shadow-[0_4px_20px_rgba(0,7,25,0.03)] border-l-4 border-tertiary">
      <div class="flex justify-between items-start mb-4">
        <span class="text-xs font-bold font-headline uppercase tracking-wider text-on-surface-variant">Agents en
          service</span>
        <span class="material-symbols-outlined text-tertiary">group</span>
      </div>
      <div class="font-mono text-4xl font-bold text-primary mb-4">18</div>
      <div class="flex -space-x-3 overflow-hidden">
        <img alt="" class="inline-block h-10 w-10 rounded-full ring-2 ring-white"
          src="https://lh3.googleusercontent.com/aida-public/AB6AXuBtQKgTDnPOnXwzZ689GAywSQlaAYK3stYp9W3LlNu-_s0BonYXh8IcQFZnaIBcLkrw50LpgOXdCSulehwhYhvLGgPK60tkNARhEqQ7uVsnrTJkO6Hy_A3gVocUifHN_yb2HAp1_O3_dIOvQROmG7ZbIJCQ6wW2UzUkXeF6ggH917EIrMZynqVSeaZPVQxdBnzr8827rrtbUe4hoxfu5eVz5beo3fprlEiA5hB6G7L-eZ-_aYPkI8e1KbH87o8WNMDp11z7io-0JxSR" />
        <img alt="" class="inline-block h-10 w-10 rounded-full ring-2 ring-white"
          src="https://lh3.googleusercontent.com/aida-public/AB6AXuD1Kme2yKn08LDYHwlaxEusnrkC7uPvnoxjjriFA66p-WVJCj56ubJpjwnbHD-dzDSGFg1W5LzThszQXARD2AEQCBywtzKOOXNnfVTXCZmR2Q8gwO2NN6c7W6jEbekXnMJMFTQjAwipdxkwBer6d4XKjBGjffO_hiaIjLjf4VtwzkZHddNT09li4dy8FQchiVzYVKJoYtduoSiFKCiLxqVgAB4OcuOZ0Wcgy2qKbUm2oFIwBEn18rwMoMMAoJAvewmN-IGxzexu2UlP" />
        <img alt="" class="inline-block h-10 w-10 rounded-full ring-2 ring-white"
          src="https://lh3.googleusercontent.com/aida-public/AB6AXuD0fdX02jHjkNNCD-GOlHdTJuBPDmOGFyObqSxHECKCAwCOlzgt0wZK5vifCmog-fPUnmoa1KJVDc4ORhWhX9YzidpzpOVSlaOwVpoIJD-uNaniCNMJLbvuTQXcfj68rN4ETtGgPQppT2X37awQ_wgy2uysgdakQIY-rRMW19yz0F54x5Kkxndl1jOhp2zfGd8iYs_q5YDfGaowdY-59ptFxq8N5f9Nws0yOK0IOGQULT34k-9xySfdm490tfdUptXwhzO4va9aaT0q" />
        <img alt="" class="inline-block h-10 w-10 rounded-full ring-2 ring-white"
          src="https://lh3.googleusercontent.com/aida-public/AB6AXuAcJTXiwF3bXM0x3K7qavJ80C2g5TjH9D85NEYvVD6-GHZ_OPCi7hZ4MASZ8vlTCz7tTzeLyIgD6cYTOawIBQPAcZ6vsKZ7ghilV5CXU7zw6402oRT_R_3GtnnwFEDfQBZ1C_l49zfHc3--IbFwdpLFeqUwLrfxHI7ktkIzPFwvUNviWvzS6GjxziOJDuTIsjKNpf9gu03KlKOAFB9YPMlBJPOaHMIifuRtxxZ28_sLvNVA1kbm22jaL9WBroRz6QJrDx_HskMbMFwT" />
        <div
          class="inline-block h-10 w-10 rounded-full bg-surface-container-highest items-center justify-center text-[10px] font-bold ring-2 ring-white">
          +14</div>
      </div>
    </div>

    <!-- Incidents Card -->
    <div
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
    </div>
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
        <?php foreach ($notifications as $notification): ?>
          <div class="relative pl-10 flex flex-col gap-1">
            <div
              class="absolute left-0 top-1 w-6 h-6 rounded-full <?= $notification['color'][0] ?> flex items-center justify-center z-10 shadow-sm">
              <span
                class="material-symbols-outlined text-[14px] text-<?= $notification['color'][1] ?>"
                style="font-variation-settings: 'FILL' 1;">
                <?= $notification['type'] ?>
              </span>
            </div>
            <p class="text-sm font-headline font-bold <?= $notification['color'][1] ?>">
              <?= $notification['title'] ?>
            </p>
            <p class="text-xs text-on-surface-variant font-mono">
              <?= $notification['text'] ?>
            </p>
          </div>
        <?php endforeach; ?>
      </div>

      <button
        class="w-full mt-8 py-3 border border-outline-variant/30 rounded-lg text-xs font-bold uppercase tracking-widest text-primary hover:bg-primary hover:text-white transition-all">
        Historique Complet
      </button>
    </div>
  </div>
</main>