<?php
$title = 'Mon Shift';

?>

<main class="pt-24 px-4 md:px-8 mb-24 max-w-7xl mx-auto">
  <!-- Shift Info Banner -->
  <section
    class="mb-8 rounded-xl overflow-hidden bg-tertiary-container relative p-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
    <div class="flex items-center gap-4">
      <div class="p-3 bg-brand-indigo rounded-lg text-white">
        <span class="material-symbols-outlined text-3xl">schedule</span>
      </div>
      <div>
        <h2 class="text-white font-headline font-bold text-lg leading-tight">Shift Actuel : Matinée</h2>
        <p class="text-on-tertiary-container text-sm font-medium tracking-wide">DÉBUTÉ À 06:00 • 24 MAI 2024
        </p>
      </div>
    </div>
    <div class="flex gap-2">
      <span
        class="px-3 py-1 bg-secondary-container text-on-secondary-container rounded-md text-xs font-bold font-headline flex items-center gap-1">
        <span class="material-symbols-outlined text-sm animate-pulse" style="font-variation-settings: 'FILL' 1">fiber_manual_record</span>
        EN COURS
      </span>
      <span class="px-3 py-1 bg-brand-indigo text-white rounded-md text-xs font-bold text-center font-headline">
        VOIE 04A
      </span>
    </div>
  </section>
  <!-- KPI Cards Grid -->
  <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
    <!-- Passages Card -->
    <div class="bg-surface-container-lowest p-6 rounded-xl ghost-border flex flex-col gap-2">
      <div class="flex justify-between items-start">
        <span
          class="text-sm font-semibold text-on-surface-variant font-headline uppercase tracking-wider">Passages</span>
        <span class="material-symbols-outlined text-brand-indigo">directions_car</span>
      </div>
      <div class="mono-data text-4xl font-bold text-primary">1,284</div>
      <div class="flex items-center gap-1 text-xs text-secondary font-bold">
        <span class="material-symbols-outlined text-xs">trending_up</span>
        +12% vs hier
      </div>
    </div>
    <!-- Revenue Card -->
    <div class="bg-surface-container-lowest p-6 rounded-xl ghost-border flex flex-col gap-2">
      <div class="flex justify-between items-start">
        <span
          class="text-sm font-semibold text-on-surface-variant font-headline uppercase tracking-wider">Encaissé
          (FCFA)</span>
        <span class="material-symbols-outlined text-brand-indigo">payments</span>
      </div>
      <div class="mono-data text-4xl font-bold text-primary">642,000</div>
      <div class="flex items-center gap-1 text-xs text-secondary font-bold">
        <span class="material-symbols-outlined text-xs">account_balance_wallet</span>
        Espèces: 400k | Tag: 242k
      </div>
    </div>
    <!-- Incidents Card -->
    <div class="bg-surface-container-lowest p-6 rounded-xl ghost-border flex flex-col gap-2">
      <div class="flex justify-between items-start">
        <span
          class="text-sm font-semibold text-on-surface-variant font-headline uppercase tracking-wider">Incidents</span>
        <span class="material-symbols-outlined text-brand-indigo">report_problem</span>
      </div>
      <div class="mono-data text-4xl font-bold text-primary">03</div>
      <div class="flex items-center gap-1 text-xs text-error font-bold">
        <span class="material-symbols-outlined text-xs">warning</span>
        2 Non-paiements résolus
      </div>
    </div>
  </div>
  <!-- Two Column Layout: Table & Profile -->
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
    <!-- Compact History Table -->
    <div class="lg:col-span-2 space-y-6">
      <div class="flex items-center justify-between">
        <h3 class="font-headline font-extrabold text-2xl tracking-tight text-primary">Historique de passage
        </h3>
        <button class="text-sm font-bold text-secondary flex items-center gap-1">
          VOIR TOUT <span class="material-symbols-outlined text-sm">arrow_forward</span>
        </button>
      </div>
      <div class="bg-surface-container-lowest rounded-xl overflow-hidden ghost-border">
        <table class="w-full text-left border-collapse">
          <thead class="bg-surface-container-low">
            <tr>
              <th class="px-6 py-4 text-xs font-bold text-on-surface-variant uppercase font-headline">
                Heure</th>
              <th class="px-6 py-4 text-xs font-bold text-on-surface-variant uppercase font-headline">
                Véhicule</th>
              <th class="px-6 py-4 text-xs font-bold text-on-surface-variant uppercase font-headline">
                Cat.</th>
              <th class="px-6 py-4 text-xs font-bold text-on-surface-variant uppercase font-headline">
                Paiement</th>
              <th
                class="px-6 py-4 text-xs font-bold text-on-surface-variant uppercase font-headline text-right">
                Montant</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-surface-container">
            <tr class="hover:bg-surface-container-low transition-colors">
              <td class="px-6 py-4 mono-data text-sm">10:42:15</td>
              <td class="px-6 py-4">
                <div
                  class="inline-flex items-center px-3 py-1 rounded bg-surface-container-highest border-l-2 border-primary-container">
                  <span class="mono-data font-bold text-sm tracking-wider">AA-123-BB</span>
                </div>
              </td>
              <td class="px-6 py-4 text-sm font-medium">C1</td>
              <td class="px-6 py-4">
                <span
                  class="flex items-center gap-1 text-xs font-bold text-on-tertiary-fixed-variant">
                  <span class="material-symbols-outlined text-sm">contactless</span>
                  TAG
                </span>
              </td>
              <td class="px-6 py-4 text-right mono-data font-bold text-primary">500</td>
            </tr>
            <tr class="hover:bg-surface-container-low transition-colors">
              <td class="px-6 py-4 mono-data text-sm">10:41:02</td>
              <td class="px-6 py-4">
                <div
                  class="inline-flex items-center px-3 py-1 rounded bg-surface-container-highest border-l-2 border-primary-container">
                  <span class="mono-data font-bold text-sm tracking-wider">CK-908-XZ</span>
                </div>
              </td>
              <td class="px-6 py-4 text-sm font-medium">C2</td>
              <td class="px-6 py-4">
                <span class="flex items-center gap-1 text-xs font-bold text-secondary">
                  <span class="material-symbols-outlined text-sm">payments</span>
                  CASH
                </span>
              </td>
              <td class="px-6 py-4 text-right mono-data font-bold text-primary">1,500</td>
            </tr>
            <tr class="hover:bg-surface-container-low transition-colors">
              <td class="px-6 py-4 mono-data text-sm">10:38:44</td>
              <td class="px-6 py-4">
                <div
                  class="inline-flex items-center px-3 py-1 rounded bg-surface-container-highest border-l-2 border-primary-container">
                  <span class="mono-data font-bold text-sm tracking-wider">LT-442-DD</span>
                </div>
              </td>
              <td class="px-6 py-4 text-sm font-medium">C1</td>
              <td class="px-6 py-4">
                <span
                  class="flex items-center gap-1 text-xs font-bold text-on-tertiary-fixed-variant">
                  <span class="material-symbols-outlined text-sm">contactless</span>
                  TAG
                </span>
              </td>
              <td class="px-6 py-4 text-right mono-data font-bold text-primary">500</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <!-- Profile Section -->
    <div class="space-y-6">
      <h3 class="font-headline font-extrabold text-2xl tracking-tight text-primary">Mon Profil</h3>
      <div
        class="bg-surface-container-lowest p-8 rounded-xl ghost-border flex flex-col items-center text-center">
        <div class="relative mb-4">
          <img alt="Operator Photo"
            class="w-24 h-24 rounded-full border-4 border-surface-container shadow-md"
            data-alt="Detailed portrait of a senior toll bridge operator in a crisp uniform, soft studio lighting, professional headshot style"
            src="https://lh3.googleusercontent.com/aida-public/AB6AXuAxQdfn5eaEx3S6ACAmfsml6_4KSNO13-xonLdiKdAS2INOx8IEFrG6Tt6Z4A1H9DUQPE5egmRTHvOYo7_SGFgyj0crBOMrVKK-cP766jaW_BvbfZBe1zbPzWG2Rctp5siPL-SUpFB9Di1czjLMlKG6ODl1qI_dqcwUASe0EFGvt3UvUxflVXVv2BPBOHVSYbcsTkY4fFW0hc59tn44w1uce3xnSGtlTQkMHYqNU6UcLO8btdr-0OODY6T4iONS1O6D1m9hNy1MXJvV" />
          <div
            class="absolute bottom-0 right-0 p-2 bg-brand-indigo text-white rounded-full border-2 border-white">
            <span class="material-symbols-outlined text-sm">edit</span>
          </div>
        </div>
        <h4 class="font-headline font-bold text-xl text-primary">Abdoulaye Diallo</h4>
        <span
          class="mt-1 px-3 py-0.5 bg-surface-container-high text-on-surface-variant text-xs font-bold rounded-full uppercase tracking-widest">Opérateur
          Senior</span>
        <div class="w-full mt-8 space-y-4 text-left">
          <div class="space-y-1">
            <label
              class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest">Identifiant
              Agent</label>
            <div
              class="p-3 bg-surface-container-low rounded-lg mono-data text-sm font-bold text-primary">
              OPS-244-08</div>
          </div>
          <div class="space-y-1">
            <label class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest">Email
              Professionnel</label>
            <div class="p-3 bg-surface-container-low rounded-lg text-sm text-on-surface">
              a.diallo@tollops.infra</div>
          </div>
          <div class="space-y-1">
            <label class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest">Gare
              d'affectation</label>
            <div class="p-3 bg-surface-container-low rounded-lg text-sm text-on-surface font-medium">
              Gare de Thiaroye (Zone A)</div>
          </div>
        </div>
      </div>
      <!-- Action Buttons -->
      <div class="flex flex-col gap-3">
        <button
          class="w-full py-4 rounded-xl border-2 border-primary text-primary font-headline font-bold text-sm tracking-wide hover:bg-primary hover:text-white transition-all">
          Clôturer le shift
        </button>
        <button
          class="w-full py-4 rounded-xl bg-[#FF6B6B] text-white font-headline font-bold text-sm tracking-wide shadow-lg hover:brightness-95 transition-all">
          Se déconnecter
        </button>
      </div>
    </div>
  </div>
</main>