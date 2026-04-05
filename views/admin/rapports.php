<?php
$title = "Rapports";

?>

<!-- Overlay -->
<div id="sidebar-overlay"
  class="fixed inset-0 bg-black/20 backdrop-blur-sm z-40 opacity-0 pointer-events-none transition-opacity duration-300">
</div>

<!-- Sidebar offcanvas -->
<aside id="sidebar"
  class="fixed top-0 left-0 h-full w-80 bg-surface-container-low z-50 flex flex-col gap-8 p-6 overflow-y-auto shadow-2xl -translate-x-full transition-transform duration-300 ease-in-out">

  <!-- Header du sidebar -->
  <div class="flex items-center justify-between pt-4">
    <h2 class="font-headline font-bold text-primary text-lg">Générateur de Rapport</h2>
    <button id="sidebar-close" class="p-1.5 rounded-md hover:bg-surface-container-high transition-all">
      <span class="material-symbols-outlined text-slate-500 text-xl">close</span>
    </button>
  </div>

  <p class="text-on-surface-variant text-xs leading-relaxed -mt-4">Configurez les paramètres pour
    générer votre analyse d'infrastructure détaillée.</p>

  <!-- Report Type -->
  <section>
    <label class="block text-[10px] font-bold uppercase tracking-widest text-slate-500 mb-3">Type de
      Rapport</label>
    <div class="flex flex-col gap-2">
      <button
        class="flex items-center gap-3 p-3 rounded-lg bg-surface-container-lowest shadow-sm border-l-4 border-secondary transition-all">
        <span class="material-symbols-outlined text-secondary">analytics</span>
        <span class="text-sm font-semibold text-primary">Revenus Mensuels</span>
      </button>
      <button
        class="flex items-center gap-3 p-3 rounded-lg hover:bg-surface-container-high transition-all text-slate-600">
        <span class="material-symbols-outlined">traffic</span>
        <span class="text-sm font-medium">Flux de Trafic</span>
      </button>
      <button
        class="flex items-center gap-3 p-3 rounded-lg hover:bg-surface-container-high transition-all text-slate-600">
        <span class="material-symbols-outlined">construction</span>
        <span class="text-sm font-medium">Maintenance &amp; Capteurs</span>
      </button>
    </div>
  </section>

  <!-- Date Picker Presets -->
  <section>
    <label class="block text-[10px] font-bold uppercase tracking-widest text-slate-500 mb-3">Période
      Temporelle</label>
    <div class="grid grid-cols-2 gap-2 mb-3">
      <button class="py-2 px-3 bg-primary text-white rounded text-xs font-bold">Derniers 30j</button>
      <button
        class="py-2 px-3 bg-surface-container-lowest text-primary rounded text-xs font-medium shadow-sm">Trimestre</button>
      <button
        class="py-2 px-3 bg-surface-container-lowest text-primary rounded text-xs font-medium shadow-sm">Année
        2024</button>
      <button
        class="py-2 px-3 bg-surface-container-lowest text-primary rounded text-xs font-medium shadow-sm">Personnalisé</button>
    </div>
    <div class="relative">
      <input
        class="w-full bg-white border-none rounded-md text-xs py-2.5 pl-9 pr-4 shadow-sm text-primary font-mono focus:ring-2 focus:ring-secondary-container"
        type="text" value="01/05/2024 - 31/05/2024" />
      <span
        class="material-symbols-outlined absolute left-3 top-2 text-slate-400 text-sm">calendar_today</span>
    </div>
  </section>

  <!-- Lane Filters -->
  <section>
    <label class="block text-[10px] font-bold uppercase tracking-widest text-slate-500 mb-3">Filtrage
      des Voies</label>
    <div class="space-y-2">
      <label class="flex items-center justify-between p-2 rounded hover:bg-white/50 cursor-pointer">
        <span class="text-xs font-medium text-slate-700">Toutes les voies</span>
        <input checked="" class="rounded text-primary focus:ring-0 w-4 h-4" type="checkbox" />
      </label>
      <label class="flex items-center justify-between p-2 rounded hover:bg-white/50 cursor-pointer">
        <span class="text-xs font-medium text-slate-700">Voies Nord (1-4)</span>
        <input class="rounded text-primary focus:ring-0 w-4 h-4" type="checkbox" />
      </label>
      <label class="flex items-center justify-between p-2 rounded hover:bg-white/50 cursor-pointer">
        <span class="text-xs font-medium text-slate-700">Voies Sud (5-8)</span>
        <input class="rounded text-primary focus:ring-0 w-4 h-4" type="checkbox" />
      </label>
    </div>
  </section>

  <!-- Export Feedback -->
  <div class="mt-auto pt-6 border-t border-slate-200">
    <div class="flex justify-between items-end mb-2">
      <span class="text-[10px] font-bold uppercase text-slate-400">Préparation PDF</span>
      <span class="text-xs font-mono font-bold text-primary">85%</span>
    </div>
    <div class="h-1 w-full bg-slate-200 rounded-full overflow-hidden">
      <div class="h-full bg-secondary-container w-[85%]"></div>
    </div>
  </div>
