<?php
require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'admin/variables.php';

$url = $params['username'] . '-' . $params['id'];

$navLinks = [
  ['text' => "Vue générale", "isTitle" => true],
  ['text' => "Dashboard", 'icon' => 'dashboard', 'link' => "/$url", 'isTitle' => false],
  ['text' => "Historiques", 'icon' => 'history', 'link' => "/$url/historiques", 'isTitle' => false],
  ['text' => "Gestion", 'isTitle' => true],
  ['text' => "Flux de Trafic", 'icon' => 'leaderboard', 'link' => "/$url/flux-trafic", 'isTitle' => false],
  ['text' => "Abonnés", 'icon' => 'group', 'link' => "/$url/abonnes", 'isTitle' => false],
  ['text' => "Système", 'isTitle' => true],
  ['text' => "Operators", 'icon' => 'engineering', 'link' => "/$url/operateurs", 'isTitle' => false],
  // ['text' => "Rapports", 'icon' => 'analytics', 'link' => "/$url/rapports", 'isTitle' => false],
  ['text' => "Paramètres", 'icon' => 'settings', 'link' => "/$url/parametres", 'isTitle' => false],
];

?>
<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $title ?? 'Péage Bridge' ?></title>
  <link rel="stylesheet" href="/output.css">
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

<body class="bg-surface font-body text-on-surface">

  <!-- Overlay (mobile only) -->
  <div id="sidebar-overlay"
    class="fixed inset-0 bg-black/20 backdrop-blur-sm z-40 opacity-0 pointer-events-none transition-opacity duration-300 md:hidden">
  </div>

  <!-- SideNavBar Mobile -->
  <aside id="sidebarMobile"
    class="fixed md:hidden left-0 top-0 h-screen w-60 bg-primary dark:bg-primary z-50 flex flex-col shadow-[0_0_20px_rgba(201,144,26,0.15)] -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out">
    <?= require 'asideAdmin.php' ?>
  </aside>

  <!-- SideNavBar -->
  <aside
    class="fixed hidden left-0 top-0 h-screen w-60 bg-primary dark:bg-primary z-50 md:flex flex-col shadow-[0_0_20px_rgba(201,144,26,0.15)]">
    <?= require 'asideAdmin.php' ?>
  </aside>

  <!-- TopAppBar -->
  <header class="fixed top-0 left-0 md:left-60 right-0 h-16 bg-[#fef9f1] flex justify-between items-center px-8 z-40">
    <div class="flex items-center gap-8 flex-1">

      <!-- Hamburger (mobile only) -->
      <button id="sidebar-toggle"
        class="md:hidden p-2 -ml-2 rounded-md hover:bg-surface-container-high transition-all text-primary">
        <span class="material-symbols-outlined">menu</span>
      </button>

      <h2 class="text-2xl font-black text-primary font-headline">
        <?= $title ?>
      </h2>
      <div class="relative w-full max-w-md hidden md:block">
        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-primary/40 text-lg">search</span>
        <input
          class="w-full bg-surface-container-low border-none rounded-md pl-10 pr-4 py-2 text-sm focus:ring-2 focus:ring-secondary-container transition-all"
          placeholder="Search infrastructure logs..." type="text" />
      </div>
    </div>
    <div class="flex items-center gap-6">
      <div class="hidden sm:flex items-center gap-2 px-3 py-1.5 bg-surface-container-highest rounded-full text-xs font-bold text-primary">
        <span class="w-2 h-2 rounded-full bg-green-500"></span>
        État du Système
      </div>
      <div class="flex items-center gap-4">
        <button class="material-symbols-outlined text-slate-500 hover:text-primary transition-all">notifications</button>
        <a href="/admin/<?= $url ?>/parametres" class="material-symbols-outlined text-slate-500 hover:text-primary transition-all">
          settings
        </a>
        <a href="/" class="material-symbols-outlined text-slate-500 hover:text-primary transition-all">
          home
        </a>
        <a
          href="/admin/<?= $url ?>/parametres"
          class="relative hidden md:inline-flex group cursor-pointer">
          <div class="flex items-center justify-center w-10 h-10 rounded-full overflow-hidden border-2 border-surface-container-high bg-surface-container shadow-sm transition-all duration-300 group-hover:shadow-md group-hover:border-primary group-hover:scale-105">
            <span class="text-primary uppercase text-2xl font-black font-mono transition-transform duration-300 group-hover:scale-110" data-icon="person">
              <?= substr($user->username, 0, 2) ?>
            </span>
          </div>
          <span class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-green-500 border-2 border-white rounded-full z-10"></span>
          <span class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-green-400 rounded-full animate-ping opacity-75"></span>
        </a>
      </div>
    </div>
  </header>

  <?= $content ?>

  <script>
    const toggle = document.getElementById('sidebar-toggle');
    const sidebar = document.getElementById('sidebarMobile');
    const overlay = document.getElementById('sidebar-overlay');

    function openSidebar() {
      sidebar.classList.remove('-translate-x-full');
      overlay.classList.remove('opacity-0', 'pointer-events-none');
    }

    function closeSidebar() {
      if (window.innerWidth < 768) {
        sidebar.classList.add('-translate-x-full');
        overlay.classList.add('opacity-0', 'pointer-events-none');
      }
    }

    toggle.addEventListener('click', openSidebar);
    overlay.addEventListener('click', closeSidebar);
    document.addEventListener('keydown', e => e.key === 'Escape' && closeSidebar());
  </script>

</body>

</html>