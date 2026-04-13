<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>404</title>
  <link rel="stylesheet" href="/output.css">
  <link rel="shortcut icon" href="icons/favicon.ico" type="image/x-icon">
  <link
    href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;700;900&amp;family=DM+Sans:wght@400;500;700&amp;family=JetBrains+Mono:wght@700&amp;family=Plus+Jakarta+Sans:wght@700;800&amp;family=Public+Sans:wght@400;500;600&amp;family=Inter:wght@400;600;700&amp;display=swap"
    rel="stylesheet" />
  <link
    href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
    rel="stylesheet" />
  <link
    href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
    rel="stylesheet" />
</head>

<body>

  <body class="bg-surface text-on-surface font-body min-h-screen flex flex-col">
    <!-- Top Navigation (Shell Implementation) -->
    <header class="bg-[#fef9f1] text-primary docked full-width top-0 z-50">
      <div class="flex justify-between items-center w-full px-12 py-8 max-w-screen-2xl mx-auto">
        <a href="/" class="flex lg:flex-row flex-col items-center md:gap-4">
          <img class="w-10 h-10 flex items-center justify-center rounded-lg" src="/icons/peage_bridge_logo_africain.svg" alt="">
          <div>
            <h1 class="font-headline text-xl font-black text-primary tracking-tight leading-none">Péage Bridge
            </h1>
            <p class="text-xs hidden md:block text-primary/60 font-medium tracking-wide">Votre passage simplifié</p>
          </div>
        </a>
        <nav class="hidden md:flex gap-8 items-center">
          <a class="font-['Plus_Jakarta_Sans'] uppercase tracking-[0.2em] font-bold text-[#1d1c17]/60 hover:text-secondary transition-colors duration-300"
            href="/">Accuiel</a>
          <a class="font-['Plus_Jakarta_Sans'] uppercase tracking-[0.2em] font-bold text-[#1d1c17]/60 hover:text-secondary transition-colors duration-300"
            href="/login">Connection</a>
          <a class="font-['Plus_Jakarta_Sans'] uppercase tracking-[0.2em] font-bold text-[#1d1c17]/60 hover:text-secondary transition-colors duration-300"
            href="/register">Inscription</a>
        </nav>
        <div class="flex items-center gap-4">
          <button class="material-symbols-outlined text-primary">help_outline</button>
        </div>
      </div>
    </header>

    <!-- Main Content Canvas -->
    <main class="grow flex flex-col items-center justify-center px-6 py-12">
      <div class="max-w-4xl w-full flex flex-col items-center text-center space-y-12">
        <!-- Hero Illustration (The Architectural Monolith Style) -->
        <div class="relative w-full max-w-lg aspect-square md:aspect-video flex items-center justify-center">
          <svg class="w-full h-full drop-shadow-2xl" fill="none" viewbox="0 0 800 450"
            xmlns="http://www.w3.org/2000/svg">
            <!-- Road Base -->
            <rect fill="#000719" height="40" rx="4" width="600" x="100" y="380"></rect>
            <rect fill="#ffffff" fill-opacity="0.1" height="2" stroke-dasharray="10 10" width="560" x="120"
              y="395"></rect>
            <!-- Toll Booth (Monolith) -->
            <rect fill="#0d1f3c" height="160" rx="2" width="80" x="550" y="220"></rect>
            <rect fill="#ffffff" fill-opacity="0.05" height="40" rx="1" width="60" x="560" y="240"></rect>
            <!-- Barrier Post -->
            <rect fill="#1d1c17" height="60" rx="2" width="30" x="520" y="320"></rect>
            <!-- Barrier Arm (Animated) -->
            <g class="barrier-arm">
              <rect fill="#7e5700" height="15" rx="2" width="380" x="150" y="335"></rect>
              <path d="M150 335H180L165 350H150V335Z" fill="#febe49"></path>
              <path d="M210 335H240L225 350H210V335Z" fill="#febe49"></path>
              <path d="M270 335H300L285 350H270V335Z" fill="#febe49"></path>
              <path d="M330 335H360L345 350H330V335Z" fill="#febe49"></path>
              <path d="M390 335H420L405 350H390V335Z" fill="#febe49"></path>
              <path d="M450 335H480L465 350H450V335Z" fill="#febe49"></path>
              <!-- 404 Sign hanging from barrier -->
              <g transform="translate(250, 350)">
                <rect fill="#f8f3eb" height="100" rx="4" stroke="#000719" stroke-width="2" width="80" x="0"
                  y="0"></rect>
                <line stroke="#1d1c17" stroke-width="1" x1="20" x2="20" y1="-5" y2="5"></line>
                <line stroke="#1d1c17" stroke-width="1" x1="60" x2="60" y1="-5" y2="5"></line>
                <text fill="#000719" font-family="JetBrains Mono" font-size="28" font-weight="bold"
                  text-anchor="middle" x="40" y="45">404</text>
                <text fill="#7e5700" font-family="DM Sans" font-size="10" letter-spacing="1"
                  text-anchor="middle" x="40" y="75">ARRÊT FORCÉ</text>
              </g>
            </g>
            <!-- Confused Vehicle (Simplified Precision Geometry) -->
            <g transform="translate(180, 310)">
              <rect fill="#0d1f3c" height="40" rx="6" width="120" x="0" y="30"></rect>
              <path d="M20 30L40 0H90L110 30H20Z" fill="#0d1f3c" fill-opacity="0.8"></path>
              <circle cx="30" cy="70" fill="#1d1c17" r="12"></circle>
              <circle cx="90" cy="70" fill="#1d1c17" r="12"></circle>
              <!-- Confused Expression (Headlights) -->
              <circle cx="10" cy="50" fill="#febe49" opacity="0.6" r="4"></circle>
              <text fill="#000719" font-family="Plus Jakarta Sans" font-size="40" font-weight="bold" x="130"
                y="10">?</text>
            </g>
            <!-- Background Detail: Bridge Silhouette -->
            <path d="M0 420 Q 400 350 800 420" fill="none" stroke="#000719" stroke-opacity="0.05"
              stroke-width="1"></path>
          </svg>
        </div>
        <!-- Content Section -->
        <div class="space-y-6">
          <div class="flex flex-col items-center gap-2">
            <div class="flex items-center gap-2 bg-secondary-container/20 px-3 py-1 rounded-full">
              <div class="w-2 h-2 rotate-45 bg-secondary"></div>
              <span class="font-mono text-xs font-bold tracking-widest text-secondary uppercase">Accès
                Refusé</span>
            </div>
            <h1 class="font-headline font-extrabold text-5xl md:text-6xl text-primary tracking-tight">Route
              introuvable</h1>
          </div>
          <p class="text-on-surface-variant max-w-lg mx-auto text-lg leading-relaxed">
            Il semble que vous ayez emprunté une bretelle inexistante. La barrière du péage <span
              class="font-mono text-secondary font-bold">BP-404</span> est actuellement fermée pour
            maintenance sur cette destination.
          </p>
          <!-- Action Cluster -->
          <div class="flex flex-col sm:flex-row gap-4 justify-center pt-8">
            <a
              href="/"
              class="px-8 py-4 bg-primary text-on-primary rounded-lg font-headline font-bold text-sm tracking-widest uppercase hover:bg-secondary transition-all duration-300 gold-glow flex items-center justify-center gap-3 active:scale-95">
              <span class="material-symbols-outlined text-xl">home</span>
              Retour à l'accueil
            </a>
          </div>
        </div>
        <!-- Precision Data Footer Detail -->
        <div class="pt-12">
          <div class="grid grid-cols-1 md:grid-cols-3 gap-8 border-t border-outline-variant/10 pt-10">
            <div class="text-left space-y-1">
              <p class="font-mono text-[10px] uppercase tracking-widest text-on-surface/40">Code Erreur</p>
              <p class="font-mono text-sm font-bold text-primary">ERR_BRIDGE_STUB_404</p>
            </div>
            <div class="text-left space-y-1">
              <p class="font-mono text-[10px] uppercase tracking-widest text-on-surface/40">Coordonnées</p>
              <p class="font-mono text-sm font-bold text-primary">48.8584° N, 2.2945° E</p>
            </div>
            <div class="text-left space-y-1">
              <p class="font-mono text-[10px] uppercase tracking-widest text-on-surface/40">Infrastructure</p>
              <p class="font-mono text-sm font-bold text-primary">Pontis Mainframe v4.2</p>
            </div>
          </div>
        </div>
      </div>
    </main>

    <!-- Footer -->
    <footer class="bg-primary-container text-white pt-24 pb-0 relative">
      <!-- 4px gold geometric band -->
      <div class="absolute top-0 left-0 w-full h-1 bg-secondary-container"></div>
      <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 md:grid-cols-4 gap-16 mb-20">
        <!-- Brand Column -->
        <div class="md:col-span-1 space-y-6">
          <div class="flex items-center gap-3">
            <div class="w-8 h-8 bg-secondary-container flex items-center justify-center rounded">
              <span class="material-symbols-outlined text-primary text-lg" data-icon="bridge">link</span>
            </div>
            <span class="font-headline text-2xl font-black tracking-tighter text-white">Péage Bridge</span>
          </div>
          <p class="text-white/60 text-sm leading-relaxed">
            Leader européen de l'infrastructure connectée. Nous transformons chaque kilomètre en une expérience
            de fluidité absolue pour des millions de conducteurs.
          </p>
          <div class="flex gap-4">
            <a class="w-10 h-10 rounded-full bg-white/5 flex items-center justify-center hover:bg-secondary-container hover:text-primary transition-all"
              href="#">
              <span class="material-symbols-outlined text-xl" data-icon="public">public</span>
            </a>
            <a class="w-10 h-10 rounded-full bg-white/5 flex items-center justify-center hover:bg-secondary-container hover:text-primary transition-all"
              href="#">
              <span class="material-symbols-outlined text-xl" data-icon="share">share</span>
            </a>
            <a class="w-10 h-10 rounded-full bg-white/5 flex items-center justify-center hover:bg-secondary-container hover:text-primary transition-all"
              href="#">
              <span class="material-symbols-outlined text-xl" data-icon="mail">mail</span>
            </a>
          </div>
        </div>
        <!-- Navigation Column -->
        <div class="space-y-6">
          <h4 class="font-headline font-bold text-secondary-container uppercase tracking-widest text-xs">Accès
            Rapide</h4>
          <nav class="flex flex-col gap-4">
            <a class="text-white/70 hover:text-white transition-colors text-sm" href="#">Tarification
              Particuliers</a>
            <a class="text-white/70 hover:text-white transition-colors text-sm" href="#">Espace
              Professionnel</a>
            <a class="text-white/70 hover:text-white transition-colors text-sm" href="#">Cartographie des
              Ponts</a>
            <a class="text-white/70 hover:text-white transition-colors text-sm" href="#">FAQ &amp;
              Assistance</a>
          </nav>
        </div>
        <!-- Info Column -->
        <div class="space-y-6">
          <h4 class="font-headline font-bold text-secondary-container uppercase tracking-widest text-xs">
            Informations</h4>
          <nav class="flex flex-col gap-4">
            <a class="text-white/70 hover:text-white transition-colors text-sm" href="#">Actualités Réseau</a>
            <a class="text-white/70 hover:text-white transition-colors text-sm" href="#">Recrutement</a>
            <a class="text-white/70 hover:text-white transition-colors text-sm" href="#">Investisseurs</a>
            <a class="text-white/70 hover:text-white transition-colors text-sm" href="#">Presse</a>
          </nav>
        </div>
        <!-- Contact Column -->
        <div class="space-y-6">
          <h4 class="font-headline font-bold text-secondary-container uppercase tracking-widest text-xs">Contact
            &amp; Siège</h4>
          <div class="space-y-4">
            <div class="flex gap-3">
              <span class="material-symbols-outlined text-secondary-container text-xl"
                data-icon="location_on">location_on</span>
              <p class="text-white/70 text-sm">Avenue du Grand Pont, 75008 Paris, France</p>
            </div>
            <div class="flex gap-3">
              <span class="material-symbols-outlined text-secondary-container text-xl"
                data-icon="phone_in_talk">phone_in_talk</span>
              <p class="text-white/70 text-sm font-mono">+33 (0) 1 23 45 67 89</p>
            </div>
            <button
              class="w-full py-3 bg-white/5 border border-white/10 rounded-lg text-white font-bold text-sm hover:bg-white/10 transition-colors">
              Nous envoyer un message
            </button>
          </div>
        </div>
      </div>
      <!-- Bottom Bar -->
      <div class="border-t border-white/5 py-8">
        <div class="max-w-7xl mx-auto px-6 flex flex-col md:flex-row justify-between items-center gap-6">
          <div class="flex flex-col md:flex-row items-center gap-4 md:gap-8">
            <p class="text-white/40 text-xs font-mono">© 2025 Péage Bridge. All Rights Reserved.</p>
            <nav class="flex gap-6">
              <a class="text-white/40 hover:text-white text-xs transition-colors" href="#">Confidentialité</a>
              <a class="text-white/40 hover:text-white text-xs transition-colors" href="#">CGV / CGU</a>
              <a class="text-white/40 hover:text-white text-xs transition-colors" href="#">Mentions
                Légales</a>
            </nav>
          </div>
          <div class="flex items-center gap-4 bg-white/5 px-4 py-2 rounded-full border border-white/5">
            <span class="text-white/40 text-[10px] uppercase tracking-widest font-bold">Volume Total</span>
            <div class="flex items-center gap-2">
              <div class="w-1.5 h-1.5 rounded-full bg-secondary-container animate-pulse"></div>
              <span class="font-mono text-secondary-container font-bold">1,200,000 passages</span>
            </div>
          </div>
        </div>
      </div>
    </footer>
  </body>
</body>

</html>