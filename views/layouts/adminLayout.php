<?php
$navLinks = [
  ['text' => "Vue générale", "isTitle" => true],
  ['text' => "Dashboard", 'icon' => 'dashboard', 'link' => '', 'isTitle' => false],
  ['text' => "Historiques", 'icon' => 'history', 'link' => '/historiques', 'isTitle' => false],
  ['text' => "Gestion", 'isTitle' => true],
  ['text' => "Flux de Trafic", 'icon' => 'leaderboard', 'link' => '/flux-trafic', 'isTitle' => false],
  ['text' => "Abonnés", 'icon' => 'group', 'link' => '/abonnes', 'isTitle' => false],
  ['text' => "Système", 'isTitle' => true],
  ['text' => "Operators", 'icon' => 'engineering', 'link' => '/operateurs', 'isTitle' => false],
  // ['text' => "Rapports", 'icon' => 'analytics', 'link' => '/rapports', 'isTitle' => false],
  ['text' => "Paramètres", 'icon' => 'settings', 'link' => '/parametres', 'isTitle' => false],
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
    <div class="p-8 py-4 flex items-center gap-3">
      <div class="w-8 h-8 bg-secondary-container rotate-45 flex items-center justify-center shadow-[0_0_15px_rgba(254,190,73,0.4)]">
        <span class="material-symbols-outlined text-primary -rotate-45 text-sm">link</span>
      </div>
      <h1 class="text-xl font-bold tracking-tight text-white font-headline">Péage Bridge</h1>
    </div>

    <nav class="flex-1 mt-2 px-0">
      <?php foreach ($navLinks as $link) : ?>
        <?php if ($link['isTitle']) : ?>
          <div class="px-6 py-2">
            <p class="text-[10px] font-bold uppercase tracking-widest text-slate-500 font-headline"><?= $link['text'] ?></p>
          </div>
        <?php else: ?>
          <a
            href="/admin<?= $link['link'] ?>"
            class="flex items-center gap-3 px-6 py-2 mb-2 <?= $title === $link['text'] ? ' text-secondary-container border-l-4 border-secondary-container bg-white/5 active-nav-glow ' : ' hover:bg-white/10 text-slate-400 hover:text-white ' ?> transition-all group">
            <span class="material-symbols-outlined text-lg"><?= $link['icon'] ?></span>
            <span class="font-medium text-sm"><?= $link['text'] ?></span>
          </a>
        <?php endif ?>
      <?php endforeach; ?>
    </nav>

    <div class="p-6 bg-white/5 mt-auto">
      <div class="flex items-center gap-3">
        <img alt="Admin User Profile" class="w-10 h-10 rounded-lg object-cover ring-1 ring-white/20"
          src="https://lh3.googleusercontent.com/aida-public/AB6AXuAahXG2huCNHN-s9fl3WxsNMoI_wO9zb0BURxV-A3Aif0qSroOlLY-RNGubo5osUgLkbyVS9NcMj5ltYF7ZHdtHF-G8Mp6F0GyLjYIsAsGqstZsu6rtZjfjxF1BQ955Su3gy7Wn3H78_SxIVe2rKvFUU0J7i7-pgL_lZRy5LhqCwWrsh_l7q6yc4GMbVZMDdJeBxqk2iNGJ5kDmvsoA7sfVWnX9R_zjd8mC8SRvpyMo5gpnyH89CHQXEEMxnmy31PkviN7VUj6NVqfY" />
        <div>
          <p class="text-xs font-bold text-white leading-none">Admin User</p>
          <p class="text-[10px] text-slate-400 mt-1">Infrastructure Admin</p>
        </div>
      </div>
      <div class="mt-4 pt-4 border-t border-white/10">
        <div class="flex items-center gap-2 text-[10px] text-secondary-container font-bold">
          <span class="w-1.5 h-1.5 rounded-full bg-secondary-container animate-pulse"></span>
          System Health: Optimal
        </div>
      </div>
    </div>
  </aside>

  <!-- SideNavBar -->
  <aside
    class="fixed hidden left-0 top-0 h-screen w-60 bg-primary dark:bg-primary z-50 md:flex flex-col shadow-[0_0_20px_rgba(201,144,26,0.15)]">
    <div class="p-8 py-4 flex items-center gap-3">
      <div class="w-8 h-8 bg-secondary-container rotate-45 flex items-center justify-center shadow-[0_0_15px_rgba(254,190,73,0.4)]">
        <span class="material-symbols-outlined text-primary -rotate-45 text-sm">link</span>
      </div>
      <h1 class="text-xl font-bold tracking-tight text-white font-headline">Péage Bridge</h1>
    </div>

    <nav class="flex-1 mt-2 px-0">
      <?php foreach ($navLinks as $link) : ?>
        <?php if ($link['isTitle']) : ?>
          <div class="px-6 py-2">
            <p class="text-[10px] font-bold uppercase tracking-widest text-slate-500 font-headline"><?= $link['text'] ?></p>
          </div>
        <?php else: ?>
          <a
            href="/admin<?= $link['link'] ?>"
            class="flex items-center gap-3 px-6 py-2 mb-2 <?= $title === $link['text'] ? ' text-secondary-container border-l-4 border-secondary-container bg-white/5 active-nav-glow ' : ' hover:bg-white/10 text-slate-400 hover:text-white ' ?> transition-all group">
            <span class="material-symbols-outlined text-lg"><?= $link['icon'] ?></span>
            <span class="font-medium text-sm"><?= $link['text'] ?></span>
          </a>
        <?php endif ?>
      <?php endforeach; ?>
    </nav>
    <div class="p-6 bg-white/5 mt-auto">
      <div class="flex items-center gap-3">
        <img alt="Admin User Profile" class="w-10 h-10 rounded-lg object-cover ring-1 ring-white/20"
          data-alt="Professional headshot of a mature administrative manager in a dark suit with a clean background"
          src="https://lh3.googleusercontent.com/aida-public/AB6AXuAahXG2huCNHN-s9fl3WxsNMoI_wO9zb0BURxV-A3Aif0qSroOlLY-RNGubo5osUgLkbyVS9NcMj5ltYF7ZHdtHF-G8Mp6F0GyLjYIsAsGqstZsu6rtZjfjxF1BQ955Su3gy7Wn3H78_SxIVe2rKvFUU0J7i7-pgL_lZRy5LhqCwWrsh_l7q6yc4GMbVZMDdJeBxqk2iNGJ5kDmvsoA7sfVWnX9R_zjd8mC8SRvpyMo5gpnyH89CHQXEEMxnmy31PkviN7VUj6NVqfY" />
        <div>
          <p class="text-xs font-bold text-white leading-none">Admin User</p>
          <p class="text-[10px] text-slate-400 mt-1">Infrastructure Admin</p>
        </div>
      </div>
      <div class="mt-4 pt-4 border-t border-white/10">
        <div class="flex items-center gap-2 text-[10px] text-secondary-container font-bold">
          <span class="w-1.5 h-1.5 rounded-full bg-secondary-container animate-pulse"></span>
          System Health: Optimal
        </div>
      </div>
    </div>
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
        <button class="material-symbols-outlined text-slate-500 hover:text-primary transition-all">settings</button>
        <a href="/" class="material-symbols-outlined text-slate-500 hover:text-primary transition-all">
          home
        </a>
        <div class="hidden md:flex items-center gap-2 w-10 h-10 rounded-full overflow-hidden border-2 border-surface-container-high">
          <img alt="Profil Administrateur"
            src="https://lh3.googleusercontent.com/aida-public/AB6AXuDT9tfpgGi8Dk5Y5hxfW_PIgncPN3pIxRiZrMIcV5ODJ8DDRu3WqY9JLE1PUSUO0yuy-gLxcdW1w1A7VSTO3i-NdndDnlCZNrfJfERD-hqNULTTw6fNq8MTr9ZPpSrO8fLgYWm7C_X72B2YMtiZQMv41tL_rWragtdTo69ENZYn8PeQApsEDJ9-4F72WFYzQpISzgc470ZM827_TKgE26M1K9oiZER9KSmfUO8CnuTtwaGmicnpTgu0Oj9xYezSXg_yh7skm78PtJCL" />
        </div>
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