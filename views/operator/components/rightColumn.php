<section class="flex flex-col">

  <!-- Récap véhicule courant -->
  <div class="flex flex-col lg:flex-row gap-8 items-center justify-center px-8 py-10 border-b border-outline-variant/20 bg-surface">

    <!-- Plaque d'immat -->
    <div class="flex flex-col items-center gap-5">
      <div class="relative">
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/25 p-3 shadow-lg transition-transform duration-300 hover:scale-[1.02]">
          <div class="flex h-2.5 w-full mb-3 overflow-hidden rounded-sm">
            <div class="w-1/2 bg-brand-indigo"></div>
            <div class="w-1/2 bg-secondary-container"></div>
          </div>
          <div class="px-8 py-6 border-[3px] border-primary rounded-xl flex items-center justify-center min-w-65 lg:min-w-[320px]">
            <span id="disp-immat"
              class="font-mono text-center text-[46px] lg:text-[58px] font-extrabold tracking-[0.14em] text-primary leading-none transition-all duration-200">
              ·&nbsp;·&nbsp;·
            </span>
          </div>
        </div>
        <div class="absolute -top-3 -right-3 w-8 h-8 bg-secondary rotate-45 flex items-center justify-center shadow">
          <span class="material-symbols-outlined -rotate-45 text-on-secondary text-xl"
            style="font-variation-settings:'FILL' 1;">directions_car</span>
        </div>
      </div>
      <!-- Badge type véhicule -->
      <div id="disp-type-badge"
        class="flex items-center gap-2 bg-surface-container px-5 py-2 rounded-full border border-outline-variant/30 text-on-surface-variant">
        <span class="material-symbols-outlined text-sm">commute</span>
        <span id="disp-type" class="font-headline font-bold text-xs uppercase tracking-wider">—</span>
      </div>
    </div>

    <!-- Montant + mode -->
    <div class="flex flex-col items-center lg:items-start gap-4 text-center lg:text-left">
      <div>
        <p class="text-[10px] uppercase tracking-[0.16em] text-on-surface-variant/70 font-bold mb-1">Montant dû</p>
        <div class="flex items-end gap-2">
          <span id="disp-montant" class="font-mono text-[68px] lg:text-[84px] font-extrabold text-secondary leading-none tracking-tighter transition-all duration-200">—</span>
          <span id="disp-fcfa" class="hidden font-mono text-2xl font-bold text-secondary/60 mb-2">FCFA</span>
        </div>
      </div>
      <div class="hidden lg:block w-full h-px bg-outline-variant/20"></div>
      <div id="disp-mode-wrap" class="hidden items-center gap-2 px-4 py-2 rounded-full border text-xs font-headline font-bold uppercase tracking-wider">
        <span id="disp-mode-icon" class="material-symbols-outlined text-base" style="font-variation-settings:'FILL' 1;">wallet</span>
        <span id="disp-mode-label">—</span>
      </div>
    </div>
  </div>

  <!-- Historique passages -->
  <div class="flex-1 px-8 py-7 bg-surface-container-low/30">
    <div class="flex items-center justify-between mb-5">
      <div class="flex items-center gap-3">
        <h3 class="flex items-center gap-2 font-headline font-extrabold text-on-surface text-sm uppercase tracking-widest">
          <span class="material-symbols-outlined text-secondary text-lg">history</span>
          Derniers passages
        </h3>
        <span class="bg-secondary-container text-secondary text-[10px] font-mono font-bold px-2.5 py-0.5 rounded-full">
          <?= count($passages) ?>
        </span>
      </div>
      <a href="operator/mon-dashboard"
        class="flex items-center gap-1 text-[11px] font-bold text-on-surface-variant hover:text-secondary transition-colors uppercase tracking-wider">
        Voir tout <span class="material-symbols-outlined text-sm">chevron_right</span>
      </a>
    </div>

    <?php if (empty($passages)): ?>
      <div class="flex flex-col items-center justify-center py-20 text-on-surface-variant/40 gap-3">
        <span class="material-symbols-outlined text-5xl">inbox</span>
        <p class="font-headline font-bold text-sm uppercase tracking-widest">Aucun passage enregistré</p>
      </div>
    <?php else: ?>
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 2xl:grid-cols-4 gap-4">
        <?php foreach ($passages as $i => $p): ?>
          <?php include 'cards/cardsPassageItem.php'; ?>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</section>