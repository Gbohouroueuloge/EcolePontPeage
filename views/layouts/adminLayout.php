<?php

require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'admin/variables.php';


$navLinks = [
  ['text' => 'Pilotage', 'isTitle' => true],
  ['text' => 'Dashboard', 'icon' => 'dashboard', 'link' => '/admin', 'isTitle' => false],
  ['text' => 'Analytics', 'icon' => 'insights', 'link' => '/admin/analytics', 'isTitle' => false],
  ['text' => 'Historiques', 'icon' => 'history', 'link' => '/admin/historiques', 'isTitle' => false],
  ['text' => 'Rapports', 'icon' => 'analytics', 'link' => '/admin/rapports', 'isTitle' => false],
  ['text' => 'Organisation', 'isTitle' => true],
  ['text' => 'Utilisateurs', 'icon' => 'group', 'link' => '/admin/utilisateurs', 'isTitle' => false],
  ['text' => 'Operateurs', 'icon' => 'engineering', 'link' => '/admin/operateurs', 'isTitle' => false],
  ['text' => 'Messages', 'icon' => 'message', 'link' => '/admin/messages', 'isTitle' => false, 'badge' => $adminUnreadCount],
  ['text' => 'Systeme', 'isTitle' => true],
  ['text' => 'Tarifs', 'icon' => 'local_atm', 'link' => '/admin/tarifs', 'isTitle' => false],
  ['text' => 'Parametres', 'icon' => 'settings', 'link' => '/admin/parametres', 'isTitle' => false],
];

if (isset($_GET['logout'])) {
  $auth->logout();
  header('Location: /');
  exit;
}
?>
<!DOCTYPE html>
<html lang="fr">

<?php require dirname(__DIR__) . DIRECTORY_SEPARATOR . '/layouts/head.php'; ?>

<body class="bg-surface font-body text-on-surface">
  <div id="sidebar-overlay" class="fixed inset-0 z-40 bg-black/20 opacity-0 pointer-events-none transition-opacity duration-300 md:hidden"></div>

  <aside id="sidebarMobile" class="fixed left-0 top-0 z-50 flex h-screen w-64 -translate-x-full flex-col bg-primary shadow-[0_0_24px_rgba(0,7,25,0.22)] transition-transform duration-300 md:hidden">
    <?= require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'layouts/components/asideAdmin.php' ?>
  </aside>

  <aside class="fixed left-0 top-0 z-50 hidden h-screen w-64 flex-col bg-primary shadow-[0_0_24px_rgba(0,7,25,0.22)] md:flex overflow-y-auto" style="scrollbar-width: none;">
    <?= require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'layouts/components/asideAdmin.php' ?>
  </aside>

  <header class="fixed left-0 right-0 top-0 z-40 flex h-16 items-center justify-between bg-[#fef9f1] px-6 md:left-64 md:px-8">
    <div class="flex flex-1 items-center gap-6">
      <button id="sidebar-toggle" class="rounded-md p-2 text-primary transition hover:bg-surface-container-high md:hidden">
        <span class="material-symbols-outlined">menu</span>
      </button>

      <div>
        <div class="text-[10px] font-bold uppercase tracking-[0.24em] text-on-surface-variant">Administration</div>
        <h2 class="font-headline text-2xl font-black text-primary"><?= $title ?></h2>
      </div>
    </div>

    <div class="hidden items-center gap-3 rounded-full bg-surface-container-highest px-4 py-2 text-xs font-bold text-primary md:flex">
      <span class="w-2 h-2 rounded-full bg-green-500"></span>
      <?= $adminUnreadCount ?> message(s) non lu(s)
    </div>

    <a
      href="?logout"
      class="ml-3 flex items-center justify-center gap-2 rounded-2xl border border-error/40 px-4 py-1 text-sm font-bold text-error transition hover:bg-white/5">
      <span class="material-symbols-outlined text-lg">logout</span>
      Deconnexion
    </a>
  </header>

  <?= $content ?? '' ?>

  <script>
    const toggle = document.getElementById('sidebar-toggle');
    const sidebar = document.getElementById('sidebarMobile');
    const overlay = document.getElementById('sidebar-overlay');

    if (toggle) {
      toggle.addEventListener('click', () => {
        sidebar.classList.remove('-translate-x-full');
        overlay.classList.remove('opacity-0', 'pointer-events-none');
      });
    }

    overlay.addEventListener('click', () => {
      sidebar.classList.add('-translate-x-full');
      overlay.classList.add('opacity-0', 'pointer-events-none');
    });

    document.addEventListener('keydown', event => {
      if (event.key === 'Escape') {
        sidebar.classList.add('-translate-x-full');
        overlay.classList.add('opacity-0', 'pointer-events-none');
      }
    });
  </script>
</body>

</html>