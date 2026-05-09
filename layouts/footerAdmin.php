
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