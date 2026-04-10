<?php
$iconVehicule = [
  "Moto" => "motorcycle",
  "Voiture" => "directions_car",
  "Van/SUV" => "airport_shuttle",
  "Poids Lourd" => "local_shipping",
];
?>

<div class="group bg-surface-container-low hover:bg-surface-container transition-colors rounded-xl p-4 flex flex-col gap-3 cursor-pointer shadow-sm border border-surface-container-highest mb-3">

  <div class="flex justify-between items-start">
    <div class="bg-surface-container-highest px-3 py-1.5 rounded-md inline-block license-plate-border shadow-sm">
      <span class="font-mono uppercase text-primary font-bold tracking-tighter text-base">
        <?= $passage->immatriculation ?>
      </span>
    </div>
    <div class="text-right">
      <div class="text-sm font-mono text-primary font-medium">
        <?= $passage->getCreatedAt()->format('H:i:s') ?>
      </div>
      <div class="text-[10px] text-slate-400 font-bold uppercase">
        <?= $passage->getCreatedAt()->format('d F Y') ?>
      </div>
    </div>
  </div>

  <div class="flex items-center justify-between mt-1">
    <div class="flex items-center gap-2">
      <span class="material-symbols-outlined text-slate-400 text-lg">
        <?= $iconVehicule[$passage->libelle] ?? "commute" ?>
      </span>
      <span class="text-sm font-medium text-on-surface">Classe <?= $passage->type_vehicule_id ?></span>
    </div>
    <span class="text-[10px] uppercase font-bold text-slate-500 px-2 py-1 bg-surface rounded max-w-30 truncate">
      VOIE_<?= $passage->guichet_id ?>_<?= $passage->emplacement ?>
    </span>
  </div>

  <hr class="border-surface-container-highest border-dashed my-1">

  <div class="flex items-end justify-between">

    <div class="flex flex-col gap-2">
      <div class="flex items-center gap-1.5">
        <span class="material-symbols-outlined text-secondary text-sm">
          <?= $passage->getIcon() ?>
        </span>
        <span class="text-xs font-medium text-slate-600"><?= $passage->mode_paiement ?></span>
      </div>
      <div class="flex items-center gap-1.5 px-2 py-1 bg-emerald-50 text-emerald-700 rounded-full w-fit">
        <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
        <span class="text-[9px] font-black uppercase tracking-wide">Confirmé</span>
      </div>
    </div>

    <div class="flex items-center gap-2">
      <span class="font-mono text-lg font-bold <?= $passage->mode_paiement === "Abonnement" ? "text-amber-600" : "text-emerald-600" ?>">
        <?= $passage->getPrice() ?>
      </span>
      <span class="material-symbols-outlined text-slate-300 group-hover:text-primary transition-colors group-hover:translate-x-1">
        chevron_right
      </span>
    </div>

  </div>
</div>