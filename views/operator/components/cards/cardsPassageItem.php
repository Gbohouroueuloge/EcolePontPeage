<?php
$heureF  = date('H:i', strtotime($p->created_at));
$dateF   = date('d/m', strtotime($p->created_at));
$isToday = date('Y-m-d', strtotime($p->created_at)) === date('Y-m-d');
$montantF = number_format((float)$p->montant, 0, ',', ' ');
$delay   = $i * 40; // ms stagger
?>

<div class="group relative bg-surface-container-lowest rounded-2xl border border-outline-variant/20 hover:border-secondary/30 hover:shadow-md transition-all duration-200 overflow-hidden anim-fade-in-up" style="animation-delay: <?= $delay ?>ms">
  <!-- Bande colorée selon mode -->
  <div class="h-1 w-full <?= $modeTopBar($p->mode_paiement) ?>"></div>

  <div class="p-4 flex flex-col gap-3">

    <!-- Immat + horodatage -->
    <div class="flex items-start justify-between gap-2">
      <span class="font-mono font-extrabold text-primary text-sm tracking-[0.12em] uppercase leading-tight">
        <?= htmlspecialchars(strtoupper($p->immatriculation)) ?>
      </span>
      <div class="flex flex-col items-end shrink-0 text-on-surface-variant">
        <span class="font-mono text-xs font-bold"><?= $heureF ?></span>
        <span class="font-mono text-[9px] opacity-60"><?= $isToday ? "Auj." : $dateF ?></span>
      </div>
    </div>

    <!-- Type véhicule -->
    <div class="flex items-center gap-1.5 text-on-surface-variant/80">
      <span class="material-symbols-outlined text-sm">commute</span>
      <span class="text-xs font-semibold truncate"><?= htmlspecialchars($p->libelle) ?></span>
    </div>

    <!-- Divider -->
    <div class="h-px bg-outline-variant/20"></div>

    <!-- Montant + badge mode -->
    <div class="flex items-center justify-between gap-2">
      <div class="flex items-baseline gap-1">
        <span class="font-mono font-extrabold text-lg text-on-surface leading-none"><?= $montantF ?></span>
        <span class="font-mono text-xs font-bold text-on-surface-variant">FCFA</span>
      </div>
      <div class="flex items-center gap-1 px-2.5 py-1 rounded-full border text-[9px] font-headline font-bold uppercase tracking-wider shrink-0
                                <?= $modeBadgeCss($p->mode_paiement) ?>">
        <span class="material-symbols-outlined text-xs" style="font-variation-settings:'FILL' 1;">
          <?= $modeIcon($p->mode_paiement) ?>
        </span>
        <?= htmlspecialchars($p->mode_paiement) ?>
      </div>
    </div>

  </div>
</div>