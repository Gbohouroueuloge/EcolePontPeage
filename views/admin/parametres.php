<?php

use App\Services\AnalyticsService;

$title = 'Parametres';

require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'admin/variables.php';

$analyticsService = new AnalyticsService($pdo);
$flash = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'] ?? 'save_settings';

  if ($action === 'toggle_guichet') {
    $guichetId = (int) ($_POST['guichet_id'] ?? 0);
    $currentValue = (int) ($_POST['is_active'] ?? 0);
    $stmt = $pdo->prepare("UPDATE guichet SET is_active = ? WHERE id = ?");
    $stmt->execute([$currentValue ? 0 : 1, $guichetId]);
    header('Location: /admin/parametres?saved=1&message=Statut+guichet+mis+a+jour');
    exit;
  }

  if ($action === 'reactivate_guichets') {
    $pdo->exec("UPDATE guichet SET is_active = 1");
    header('Location: /admin/parametres?saved=1&message=Toutes+les+voies+sont+reactivees');
    exit;
  }

  if ($action === 'save_settings') {
    $adminService->saveSettings($_POST, (int) $user->id);
    header('Location: /admin/parametres?saved=1&message=Configuration+enregistree');
    exit;
  }
}

if (!empty($_GET['saved'])) {
  $flash = urldecode($_GET['message'] ?? 'Configuration enregistree');
}

$settings = $adminService->getSettings();
$guichets = $adminService->getGuichets();
$stats = $adminService->getUserStats();
$overview = $analyticsService->getOverview(
  (new DateTimeImmutable('today'))->modify('-29 days')->setTime(0, 0, 0),
  (new DateTimeImmutable('today'))->setTime(23, 59, 59)
);
?>

