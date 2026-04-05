<?php
$title = "Paramètres";

?>

<main class="md:ml-72 pt-20 px-8 pb-12 relative">
  <!-- Header Section -->
  <div class="flex flex-col xl:flex-row justify-between items-center xl:items-end gap-2 mb-12">
    <div>
      <p class="text-secondary font-mono text-xs font-bold tracking-[0.3em] uppercase mb-2">
        Système
      </p>
      <h2 class="text-6xl font-['Outfit'] font-black tracking-tight text-primary">
        Configuration Système
      </h2>
      <p class="text-on-surface-variant text-lg mt-4 font-body leading-relaxed">
        Ajustez le cœur numérique de l'infrastructure du pont.
      </p>
    </div>
    <div class="flex items-center gap-3 pb-2">
      <span class="material-symbols-outlined text-green-500 text-sm" data-icon="check_circle"
        style="font-variation-settings: 'FILL' 1;">check_circle</span>
      <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">Auto-enregistré il y a
        2m</span>
    </div>
  </div>

  <div class="grid grid-cols-12 gap-8 items-start">
    <!-- Section 1: Active Lanes (Bento Card Large) -->
    <section
      class="col-span-12 xl:col-span-8 bg-surface-container-lowest monolith-shadow rounded-xl p-8 border border-outline-variant/10 relative overflow-hidden">
      <div class="absolute top-0 left-0 w-2 h-full bg-primary-container"></div>
      <div class="flex flex-col md:flex-row justify-between items-center mb-8">
        <div>
          <h3 class="text-xl font-bold text-primary">Passages Actifs</h3>
          <p class="text-sm text-on-surface-variant">Gérer la disponibilité des barrières physiques
          </p>
        </div>
        <button class="text-primary font-bold text-xs flex items-center gap-2 hover:underline">
          <span class="material-symbols-outlined text-sm" data-icon="refresh">refresh</span>
          RÉINITIALISER TOUT
        </button>
      </div>
      <div class="grid md:grid-cols-2 gap-4">
        <!-- Lane Card -->
        <div
          class="bg-surface-container-low p-5 rounded-lg border border-transparent hover:border-secondary-container transition-all group">
          <div class="flex justify-between items-start mb-4">
            <div class="bg-primary-container w-10 h-10 flex items-center justify-center rounded-md">
              <span class="mono-data text-white font-bold">V1</span>
            </div>
            <label class="relative inline-flex items-center cursor-pointer">
              <input checked="" class="sr-only peer" type="checkbox" />
              <div
                class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-secondary">
              </div>
            </label>
          </div>
          <h4 class="font-bold text-primary mb-1">Entrée Nord A</h4>
          <div class="flex items-center gap-2">
            <div class="w-2 h-2 bg-secondary rotate-45"></div>
            <span class="mono-data text-xs text-secondary font-bold">94% EFFICACITÉ</span>
          </div>
        </div>
        <div
          class="bg-surface-container-low p-5 rounded-lg border border-transparent hover:border-secondary-container transition-all">
          <div class="flex justify-between items-start mb-4">
            <div class="bg-primary-container w-10 h-10 flex items-center justify-center rounded-md">
              <span class="mono-data text-white font-bold">V2</span>
            </div>
            <label class="relative inline-flex items-center cursor-pointer">
              <input checked="" class="sr-only peer" type="checkbox" />
              <div
                class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-secondary">
              </div>
            </label>
          </div>
          <h4 class="font-bold text-primary mb-1">Entrée Nord B</h4>
          <div class="flex items-center gap-2">
            <div class="w-2 h-2 bg-secondary rotate-45"></div>
            <span class="mono-data text-xs text-secondary font-bold">ACTIF</span>
          </div>
        </div>
        <div
          class="bg-surface-container-low p-5 rounded-lg border border-transparent hover:border-secondary-container transition-all">
          <div class="flex justify-between items-start mb-4">
            <div class="bg-primary-container w-10 h-10 flex items-center justify-center rounded-md">
              <span class="mono-data text-white font-bold">V3</span>
            </div>
            <label class="relative inline-flex items-center cursor-pointer">
              <input class="sr-only peer" type="checkbox" />
              <div
                class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-secondary">
              </div>
            </label>
          </div>
          <h4 class="font-bold text-primary mb-1">Sortie Sud Alpha</h4>
          <div class="flex items-center gap-2">
            <div class="w-2 h-2 bg-slate-300 rotate-45"></div>
            <span class="mono-data text-xs text-slate-400 font-bold uppercase tracking-widest">Mode
              Maintenance</span>
          </div>
        </div>
        <div
          class="bg-surface-container-low p-5 rounded-lg border border-transparent hover:border-secondary-container transition-all">
          <div class="flex justify-between items-start mb-4">
            <div class="bg-primary-container w-10 h-10 flex items-center justify-center rounded-md">
              <span class="mono-data text-white font-bold">V4</span>
            </div>
            <label class="relative inline-flex items-center cursor-pointer">
              <input checked="" class="sr-only peer" type="checkbox" />
              <div
                class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-secondary">
              </div>
            </label>
          </div>
          <h4 class="font-bold text-primary mb-1">Contournement Poids Lourds</h4>
          <div class="flex items-center gap-2">
            <div class="w-2 h-2 bg-secondary rotate-45"></div>
            <span class="mono-data text-xs text-secondary font-bold">ACTIF</span>
          </div>
        </div>
      </div>
    </section>

    <!-- Section 2: Payment Modes (Vertical Card) -->
    <section
      class="col-span-12 xl:col-span-4 bg-primary monolith-shadow rounded-xl p-8 text-white relative overflow-hidden">
      <div class="absolute -right-12 -top-12 w-48 h-48 bg-secondary/10 rounded-full blur-3xl"></div>
      <div class="mb-8">
        <h3 class="text-xl font-bold font-['Outfit'] tracking-tight">Protocoles de Paiement</h3>
        <p class="text-xs text-slate-400 mt-1 uppercase tracking-widest">Configuration de la passerelle
        </p>
      </div>
      <div class="space-y-6">
        <div class="flex items-center justify-between group">
          <div class="flex items-center gap-4">
            <div class="w-10 h-10 bg-white/5 rounded-lg flex items-center justify-center">
              <span class="material-symbols-outlined text-secondary"
                data-icon="credit_card">credit_card</span>
            </div>
            <div>
              <p class="text-sm font-bold">Traitement par Carte</p>
              <p class="text-[10px] text-slate-500 mono-data">STRIPE_V4_SECURE</p>
            </div>
          </div>
          <label class="relative inline-flex items-center cursor-pointer">
            <input checked="" class="sr-only peer" type="checkbox" />
            <div
              class="w-10 h-5 bg-white/10 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-secondary-container">
            </div>
          </label>
        </div>
        <div class="flex items-center justify-between group">
          <div class="flex items-center gap-4">
            <div class="w-10 h-10 bg-white/5 rounded-lg flex items-center justify-center">
              <span class="material-symbols-outlined text-secondary" data-icon="nfc">nfc</span>
            </div>
            <div>
              <p class="text-sm font-bold">Badge Monolith (RFID)</p>
              <p class="text-[10px] text-slate-500 mono-data">INTERNAL_UHF_800</p>
            </div>
          </div>
          <label class="relative inline-flex items-center cursor-pointer">
            <input checked="" class="sr-only peer" type="checkbox" />
            <div
              class="w-10 h-5 bg-white/10 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-secondary-container">
            </div>
          </label>
        </div>
        <div class="flex items-center justify-between group">
          <div class="flex items-center gap-4">
            <div class="w-10 h-10 bg-white/5 rounded-lg flex items-center justify-center">
              <span class="material-symbols-outlined text-secondary"
                data-icon="qr_code_2">qr_code_2</span>
            </div>
            <div>
              <p class="text-sm font-bold">Portefeuilles Mobiles</p>
              <p class="text-[10px] text-slate-500 mono-data">APPLE_GOOGLE_PAY</p>
            </div>
          </div>
          <label class="relative inline-flex items-center cursor-pointer">
            <input checked="" class="sr-only peer" type="checkbox" />
            <div
              class="w-10 h-5 bg-white/10 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-secondary-container">
            </div>
          </label>
        </div>
        <div class="flex items-center justify-between group">
          <div class="flex items-center gap-4 opacity-50">
            <div class="w-10 h-10 bg-white/5 rounded-lg flex items-center justify-center">
              <span class="material-symbols-outlined text-slate-400"
                data-icon="payments">payments</span>
            </div>
            <div>
              <p class="text-sm font-bold">Espèces (Héritage)</p>
              <p class="text-[10px] text-slate-500 mono-data">DÉSACTIVÉ</p>
            </div>
          </div>
          <label class="relative inline-flex items-center cursor-pointer">
            <input class="sr-only peer" type="checkbox" />
            <div
              class="w-10 h-5 bg-white/10 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-secondary-container">
            </div>
          </label>
        </div>
      </div>
      <div class="mt-10 pt-6 border-t border-white/10">
        <p class="text-[10px] text-slate-400 uppercase tracking-widest mb-3">Terminal Prioritaire</p>
        <div class="bg-white/5 p-4 rounded-lg flex items-center justify-between border border-white/5">
          <span class="mono-data font-bold text-sm">TERMINAL_B_01</span>
          <span class="material-symbols-outlined text-xs"
            data-icon="chevron_right">chevron_right</span>
        </div>
      </div>
    </section>

    <!-- Section 3: General Settings (Flat Editorial Style) -->
    <section
      class="col-span-12 xl:col-span-7 bg-surface-container-low monolith-shadow rounded-xl p-10 border border-outline-variant/10">
      <h3 class="text-2xl font-bold text-primary font-['Outfit'] mb-8">Métadonnées &amp; Image de marque
      </h3>
      <div class="grid grid-cols-2 gap-x-12 gap-y-8">
        <div class="col-span-2">
          <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Nom
            de l'Identité du Pont</label>
          <input
            class="w-full bg-white border-outline-variant/30 rounded-md px-4 py-3 focus:border-secondary-container focus:ring-0 text-sm font-semibold transition-all"
            type="text" value="Pont de Péage Monolith Sud" />
        </div>
        <div>
          <label
            class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Devise
            Principale</label>
          <div class="relative">
            <select
              class="w-full bg-white border-outline-variant/30 rounded-md px-4 py-3 appearance-none focus:border-secondary-container focus:ring-0 text-sm font-semibold mono-data">
              <option>FCFA (XOF)</option>
              <option>EUR (€)</option>
              <option>USD ($)</option>
              <option>GBP (£)</option>
            </select>
            <span
              class="material-symbols-outlined absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400"
              data-icon="expand_more">expand_more</span>
          </div>
        </div>
        <div>
          <label
            class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Mode
            de Fonctionnement</label>
          <div class="relative">
            <select
              class="w-full bg-white border-outline-variant/30 rounded-md px-4 py-3 appearance-none focus:border-secondary-container focus:ring-0 text-sm font-semibold">
              <option>Commercial Standard</option>
              <option>Équilibrage Charge de Pointe</option>
              <option>VIP / Priorité Uniquement</option>
            </select>
            <span
              class="material-symbols-outlined absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400"
              data-icon="expand_more">expand_more</span>
          </div>
        </div>
        <div class="col-span-2">
          <label
            class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Téléchargement
            du Logo Global</label>
          <div
            class="border-2 border-dashed border-outline-variant/40 rounded-lg p-8 flex flex-col items-center justify-center bg-white/50 hover:bg-white hover:border-secondary transition-all cursor-pointer group">
            <span
              class="material-symbols-outlined text-4xl text-slate-300 group-hover:text-secondary mb-3 transition-colors"
              data-icon="cloud_upload">cloud_upload</span>
            <p class="text-xs font-bold text-slate-500 group-hover:text-primary transition-colors">
              GLISSER &amp; DÉPOSER LOGO VECTORIEL</p>
            <p class="text-[10px] text-slate-400 mt-1">SVG ou PNG haute résolution (min. 1024px)</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Section 4: Backups & System Integrity -->
    <section class="col-span-12 xl:col-span-5 space-y-8">
      <!-- Backup Card -->
      <div
        class="bg-surface-container-highest monolith-shadow rounded-xl p-8 border border-outline-variant/20">
        <div class="flex items-center gap-4 mb-6">
          <div class="w-12 h-12 bg-white rounded-lg flex items-center justify-center shadow-sm">
            <span class="material-symbols-outlined text-primary"
              data-icon="database">database</span>
          </div>
          <div>
            <h3 class="font-bold text-primary">Intégrité du Système</h3>
            <p class="text-xs text-on-surface-variant">Dernière sauvegarde : Aujourd'hui, 04:00 AM
            </p>
          </div>
        </div>
        <div class="space-y-4 mb-8">
          <div class="flex justify-between items-center text-sm">
            <span class="text-slate-600 font-medium">Planification Auto.</span>
            <span class="mono-data font-bold text-primary">TOUTES LES 6H</span>
          </div>
          <div class="flex justify-between items-center text-sm">
            <span class="text-slate-600 font-medium">Sync. Hors Site</span>
            <div class="flex items-center gap-2">
              <div class="w-1.5 h-1.5 bg-green-500 rounded-full"></div>
              <span class="mono-data font-bold text-primary">ACTIF</span>
            </div>
          </div>
        </div>
        <div class="grid grid-cols-2 gap-3">
          <button
            class="bg-primary text-white py-3 rounded-md font-bold text-xs hover:bg-primary-container transition-all flex items-center justify-center gap-2">
            <span class="material-symbols-outlined text-sm" data-icon="backup">backup</span>
            SYNC. MANUELLE
          </button>
          <button
            class="bg-transparent border border-primary text-primary py-3 rounded-md font-bold text-xs hover:bg-slate-50 transition-all">
            ARCHIVE LOGS
          </button>
        </div>
      </div>
      <!-- Security Alert -->
      <div class="bg-error-container/20 border-l-4 border-error p-6 rounded-lg flex items-start gap-4">
        <span class="material-symbols-outlined text-error" data-icon="warning"
          style="font-variation-settings: 'FILL' 1;">warning</span>
        <div>
          <p class="text-xs font-bold text-error uppercase tracking-widest mb-1">Application de la
            Sécurité</p>
          <p class="text-sm text-on-error-container">Mise à jour critique disponible pour le
            contrôleur de la voie V3. Prévue pour demain 03:00.</p>
        </div>
      </div>
    </section>
  </div>
</main>


<!-- Success Toast (Floating) -->
<div
  class="fixed bottom-10 right-10 bg-primary-container text-white py-4 px-6 rounded-xl monolith-shadow gold-glow flex items-center gap-4 z-60">
  <div class="bg-secondary p-1.5 rounded-full flex items-center justify-center">
    <span class="material-symbols-outlined text-white text-sm" data-icon="check"
      style="font-variation-settings: 'FILL' 1;">check</span>
  </div>
  <div>
    <p class="text-sm font-bold">Synchronisation Réussie</p>
    <p class="text-[10px] text-slate-400 mono-data">REF_ID: MON-992-TX-P</p>
  </div>
  <button class="ml-4 text-slate-500 hover:text-white transition-colors">
    <span class="material-symbols-outlined text-lg" data-icon="close">close</span>
  </button>
</div>