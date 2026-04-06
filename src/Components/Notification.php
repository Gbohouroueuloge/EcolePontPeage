<?php
namespace App\Components;

class Notification
{
  private const STYLES = [
    'success' => [
      'container' => 'bg-primary-container gold-glow',
      'icon_bg'   => 'bg-secondary',
      'icon'      => 'check',
    ],
    'error' => [
      'container' => 'bg-red-900/80',
      'icon_bg'   => 'bg-red-500',
      'icon'      => 'error',
    ],
    'warning' => [
      'container' => 'bg-yellow-900/80',
      'icon_bg'   => 'bg-yellow-500',
      'icon'      => 'warning',
    ],
  ];

  public static function display(string $message, string $type = 'success', int $time = 3000): string
  {
    $style     = self::STYLES[$type] ?? self::STYLES['success'];
    $container = $style['container'];
    $icon_bg   = $style['icon_bg'];
    $icon      = $style['icon'];

    return <<<HTML
      <div
        id="notification"
        class="fixed bottom-5 right-10 {$container} text-white py-2 px-4 rounded-xl monolith-shadow flex items-center gap-4 z-999 transition-all duration-500"
      >
        <div class="{$icon_bg} p-1.5 rounded-full flex items-center justify-center">
          <span class="material-symbols-outlined text-white text-sm" style="font-variation-settings: 'FILL' 1;">{$icon}</span>
        </div>
        <div>
          <p class="text-xs text-white font-bold">{$type}</p>
          <p class="text-[11px] mono-data">{$message}</p>
        </div>
        <button onclick="this.closest('#notification').remove()" class="ml-4 text-slate-500 hover:text-white transition-colors">
          <span class="material-symbols-outlined text-lg">close</span>
        </button>
      </div>

      <script>
        setTimeout(() => {
          const n = document.getElementById('notification');
          if (n) {
            n.style.opacity = '0';
            n.style.transform = 'translateY(1rem)';
            setTimeout(() => n.remove(), 500);
          }
        }, {$time});
      </script>
    HTML;
  }
}
