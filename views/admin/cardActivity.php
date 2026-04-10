<div class="relative pl-10 flex flex-col gap-1">
  <div
    class="absolute left-0 top-1 w-6 h-6 rounded-full bg-secondary-container flex items-center justify-center z-10 shadow-sm">
    <span
      class="material-symbols-outlined text-[14px] text-primary"
      style="font-variation-settings: 'FILL' 1;">
      payments
    </span>
  </div>
  <p class="text-sm font-headline font-bold text-primary">
    Paiement Validé - Plaque
    <span class="uppercase">[<?= $payment->immatriculation ?>]</span>
  </p>
  <p class="text-xs text-on-surface-variant font-mono">
    <?= $payment->getCreatedAt()->format('d/m/Y H:i') ?> • <?= $payment->getPrice() ?> FCFA • Cabine <?= $payment->guichet_id . " - " . $payment->emplacement ?>
  </p>
</div>