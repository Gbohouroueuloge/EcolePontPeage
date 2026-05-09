<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $title ?? 'Peage Bridge' ?></title>
  <link rel="shortcut icon" href="icons/favicon.ico" type="image/x-icon">
  <link rel="stylesheet" href="/output.css">
  <script>
    // ── Intersection Observer pour les révélations scroll ──
    document.addEventListener('DOMContentLoaded', () => {
      const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry, i) => {
          if (entry.isIntersecting) {
            const el = entry.target;
            const delay = el.dataset.delay ?? 0;
            setTimeout(() => el.classList.add('visible'), parseInt(delay));
            observer.unobserve(el);
          }
        });
      }, { threshold: 0.1 });

      document.querySelectorAll('.reveal, .reveal-left').forEach(el => observer.observe(el));

      // ── Animation des barres de progression ──
      document.querySelectorAll('.progress-bar-anim').forEach(bar => {
        const target = bar.dataset.width ?? '0%';
        bar.style.setProperty('--progress-w', target);
        observer.observe(bar);
      });

      // ── Ripple effect sur les boutons ──
      document.querySelectorAll('.btn-micro').forEach(btn => {
        btn.addEventListener('click', function(e) {
          const ripple = document.createElement('span');
          ripple.className = 'ripple';
          const rect = this.getBoundingClientRect();
          const size = Math.max(rect.width, rect.height);
          ripple.style.cssText = `width:${size}px;height:${size}px;left:${e.clientX - rect.left - size/2}px;top:${e.clientY - rect.top - size/2}px`;
          this.appendChild(ripple);
          setTimeout(() => ripple.remove(), 600);
        });
      });

      // ── Animation des barres du graphique ──
      document.querySelectorAll('.bar-animate').forEach((bar, i) => {
        const h = bar.style.height;
        bar.style.setProperty('--bar-h', h);
        bar.style.height = '0';
        bar.style.animationDelay = `${i * 80}ms`;
      });

      // ── Stagger automatique sur les listes d'activités ──
      document.querySelectorAll('[data-stagger]').forEach(container => {
        const children = container.querySelectorAll('[data-stagger-item]');
        children.forEach((child, i) => {
          child.style.animationDelay = `${i * 60}ms`;
        });
      });
    });
  </script>
</head>