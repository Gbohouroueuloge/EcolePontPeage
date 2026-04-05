<?php
$title = "Abonnés";

?>

<main class="md:ml-72 pt-20 px-8 pb-12 relative">
  <section class="flex flex-col lg:flex-row justify-between items-center lg:items-end gap-2 mb-16">
    <div>
      <p class="text-secondary font-mono text-xs font-bold tracking-[0.3em] uppercase mb-2">
        Gestion
      </p>
      <h2 class="text-6xl font-['Outfit'] font-black tracking-tight text-primary">
        Gestion des Abonnés
      </h2>
      <p class="text-on-surface-variant text-lg mt-4 font-body leading-relaxed">
        Gérez les laissez-passer de transit actifs, surveillez les cycles de renouvellement et traitez les paiements logistiques d'infrastructure.
      </p>
    </div>

    <button
      class="bg-primary text-white px-8 py-4 rounded-lg font-headline font-bold flex items-center gap-3 transition-all hover:bg-secondary gold-glow">
      <span class="material-symbols-outlined" data-icon="add">add</span>
      Nouvel Enregistrement
    </button>
  </section>

  <!-- Grille de statistiques (Bento compact) -->
  <section class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-4 gap-6 mb-12">
    <div
      class="bg-surface-container-lowest p-6 rounded-lg shadow-sm flex flex-col justify-between h-32 relative overflow-hidden group">
      <div class="flex gap-2 items-center">
        <div class="group-hover:scale-110 transition-transform">
          <span class="material-symbols-outlined text-8xl" data-icon="groups">groups</span>
        </div>
        <span class="text-xs font-mono text-on-surface-variant font-bold uppercase tracking-widest">Abonnements Actifs</span>
      </div>

      <div class="flex items-baseline gap-2">
        <span class="text-3xl font-mono font-bold text-primary">12 842</span>
        <span class="text-xs text-green-600 font-bold">+4,2%</span>
      </div>
    </div>

    <div
      class="bg-surface-container-lowest p-6 rounded-lg shadow-sm flex flex-col justify-between h-32 relative overflow-hidden group">
      <div class="flex items-center gap-2">
        <div class="group-hover:scale-110 transition-transform text-secondary">
          <span class="material-symbols-outlined text-8xl">account_balance_wallet</span>
        </div>
        <span class="text-xs font-mono text-on-surface-variant font-bold uppercase tracking-widest">Revenus (Mois en cours)</span>
      </div>
      <div class="flex items-baseline gap-2">
        <span class="text-3xl font-mono font-bold text-primary">2,8M FCFA</span>
      </div>

    </div>

    <div
      class="bg-surface-container-lowest p-6 rounded-lg shadow-sm border-l-4 border-secondary-container flex flex-col justify-between h-32 relative overflow-hidden group">
      <div class="flex items-center gap-2">
        <div class="group-hover:scale-110 transition-transform text-secondary">
          <span class="material-symbols-outlined text-8xl" data-icon="schedule">schedule</span>
        </div>
        <span class="text-xs font-mono text-on-surface-variant font-bold uppercase tracking-widest">
          Renouvellements Critiques
        </span>
      </div>
      <div class="flex items-baseline gap-2">
        <span class="text-4xl font-mono font-bold text-secondary">147</span>
        <span class="material-symbols-outlined text-secondary text-sm"
          data-icon="priority_high">priority_high</span>
      </div>
    </div>

    <div
      class="bg-primary text-white p-6 rounded-lg shadow-sm flex flex-col justify-between h-32 relative overflow-hidden group">
      <div class="flex items-center gap-2">
        <div class="group-hover:scale-110 transition-transform">
          <span class="material-symbols-outlined text-8xl" data-icon="bolt">bolt</span>
        </div>
        <span class="text-xs font-mono text-slate-400 font-bold uppercase tracking-widest">
          Santé Système
        </span>
      </div>

      <div class="flex items-center gap-2">
        <span class="text-4xl font-mono font-bold">99,8%</span>
        <div class="w-3 h-3 bg-green-400 rounded-full animate-pulse shadow-[0_0_10px_rgba(74,222,128,0.5)]">
        </div>
      </div>
    </div>
  </section>

  <!-- Registres d'abonnements -->
  <section class="bg-surface-container-lowest rounded-lg overflow-hidden shadow-lg shadow-primary/5">
    <div class="px-8 py-6 flex justify-between items-center bg-surface-container-low/50">
      <h3 class="font-headline font-bold text-primary uppercase tracking-widest text-sm">Registres de Transit en Temps Réel</h3>
      <div class="flex gap-4">
        <button
          class="text-xs font-headline font-bold text-on-surface-variant flex items-center gap-2 hover:text-primary transition-colors">
          <span class="material-symbols-outlined text-sm" data-icon="filter_list">filter_list</span>
          Filtrer
        </button>
        <button
          class="text-xs font-headline font-bold text-on-surface-variant flex items-center gap-2 hover:text-primary transition-colors">
          <span class="material-symbols-outlined text-sm" data-icon="sort">sort</span> Trier
        </button>
      </div>
    </div>

    <table class="w-full text-left">
      <thead>
        <tr
          class="bg-surface-container-low/30 border-b border-outline-variant/10 text-[10px] font-mono uppercase text-on-surface-variant font-bold tracking-widest">
          <th class="px-8 py-4">Plaque Véhicule</th>
          <th class="px-8 py-4">Profil Conducteur</th>
          <th class="px-8 py-4">Type</th>
          <th class="px-8 py-4">Suivi Expiration</th>
          <th class="px-8 py-4 text-right">Actions</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-outline-variant/5">
        <!-- Ligne 1: État d'alerte -->
        <tr class="group hover:bg-surface-container-low/20 transition-colors">
          <td class="px-8 py-6">
            <div
              class="bg-surface-container-highest px-3 py-1.5 rounded-md border-l-[3px] border-primary-container inline-block">
              <span class="mono-data font-bold text-lg text-primary tracking-wide">FR 741 AA 92</span>
            </div>
          </td>
          <td class="px-8 py-6">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded bg-surface-container-high overflow-hidden">
                <img alt="Portrait Conducteur Professionnel"
                  class="w-full h-full object-cover grayscale group-hover:grayscale-0 transition-all"
                  data-alt="close-up portrait of a professional driver with a neutral expression in a sunlit cabin, modern lighting"
                  src="https://lh3.googleusercontent.com/aida-public/AB6AXuCN1598YyTQKku_TQJOvNeIwOyyKnlqUv-yhRtK99XSXI2Okt3azPt-6dKw07qYiFlSYib8u6LLtpkHVC8RHHJq_m79AxkRdN0cGsb3JcF0i6iWkfVjUExct0Z_e7KtBVm-0ShlfBHple8FrpsPMS_WW-CHySg8U3OC6Re2Y_R3P7FDjvKJT0e5JUVYQmvEc9cJHA_cH2MavGnyMzcZim18rStOUWhRXtHNCySwvBc0Wb9r0MHBdnQQI0FOdZOGEiQoyIaf8vv57FH8" />
              </div>
              <div>
                <p class="font-headline font-bold text-sm text-primary">Jean-Pierre Lefebvre</p>
                <p class="text-xs text-on-surface-variant mono-data">ID: INFRA-9821</p>
              </div>
            </div>
          </td>
          <td class="px-8 py-6">
            <span
              class="px-3 py-1 rounded-full text-[10px] font-headline font-bold bg-secondary-fixed text-on-secondary-fixed uppercase tracking-wider">Mensuel</span>
          </td>
          <td class="px-8 py-6">
            <div class="flex items-center gap-2 text-secondary font-bold text-sm mono-data">
              <span class="material-symbols-outlined text-base"
                data-icon="report_problem">report_problem</span>
              2 Jours Restants
            </div>
            <p class="text-[10px] text-on-surface-variant mt-1">Exp : 24 Oct 2023</p>
          </td>
          <td class="px-8 py-6 text-right">
            <button
              class="bg-primary text-white px-6 py-2 rounded-lg font-headline font-bold text-xs transition-all hover:bg-secondary gold-glow">Renouveler</button>
          </td>
        </tr>
        <!-- Ligne 2: État Standard -->
        <tr class="group hover:bg-surface-container-low/20 transition-colors">
          <td class="px-8 py-6">
            <div
              class="bg-surface-container-highest px-3 py-1.5 rounded-md border-l-[3px] border-primary-container inline-block">
              <span class="mono-data font-bold text-lg text-primary tracking-wide">BE 1-TXL-04</span>
            </div>
          </td>
          <td class="px-8 py-6">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded bg-surface-container-high overflow-hidden">
                <img alt="Portrait Conducteur Professionnel"
                  class="w-full h-full object-cover grayscale group-hover:grayscale-0 transition-all"
                  data-alt="portrait of a confident female driver wearing professional attire, bright daylight setting with soft focus background"
                  src="https://lh3.googleusercontent.com/aida-public/AB6AXuDmua8511NoDOF-2CIbeZmOP_jyPW2YUCBGmceQHkSEZ7oZSVsl9oyg1eGMbZ-xEGV1Ex1mkeFs2echO8CC7vfShKW7HZUUYZsI_i75UOmeCEez9B3oRs-C0mlwNNv8x5cSrwWfBzaiEnXXMUKCsYqufCWB_srnGZc-U-3lgi8OaLbyQPm4xrhzxbOXEfU0H8cPnp-Mo0V34NNGNYu79cm2peR4OMt8G0RkcszHYU8aDQTRpu2_0ovR5mHBaPSV28NQVFFh9fgSyA_o" />
              </div>
              <div>
                <p class="font-headline font-bold text-sm text-primary">Marie Vandermeer</p>
                <p class="text-xs text-on-surface-variant mono-data">ID: LOGI-4432</p>
              </div>
            </div>
          </td>
          <td class="px-8 py-6">
            <span
              class="px-3 py-1 rounded-full text-[10px] font-headline font-bold bg-primary text-white uppercase tracking-wider">Annuel</span>
          </td>
          <td class="px-8 py-6">
            <div class="flex items-center gap-2 text-primary font-bold text-sm mono-data">
              312 Jours Restants
            </div>
            <p class="text-[10px] text-on-surface-variant mt-1">Exp : 15 Sep 2024</p>
          </td>
          <td class="px-8 py-6 text-right">
            <button class="text-primary hover:text-secondary p-2 transition-all">
              <span class="material-symbols-outlined" data-icon="more_vert">more_vert</span>
            </button>
          </td>
        </tr>
        <!-- Ligne 3: État d'alerte -->
        <tr class="group hover:bg-surface-container-low/20 transition-colors">
          <td class="px-8 py-6">
            <div
              class="bg-surface-container-highest px-3 py-1.5 rounded-md border-l-[3px] border-primary-container inline-block">
              <span class="mono-data font-bold text-lg text-primary tracking-wide">DE HH SY 990</span>
            </div>
          </td>
          <td class="px-8 py-6">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded bg-surface-container-high overflow-hidden">
                <img alt="Portrait Conducteur Professionnel"
                  class="w-full h-full object-cover grayscale group-hover:grayscale-0 transition-all"
                  data-alt="close-up of a middle-aged male driver with glasses, professional aesthetic, soft indoor lighting"
                  src="https://lh3.googleusercontent.com/aida-public/AB6AXuCWeEeODWpW_DzluEfiQWAXoIJZkZzZVZXwdYurED_A5WtdibrC1gWXyc9VM4v_V4Rs4cCHMNDOjSqDxC0PgfPhZTgzVZRTikGD2BSyL9x6bkBhjMskElZh1TtlxACSpA8Zqeu921cBqjdJjG0OLqcNiUMP3oANMzxseQf_epIR3cuNU8Svoztzapr07q0Qz4B-pvy9j6CktXqrl_w86eKXoAHEUrkVOLmdq81LsCSvrC_Iums-iuSfQMUNkP-O32BLopvMn5iDg-x4" />
              </div>
              <div>
                <p class="font-headline font-bold text-sm text-primary">Hans Schneider</p>
                <p class="text-xs text-on-surface-variant mono-data">ID: INFRA-0021</p>
              </div>
            </div>
          </td>
          <td class="px-8 py-6">
            <span
              class="px-3 py-1 rounded-full text-[10px] font-headline font-bold bg-secondary-fixed text-on-secondary-fixed uppercase tracking-wider">Mensuel</span>
          </td>
          <td class="px-8 py-6">
            <div class="flex items-center gap-2 text-secondary font-bold text-sm mono-data">
              <span class="material-symbols-outlined text-base"
                data-icon="report_problem">report_problem</span>
              6 Jours Restants
            </div>
            <p class="text-[10px] text-on-surface-variant mt-1">Exp : 28 Oct 2023</p>
          </td>
          <td class="px-8 py-6 text-right">
            <button
              class="bg-primary text-white px-6 py-2 rounded-lg font-headline font-bold text-xs transition-all hover:bg-secondary gold-glow">Renouveler</button>
          </td>
        </tr>
        <!-- Ligne 4: État Standard -->
        <tr class="group hover:bg-surface-container-low/20 transition-colors">
          <td class="px-8 py-6">
            <div
              class="bg-surface-container-highest px-3 py-1.5 rounded-md border-l-[3px] border-primary-container inline-block">
              <span class="mono-data font-bold text-lg text-primary tracking-wide">IT BX 441 RT</span>
            </div>
          </td>
          <td class="px-8 py-6">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded bg-surface-container-high overflow-hidden">
                <img alt="Portrait Conducteur Professionnel"
                  class="w-full h-full object-cover grayscale group-hover:grayscale-0 transition-all"
                  data-alt="serious female fleet manager portrait, clean professional look, natural side lighting"
                  src="https://lh3.googleusercontent.com/aida-public/AB6AXuCRW4gOiDFuP-FRmQ-fm1ujEX2MeLuUaews3oZcNNfb4lgHu96AmAdIdu5w2I7kcAJQhPdLcYW3VibNbPRuoXJMT0ubzKIH3FOa4HHO5rxaIyoj3flJx86FzEzIym5T0IGhJXt5Oohzb0cQRwvktrV96dJs8fW8fG1gpdXh9iYZTNtlN7xc64Mg7tHFFjTv2WOVrhfNIW5zf84lfXYrFZbfNlcsbeB-fwa8Qwxu03YHIy2KPjhQ54L5Pdb0arUx-fPo2bikjfKl5i_P" />
              </div>
              <div>
                <p class="font-headline font-bold text-sm text-primary">Sofia Rossi</p>
                <p class="text-xs text-on-surface-variant mono-data">ID: LOGI-1192</p>
              </div>
            </div>
          </td>
          <td class="px-8 py-6">
            <span
              class="px-3 py-1 rounded-full text-[10px] font-headline font-bold bg-primary text-white uppercase tracking-wider">Annuel</span>
          </td>
          <td class="px-8 py-6">
            <div class="flex items-center gap-2 text-primary font-bold text-sm mono-data">
              142 Jours Restants
            </div>
            <p class="text-[10px] text-on-surface-variant mt-1">Exp : 04 Mar 2024</p>
          </td>
          <td class="px-8 py-6 text-right">
            <button class="text-primary hover:text-secondary p-2 transition-all">
              <span class="material-symbols-outlined" data-icon="more_vert">more_vert</span>
            </button>
          </td>
        </tr>
      </tbody>
    </table>

    <div class="px-8 py-6 bg-surface-container-low/20 flex justify-between items-center">
      <span class="text-xs text-on-surface-variant mono-data">Affichage 1-10 sur 12 842 enregistrements au
        total</span>
      <div class="flex gap-2">
        <button
          class="w-8 h-8 flex items-center justify-center rounded border border-outline-variant/30 text-primary hover:bg-surface-container-high transition-all">
          <span class="material-symbols-outlined text-sm" data-icon="chevron_left">chevron_left</span>
        </button>
        <button
          class="w-8 h-8 flex items-center justify-center rounded bg-primary text-white text-xs font-bold font-mono">1</button>
        <button
          class="w-8 h-8 flex items-center justify-center rounded border border-outline-variant/30 text-primary text-xs font-bold font-mono hover:bg-surface-container-high transition-all">2</button>
        <button
          class="w-8 h-8 flex items-center justify-center rounded border border-outline-variant/30 text-primary text-xs font-bold font-mono hover:bg-surface-container-high transition-all">3</button>
        <button
          class="w-8 h-8 flex items-center justify-center rounded border border-outline-variant/30 text-primary hover:bg-surface-container-high transition-all">
          <span class="material-symbols-outlined text-sm" data-icon="chevron_right">chevron_right</span>
        </button>
      </div>
    </div>
  </section>

  <!-- Fenêtre modale de confirmation de renouvellement -->
  <div class="fixed hidden inset-0 bg-primary/60 backdrop-blur-sm z-100 flex items-center justify-center">
    <div class="bg-surface-container-lowest w-full max-w-lg rounded-lg shadow-2xl overflow-hidden">
      <div class="bg-primary p-8 text-white">
        <div class="flex items-center gap-3 mb-2">
          <div class="w-2 h-2 bg-secondary transform rotate-45"></div>
          <span
            class="text-[10px] font-mono tracking-widest text-secondary-container">CONFIRMATION_REQUISE</span>
        </div>
        <h4 class="text-3xl font-brand font-black tracking-tight">Renouveler l'Abonnement</h4>
        <p class="text-slate-400 text-sm mt-2">Traitement des droits de passage d'infrastructure pour la plaque
          : FR 741 AA 92</p>
      </div>
      <div class="p-8">
        <div class="bg-surface-container-low p-6 rounded-lg mb-8">
          <div class="flex justify-between items-center mb-4">
            <span
              class="text-xs font-headline font-bold text-on-surface-variant uppercase tracking-widest">Plan
              Sélectionné</span>
            <span
              class="px-3 py-1 rounded bg-secondary-container text-on-secondary-container text-[10px] font-bold font-headline uppercase">Mensuel</span>
          </div>
          <div class="flex justify-between items-end border-t border-outline-variant/20 pt-4">
            <div>
              <p class="text-xs text-on-surface-variant mb-1">Total à payer</p>
              <p class="text-3xl font-mono font-bold text-primary">58 400 FCFA</p>
            </div>
            <p class="text-[10px] text-on-surface-variant mono-data">Valide jusqu'au 24 Nov 2023</p>
          </div>
        </div>
        <div class="flex gap-4">
          <button
            class="flex-1 border border-outline-variant text-primary py-4 rounded-lg font-headline font-bold text-sm hover:bg-surface-container-low transition-all">
            Annuler l'Action
          </button>
          <button
            class="flex-1 bg-primary text-white py-4 rounded-lg font-headline font-bold text-sm gold-glow hover:bg-secondary transition-all">
            Autoriser le Paiement
          </button>
        </div>
      </div>
    </div>
  </div>
</main>