</aside>

<main class="md:ml-60 pt-20 relative overflow-hidden">
  <!-- Document Preview Panel -->
  <section class="w-full bg-surface-container overflow-y-auto h-[calc(100vh-5rem)] p-12 flex flex-col items-center">
    <div class="flex w-full max-w-4xl justify-between items-center mb-8">
      <div class="flex gap-4">
        <!-- Bouton toggle sidebar -->
        <button id="sidebar-toggle"
          class="bg-surface-container-lowest border border-slate-200 px-4 py-2.5 rounded-lg flex items-center gap-2 text-primary font-bold shadow-sm hover:bg-surface-container-high transition-all">
          <span class="material-symbols-outlined text-lg">tune</span>
          Paramètres
        </button>
        <button
          class="bg-primary text-white px-6 py-2.5 rounded-lg flex items-center gap-2 font-bold transition-transform active:scale-95 shadow-lg shadow-primary/20">
          <span class="material-symbols-outlined text-secondary-container"
            style="font-variation-settings: 'FILL' 1;">picture_as_pdf</span>
          Générer PDF
        </button>
        <button
          class="bg-transparent border-2 border-primary text-primary px-6 py-2.5 rounded-lg flex items-center gap-2 font-bold hover:bg-primary hover:text-white transition-all">
          <span class="material-symbols-outlined">csv</span>
          Exporter CSV
        </button>
      </div>
      <div class="flex gap-2">
        <button class="p-2 bg-white rounded-md shadow-sm text-slate-500 hover:text-primary">
          <span class="material-symbols-outlined">zoom_in</span>
        </button>
        <button class="p-2 bg-white rounded-md shadow-sm text-slate-500 hover:text-primary">
          <span class="material-symbols-outlined">zoom_out</span>
        </button>
        <button class="p-2 bg-white rounded-md shadow-sm text-slate-500 hover:text-primary">
          <span class="material-symbols-outlined">print</span>
        </button>
      </div>
    </div>

    <!-- A4 Preview -->
    <div class="w-[794px] min-h-[1123px] bg-white shadow-2xl relative p-16 overflow-hidden">
      <div class="watermark">APERÇU</div>

      <!-- Document Header -->
      <div class="flex justify-between items-start border-b-2 border-primary pb-8 mb-12">
        <div>
          <div class="flex items-center gap-2 mb-4">
            <div class="w-8 h-8 bg-primary flex items-center justify-center">
              <span class="material-symbols-outlined text-white text-lg">link</span>
            </div>
            <span class="font-headline font-black text-primary text-xl tracking-tighter">PÉAGE BRIDGE</span>
          </div>
          <h3 class="text-3xl font-headline font-extrabold text-primary mb-1">Rapport de Performance Financière</h3>
          <p class="text-slate-500 text-sm">Période : Mai 2024 | ID : RPT-4492-BX</p>
        </div>
        <div class="text-right">
          <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Généré le</p>
          <p class="font-mono text-sm text-primary font-bold">14/06/2024 10:42</p>
        </div>
      </div>

      <!-- Summary Grid -->
      <div class="grid grid-cols-3 gap-8 mb-12">
        <div class="bg-surface p-4 border-l-4 border-primary">
          <p class="text-[10px] font-bold text-slate-500 uppercase mb-1">Revenu Total</p>
          <p class="font-mono text-xl font-bold text-primary">816 601 238 FCFA</p>
        </div>
        <div class="bg-surface p-4 border-l-4 border-secondary">
          <p class="text-[10px] font-bold text-slate-500 uppercase mb-1">Volume Trafic</p>
          <p class="font-mono text-xl font-bold text-primary">482 091</p>
        </div>
        <div class="bg-surface p-4 border-l-4 border-on-primary-container">
          <p class="text-[10px] font-bold text-slate-500 uppercase mb-1">Taux de Passage</p>
          <p class="font-mono text-xl font-bold text-primary">99.8%</p>
        </div>
      </div>

      <!-- Chart Placeholder -->
      <div class="mb-12">
        <h4 class="font-headline font-bold text-sm uppercase tracking-widest text-slate-800 mb-6 flex items-center gap-2">
          <span class="w-2 h-2 bg-secondary rotate-45"></span>
          Analyse Hebdomadaire des Revenus
        </h4>
        <div
          class="h-64 bg-slate-50 rounded border border-dashed border-slate-200 flex items-end justify-between px-10 pb-6 relative">
          <div class="absolute inset-0 flex flex-col justify-between p-6 opacity-20">
            <div class="border-b border-slate-300 w-full"></div>
            <div class="border-b border-slate-300 w-full"></div>
            <div class="border-b border-slate-300 w-full"></div>
            <div class="border-b border-slate-300 w-full"></div>
          </div>
          <div class="w-12 bg-primary rounded-t-sm h-[60%] z-10"></div>
          <div class="w-12 bg-primary rounded-t-sm h-[85%] z-10"></div>
          <div class="w-12 bg-secondary rounded-t-sm h-[70%] z-10"></div>
          <div class="w-12 bg-primary rounded-t-sm h-[40%] z-10"></div>
          <div class="w-12 bg-primary rounded-t-sm h-[95%] z-10"></div>
        </div>
      </div>

      <!-- Table Section -->
      <div>
        <h4 class="font-headline font-bold text-sm uppercase tracking-widest text-slate-800 mb-6 flex items-center gap-2">
          <span class="w-2 h-2 bg-secondary rotate-45"></span>
          Détails par Catégorie de Véhicule
        </h4>
        <table class="w-full">
          <thead class="bg-primary text-white">
            <tr>
              <th class="py-3 px-4 text-left text-[10px] font-bold uppercase tracking-widest">Catégorie</th>
              <th class="py-3 px-4 text-right text-[10px] font-bold uppercase tracking-widest">Volume</th>
              <th class="py-3 px-4 text-right text-[10px] font-bold uppercase tracking-widest">Tarif Moyen</th>
              <th class="py-3 px-4 text-right text-[10px] font-bold uppercase tracking-widest">Total Revenu</th>
            </tr>
          </thead>
          <tbody class="text-sm">
            <tr class="border-b border-slate-100">
              <td class="py-4 px-4 font-bold text-primary">Classe 1 (Léger)</td>
              <td class="py-4 px-4 text-right font-mono text-slate-600">340 122</td>
              <td class="py-4 px-4 text-right font-mono text-slate-600">1 640 FCFA</td>
              <td class="py-4 px-4 text-right font-mono font-bold text-primary">557 800 080 FCFA</td>
            </tr>
            <tr class="border-b border-slate-100 bg-slate-50/50">
              <td class="py-4 px-4 font-bold text-primary">Classe 2 (Utilitaire)</td>
              <td class="py-4 px-4 text-right font-mono text-slate-600">82 441</td>
              <td class="py-4 px-4 text-right font-mono text-slate-600">2 493 FCFA</td>
              <td class="py-4 px-4 text-right font-mono font-bold text-primary">205 525 413 FCFA</td>
            </tr>
            <tr class="border-b border-slate-100">
              <td class="py-4 px-4 font-bold text-primary">Classe 3 (Poids Lourds)</td>
              <td class="py-4 px-4 text-right font-mono text-slate-600">59 528</td>
              <td class="py-4 px-4 text-right font-mono text-slate-600">892 FCFA</td>
              <td class="py-4 px-4 text-right font-mono font-bold text-primary">53 275 745 FCFA</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Footer -->
      <div
        class="absolute bottom-16 left-16 right-16 flex justify-between items-center text-[10px] text-slate-400 font-bold uppercase tracking-widest border-t border-slate-100 pt-8">
        <div>© 2024 Monolith Infrastructure Management</div>
        <div>Page 1 sur 24</div>
        <div class="flex items-center gap-2">
          <span class="w-1 h-1 bg-secondary rounded-full"></span>
          Confidentiel
        </div>
      </div>
    </div>
    <div class="h-20"></div>
  </section>
</main>

<script>
  const toggle = document.getElementById('sidebar-toggle');
  const sidebar = document.getElementById('sidebar');
  const overlay = document.getElementById('sidebar-overlay');

  function openSidebar() {
    sidebar.classList.remove('-translate-x-full');
    overlay.classList.remove('opacity-0', 'pointer-events-none');
  }

  function closeSidebar() {
    sidebar.classList.add('-translate-x-full');
    overlay.classList.add('opacity-0', 'pointer-events-none');
  }

  toggle.addEventListener('click', openSidebar);
  overlay.addEventListener('click', closeSidebar);
  document.getElementById('sidebar-close').addEventListener('click', closeSidebar);
  document.addEventListener('keydown', e => e.key === 'Escape' && closeSidebar());
</script>