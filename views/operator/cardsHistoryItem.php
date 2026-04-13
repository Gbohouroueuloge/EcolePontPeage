<?php
$color = [
  "Espece"       => ["border" => "border-brand-success",    "text" => "text-brand-success"],
  "Mobile Money" => ["border" => "border-inverse-primary",  "text" => "text-inverse-primary"],
  "Carte"        => ["border" => "border-brand-indigo",     "text" => "text-brand-indigo"],
  "Abonnement"   => ["border" => "border-secondary-container", "text" => "text-secondary-container"],
];

$c = $color[$passage->mode_paiement] ?? ["border" => "border-slate-300", "text" => "text-slate-500"];
?>

<div class="bg-surface-container-lowest p-3 rounded-lg flex items-center gap-4 border-l-4 <?= $c['border'] ?> shadow-sm">
  <div class="grow">
    <p class="font-mono font-bold uppercase text-sm text-primary">
      <?= $passage->immatriculation ?>
    </p>
    <p class="text-[10px] text-on-surface-variant font-bold uppercase">
      <?= $passage->getCreatedAt()->format('H:i:s') ?>
    </p>
  </div>
  <div class="font-mono flex flex-col items-center font-bold">
    <span class="material-symbols-outlined <?= $c['text'] ?>"><?= $passage->getIcon() ?></span>
    <span class="<?= $c['text'] ?>">
      <?= $passage->getPrice() ?><?= $passage->getPrice() === "Abonnement" ? "" : " FCFA" ?>
    </span>
  </div>
</div>