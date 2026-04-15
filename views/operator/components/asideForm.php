<aside class="border-r border-outline-variant/20 bg-surface-container-lowest xl:sticky xl:top-14 xl:h-[calc(100vh-3.5rem)] xl:overflow-y-auto flex flex-col">

  <!-- En-tête -->
  <div class="px-7 pt-7 pb-5 border-b border-outline-variant/20">
    <div class="flex items-center gap-3">
      <div class="w-10 h-10 rounded-xl bg-secondary-container flex items-center justify-center shrink-0">
        <span class="material-symbols-outlined text-secondary" style="font-variation-settings:'FILL' 1;">toll</span>
      </div>
      <div>
        <h2 class="font-headline font-extrabold text-on-surface text-base leading-none tracking-tight">
          Enregistrer un passage
        </h2>
        <p class="text-on-surface-variant text-[10px] font-mono tracking-widest uppercase mt-1">
          Poste #<?= $guichet->id ?> · <?= htmlspecialchars($guichet->emplacement) ?>
        </p>
      </div>
    </div>
  </div>

  <!-- Toast succès -->
  <?php if ($form_success): ?>
    <div class="mx-6 mt-5 flex items-center gap-3 bg-brand-success/10 border border-brand-success/30
                    text-brand-success rounded-xl px-4 py-3 anim-fade-in-up">
      <span class="material-symbols-outlined text-xl shrink-0" style="font-variation-settings:'FILL' 1;">check_circle</span>
      <span class="text-sm font-headline font-bold">Passage enregistré avec succès !</span>
    </div>
  <?php elseif ($form_error): ?>
    <div class="mx-6 mt-5 flex items-center gap-3 bg-error/10 border border-error/30 text-error rounded-xl px-4 py-3 anim-fade-in-up">
      <span class="material-symbols-outlined text-xl shrink-0" style="font-variation-settings:'FILL' 1;">error</span>
      <span class="text-sm font-headline font-bold"><?= htmlspecialchars($form_error) ?></span>
    </div>
  <?php endif; ?>

  <!-- Formulaire -->
  <?php if ($agent->is_en_cours()): ?>
    <form id="payment-form" action="" method="POST" autocomplete="off" novalidate
      class="flex flex-col gap-5 px-6 py-6 flex-1">

      <!-- Immatriculation -->
      <div class="flex flex-col gap-1.5">
        <label for="immatriculation"
          class="flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-[0.14em] text-on-surface-variant">
          <span class="material-symbols-outlined text-sm">pin</span>Immatriculation
        </label>
        <div class="relative">
          <input
            type="text" id="immatriculation" name="immatriculation"
            placeholder="AB-123-CD"
            value="<?= $form_success ? '' : htmlspecialchars($_POST['immatriculation'] ?? '') ?>"
            class="w-full bg-surface-container-low border-[1.5px] border-outline-variant rounded-xl px-4 py-3 pr-12 font-mono uppercase tracking-[0.18em] text-lg font-bold text-on-surface placeholder:font-normal placeholder:tracking-normal placeholder:text-on-surface-variant/40 placeholder:text-sm outline-none focus:border-secondary focus:ring-2 focus:ring-secondary/15 transition-all duration-200"
            required />
          <div class="absolute inset-y-0 right-4 flex items-center pointer-events-none gap-0.5">
            <div class="w-1 h-4 bg-brand-indigo/50 rounded-sm"></div>
            <div class="w-1 h-4 bg-secondary-container rounded-sm"></div>
          </div>
        </div>
      </div>

      <!-- Type de véhicule -->
      <div class="flex flex-col gap-1.5">
        <label for="type_vehicule_id"
          class="flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-[0.14em] text-on-surface-variant">
          <span class="material-symbols-outlined text-sm">commute</span>Type de véhicule
        </label>
        <div class="relative">
          <select id="type_vehicule_id" name="type_vehicule_id"
            class="w-full appearance-none bg-surface-container-low border-[1.5px] border-outline-variant rounded-xl px-4 py-3 pr-10 text-on-surface text-sm font-semibold outline-none focus:border-secondary focus:ring-2 focus:ring-secondary/15 cursor-pointer transition-all duration-200"
            required>
            <option value="">Toutes les classes</option>
            <?php foreach ($typevehicules as $type) : ?>
              <option data-tarif="<?= $type->price ?>" value="<?= $type->id ?>" <?= ($_GET['type'] ?? '') == $type->id ? 'selected' : '' ?>>
                Cat <?= $type->id ?> - <?= $type->libelle ?>
              </option>
            <?php endforeach ?>
          </select>
          <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-on-surface-variant pointer-events-none text-lg">
            expand_more
          </span>
        </div>
      </div>

      <!-- Tarif calculé (affiché dynamiquement) -->
      <div id="tarif-display"
        class="hidden flex-row items-center justify-between bg-secondary-container/25 border border-secondary/20 rounded-xl px-5 py-3">
        <span class="text-[10px] uppercase tracking-widest text-on-surface-variant font-bold">Montant dû</span>
        <span id="tarif-value" class="font-mono font-extrabold text-secondary text-2xl">— FCFA</span>
        <input type="hidden" name="montant" id="montant-hidden" value="0" />
      </div>

      <!-- Mode de paiement -->
      <div class="flex flex-col gap-2">
        <span class="flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-[0.14em] text-on-surface-variant">
          <span class="material-symbols-outlined text-sm">wallet</span>Mode de paiement
        </span>
        <div class="grid grid-cols-3 gap-2.5" role="radiogroup">

          <label id="card-Espèces" data-mode="Espèces"
            class="flex flex-col items-center justify-center gap-1 py-4 px-2 rounded-xl border-2 border-outline-variant bg-surface-container-low text-on-surface-variant cursor-pointer transition-all duration-200">
            <input type="radio" name="mode_paiement" value="Espèces" class="sr-only" required />
            <span class="material-symbols-outlined text-2xl" style="font-variation-settings:'FILL' 1;">payments</span>
            <span class="text-[10px] font-headline font-bold uppercase tracking-wider">Espèces</span>
          </label>

          <label id="card-Carte" data-mode="Carte"
            class="flex flex-col items-center justify-center gap-1 py-4 px-2 rounded-xl border-2 border-outline-variant bg-surface-container-low text-on-surface-variant cursor-pointer transition-all duration-200">
            <input type="radio" name="mode_paiement" value="Carte" class="sr-only" />
            <span class="material-symbols-outlined text-2xl" style="font-variation-settings:'FILL' 1;">credit_card</span>
            <span class="text-[10px] font-headline font-bold uppercase tracking-wider">Carte</span>
          </label>

          <label id="card-Abonnement" data-mode="Abonnement"
            class="flex flex-col items-center justify-center gap-1 py-4 px-2 rounded-xl border-2 border-outline-variant bg-surface-container-low text-on-surface-variant cursor-pointer transition-all duration-200">
            <input type="radio" name="mode_paiement" value="Abonnement" class="sr-only" />
            <span class="material-symbols-outlined text-2xl" style="font-variation-settings:'FILL' 1;">badge</span>
            <span class="text-[10px] font-headline font-bold uppercase tracking-wider">Abonnement</span>
          </label>

        </div>
      </div>

      <!-- Bloc Espèces (conditionnel) -->
      <div id="especes-block" class="hidden flex-col gap-3">
        <div class="flex flex-col gap-1.5">
          <label for="montant_donne"
            class="flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-[0.14em] text-on-surface-variant">
            <span class="material-symbols-outlined text-sm">account_balance_wallet</span>
            Montant donné par le client
          </label>
          <div class="relative">
            <input type="number" id="montant_donne" name="montant_donne"
              placeholder="0" min="0" step="50"
              class="w-full bg-surface-container-low border-[1.5px] border-outline-variant rounded-xl px-4 py-3 pr-16 font-mono text-xl font-bold text-on-surface outline-none focus:border-secondary focus:ring-2 focus:ring-secondary/15 transition-all duration-200" />
            <span class="absolute right-4 top-1/2 -translate-y-1/2 text-on-surface-variant font-bold text-xs pointer-events-none">FCFA</span>
          </div>
        </div>

        <!-- Rendu monnaie -->
        <div id="rendu-block"
          class="rounded-xl border-2 border-outline-variant bg-surface-container-low px-5 py-4 flex items-center justify-between transition-all duration-250">
          <div class="flex flex-col gap-0.5">
            <p class="text-[10px] uppercase tracking-widest font-bold text-on-surface-variant">Monnaie à rendre</p>
            <p id="rendu-value" class="font-mono font-extrabold text-2xl leading-none text-on-surface-variant">— FCFA</p>
          </div>
          <span id="rendu-icon" class="material-symbols-outlined text-4xl text-on-surface-variant/40"
            style="font-variation-settings:'FILL' 1;">swap_horiz</span>
        </div>
      </div>

      <!-- Spacer -->
      <div class="flex-1"></div>

      <!-- Incident -->
      <!-- <a href="operator/incident"
          class="flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl border border-error/25 bg-error/5 text-error text-xs font-headline font-bold uppercase tracking-wider hover:bg-error/10 transition-colors duration-200">
          <span class="material-symbols-outlined text-base">report</span>
          Signaler un incident
        </a> -->

      <div class="flex items-center gap-3">
        <!-- Soumettre -->
        <button type="submit" id="submit-btn"
          class="flex items-center justify-center gap-2 w-full py-4 rounded-xl bg-secondary text-on-secondary font-headline font-extrabold text-sm uppercase tracking-widest shadow-md hover:brightness-110 active:scale-[0.98] transition-all duration-200 disabled:opacity-40 disabled:cursor-not-allowed">
          <span class="material-symbols-outlined text-xl" style="font-variation-settings:'FILL' 1;">check_circle</span>
          Valider le passage
        </button>

        <button type="reset"
          class="flex items-center justify-center gap-2 w-14 py-4 rounded-xl bg-error text-on-error font-headline font-extrabold text-sm uppercase tracking-widest shadow-md hover:brightness-110 active:scale-[0.98] transition-all duration-200 disabled:opacity-40 disabled:cursor-not-allowed">
          <span class="material-symbols-outlined text-xl" style="font-variation-settings:'FILL' 1;">close</span>
        </button>
      </div>
    </form>
  <?php else: ?>
    <div class="flex-1 flex flex-col items-center justify-center px-6 text-center gap-4">
      <div class="w-16 h-16 rounded-full bg-error/10 flex items-center justify-center">
        <span class="material-symbols-outlined text-error text-4xl" style="font-variation-settings:'FILL' 1;">
          lock
        </span>
      </div>
      <div>
        <h3 class="font-headline font-extrabold text-on-surface text-lg uppercase tracking-tight">
          Saisie désactivée
        </h3>
        <p class="text-on-surface-variant text-sm mt-2">
          La voie est actuellement marquée comme <strong>fermée</strong>.
          Ouvrez la voie depuis votre gestion de shift pour enregistrer des passages.
        </p>
      </div>
      <a href="/operator/mon-dashboard"
        class="mt-4 px-6 py-3 bg-surface-container-high border border-outline-variant rounded-xl 
              text-xs font-bold uppercase tracking-widest hover:bg-surface-container-highest transition-colors">
        Aller à mon dashboard
      </a>
    </div>
  <?php endif; ?>

</aside>