

  <nav
    class="fixed bottom-0 w-full mt-18 flex md:hidden justify-around items-center h-20 px-4 bg-[#fef9f1] dark:bg-primary z-50 border-t border-primary/5">
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