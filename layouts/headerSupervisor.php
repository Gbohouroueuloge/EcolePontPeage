<?php

require_once dirname(__DIR__) . '/includes/connectionDBB.php';
require_once dirname(__DIR__) . '/functions/auth.php';

if (isset($_GET['logout'])) {
  logout();
  http_response_code(301);
  header('Location: /');
  exit;
}

if (!isConnected() || !isSupervisor()) {
  http_response_code(302);
  header('Location: /login.php');
  exit;
}

$user = getUser();

$navLinks = [
  ['text' => 'Pilotage', 'isTitle' => true],
  ['text' => 'Dashboard', 'icon' => 'dashboard', 'link' => '', 'isTitle' => false],
  ['text' => 'Voies', 'icon' => 'route', 'link' => 'voies.php', 'isTitle' => false],
  ['text' => 'Equipes', 'isTitle' => true],
  ['text' => 'Operateurs', 'icon' => 'engineering', 'link' => 'operateurs.php', 'isTitle' => false],
  ['text' => 'Incidents', 'icon' => 'warning', 'link' => 'incidents.php', 'isTitle' => false],
  ['text' => 'Controle', 'isTitle' => true],
  ['text' => 'Historiques', 'icon' => 'history', 'link' => 'historiques.php', 'isTitle' => false],
];

?>

<body class="bg-surface font-body text-on-surface">
  <div id="sidebar-overlay"
    class="fixed inset-0 bg-black/20 backdrop-blur-sm z-40 opacity-0 pointer-events-none transition-opacity duration-300 md:hidden">
  </div>

  <aside id="sidebarMobile"
    class="fixed md:hidden left-0 top-0 h-screen w-60 bg-primary z-50 flex flex-col shadow-[0_0_20px_rgba(201,144,26,0.15)] -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out">
    <?php require __DIR__ . '/asideSupervisor.php'; ?>
  </aside>

  <aside
    class="fixed hidden left-0 top-0 h-screen w-60 bg-primary z-50 md:flex flex-col shadow-[0_0_20px_rgba(201,144,26,0.15)]">
    <?php require __DIR__ . '/asideSupervisor.php'; ?>
  </aside>

  <header class="fixed top-0 left-0 md:left-60 right-0 h-16 bg-[#fef9f1] flex justify-between items-center px-8 z-40">
    <div class="flex items-center gap-8 flex-1">
      <button id="sidebar-toggle"
        class="md:hidden p-2 -ml-2 rounded-md hover:bg-surface-container-high transition-all text-primary">
        <span class="material-symbols-outlined">menu</span>
      </button>

      <div>
        <h2 class="text-2xl font-black text-primary font-headline"><?= $title ?></h2>
        <p class="hidden lg:block text-[10px] uppercase tracking-[0.24em] text-on-surface-variant font-bold">
          Zone <?= htmlspecialchars($supervisor->zone_nominale ?? 'attribuee') ?>
        </p>
      </div>
    </div>

    <div class="flex items-center gap-6">
      <div class="hidden sm:flex items-center gap-2 px-3 py-1.5 bg-surface-container-highest rounded-full text-xs font-bold text-primary">
        <span class="w-2 h-2 rounded-full bg-green-500"></span>
        Supervision active
      </div>

      <a href="" class="relative hidden md:inline-flex group cursor-pointer">
        <div class="flex items-center justify-center w-10 h-10 rounded-full overflow-hidden border-2 border-surface-container-high bg-surface-container shadow-sm transition-all duration-300 group-hover:shadow-md group-hover:border-primary group-hover:scale-105">
          <span class="text-primary uppercase text-2xl font-black font-mono transition-transform duration-300 group-hover:scale-110">
            <?= substr($user->username, 0, 2) ?>
          </span>
        </div>
        <span class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-green-500 border-2 border-white rounded-full z-10"></span>
        <span class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-green-400 rounded-full animate-ping opacity-75"></span>
      </a>
    </div>
  </header>
