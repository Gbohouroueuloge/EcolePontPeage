<!-- Bouton -->
<div class="relative" id="user-menu-wrapper">
  <button
    id="user-menu-btn"
    href="#"
    onclick="toggleUserMenu(event)"
    class="relative inline-flex group cursor-pointer">
    <div class="flex items-center justify-center w-14 h-14 rounded-full overflow-hidden border-2 border-surface-container-high bg-<?= $type === "admin" ? "primary" : "surface-container" ?> shadow-sm transition-all duration-300 group-hover:shadow-md group-hover:border-<?= $type === "admin" ? "primary" : "surface-container" ?> group-hover:scale-105">
      <span class="text-on-primary uppercase text-2xl font-black font-mono transition-transform duration-300 group-hover:scale-110">
        <?= substr($user->username, 0, 2) ?>
      </span>
    </div>
    <span class="absolute bottom-0 right-0 w-3.5 h-3.5 bg-green-500 border-2 border-white rounded-full z-10"></span>
    <span class="absolute bottom-0 right-0 w-3.5 h-3.5 bg-green-400 rounded-full animate-ping opacity-75"></span>
  </button>

  <!-- Panel -->
  <div
    id="user-menu-panel"
    class="hidden absolute right-0 mt-4 w-64 bg-white/90 backdrop-blur-xl rounded-3xl shadow-[0_20px_50px_rgba(0,0,0,0.2)] border border-slate-100 py-3 z-50">

    <!-- Header -->
    <div class="px-5 py-3 mb-2 border-b border-slate-100">
      <p class="font-bold text-slate-800 truncate"><?= $user->email ?></p>
    </div>

    <div class="px-2 space-y-1">
      <a href="/<?= $type ?? 'operator'  ?>" class="flex items-center gap-3 w-full px-4 py-3 rounded-xl text-slate-700 hover:bg-slate-100 font-semibold transition-all">
        <span class="material-symbols-outlined text-base">person</span>
        Mon Compte
      </a>
      <a href="/<?= $type ?? 'operator'  ?>/<?= $type === 'operator' ? 'mon-shift' : 'parametre' ?>" class="flex items-center gap-3 w-full px-4 py-3 rounded-xl text-slate-700 hover:bg-slate-100 font-semibold transition-all">
        <span class="material-symbols-outlined text-base">settings</span>
        Paramètres
      </a>

      <div class="my-2 border-t border-slate-100 mx-3"></div>

      <a href="/?logout" class="flex items-center gap-3 w-full px-4 py-3 rounded-xl text-red-500 hover:bg-red-50 font-bold transition-all">
        <span class="material-symbols-outlined text-base">logout</span>
        Déconnexion
      </a>
    </div>
  </div>
</div>

<script>
  function toggleUserMenu(e) {
    e.preventDefault();
    document.getElementById('user-menu-panel').classList.toggle('hidden');
  }

  // Fermer en cliquant en dehors
  document.addEventListener('click', function(e) {
    const wrapper = document.getElementById('user-menu-wrapper');
    const panel = document.getElementById('user-menu-panel');

    if (!wrapper.contains(e.target)) {
      panel.classList.add('hidden');
    }
  });
</script>