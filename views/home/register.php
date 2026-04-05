<main class="flex min-h-screen">
  <!-- Left Section: Visual Narrative (Deep Navy) -->
  <section class="hidden md:flex w-1/2 bg-primary-container relative flex-col justify-end p-20 overflow-hidden">
    <!-- Background Texture / Image -->
    <div class="absolute inset-0 z-0">
      <img class="w-full h-full object-cover opacity-30 mix-blend-luminosity"
        data-alt="monolithic modern bridge architecture at dusk with long exposure car light trails and deep navy tones"
        src="https://lh3.googleusercontent.com/aida-public/AB6AXuCdaxGyZs_vau70cvWz4sOUiRbFbMPPHRLMKjaB1CpWK9DYmXqwhsgGBhmgpevyG7VsuXRkwy6AXP8SPozmoyXOiIoKYZrxsXkZ1Rep3KNOcW3XLTHaLzu2OVmMPobuXZ8W2GOf2HyFSqSoRLJRB0Hy5kdDfhQRNkgb70omYTE8w1mHtqcLGwc1Iw4VYhva6TK8qg95eakSvlKC2eEXI8PbywJbDRaekjuyPpDcqn_oy25inWULz23K0gUBL6SxjD80PmFhjhxubmYZ" />
      <div class="absolute inset-0 bg-gradient-to-tr from-primary via-primary-container/80 to-transparent">
      </div>
    </div>
    <!-- Floating Card (Signature Pattern) -->
    <div class="relative z-10 max-w-lg mb-12">
      <div class="inline-flex items-center gap-2 mb-8">
        <div class="w-8 h-8 bg-secondary-container flex items-center justify-center rounded-lg">
          <span class="material-symbols-outlined text-primary text-lg" data-icon="diamond"
            style="font-variation-settings: 'FILL' 1;">diamond</span>
        </div>
        <span class="font-mono text-[10px] uppercase tracking-[0.3em] text-secondary-container">Système de
          Transit Actif</span>
      </div>
      <blockquote
        class="text-5xl font-headline font-extrabold text-surface tracking-tighter leading-[1.1] mb-12">
        "L'architecture n'est pas seulement une structure, c'est la fluidité du mouvement."
      </blockquote>
      <div class="flex items-center gap-6">
        <div class="h-px w-12 bg-secondary"></div>
        <div>
          <p class="font-headline font-bold text-white uppercase tracking-widest text-xs">Bureau de
            l'Ingénierie</p>
          <p class="font-mono text-[10px] text-on-primary-container uppercase tracking-wider mt-1">Pontis
            Infrastructure Group</p>
        </div>
      </div>
    </div>
    <!-- Precision Data Layer (Signature Pattern) -->
    <div class="relative z-10 flex gap-12 pt-12 border-t border-white/10">
      <div class="flex flex-col">
        <span class="font-label text-[9px] uppercase tracking-widest text-on-primary-container mb-2">ID
          Passage</span>
        <span class="font-mono text-xl text-secondary-container tracking-tighter">PR-0932-TX</span>
      </div>
      <div class="flex flex-col">
        <span class="font-label text-[9px] uppercase tracking-widest text-on-primary-container mb-2">Statut
          Réseau</span>
        <span class="font-mono text-xl text-white tracking-tighter">OPÉRATIONNEL</span>
      </div>
    </div>
  </section>

  <!-- Right Section: Registration Form (Warm Ivory) -->
  <section
    class="w-full md:w-1/2 bg-surface-bright flex flex-col justify-center px-12 lg:px-24 xl:px-32 relative z-10">
    <div class="max-w-md w-full mx-auto">
      <header class="mb-12 mt-12">
        <h1 class="text-4xl font-headline font-extrabold text-primary tracking-tight leading-none mb-4">
          Rejoindre l'Infrastructure.
        </h1>
        <p class="text-on-surface-variant font-body">
          Sélectionnez votre profil pour accéder au terminal de gestion du Péage Bridge.
        </p>
      </header>

      <!-- Registration Form -->
      <form class="space-y-6">
        <div class="space-y-1.5">
          <label
            class="font-label text-[10px] uppercase tracking-widest text-on-surface/60 font-semibold px-1">Nom
            Complet</label>
          <input
            class="w-full bg-surface-container-lowest border-none ring-1 ring-outline-variant/15 rounded-lg px-4 py-3.5 focus:ring-2 focus:ring-secondary-container transition-all placeholder:text-on-surface/20 text-on-surface font-body outline-none"
            placeholder="Jean-Pierre Architecte" type="text" />
        </div>
        <div class="space-y-1.5">
          <label
            class="font-label text-[10px] uppercase tracking-widest text-on-surface/60 font-semibold px-1">Email
            Professionnel</label>
          <input
            class="w-full bg-surface-container-lowest border-none ring-1 ring-outline-variant/15 rounded-lg px-4 py-3.5 focus:ring-2 focus:ring-secondary-container transition-all placeholder:text-on-surface/20 text-on-surface font-body outline-none"
            placeholder="jp.architecte@pontis.com" type="email" />
        </div>

        <div class="space-y-1.5">
          <div class="flex justify-between items-center px-1">
            <label
              class="font-label text-[10px] uppercase tracking-widest text-on-surface/60 font-semibold">Mot
              de Passe</label>
            <span class="text-[9px] font-mono uppercase tracking-tighter text-secondary">Sécurisé</span>
          </div>

          <div class="relative">
            <input
              class="w-full bg-surface-container-lowest border-none ring-1 ring-outline-variant/15 rounded-lg px-4 py-3.5 focus:ring-2 focus:ring-secondary-container transition-all placeholder:text-on-surface/20 text-on-surface font-body outline-none"
              placeholder="••••••••••••"
              type="password" />

            <button
              class="absolute right-4 top-1/2 -translate-y-1/2 text-on-surface-variant hover:text-primary transition-colors"
              type="button">
              <span class="material-symbols-outlined text-[20px]">visibility</span>
            </button>
          </div>

          <!-- Password Strength Bar -->
          <div class="flex gap-1 h-1 mt-2">
            <div class="flex-1 bg-secondary rounded-full"></div>
            <div class="flex-1 bg-secondary rounded-full"></div>
            <div class="flex-1 bg-secondary rounded-full"></div>
            <div class="flex-1 bg-surface-container-high rounded-full"></div>
          </div>
        </div>

        <div>
          <div class="flex justify-between items-center mb-2">
            <label class="font-label text-[10px] uppercase tracking-widest text-on-surface/60 font-semibold" for="confirm-password">Confimer Mot de
              passe</label>
          </div>

          <input
            class="w-full px-4 py-3 bg-surface-container-lowest border-0 ring-1 ring-outline-variant/30 focus:ring-2 focus:ring-secondary-container rounded-lg font-body transition-all duration-200 outline-none"
            id="confirm-password" name="confirm-password" placeholder="confirmer le mot de passe" type="password" />
        </div>

        <button
          class="w-full bg-primary text-on-primary font-headline font-bold uppercase tracking-[0.2em] py-4 rounded-lg flex items-center justify-center gap-3 group hover:bg-secondary transition-all duration-300 active:scale-95 gold-glow">
          Créer mon compte
          <span class="material-symbols-outlined text-sm" data-icon="arrow_forward">arrow_forward</span>
        </button>
      </form>

      <div class="mt-8 flex justify-center">
        <p class="font-body text-sm text-on-surface-variant">
          Déjà enregistré ?
          <a class="text-primary font-bold hover:text-secondary transition-colors" href="/login">
            Se connecter
          </a>
        </p>
      </div>

      <div class="mt-12 pt-8 flex items-center justify-between opacity-40">
        <div class="h-px bg-on-surface/20 flex-1"></div>
        <span class="px-6 font-mono text-[9px] uppercase tracking-widest">Identification Système</span>
        <div class="h-px bg-on-surface/20 flex-1"></div>
      </div>
    </div>
  </section>
</main>