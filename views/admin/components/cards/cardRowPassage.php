<?php
$iconVehicule = [
  "Moto" => "motorcycle",
  "Voiture" => "directions_car",
  "Van/SUV" => "airport_shuttle",
  "Poids Lourd" => "local_shipping",
];

$color = [
  "Espèces" => "brand-success",
  "Espece" => "brand-success",
  "Mobile Money" => "inverse-primary",
  "Carte" => "brand-indigo",
  "Abonnement" => "secondary-container",
]
?>

<tr class="group hover:bg-surface-container-low transition-colors cursor-pointer">
  <td class="px-6 py-4">
    <div class="flex flex-col items-center">
      <div
        class="bg-surface-container-highest px-3 py-1 rounded inline-block license-plate-border">
        <span class="font-mono uppercase text-primary font-bold tracking-tighter text-sm">
          <?= $passage->immatriculation ?>
        </span>
      </div>
      <div>
        <div class="text-sm font-mono text-primary">
          <?= $passage->getCreatedAt()->format('H:i:s') ?>
        </div>
        <div class="text-[10px] text-slate-400 font-bold">
          <?= $passage->getCreatedAt()->format('d F Y') ?>
        </div>
      </div>
    </div>
  </td>
  <td class="px-6 py-4">
    <div class="flex items-center gap-3">
      <span class="material-symbols-outlined text-slate-400">
        <?= $iconVehicule[$passage->libelle] ?? "commute" ?>
      </span>
      <span class="text-sm font-medium text-on-surface">Classe <?= $passage->type_vehicule_id ?></span>
    </div>
  </td>
  <td class="px-6 py-4">
    <span class="text-[10px] uppercase font-bold text-slate-500 px-2 py-1 bg-surface rounded line-clamp-1">
      VOIE_<?= $passage->guichet_id ?>_<?= $passage->emplacement ?>
    </span>
  </td>
  <td class="px-6 py-4 text-right">
    <span class="font-mono font-bold <?= $passage->mode_paiement === "Abonnement" ? "text-amber-600" : "text-emerald-600" ?>">
      <?= $passage->getPrice() ?>
    </span>
  </td>
  <td class="px-6 py-4">
    <div class="flex items-center gap-2 text-<?= $color[$passage->mode_paiement] ?>">
      <span class="material-symbols-outlined text-sm">
        <?= $passage->getIcon() ?>
      </span>
      <span class="text-xs font-medium"><?= $passage->mode_paiement ?></span>
    </div>
  </td>
  <td class="px-6 py-4">
    <div
      class="flex items-center gap-2 px-2 py-1 bg-emerald-50 text-emerald-700 rounded-full w-fit">
      <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span>
      <span class="text-[10px] font-black uppercase">Confirmé</span>
    </div>
  </td>
  <td class="px-6 py-4 text-right">
    <span
      class="material-symbols-outlined text-slate-300 group-hover:text-primary transition-colors">chevron_right</span>
  </td>
</tr>