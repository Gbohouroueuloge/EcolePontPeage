<?php
$navLinks = [
  ['name' => "Passage", 'href' => "/operator"],
  ['name' => "Caisse", 'href' => "/operator/caisse"],
  ['name' => "Incident", 'href' => "/operator/incident"],
  ['name' => "Mon Shift", 'href' => "/operator/mon-shift"],
];

?>
<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
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
        TollOps Monolith
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
        <span class="font-bold text-primary">Lane 04 Active</span>
      </div>
      <div class="flex items-center gap-2">
        <button
          class="p-2 transition-all duration-200 hover:bg-slate-50 dark:hover:bg-slate-800 rounded-full active:scale-95">
          <span class="material-symbols-outlined text-slate-600">notifications</span>
        </button>
        <button
          class="p-2 transition-all duration-200 hover:bg-slate-50 dark:hover:bg-slate-800 rounded-full active:scale-95">
          <span class="material-symbols-outlined text-slate-600">settings</span>
        </button>
        <div class="w-8 h-8 rounded-full overflow-hidden ml-2 ring-2 ring-surface-container-highest">
          <img alt="Operator Avatar" class="w-full h-full object-cover"
            data-alt="Close-up portrait of a professional male highway operator in a smart uniform, clean lighting, studio background"
            src="https://lh3.googleusercontent.com/aida-public/AB6AXuDhvaV1U3ZLl4gdG95-OiIPrCe41blG3YoExDuAOXxv6shfPSGJt_JaWYMXuLsGdT5YHfIMb50LSBGAUNaxaQZkorHlNchAEVx9XUVHdB1ulfWGgP-rgWktZlXpWHqTWHeaRO2kgTRu5lIEAu8GSMkOpw3fVWctPBOSNOZIKCKWcBpb3gyJBNUdPhTu1kiwOGUIA2Stv6h2YPrR9WX4DLo0N_kUHuh24Co5jfwhNRg7joQVhKdhJu8CzCA30jQqy67wIQMnS-zdMn0V" />
        </div>
      </div>
    </div>
  </header>

  <?= $content ?>

  <!-- BottomNavBar for Mobile (Hidden on Desktop) -->
  <nav
    class="fixed bottom-0 w-full flex md:hidden justify-around items-center h-20 px-4 bg-[#fef9f1] dark:bg-primary z-50 border-t border-primary/5">
    <div class="flex flex-col items-center justify-center text-secondary dark:text-secondary-container font-bold">
      <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">directions_car</span>
      <span class="font-['Plus_Jakarta_Sans'] text-xs font-semibold">Passage</span>
    </div>
    <div class="flex flex-col items-center justify-center text-[#1d1c17] opacity-60 dark:text-slate-400">
      <span class="material-symbols-outlined">payments</span>
      <span class="font-['Plus_Jakarta_Sans'] text-xs font-semibold">Caisse</span>
    </div>
    <div class="flex flex-col items-center justify-center text-[#1d1c17] opacity-60 dark:text-slate-400">
      <span class="material-symbols-outlined">warning</span>
      <span class="font-['Plus_Jakarta_Sans'] text-xs font-semibold">Incident</span>
    </div>
    <div class="flex flex-col items-center justify-center text-[#1d1c17] opacity-60 dark:text-slate-400">
      <span class="material-symbols-outlined">schedule</span>
      <span class="font-['Plus_Jakarta_Sans'] text-xs font-semibold">Mon Shift</span>
    </div>
    <div class="flex flex-col items-center justify-center text-[#1d1c17] opacity-60 dark:text-slate-400">
      <span class="material-symbols-outlined">person</span>
      <span class="font-['Plus_Jakarta_Sans'] text-xs font-semibold">Profil</span>
    </div>
  </nav>
</body>

</html>