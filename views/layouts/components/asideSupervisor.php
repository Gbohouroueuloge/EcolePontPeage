<div class="flex items-center gap-3 px-6 py-5">
  <img class="h-10 w-10 rounded-lg bg-white/10 p-1.5" src="/icons/peage_bridge_logo_africain.svg" alt="Peage Bridge">
  <div>
    <div class="font-headline text-lg font-black text-white">Peage Bridge</div>
    <div class="text-[10px] uppercase tracking-[0.2em] text-slate-400">Superviseur</div>
  </div>
</div>

<nav class="mt-2 flex-1 px-0">
  <?php foreach ($navLinks ?? [] as $link) : ?>
    <?php if ($link['isTitle']) : ?>
      <div class="px-6 py-2">
        <p class="text-[10px] font-bold uppercase tracking-widest text-slate-500"><?= $link['text'] ?></p>
      </div>
    <?php else : ?>
      <a
        href="<?= $link['link'] ?>"
        class="mb-2 flex items-center gap-3 px-6 py-2 transition-all <?= $title === $link['text'] ? 'border-l-4 border-secondary-container bg-white/5 text-secondary-container active-nav-glow' : 'text-slate-400 hover:bg-white/10 hover:text-white' ?>">
        <span class="material-symbols-outlined text-lg"><?= $link['icon'] ?></span>
        <span class="text-sm font-medium"><?= $link['text'] ?></span>
      </a>
    <?php endif; ?>
  <?php endforeach; ?>
</nav>

<div class="mt-auto bg-white/5 p-6">
  <div class="flex items-center gap-3">
    <div class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-surface-container shadow-sm">
      <span class="font-mono text-xl font-black uppercase text-primary">
        <?= substr($user->username ?? '', 0, 2) ?>
      </span>
    </div>
    <div>
      <p class="text-xs font-bold text-white"><?= htmlspecialchars($user->username ?? '') ?></p>
      <p class="mt-1 text-[10px] text-slate-400"><?= htmlspecialchars($user->email ?? '') ?></p>
    </div>
  </div>

  <div class="mt-4 rounded-2xl bg-white/5 p-4">
    <div class="text-[10px] uppercase tracking-[0.2em] text-slate-500">Zone</div>
    <div class="mt-2 text-sm font-bold text-white"><?= htmlspecialchars($supervisorProfile->zone_nominale ?? 'Non definie') ?></div>
  </div>

  <a
    href="?logout"
    class="mt-4 flex items-center justify-center gap-2 rounded-2xl border border-error/40 px-4 py-3 text-sm font-bold text-error transition hover:bg-white/5">
    <span class="material-symbols-outlined text-lg">logout</span>
    Deconnexion
  </a>
</div>