<main class="md:ml-64 px-6 pb-12 pt-24 md:px-8">
  <section class="mb-10 flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
    <div>
      <div class="text-[11px] font-bold uppercase tracking-[0.3em] text-secondary">Configuration</div>
      <h1 class="mt-2 font-headline text-5xl font-black tracking-tight text-primary">Parametres administrateur</h1>
      <p class="mt-3 max-w-3xl text-sm leading-7 text-on-surface-variant">
        Parametres persistants du pont, contact support, frequence dashboard et activation des voies.
      </p>
    </div>

    <form method="post">
      <input type="hidden" name="action" value="reactivate_guichets">
      <button class="rounded-2xl border border-primary/15 px-4 py-3 text-xs font-bold uppercase tracking-[0.2em] text-primary" type="submit">
        Reactiver toutes les voies
      </button>
    </form>
  </section>

  <?php if ($flash) : ?>
    <div class="mb-6 rounded-3xl border border-brand-success/20 bg-brand-success/10 px-5 py-4 text-sm font-semibold text-brand-success">
      <?= htmlspecialchars($flash) ?>
    </div>
  <?php endif; ?>

  <section class="mb-8 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
    <div class="rounded-3xl border border-primary/10 bg-white p-5 shadow-sm">
      <div class="text-[10px] uppercase tracking-[0.2em] text-on-surface-variant">Voies actives</div>
      <div class="mt-3 font-mono text-3xl font-bold text-primary"><?= $overview['guichets_actifs'] ?></div>
      <div class="mt-2 text-sm text-on-surface-variant">sur <?= count($guichets) ?> voies</div>
    </div>
    <div class="rounded-3xl border border-primary/10 bg-white p-5 shadow-sm">
      <div class="text-[10px] uppercase tracking-[0.2em] text-on-surface-variant">Agents actifs</div>
      <div class="mt-3 font-mono text-3xl font-bold text-primary"><?= $overview['agents_actifs'] ?></div>
    </div>
    <div class="rounded-3xl border border-primary/10 bg-white p-5 shadow-sm">
      <div class="text-[10px] uppercase tracking-[0.2em] text-on-surface-variant">Comptes</div>
      <div class="mt-3 font-mono text-3xl font-bold text-primary"><?= $stats['total'] ?></div>
      <div class="mt-2 text-sm text-on-surface-variant"><?= $stats['pending_assignments'] ?> en attente d affectation</div>
    </div>
    <div class="rounded-3xl border border-primary/10 bg-white p-5 shadow-sm">
      <div class="text-[10px] uppercase tracking-[0.2em] text-on-surface-variant">Support</div>
      <div class="mt-3 text-lg font-bold text-primary"><?= htmlspecialchars($settings['support_email']) ?></div>
    </div>
  </section>

  <section class="grid gap-8 xl:grid-cols-[0.95fr_1.05fr]">
    <div class="rounded-4xl border border-primary/10 bg-white p-6 shadow-sm">
      <div class="mb-6">
        <div class="text-[11px] font-bold uppercase tracking-[0.24em] text-secondary">Systeme</div>
        <h2 class="mt-2 font-headline text-3xl font-black text-primary">Configuration persistante</h2>
      </div>

      <form method="post" class="space-y-5">
        <input type="hidden" name="action" value="save_settings">

        <div>
          <label class="mb-2 block text-sm font-semibold text-primary">Nom du pont</label>
          <input class="w-full rounded-2xl border border-outline-variant/40 bg-surface-container-low px-4 py-3 outline-none focus:border-secondary" type="text" name="bridge_name" value="<?= htmlspecialchars($settings['bridge_name']) ?>">
        </div>

        <div class="grid gap-5 md:grid-cols-2">
          <div>
            <label class="mb-2 block text-sm font-semibold text-primary">Code infrastructure</label>
            <input class="w-full rounded-2xl border border-outline-variant/40 bg-surface-container-low px-4 py-3 outline-none focus:border-secondary" type="text" name="bridge_code" value="<?= htmlspecialchars($settings['bridge_code']) ?>">
          </div>
          <div>
            <label class="mb-2 block text-sm font-semibold text-primary">Devise</label>
            <input class="w-full rounded-2xl border border-outline-variant/40 bg-surface-container-low px-4 py-3 outline-none focus:border-secondary" type="text" name="currency" value="<?= htmlspecialchars($settings['currency']) ?>">
          </div>
        </div>

        <div class="grid gap-5 md:grid-cols-2">
          <div>
            <label class="mb-2 block text-sm font-semibold text-primary">Email support</label>
            <input class="w-full rounded-2xl border border-outline-variant/40 bg-surface-container-low px-4 py-3 outline-none focus:border-secondary" type="email" name="support_email" value="<?= htmlspecialchars($settings['support_email']) ?>">
          </div>
          <div>
            <label class="mb-2 block text-sm font-semibold text-primary">Telephone support</label>
            <input class="w-full rounded-2xl border border-outline-variant/40 bg-surface-container-low px-4 py-3 outline-none focus:border-secondary" type="text" name="support_phone" value="<?= htmlspecialchars($settings['support_phone']) ?>">
          </div>
        </div>

        <div class="grid gap-5 md:grid-cols-2">
          <div>
            <label class="mb-2 block text-sm font-semibold text-primary">Fuseau horaire</label>
            <input class="w-full rounded-2xl border border-outline-variant/40 bg-surface-container-low px-4 py-3 outline-none focus:border-secondary" type="text" name="timezone" value="<?= htmlspecialchars($settings['timezone']) ?>">
          </div>
          <div>
            <label class="mb-2 block text-sm font-semibold text-primary">Refresh dashboard (secondes)</label>
            <input class="w-full rounded-2xl border border-outline-variant/40 bg-surface-container-low px-4 py-3 outline-none focus:border-secondary" type="number" min="10" name="dashboard_refresh_seconds" value="<?= htmlspecialchars($settings['dashboard_refresh_seconds']) ?>">
          </div>
        </div>

        <div>
          <label class="mb-2 block text-sm font-semibold text-primary">Message de la page d attente</label>
          <textarea class="w-full rounded-2xl border border-outline-variant/40 bg-surface-container-low px-4 py-3 outline-none focus:border-secondary" name="waiting_message" rows="5"><?= htmlspecialchars($settings['waiting_message']) ?></textarea>
        </div>

        <button class="w-full rounded-2xl bg-primary px-4 py-3 text-sm font-bold uppercase tracking-[0.2em] text-white" type="submit">
          Enregistrer les parametres
        </button>
      </form>
    </div>

    <div class="rounded-4xl border border-primary/10 bg-white p-6 shadow-sm">
      <div class="mb-6">
        <div class="text-[11px] font-bold uppercase tracking-[0.24em] text-secondary">Infrastructure</div>
        <h2 class="mt-2 font-headline text-3xl font-black text-primary">Activation des guichets</h2>
      </div>

      <div class="space-y-4">
        <?php foreach ($guichets as $guichet) : ?>
          <div class="rounded-3xl bg-surface-container-low p-5">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
              <div>
                <div class="font-bold text-primary">Voie <?= $guichet['id'] ?> - <?= htmlspecialchars($guichet['emplacement']) ?></div>
                <div class="mt-2 text-sm text-on-surface-variant"><?= $guichet['is_active'] ? 'Voie disponible pour affectation' : 'Voie desactivee temporairement' ?></div>
              </div>

              <form method="post">
                <input type="hidden" name="action" value="toggle_guichet">
                <input type="hidden" name="guichet_id" value="<?= $guichet['id'] ?>">
                <input type="hidden" name="is_active" value="<?= $guichet['is_active'] ?>">
                <button class="rounded-2xl px-4 py-3 text-xs font-bold uppercase tracking-[0.2em] <?= $guichet['is_active'] ? 'bg-error/10 text-error' : 'bg-brand-success/10 text-brand-success' ?>" type="submit">
                  <?= $guichet['is_active'] ? 'Desactiver' : 'Reactiver' ?>
                </button>
              </form>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
</main>
