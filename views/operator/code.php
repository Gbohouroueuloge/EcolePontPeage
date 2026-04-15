<script>
  (function() {
    /* ── DOM ──────────────────────────────────────── */
    const form = document.getElementById('payment-form');
    const immatInput = document.getElementById('immatriculation');
    const typeSelect = document.getElementById('type_vehicule_id');
    const tarifDisplay = document.getElementById('tarif-display');
    const tarifValue = document.getElementById('tarif-value');
    const montantH = document.getElementById('montant-hidden');
    const methodCards = document.querySelectorAll('[data-mode]');
    const especesBlock = document.getElementById('especes-block');
    const montantD = document.getElementById('montant_donne');
    const renduBlock = document.getElementById('rendu-block');
    const renduValue = document.getElementById('rendu-value');
    const renduIcon = document.getElementById('rendu-icon');
    const submitBtn = document.getElementById('submit-btn');

    // Affichage live
    const dispImmat = document.getElementById('disp-immat');
    const dispType = document.getElementById('disp-type');
    const dispMontant = document.getElementById('disp-montant');
    const dispFcfa = document.getElementById('disp-fcfa');
    const dispModeWrap = document.getElementById('disp-mode-wrap');
    const dispModeIcon = document.getElementById('disp-mode-icon');
    const dispModeLabel = document.getElementById('disp-mode-label');

    let currentTarif = 0;
    let selectedMode = '';

    /* ── Styles des cartes mode ──────────────────── */
    const MODE_STYLES = {
      'Espèces': {
        border: 'border-brand-success',
        bg: 'bg-brand-success/10',
        text: 'text-brand-success'
      },
      'Carte': {
        border: 'border-brand-indigo',
        bg: 'bg-brand-indigo/10',
        text: 'text-brand-indigo'
      },
      'Abonnement': {
        border: 'border-secondary',
        bg: 'bg-secondary-container',
        text: 'text-secondary'
      },
    };
    const BASE_CLASSES = ['border-outline-variant', 'bg-surface-container-low', 'text-on-surface-variant'];

    function setCardActive(card, active) {
      const s = MODE_STYLES[card.dataset.mode] || {};
      BASE_CLASSES.forEach(c => card.classList.toggle(c, !active));
      if (active && s.border) {
        card.classList.add(s.border, s.bg, s.text);
      } else if (!active && s.border) {
        card.classList.remove(s.border, s.bg, s.text);
      }
    }

    /* ── Live display ────────────────────────────── */
    function updateLive() {
      const immat = immatInput.value.trim().toUpperCase();
      dispImmat.textContent = immat || '·\u00A0·\u00A0·';

      const opt = typeSelect.options[typeSelect.selectedIndex];
      dispType.textContent = (opt && opt.value) ?
        (opt.textContent.split('—')[1]?.trim() || opt.textContent) :
        '—';

      if (currentTarif > 0) {
        dispMontant.textContent = currentTarif.toLocaleString('fr-FR');
        dispFcfa.classList.replace('hidden', 'inline');
      } else {
        dispMontant.textContent = '—';
        dispFcfa.classList.replace('inline', 'hidden');
      }

      if (selectedMode) {
        dispModeWrap.classList.replace('hidden', 'flex');
        dispModeLabel.textContent = selectedMode;
        const icons = {
          'Espèces': 'payments',
          'Carte': 'credit_card',
          'Abonnement': 'badge'
        };
        dispModeIcon.textContent = icons[selectedMode] || 'wallet';

        // Couleurs badge mode live
        dispModeWrap.className = dispModeWrap.className
          .replace(/\b(bg|text|border)-\S+/g, '').trim() +
          ' flex items-center gap-2 px-4 py-2 rounded-full border text-xs font-headline font-bold uppercase tracking-wider';

        const badgeMap = {
          'Espèces': 'bg-brand-success/10 text-brand-success border-brand-success/30',
          'Carte': 'bg-brand-indigo/10 text-brand-indigo border-brand-indigo/30',
          'Abonnement': 'bg-secondary-container text-secondary border-secondary/30',
        };
        (badgeMap[selectedMode] || '').split(' ').forEach(c => c && dispModeWrap.classList.add(c));
      } else {
        dispModeWrap.classList.replace('flex', 'hidden');
      }
    }

    /* ── Type véhicule ───────────────────────────── */
    typeSelect.addEventListener('change', function() {
      const opt = this.options[this.selectedIndex];
      const tarif = parseInt(opt.dataset.tarif, 10) || 0;
      currentTarif = tarif;
      montantH.value = tarif;

      if (tarif > 0) {
        tarifValue.textContent = tarif.toLocaleString('fr-FR') + ' FCFA';
        tarifDisplay.classList.replace('hidden', 'flex');
      } else {
        tarifDisplay.classList.replace('flex', 'hidden');
      }
      if (montantD.value) calcRendu();
      updateLive();
    });

    immatInput.addEventListener('input', updateLive);

    /* ── Mode paiement ───────────────────────────── */
    methodCards.forEach(card => {
      card.addEventListener('click', function() {
        selectedMode = this.dataset.mode;
        methodCards.forEach(c => setCardActive(c, false));
        setCardActive(this, true);
        this.querySelector('input[type="radio"]').checked = true;

        if (selectedMode === 'Espèces') {
          especesBlock.classList.replace('hidden', 'flex');
          especesBlock.classList.add('anim-slide-down');
          montantD.required = true;
          resetRendu();
        } else {
          especesBlock.classList.replace('flex', 'hidden');
          montantD.required = false;
          montantD.value = '';
          submitBtn.disabled = false;
        }
        updateLive();
      });
    });

    /* ── Rendu monnaie ───────────────────────────── */
    function resetRendu() {
      renduBlock.className = renduBlock.className
        .replace(/\b(border|bg)-(brand-success|error)\S*/g, '').trim() +
        ' border-outline-variant bg-surface-container-low';
      renduValue.className = renduValue.className.replace(/\btext-\S+/g, '') + ' text-on-surface-variant';
      renduIcon.className = renduIcon.className.replace(/\btext-\S+/g, '') + ' text-on-surface-variant/40';
      renduValue.textContent = '— FCFA';
      renduIcon.textContent = 'swap_horiz';
      submitBtn.disabled = false;
    }

    function setRenduState(state, text, icon) {
      const map = {
        ok: {
          border: 'border-brand-success',
          bg: 'bg-brand-success/10',
          text: 'text-brand-success',
          iconC: 'text-brand-success/50'
        },
        err: {
          border: 'border-error',
          bg: 'bg-error/10',
          text: 'text-error',
          iconC: 'text-error/50'
        },
      };
      const s = map[state];
      renduBlock.className = 'rounded-xl border-2 px-5 py-4 flex items-center justify-between transition-all duration-250 ' + s.border + ' ' + s.bg;
      renduValue.className = 'font-mono font-extrabold text-2xl leading-none ' + s.text;
      renduIcon.className = 'material-symbols-outlined text-4xl ' + s.iconC;
      renduValue.textContent = text;
      renduIcon.textContent = icon;
    }

    function calcRendu() {
      const donne = parseFloat(montantD.value) || 0;
      const rendu = donne - currentTarif;

      if (!donne || !currentTarif) {
        resetRendu();
        return;
      }

      if (rendu < 0) {
        setRenduState('err', 'Insuffisant — ' + Math.abs(rendu).toLocaleString('fr-FR') + ' FCFA manquant', 'warning');
        submitBtn.disabled = true;
      } else {
        setRenduState('ok', rendu.toLocaleString('fr-FR') + ' FCFA', rendu === 0 ? 'check_circle' : 'currency_exchange');
        submitBtn.disabled = false;
      }
    }

    montantD.addEventListener('input', calcRendu);

    /* ── Validation submit ───────────────────────── */
    form.addEventListener('submit', function(e) {
      const checked = form.querySelector('input[name="mode_paiement"]:checked');
      if (!checked) {
        e.preventDefault();
        return;
      }
      if (checked.value === 'Espèces' && (parseFloat(montantD.value) || 0) < currentTarif) {
        e.preventDefault();
        montantD.focus();
      }
    });

    /* ── Init ─────────────────────────────────────── */
    updateLive();
  })();
</script>