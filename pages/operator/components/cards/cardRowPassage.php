<?php
$iconVehicule = [
  "Moto" => "motorcycle",
  "Voiture" => "directions_car",
  "Van/SUV" => "airport_shuttle",
  "Poids Lourd" => "local_shipping",
];

$icons = [
  "Espece" => "payments",
  "Mobile Money" => "phone_android",
  "Carte" => "credit_card",
  "Abonnement" => "contactless",
];

$createdAt = new DateTime($passage->created_at) ?? null;


?>

<tr class="hover:bg-surface-container-low transition-colors">
  <td class="px-6 py-4 mono-data text-sm">
    <div class="flex flex-col items-center">
      <span class="text-sm text-secondary-container font-bold">
        <?= $createdAt->format('d F Y') ?>
      </span>
      <span class="mono-data text-sm text-outline"><?= $createdAt->format('H:i:s') ?></span>
    </div>
  </td>
  <td class="px-6 py-4">
    <div
      class="inline-flex items-center px-3 py-1 rounded bg-surface-container-highest border-l-2 border-primary-container">
      <span class="mono-data font-bold text-sm uppercase tracking-wider">
        <?= $passage->immatriculation ?>
      </span>
    </div>
  </td>
  <td class="px-6 py-4 text-sm font-medium">
    <span
      class="flex flex-col items-center gap-1 text-xs font-bold text-on-tertiary-fixed-variant">
      <span class="material-symbols-outlined text-sm"><?= $iconVehicule[$passage->libelle] ?? "commute" ?></span>
      <?= $passage->libelle ?>
    </span>
  </td>
  <td class="px-6 py-4">
    <span
      class="flex items-center gap-1 text-xs font-bold <?= $passage->mode_paiement === "Abonnement" ? "text-amber-600" : "text-on-tertiary-fixed-variant" ?>">
      <span class="material-symbols-outlined text-sm"><?= $icons[$passage->mode_paiement] ?></span>
      <?= $passage->mode_paiement ?>
    </span>
  </td>
  <td class="px-6 py-4 text-right mono-data font-bold <?= $passage->mode_paiement === "Abonnement" ? "text-amber-600" : "text-primary" ?>">
    <?= number_format($passage->montant, 0, ',', ' ') ?>
  </td>
</tr>