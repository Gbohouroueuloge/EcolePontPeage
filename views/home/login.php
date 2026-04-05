<main class="flex md:min-h-screen">
  <!-- Left Side: Login Form -->
  <section
    class="w-full lg:w-1/2 bg-surface flex flex-col lg:justify-center p-8 lg:p-12 xl:p-24 relative overflow-hidden">
    <!-- Form Container -->
    <div class="max-w-md w-full mx-auto z-10">
      <header class="mb-10">
        <h1 class="font-outfit text-[32px] font-bold text-primary leading-tight mb-2">Bon retour 👋</h1>
        <p class="font-dmsans text-[16px] text-on-surface-variant">Connectez-vous à votre espace.</p>
      </header>
      <form class="space-y-6">
        <div>
          <label class="block text-sm font-label font-medium text-on-surface mb-2" for="email">Adresse
            e-mail</label>
          <input
            class="w-full px-4 py-3 bg-surface-container-lowest border-0 ring-1 ring-outline-variant/30 focus:ring-2 focus:ring-secondary-container rounded-lg font-body transition-all duration-200 outline-none"
            id="email" name="email" placeholder="nom@entreprise.fr" type="email" />
        </div>

        <div>
          <div class="flex justify-between items-center mb-2">
            <label class="block text-sm font-label font-medium text-on-surface" for="password">Mot de
              passe</label>
          </div>
          <div class="relative">
            <input
              class="w-full px-4 py-3 bg-surface-container-lowest border-0 ring-1 ring-outline-variant/30 focus:ring-2 focus:ring-secondary-container rounded-lg font-body transition-all duration-200 outline-none"
              id="password" name="password" placeholder="••••••••" type="password" />
            <button
              class="absolute right-4 top-1/2 -translate-y-1/2 text-on-surface-variant hover:text-primary transition-colors"
              type="button">
              <span class="material-symbols-outlined text-[20px]">visibility</span>
            </button>
          </div>
        </div>

        <div class="flex items-center justify-between">
          <div class="flex items-center">
            <input
              class="h-4 w-4 rounded border-outline-variant/30 text-primary focus:ring-secondary-container"
              id="remember-me" name="remember-me" type="checkbox" />
            <label class="ml-2 block text-sm font-label text-on-surface-variant" for="remember-me">
              Se souvenir de moi
            </label>
          </div>
          <div class="text-sm">
            <a class="font-medium text-[#FF7F50] hover:opacity-80 transition-opacity" href="#">
              Mot de passe oublié ?
            </a>
          </div>
        </div>
        <div>
          <button
            class="w-full bg-primary text-on-primary font-label font-semibold py-4 rounded-lg hover:bg-secondary transition-all duration-300 active:scale-[0.98] gold-glow uppercase tracking-widest text-xs"
            type="submit">
            Se connecter
          </button>
        </div>
      </form>

      <div class="mt-8 flex justify-center">
        <p class="font-body text-sm text-on-surface-variant">
          Pas encore enregistré ?
          <a class="text-primary font-bold hover:text-secondary transition-colors" href="/register">
            S'inscrire
          </a>
        </p>
      </div>

      <div class="mt-10 flex items-center gap-4">
        <span class="h-px bg-outline-variant/20 grow"></span>
        <span class="text-[10px] font-mono text-outline uppercase tracking-widest">Précision
          Architecturale</span>
        <span class="h-px bg-outline-variant/20 grow"></span>
      </div>
    </div>
  </section>
  <!-- Right Side: Brand Imagery & Stats -->
  <section
    class="hidden lg:flex w-1/2 bg-primary relative flex-col justify-center items-center p-24 overflow-hidden">
    <!-- Background Image with Overlay -->
    <div class="absolute inset-0 z-0">
      <img alt="Pontis Architecture" class="w-full h-full object-cover mix-blend-overlay opacity-40"
        data-alt="Modern geometric bridge structure at twilight with dramatic deep navy lighting and warm yellow safety lights reflecting on steel surfaces"
        src="https://lh3.googleusercontent.com/aida-public/AB6AXuDNUVh5qRPTlZzrKuKcsrF9oQQbLvCctcz8jgpcWmMZuq_oDNPB4egzYur1rU5mwvQPN1TfHJiKMeW5PH34NfJq7WS6JldXVlZRj0YE551w4bH2XgB8ucFhz3B78Kur7btZ7Y727JS-2AIoGOFka4XMmGXJ1uhtiu4_gJ5YOG44WEGnf-AAcyTPNcQIt3y8tYZrZuRNjAwAtm39EQlJ1845oSiOVsI3J-kNJIiqJ7-qRQ9StjbtOwA12gxXve0p0iR2Siahrm0cbYqV" />
      <div class="absolute inset-0 bg-linear-to-br from-primary via-primary-container/80 to-transparent">
      </div>
    </div>
    <!-- Content -->
    <div class="relative z-10 text-center max-w-lg">
      <div class="mb-8 flex justify-center">
        <div class="w-12 h-12 rounded-lg bg-secondary-container flex items-center justify-center gold-glow">
          <span class="material-symbols-outlined text-primary text-2xl"
            style="font-variation-settings: 'FILL' 1;">architecture</span>
        </div>
      </div>
      <h2 class="font-outfit text-4xl text-on-primary leading-tight italic font-light mb-12">
        « Chaque passage compte. <br />
        <span class="text-secondary-container font-bold">Chaque trajet simplifié. »</span>
      </h2>
      <!-- Stats Grid -->
      <div class="grid grid-cols-3 gap-8 pt-12 border-t border-on-primary/10">
        <div class="flex flex-col items-center">
          <span class="font-mono text-secondary-container text-2xl mb-1">1.2M</span>
          <span
            class="font-label text-[10px] text-on-primary/60 uppercase tracking-widest">passages</span>
        </div>
        <div class="flex flex-col items-center border-x border-on-primary/10 px-4">
          <span class="font-mono text-secondary-container text-2xl mb-1">3</span>
          <span class="font-label text-[10px] text-on-primary/60 uppercase tracking-widest">voies</span>
        </div>
        <div class="flex flex-col items-center">
          <span class="font-mono text-secondary-container text-2xl mb-1">24h/24</span>
          <span
            class="font-label text-[10px] text-on-primary/60 uppercase tracking-widest">disponible</span>
        </div>
      </div>
    </div>
    <!-- Decorative Road Line Graphic -->
    <div class="absolute bottom-0 left-0 w-full h-32 opacity-10 pointer-events-none">
      <svg fill="none" height="100%" preserveaspectratio="none" viewbox="0 0 800 200" width="100%"
        xmlns="http://www.w3.org/2000/svg">
        <path d="M0 150L200 120L400 150L600 110L800 140" stroke="white" stroke-dasharray="20 20"
          stroke-width="4"></path>
        <path d="M0 180L200 150L400 180L600 140L800 170" stroke="white" stroke-dasharray="20 20"
          stroke-width="4"></path>
      </svg>
    </div>
  </section>
