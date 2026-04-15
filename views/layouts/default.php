<?php

use App\Auth;
use App\ConnectionBDD;

$navLinks = [
  ['label' => "Tarifs", 'href' => "/tarifs", 'icon' => "directions_car"],
  ['label' => "Abonnements", 'href' => "/abonnements", 'icon' => "payments"],
  ['label' => "Contact", 'href' => "/contact", 'icon' => "email"],
];

$auth = new Auth(ConnectionBDD::getPdo());

$isconnected = Auth::isConnected();
$isAdmin = $auth->isAdmin();

$user = $auth->getUser();

if (isset($_GET['logout'])) {
  $auth->logout();

  header('Location: /');
  exit();
}

?>
<!DOCTYPE html>
<html lang="fr">

<?php require dirname(__DIR__) . DIRECTORY_SEPARATOR . '/layouts/head.php'; ?>

<body class="bg-surface font-body text-on-surface">
  <!-- TopAppBar -->
  <header class="sticky top-0 z-50 w-full bg-surface-container-lowest shadow-sm">
    <div class="max-w-7xl mx-auto px-6 h-20 flex justify-between items-center">
      <a href="/" class="flex lg:flex-row flex-col items-center md:gap-4">
        <img class="w-10 h-10 flex items-center justify-center rounded-lg" src="/icons/peage_bridge_logo_africain.svg" alt="">
        <div>
          <h1 class="font-headline text-xl font-black text-primary tracking-tight leading-none">Péage Bridge
          </h1>
          <p class="text-xs hidden md:block text-primary/60 font-medium tracking-wide">Votre passage simplifié</p>
        </div>
      </a>

      <!-- <nav class="hidden md:flex items-center gap-8">
        <?php foreach ($navLinks as $link) : ?>
          <a
            href="<?= $link['href'] ?>"
            class="font-headline font-bold text-primary/60 hover:text-secondary transition-colors">
            <?= $link['label'] ?>
          </a>
        <?php endforeach; ?>
      </nav> -->

      <?php if ($isAdmin) : ?>
        <?php
        $type = "admin";
        require "components/badgeUser.php"
        ?>
      <?php elseif ($isconnected): ?>
        <?php
        $type = "operator";
        require "components/badgeUser.php"
        ?>
      <?php else: ?>
        <div class="flex items-center gap-3">
          <a
            href="/login"
            class="px-5 py-2.5 rounded-lg border border-primary/10 font-headline font-bold text-primary hover:bg-primary hover:text-on-primary transition-all">
            Connexion
          </a>
          <a
            href="/register"
            class="px-5 py-2.5 rounded-lg bg-secondary-container text-primary font-headline font-bold gold-glow hover:bg-secondary hover:text-on-secondary transition-all">
            S'inscrire
          </a>
        </div>
      <?php endif; ?>
    </div>
  </header>

  <?= $content ?>

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
          Leader Africain de l'infrastructure connectée. Nous transformons chaque kilomètre en une expérience de fluidité absolue pour des millions de conducteurs.
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
            <p class="text-white/70 text-sm">Plateau 1, BP32 Abidjan, Côte d'Ivoire</p>
          </div>
          <div class="flex gap-3">
            <span class="material-symbols-outlined text-secondary-container text-xl"
              data-icon="phone_in_talk">phone_in_talk</span>
            <p class="text-white/70 text-sm font-mono">+225 02 43 61 42 52</p>
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
          <p class="text-white/40 text-xs font-mono">&copy; 2026 Péage Bridge. Tous droits reservé.</p>
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

  <!-- BottomNavBar for Mobile (Hidden on Desktop) -->
  <!-- <nav
    class="fixed bottom-0 w-full flex md:hidden justify-around items-center h-20 px-4 bg-[#fef9f1] dark:bg-primary z-50 border-t border-primary/5">
    <?php foreach ($navLinks as $link) : ?>
      <a
        href="<?= $link['href'] ?>"
        class="flex flex-col items-center justify-center text-white font-bold">
        <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">
          <?= $link['icon'] ?>
        </span>
        <span class="font-['Plus_Jakarta_Sans'] text-xs font-semibold">
          <?= $link['label'] ?>
        </span>
      </a>
    <?php endforeach; ?>
  </nav> -->
</body>

</html>