<?php
$title = 'Incident';
require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'operator/variables.php';
?>

<!-- Urgency Alert Banner -->
<div class="mt-16 bg-[#FF6B6B] py-3 px-6 flex items-center justify-center gap-3 shadow-lg z-40 relative">
  <span class="material-symbols-outlined text-white" style="font-variation-settings: 'FILL' 1;">warning</span>
  <span class="text-white font-headline font-extrabold tracking-widest text-sm uppercase">⚠️ SIGNALEMENT EN
    COURS</span>
</div>
<main class="max-w-3xl mx-auto px-6 py-8">
  <!-- Title & Identification -->
  <div class="mb-10 flex flex-col md:flex-row md:items-end justify-between gap-4">
    <div>
      <h1 class="font-headline text-4xl font-extrabold text-primary tracking-tight leading-none mb-2">Signaler
        un Incident</h1>
      <p class="text-on-surface-variant font-body">Saisie opérationnelle pour la maintenance et la sécurité.
      </p>
    </div>
    <div class="bg-surface-container-highest px-4 py-2 border-l-4 border-primary-container rounded-r-md">
      <span class="block text-[10px] uppercase font-bold text-on-surface-variant opacity-70">Identifiant de
        Session</span>
      <span class="font-mono text-lg font-bold text-primary">TX-992-04-B</span>
    </div>
  </div>
  <!-- Incident Type Grid -->
  <section class="mb-12">
    <div class="flex items-center gap-2 mb-6">
      <div class="diamond-indicator w-3 h-3 bg-secondary"></div>
      <h2 class="font-headline font-bold text-xl uppercase tracking-wider text-primary">Nature de l'incident
      </h2>
    </div>
    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
      <!-- Card: Panne -->
      <button
        class="group flex flex-col items-center justify-center p-6 bg-surface-container-lowest rounded-xl shadow-[0_4px_20px_rgba(13,31,60,0.05)] border-b-4 border-transparent hover:border-secondary transition-all duration-200">
        <span
          class="material-symbols-outlined text-4xl mb-3 text-primary group-hover:text-secondary transition-colors"
          data-icon="car_repair">car_repair</span>
        <span class="font-headline font-bold text-sm text-primary">Panne</span>
      </button>
      <!-- Card: Refus -->
      <button
        class="group flex flex-col items-center justify-center p-6 bg-surface-container-lowest rounded-xl shadow-[0_4px_20px_rgba(13,31,60,0.05)] border-b-4 border-transparent hover:border-secondary transition-all duration-200">
        <span
          class="material-symbols-outlined text-4xl mb-3 text-primary group-hover:text-secondary transition-colors"
          data-icon="block">block</span>
        <span class="font-headline font-bold text-sm text-primary">Refus</span>
      </button>
      <!-- Card: Fraude -->
      <button
        class="group flex flex-col items-center justify-center p-6 bg-surface-container-lowest rounded-xl shadow-[0_4px_20px_rgba(13,31,60,0.05)] border-b-4 border-transparent hover:border-secondary transition-all duration-200">
        <span
          class="material-symbols-outlined text-4xl mb-3 text-primary group-hover:text-secondary transition-colors"
          data-icon="person_off">person_off</span>
        <span class="font-headline font-bold text-sm text-primary">Fraude</span>
      </button>
      <!-- Card: Barrière bloquée -->
      <button
        class="group flex flex-col items-center justify-center p-6 bg-surface-container-lowest rounded-xl shadow-[0_4px_20px_rgba(13,31,60,0.05)] border-b-4 border-secondary transition-all duration-200 ring-2 ring-secondary/20">
        <span class="material-symbols-outlined text-4xl mb-3 text-secondary transition-colors"
          data-icon="traffic">traffic</span>
        <span class="font-headline font-bold text-sm text-primary">Barrière bloquée</span>
      </button>
      <!-- Card: Urgence -->
      <button
        class="group flex flex-col items-center justify-center p-6 bg-surface-container-lowest rounded-xl shadow-[0_4px_20px_rgba(13,31,60,0.05)] border-b-4 border-transparent hover:border-secondary transition-all duration-200">
        <span
          class="material-symbols-outlined text-4xl mb-3 text-primary group-hover:text-secondary transition-colors"
          data-icon="emergency">emergency</span>
        <span class="font-headline font-bold text-sm text-primary">Urgence</span>
      </button>
      <!-- Card: Technique -->
      <button
        class="group flex flex-col items-center justify-center p-6 bg-surface-container-lowest rounded-xl shadow-[0_4px_20px_rgba(13,31,60,0.05)] border-b-4 border-transparent hover:border-secondary transition-all duration-200">
        <span
          class="material-symbols-outlined text-4xl mb-3 text-primary group-hover:text-secondary transition-colors"
          data-icon="build">build</span>
        <span class="font-headline font-bold text-sm text-primary">Technique</span>
      </button>
    </div>
  </section>
  <!-- Form Section -->
  <section class="space-y-8">
    <div
      class="bg-surface-container-lowest p-8 rounded-xl shadow-[0_4px_20px_rgba(13,31,60,0.05)] relative overflow-hidden">
      <div class="absolute top-0 right-0 p-4 opacity-5 pointer-events-none">
        <span class="material-symbols-outlined text-8xl">description</span>
      </div>
      <div class="mb-6">
        <label
          class="block font-headline font-bold text-sm uppercase tracking-widest text-primary mb-3">Description
          détaillée</label>
        <textarea
          class="w-full bg-surface-container-low border-none rounded-xl focus:ring-2 focus:ring-secondary-container transition-all p-4 font-body placeholder:opacity-50"
          placeholder="Décrivez l'incident de manière précise..." rows="4"></textarea>
      </div>
      <div>
        <label
          class="block font-headline font-bold text-sm uppercase tracking-widest text-primary mb-3">Preuves
          Visuelles (Photo)</label>
        <div
          class="border-2 border-dashed border-outline-variant rounded-xl p-10 flex flex-col items-center justify-center bg-surface-container-low group cursor-pointer hover:bg-surface-container-high transition-all">
          <span
            class="material-symbols-outlined text-5xl text-on-surface-variant mb-4 group-hover:scale-110 transition-transform">add_a_photo</span>
          <p class="font-body text-sm text-on-surface-variant font-semibold">Capturer ou déposer une image
          </p>
          <p class="font-body text-[10px] text-on-surface-variant opacity-60 mt-1">PNG, JPG jusqu'à 10MB
          </p>
        </div>
      </div>
    </div>
  </section>
  <!-- Action Button -->
  <div class="mt-12">
    <button
      class="w-full bg-[#FF6B6B] hover:bg-[#fa5252] text-white font-headline font-extrabold text-lg py-5 rounded-xl shadow-[0_10px_25px_rgba(255,107,107,0.3)] transition-all active:scale-95 flex items-center justify-center gap-3">
      <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">send</span>
      SIGNALER L'INCIDENT
    </button>
    <p
      class="text-center mt-6 text-on-surface-variant opacity-50 text-[10px] uppercase font-bold tracking-[0.2em]">
      Enregistrement horodaté certifié par TollOps Monolith</p>
  </div>
</main>