</main>

<!-- Modal: Password Reset -->
<div class="fixed inset-0 flex justify-content-center bg-primary/60 backdrop-blur-sm z-100 items-center justify-center hidden">
  <div
    class="bg-surface-container-lowest p-10 rounded-xl relative w-full max-w-lg shadow-2xl overflow-hidden">
    <!-- Architectural Accent -->
    <div class="absolute top-0 left-0 w-1.5 h-full bg-primary-container"></div>
    <div class="flex flex-col items-center text-center space-y-6">
      <!-- Lock & Key Icon Wrapper -->
      <div
        class="w-20 h-20 bg-surface-container-low rounded-xl flex items-center justify-center text-secondary relative">
        <span class="material-symbols-outlined text-4xl" data-icon="lock_reset">lock_reset</span>
        <div
          class="absolute -top-1 -right-1 bg-secondary text-on-primary w-6 h-6 rounded-full flex items-center justify-center shadow-lg">
          <span class="material-symbols-outlined text-xs" data-icon="key">key</span>
        </div>
      </div>
      <div class="space-y-2">
        <h1 class="text-3xl font-headline font-extrabold tracking-tight text-primary">
          Mot de passe oublié?
        </h1>
        <p class="text-on-surface-variant text-sm leading-relaxed max-w-[320px] mx-auto">
          Entrez votre adresse e-mail pour recevoir les instructions de réinitialisation de votre
          accès.
        </p>
      </div>
      <form class="w-full space-y-6 pt-2">
        <div class="text-left space-y-2">
          <label
            class="text-[10px] font-mono tracking-widest uppercase text-on-surface-variant px-1">Identifiant
            e-mail</label>
          <div class="relative">
            <input
              class="w-full bg-surface-container-low border-none rounded-lg py-4 px-5 text-primary placeholder:text-outline-variant focus:ring-2 focus:ring-secondary-container transition-all"
              placeholder="nom@entreprise.fr" type="email" />
            <span
              class="material-symbols-outlined absolute right-4 top-1/2 -translate-y-1/2 text-outline-variant text-xl"
              data-icon="mail">mail</span>
          </div>
        </div>
        <button
          class="w-full bg-primary text-on-primary font-headline font-bold py-4 rounded-lg gold-glow transition-all active:scale-95 flex items-center justify-center gap-2"
          type="button">
          <span>Envoyer le lien</span>
          <span class="material-symbols-outlined text-sm"
            data-icon="arrow_forward">arrow_forward</span>
        </button>
      </form>
      <a class="text-[10px] font-mono tracking-widest uppercase text-secondary hover:text-primary transition-colors flex items-center gap-2"
        href="/login">
        <span class="material-symbols-outlined text-xs" data-icon="arrow_back">arrow_back</span>
        Retour à la connexion
      </a>
    </div>
  </div>
</div>

<div class="fixed inset-0 flex justify-content-center bg-primary/60 backdrop-blur-sm z-100 items-center justify-center hidden">
  <div
    class="bg-surface-container-lowest p-8 rounded-xl border border-outline-variant/15 flex flex-col items-center text-center space-y-4">
    <div class="flex items-center gap-3">
      <div class="relative flex items-center justify-center">
        <span class="material-symbols-outlined text-secondary text-2xl"
          data-icon="mark_email_read">mark_email_read</span>
        <div
          class="absolute -bottom-1 -right-1 w-3 h-3 bg-secondary rotate-45 border-2 border-surface flex items-center justify-center">
        </div>
      </div>
      <span class="font-headline font-bold text-primary tracking-tight">Vérifiez votre boîte mail!</span>
    </div>
    <p class="text-xs text-on-surface-variant">Un lien de sécurité a été envoyé. Expire dans 15 minutes.</p>
    <div class="flex items-center gap-2">
      <span class="text-[10px] font-mono text-outline uppercase tracking-wider">Renvoyer dans</span>
      <span
        class="text-[10px] font-mono text-secondary font-bold bg-secondary-container/20 px-2 py-1 rounded">00:59</span>
    </div>
  </div>
</div>
<!-- Modal: Success State -->