<?php
$title = 'Passage';

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

?>

<main class="pt-16 mb-24 min-h-screen flex flex-col">
  <!-- Status Bar -->
  <div class="h-16 bg-brand-success flex items-center justify-between px-8 text-white shadow-lg">
    <div class="flex items-center gap-4">
      <?php if ($agent->is_en_cours()) : ?> 
        <span class="material-symbols-outlined text-3xl" style="font-variation-settings: 'FILL' 1;">
          door_open
        </span>
        <span class="font-headline font-extrabold text-2xl tracking-widest uppercase">VOIE OUVERTE</span>
      <?php else : ?>
        <span class="material-symbols-outlined text-3xl" style="font-variation-settings: 'FILL' 1;">
          door_front
        </span>
        <span class="font-headline font-extrabold text-2xl text-red-500 tracking-widest uppercase">VOIE FERMEE</span>
      <?php endif; ?>
    </div>
    <div class="flex items-center gap-6">
      <div class="hidden md:flex items-center gap-2">
        <div class="w-2.5 h-2.5 bg-white rounded-full animate-pulse"></div>
        <span class="font-mono text-sm opacity-90 tracking-tighter">SENS: <?= $guichet->emplacement ?></span>
      </div>
      <div class="bg-white/20 px-4 py-1 rounded-sm border border-white/30">
        <span class="font-mono text-sm font-bold uppercase tracking-widest">Poste #<?= $guichet->id ?></span>
      </div>
    </div>
  </div>

  <div class="grow flex flex-col p-8 gap-8 items-center xl:justify-center max-w-7xl mx-auto w-full">
    <!-- Central Display Section -->
    <div class="w-full grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
      <!-- Vehicle & Plate Identification -->
      <div class="flex flex-col items-center gap-6">
        <div class="relative group">
          <!-- License Plate Card -->
          <div
            class="bg-surface-container-lowest p-2 rounded-xl license-plate-shadow border border-primary/5 transform transition-transform group-hover:scale-[1.02]">
            <!-- Decorative Blue/Yellow Band -->
            <div class="flex h-3 w-full mb-2 overflow-hidden rounded-t-sm">
              <div class="w-1/2 bg-brand-indigo"></div>
              <div class="w-1/2 bg-secondary-container"></div>
            </div>
            <div
              class="px-10 py-8 border-4 border-primary rounded-lg flex items-center justify-center">
              <span
                class="font-mono text-center xl:text-[72px] text-[42px] font-bold tracking-[0.2em] text-primary leading-none">AB-123-CD</span>
            </div>
          </div>
          <!-- High-Contrast Status Diamond -->
          <div
            class="absolute -top-3 -right-3 w-8 h-8 bg-secondary-container rotate-45 flex items-center justify-center shadow-lg">
            <span class="material-symbols-outlined -rotate-45 text-primary text-xl"
              style="font-variation-settings: 'FILL' 1;">check</span>
          </div>
        </div>
        <!-- Category Badge -->
        <div class="flex items-center gap-4">
          <div
            class="flex items-center gap-3 bg-brand-indigo text-white px-8 py-3 rounded-full shadow-[0_8px_15px_rgba(61,58,140,0.3)]">
            <span class="material-symbols-outlined">motorcycle</span>
            <span class="font-headline font-bold text-xs uppercase tracking-wider">Moto (Cat 1)</span>
          </div>

          <a
            href="operator/incident"
            class="flex items-center gap-3 bg-error hover:bg-error/80 text-white px-8 py-3 rounded-full shadow-[0_8px_15px_rgba(61,58,140,0.3)]">
            <span class="material-symbols-outlined">error</span>
            <span class="font-headline font-bold text-xs uppercase tracking-wider">Signaler un incident</span>
          </a>
        </div>
      </div>
      <!-- Transaction Summary -->
      <div class="flex flex-col justify-center items-center lg:items-start space-y-4">
        <div class="space-y-0 text-center lg:text-left">
          <p class="text-on-surface-variant font-headline font-bold tracking-[0.15em] text-sm opacity-60">
            MONTANT DÛ</p>
          <h1 class="font-mono text-[96px] font-bold text-secondary leading-tight tracking-tighter">
            500 <span class="text-4xl -ml-4">FCFA</span>
          </h1>
        </div>
        <div class="w-full h-px bg-outline-variant/30"></div>
        <div class="grid grid-cols-2 gap-8 w-full py-4">
          <div>
            <p class="text-[10px] uppercase tracking-widest text-on-surface-variant font-bold">Origine
            </p>
            <p class="font-headline font-bold text-primary">BARI CENTRALE</p>
          </div>
          <div>
            <p class="text-[10px] uppercase tracking-widest text-on-surface-variant font-bold">Distance
            </p>
            <p class="font-mono font-bold text-primary">142.4 KM</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Payment Methods - Large Touch Targets -->
    <div class="w-full grid md:grid-cols-3 gap-6 mt-4">
      <button
        class="h-24 bg-brand-success text-white rounded-xl shadow-lg flex items-center justify-center gap-4 transition-all hover:brightness-110 active:scale-95 group">
        <div class="w-12 h-12 rounded-lg bg-white/20 flex items-center justify-center">
          <span class="material-symbols-outlined text-3xl">payments</span>
        </div>
        <span class="font-headline font-extrabold text-2xl tracking-wide uppercase">ESPÈCES</span>
      </button>
      <button
        class="h-24 bg-brand-indigo text-white rounded-xl shadow-lg flex items-center justify-center gap-4 transition-all hover:brightness-110 active:scale-95 group">
        <div class="w-12 h-12 rounded-lg bg-white/20 flex items-center justify-center">
          <span class="material-symbols-outlined text-3xl">credit_card</span>
        </div>
        <span class="font-headline font-extrabold text-2xl tracking-wide uppercase">CARTE</span>
      </button>
      <button
        class="h-24 bg-secondary-container text-primary rounded-xl shadow-lg flex items-center justify-center gap-4 transition-all hover:brightness-110 active:scale-95 group">
        <div class="w-12 h-12 rounded-lg bg-primary/10 flex items-center justify-center">
          <span class="material-symbols-outlined text-3xl">badge</span>
        </div>
        <span class="font-headline font-extrabold text-2xl tracking-wide uppercase">ABONNEMENT</span>
      </button>
    </div>
  </div>

  <!-- Footer: History Row -->
  <footer class="bg-surface-container-low px-8 py-4 mt-auto">
    <div class="flex items-center justify-between mb-4">
      <div class="flex items-center gap-4">
        <h3 class="font-headline font-bold text-primary-container text-sm flex items-center gap-2">
          <span class="material-symbols-outlined text-lg">history</span>
          DERNIERS PASSAGES
        </h3>
        <a href="#" class="flex items-center gap-2 hover:text-secondary font-bold">
          Voir tout
          <span class="material-symbols-outlined text-sm">keyboard_arrow_right</span>
        </a>
      </div>
      <span class="font-mono text-xs text-on-surface-variant">SESSION: 04H 12M</span>
    </div>
    <div class="grid md:grid-cols-5 gap-4">
      <!-- History Item 1 -->
      <div
        class="bg-surface-container-lowest p-3 rounded-lg flex items-center gap-4 border-l-4 border-brand-success shadow-sm">
        <div class="grow">
          <p class="font-mono font-bold text-sm text-primary">XY-991-ZZ</p>
          <p class="text-[10px] text-on-surface-variant font-bold uppercase">12:44 • ESPÈCES</p>
        </div>
        <span class="font-mono font-bold text-brand-success">500 FCFA</span>
      </div>
      <!-- History Item 2 -->
      <div
        class="bg-surface-container-lowest p-3 rounded-lg flex items-center gap-4 border-l-4 border-secondary-container shadow-sm">
        <div class="grow">
          <p class="font-mono font-bold text-sm text-primary">PL-450-MK</p>
          <p class="text-[10px] text-on-surface-variant font-bold uppercase">12:41 • ABON.</p>
        </div>
        <span class="font-mono font-bold text-secondary">00 FCFA</span>
      </div>
      <!-- History Item 3 -->
      <div
        class="bg-surface-container-lowest p-3 rounded-lg flex items-center gap-4 border-l-4 border-brand-indigo shadow-sm">
        <div class="grow">
          <p class="font-mono font-bold text-sm text-primary">TR-002-HH</p>
          <p class="text-[10px] text-on-surface-variant font-bold uppercase">12:38 • CARTE</p>
        </div>
        <span class="font-mono font-bold text-brand-indigo">1500 FCFA</span>
      </div>
      <!-- History Item 4 -->
      <div
        class="bg-surface-container-lowest p-3 rounded-lg flex items-center gap-4 border-l-4 border-brand-indigo shadow-sm">
        <div class="grow">
          <p class="font-mono font-bold text-sm text-primary">TR-002-HH</p>
          <p class="text-[10px] text-on-surface-variant font-bold uppercase">12:38 • CARTE</p>
        </div>
        <span class="font-mono font-bold text-brand-indigo">1500 FCFA</span>
      </div>
      <!-- History Item 5 -->
      <div
        class="bg-surface-container-lowest p-3 rounded-lg flex items-center gap-4 border-l-4 border-secondary-container shadow-sm">
        <div class="grow">
          <p class="font-mono font-bold text-sm text-primary">PL-450-MK</p>
          <p class="text-[10px] text-on-surface-variant font-bold uppercase">12:41 • ABON.</p>
        </div>
        <span class="font-mono font-bold text-secondary">00 FCFA</span>
      </div>
    </div>
  </footer>
</main>