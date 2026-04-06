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

<div class="p-6 -mb-6 bg-white/5 mt-auto">
  <div class="flex items-center gap-3">
    <a
      href="/admin/<?= $url ?>/parametres"
      class="relative inline-flex group cursor-pointer">
      <div class="flex items-center justify-center w-10 h-10 rounded-full overflow-hidden border-2 border-surface-container-high bg-surface-container shadow-sm transition-all duration-300 group-hover:shadow-md group-hover:border-primary group-hover:scale-105">
        <span class="text-primary uppercase text-2xl font-black font-mono transition-transform duration-300 group-hover:scale-110" data-icon="person">
          <?= substr($user->username, 0, 2) ?>
        </span>
      </div>
      <span class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-green-500 border-2 border-white rounded-full z-10"></span>
      <span class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-green-400 rounded-full animate-ping opacity-75"></span>
    </a>
    <div>
      <p class="text-xs font-bold text-white leading-none"><?= $user->username ?></p>
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