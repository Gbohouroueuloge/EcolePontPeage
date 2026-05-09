<?php
$iconVehicule = [
  "Moto"        => "motorcycle",
  "Voiture"     => "directions_car",
  "Van/SUV"     => "airport_shuttle",
  "Poids Lourd" => "local_shipping",
];

$colorMode = [
  "Espèces"     => "brand-success",
  "Espece"      => "brand-success",
  "Mobile Money" => "inverse-primary",
  "Carte"       => "brand-indigo",
  "Abonnement"  => "secondary-container",
];

$modeIcon = match (strtolower($passage->mode_paiement ?? '')) {
  'espèces', 'espece' => 'payments',
  'carte'             => 'credit_card',
  'abonnement'        => 'badge',
  'mobile money'      => 'phone_android',
  default             => 'toll',
};
?>

<tr class="group hover:bg-surface-container-low transition-colors cursor-pointer">
  <td class="px-6 py-4">
    <div class="flex flex-col items-center">
      <div
        class="bg-surface-container-highest px-3 py-1 rounded inline-block license-plate-border">
        <span class="font-mono uppercase text-primary font-bold tracking-tighter text-sm">
          <?= htmlspecialchars($passage->immatriculation) ?>
        </span>
      </div>
      <div>
        <div class="text-sm font-mono text-primary">
          <?= date('H:i:s', strtotime($passage->created_at)) ?>
        </div>
        <div class="text-[10px] text-slate-400 font-bold">
          <?= date('d F Y', strtotime($passage->created_at)) ?>
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
      VOIE_<?= $passage->guichet_id ?>_<?= htmlspecialchars($passage->emplacement) ?>
    </span>
  </td>
  <td class="px-6 py-4 text-right">
    <span class="font-mono font-bold <?= $passage->mode_paiement === "Abonnement" ? "text-amber-600" : "text-emerald-600" ?>">
      <?= number_format((float)$passage->montant, 0, ',', ' ') ?>
    </span>
  </td>
  <td class="px-6 py-4">
    <div class="flex items-center gap-2 text-<?= $colorMode[$passage->mode_paiement] ?? 'on-surface' ?>">
      <span class="material-symbols-outlined text-sm">
        <?= $modeIcon ?>
      </span>
      <span class="text-xs font-medium"><?= htmlspecialchars($passage->mode_paiement) ?></span>
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