<?php
$title = 'Caisse';
require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'operator/variables.php';
?>

<main class="pt-20 pb-24 px-4 min-h-screen max-w-4xl mx-auto flex flex-col">
  <!-- Summary Bar (Indigo Accent) -->
  <section
    class="bg-brand-indigo rounded-xl p-6 shadow-xl mb-6 flex flex-col md:flex-row justify-between items-center gap-4 border-l-[6px] border-secondary">
    <div class="flex items-center gap-6">
      <div class="bg-surface-container-lowest/10 p-3 rounded-lg border border-white/10">
        <span
          class="text-white/60 text-[10px] block font-label uppercase tracking-widest mb-1">Immatriculation</span>
        <span class="font-mono text-2xl text-white font-bold tracking-tight">AB-123-CD</span>
      </div>
      <div class="h-10 w-px bg-white/10 hidden md:block"></div>
      <div>
        <span
          class="text-white/60 text-[10px] block font-label uppercase tracking-widest mb-1">Catégorie</span>
        <span class="text-white font-headline font-extrabold text-xl">CLASSE 2</span>
      </div>
    </div>
    <div class="text-right">
      <span class="text-white/60 text-[10px] block font-label uppercase tracking-widest mb-1">Montant
        Dû</span>
      <span class="font-mono text-4xl text-secondary-container font-bold">5.500 <span
          class="text-lg">FCFA</span></span>
    </div>
  </section>
  <!-- POS Interface -->
  <div class="flex-1 grid grid-cols-1 md:grid-cols-12 gap-6 items-start">
    <!-- Payment Column -->
    <div class="md:col-span-12 lg:col-span-8 flex flex-col gap-6">
      <!-- Entry Display -->
      <div
        class="bg-surface-container-lowest rounded-xl p-8 shadow-[0_4px_30px_rgba(13,31,60,0.04)] border border-outline-variant/15 flex flex-col items-center">
        <span class="text-primary/40 font-label text-xs uppercase tracking-widest mb-4">Montant Saisi</span>
        <div class="text-[40px] font-mono text-primary font-bold flex items-baseline gap-2">
          6.000 <span class="text-xl text-primary/40">FCFA</span>
        </div>
      </div>
      <!-- Keypad -->
      <div class="grid grid-cols-3 gap-3">
        <!-- Key Row 1 -->
        <button
          class="bg-white h-20 rounded-lg shadow-sm border border-outline-variant/20 hover:bg-slate-50 active:scale-95 transition-all text-2xl font-mono text-primary font-bold">1</button>
        <button
          class="bg-white h-20 rounded-lg shadow-sm border border-outline-variant/20 hover:bg-slate-50 active:scale-95 transition-all text-2xl font-mono text-primary font-bold">2</button>
        <button
          class="bg-white h-20 rounded-lg shadow-sm border border-outline-variant/20 hover:bg-slate-50 active:scale-95 transition-all text-2xl font-mono text-primary font-bold">3</button>
        <!-- Key Row 2 -->
        <button
          class="bg-white h-20 rounded-lg shadow-sm border border-outline-variant/20 hover:bg-slate-50 active:scale-95 transition-all text-2xl font-mono text-primary font-bold">4</button>
        <button
          class="bg-white h-20 rounded-lg shadow-sm border border-outline-variant/20 hover:bg-slate-50 active:scale-95 transition-all text-2xl font-mono text-primary font-bold">5</button>
        <button
          class="bg-white h-20 rounded-lg shadow-sm border border-outline-variant/20 hover:bg-slate-50 active:scale-95 transition-all text-2xl font-mono text-primary font-bold">6</button>
        <!-- Key Row 3 -->
        <button
          class="bg-white h-20 rounded-lg shadow-sm border border-outline-variant/20 hover:bg-slate-50 active:scale-95 transition-all text-2xl font-mono text-primary font-bold">7</button>
        <button
          class="bg-white h-20 rounded-lg shadow-sm border border-outline-variant/20 hover:bg-slate-50 active:scale-95 transition-all text-2xl font-mono text-primary font-bold">8</button>
        <button
          class="bg-white h-20 rounded-lg shadow-sm border border-outline-variant/20 hover:bg-slate-50 active:scale-95 transition-all text-2xl font-mono text-primary font-bold">9</button>
        <!-- Key Row 4 -->
        <button
          class="bg-white h-20 rounded-lg shadow-sm border border-outline-variant/20 hover:bg-slate-50 active:scale-95 transition-all text-2xl font-mono text-error font-bold flex items-center justify-center">
          <span class="material-symbols-outlined">backspace</span>
        </button>
        <button
          class="bg-white h-20 rounded-lg shadow-sm border border-outline-variant/20 hover:bg-slate-50 active:scale-95 transition-all text-2xl font-mono text-primary font-bold">0</button>
        <button
          class="bg-white h-20 rounded-lg shadow-sm border border-outline-variant/20 hover:bg-slate-50 active:scale-95 transition-all text-2xl font-mono text-primary font-bold">00</button>
      </div>
    </div>
    <!-- Side Actions -->
    <div class="md:col-span-12 lg:col-span-4 flex flex-col gap-4">
      <!-- Change Display -->
      <div class="bg-[#e6f4ea] rounded-xl p-6 shadow-md border border-[#c6e7d1] flex flex-col items-center">
        <span class="text-[#1e7e34] font-label text-xs uppercase tracking-widest font-bold mb-2">À Rendre
          (Rendu)</span>
        <div class="text-3xl font-mono text-[#115e24] font-bold">
          +500 <span class="text-sm">FCFA</span>
        </div>
      </div>
      <!-- Payment Modes -->
      <div class="bg-surface-container-low rounded-xl p-4 flex flex-col gap-2">
        <button
          class="flex items-center justify-between p-4 bg-secondary-container text-on-secondary-container rounded-lg font-headline font-bold shadow-sm ring-2 ring-secondary">
          <span class="flex items-center gap-3">
            <span class="material-symbols-outlined"
              style="font-variation-settings: 'FILL' 1;">payments</span> Espèces
          </span>
          <span class="w-3 h-3 bg-white rounded-full"></span>
        </button>
        <button
          class="flex items-center justify-between p-4 bg-white/50 text-primary/60 rounded-lg font-headline font-semibold hover:bg-white transition-all">
          <span class="flex items-center gap-3">
            <span class="material-symbols-outlined">credit_card</span> Carte Bancaire
          </span>
        </button>
        <button
          class="flex items-center justify-between p-4 bg-white/50 text-primary/60 rounded-lg font-headline font-semibold hover:bg-white transition-all">
          <span class="flex items-center gap-3">
            <span class="material-symbols-outlined">phone_android</span> Mobile Money
          </span>
        </button>
      </div>
      <!-- Camera Feed Mini -->
      <div class="bg-primary rounded-xl aspect-video overflow-hidden relative shadow-lg">
        <img alt="Live Feed" class="w-full h-full object-cover opacity-60 grayscale"
          data-alt="CCTV security footage of a car at a toll booth lane from a high angle at night with headlights visible"
          src="https://lh3.googleusercontent.com/aida-public/AB6AXuDPqowUkePd7hweElc-SVtU1JuHyqU3W9mKVZ_io00E2TxTQCyKvLjzV5OU-r79YPEBI9pRBi6t0z75y6sTlBHvFtVc8HcedBxxvlFV-fISvR6j4EAQdocvKTO4_Bbm8-GtvaAg1HGLMPC0iQSjIHWTPn36U7E6WXbGxJVT8Rt1wLKtdGEByUjpVkaRWQV2nGOrU8RU_Exk1btifxGzLkl7P4sb5x5Ya2mPnM78hzvExAb9KqF0sTO-PtK2roE2BmPfyoUaI_3XMagJ" />
        <div
          class="absolute top-2 left-2 flex items-center gap-2 px-2 py-1 bg-error rounded text-[10px] text-white font-bold animate-pulse">
          <span class="w-2 h-2 bg-white rounded-full"></span> REC
        </div>
        <div
          class="absolute bottom-2 right-2 bg-black/40 backdrop-blur px-2 py-1 rounded text-[10px] text-white font-mono">
          LANE_04_FRONT
        </div>
      </div>
    </div>
  </div>
</main>