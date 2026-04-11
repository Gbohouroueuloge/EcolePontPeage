<?php

require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'operator/variables.php';

$navLinks = [
  ['name' => "Passage", 'href' => "/operator", 'icon' => "directions_car"],
  ['name' => "Caisse", 'href' => "/operator/caisse", 'icon' => "payments"],
  ['name' => "Incident", 'href' => "/operator/incident", 'icon' => "warning"],
  ['name' => "Mon Shift", 'href' => "/operator/mon-shift", 'icon' => "schedule"],
];


?>
<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Operator <?= $title ?? 'Péage Bridge' ?></title>
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

<body class="bg-surface text-on-surface font-body selection:bg-secondary-container/30">
  <header
    class="flex justify-between items-center px-6 h-16 w-full fixed top-0 z-50 bg-white dark:bg-primary  font-['Plus_Jakarta_Sans'] tracking-tight">
    <div class="flex items-center gap-8">
      <span class="text-xl font-bold tracking-tighter text-primary dark:text-[#fef9f1]">
        Peage Bridge
      </span>
      <nav class="hidden md:flex gap-6 items-center h-full pt-1">
        <?php foreach ($navLinks as $link) : ?>
          <a
            class="hover:dark:text-secondary hover:text-primary <?= $title === $link['name'] ? ' text-secondary border-b-2 border-secondary' : ' text-slate-500' ?> h-full flex items-center px-2 transition-all duration-200"
            href="<?= $link['href'] ?>">
            <?= $link['name'] ?>
          </a>
        <?php endforeach; ?>
      </nav>
    </div>
    <div class="flex items-center gap-4">
      <div class="hidden md:flex items-center gap-2 bg-surface-container-low px-4 py-1.5 rounded-lg">
        <span class="material-symbols-outlined text-primary"
          style="font-variation-settings: 'FILL' 1;">sensors</span>
        <span class="font-bold text-primary">Voie #<?= $guichet->id ?></span>
      </div>
      <div class="flex items-center gap-2">
        <button
          class="p-2 transition-all duration-200 hover:bg-slate-50 dark:hover:bg-slate-800 rounded-full active:scale-95">
          <span class="material-symbols-outlined text-slate-600">notifications</span>
        </button>
        <a href="/" class="p-2 transition-all duration-200 hover:bg-slate-50 dark:hover:bg-slate-800 rounded-full active:scale-95">
          <span class="material-symbols-outlined text-slate-600">home</span>
        </a>
        <a
          href="/operator/mon-shift"
          class="relative inline-flex group cursor-pointer">
          <div class="flex items-center justify-center w-10 h-10 rounded-full overflow-hidden border-2 border-surface-container-high bg-surface-container shadow-sm transition-all duration-300 group-hover:shadow-md group-hover:border-primary group-hover:scale-105">
            <span class="text-primary uppercase text-2xl font-black font-mono transition-transform duration-300 group-hover:scale-110" data-icon="person">
              <?= substr($agent->username, 0, 2) ?>
            </span>
          </div>
          <span class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-green-500 border-2 border-white rounded-full z-10"></span>
          <span class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-green-400 rounded-full animate-ping opacity-75"></span>
        </a>
      </div>
    </div>
  </header>

  <?= $content ?>

  <!-- BottomNavBar for Mobile (Hidden on Desktop) -->
  <nav
    class="fixed bottom-0 w-full flex md:hidden justify-around items-center h-20 px-4 bg-[#fef9f1] dark:bg-primary z-50 border-t border-primary/5">
    <?php foreach ($navLinks as $link) : ?>
      <a
        href="<?= $link['href'] ?>"
        class="flex flex-col items-center justify-center <?= $title === $link['name'] ? ' text-secondary border-b-2 border-secondary' : ' text-slate-500' ?> font-bold">
        <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">
          <?= $link['icon'] ?>
        </span>
        <span class="font-['Plus_Jakarta_Sans'] text-xs font-semibold">
          <?= $link['name'] ?>
        </span>
      </a>
    <?php endforeach; ?>
  </nav>
</body>

</html>