<?php
$color = [
  "Espece" => "brand-success",
  "Mobile Money" => "inverse-primary",
  "Carte" => "brand-indigo",
  "Abonnement" => "secondary-container",
]
?>

<div
  class="bg-surface-container-lowest p-3 rounded-lg flex items-center gap-4 border-l-4 border-<?= $color[$passage->mode_paiement] ?> shadow-sm">
  <div class="grow">
    <p class="font-mono font-bold uppercase text-sm text-primary">
      <?= $passage->immatriculation ?>
    </p>
    <p class="text-[10px] text-on-surface-variant font-bold uppercase">
      <?= $passage->getCreatedAt()->format('H:i:s') ?>
    </p>
  </div>
  <div class="font-mono flex flex-col items-center font-bold">
    <span class="material-symbols-outlined"><?= $passage->getIcon() ?></span>
    <span class=" text-<?= $color[$passage->mode_paiement] ?>">
      <?= $passage->getPrice() ?> <?= $passage->getPrice() === "Abonnement" ? "" : " FCFA" ?>
    </span>
  </div>
</div>