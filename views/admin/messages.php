<?php

$title = 'Messages';

require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'admin/variables.php';

if (!empty($_GET['read'])) {
  $adminService->markNotificationRead((int) $_GET['read']);
  header('Location: /admin/messages');
  exit;
}

$notifications = $adminService->getNotifications(false, 100);
$unread = $adminService->countUnreadNotifications();
$categories = [];
foreach ($notifications as $notification) {
  $categories[$notification['category']] = ($categories[$notification['category']] ?? 0) + 1;
}
?>

<main class="md:ml-64 px-6 pb-12 pt-24 md:px-8">
  <section class="mb-10 flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
    <div>
      <div class="text-[11px] font-bold uppercase tracking-[0.3em] text-secondary">Communication</div>
      <h1 class="mt-2 font-headline text-5xl font-black tracking-tight text-primary">Messages administrateur</h1>
      <p class="mt-3 max-w-3xl text-sm leading-7 text-on-surface-variant">
        Suivez les demandes d affectation, les alertes systeme et les signaux operationnels.
      </p>
    </div>

    <div class="rounded-3xl border border-primary/10 bg-white/80 px-8 py-4 shadow-sm">
      <div class="text-[10px] uppercase tracking-[0.2em] text-on-surface-variant">Non lus</div>
      <div class="mt-2 font-mono text-3xl font-bold text-primary"><?= $unread ?></div>
    </div>
  </section>

  <section class="mb-8 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
    <div class="rounded-3xl border border-primary/10 bg-white p-5 shadow-sm">
      <div class="text-[10px] uppercase tracking-[0.2em] text-on-surface-variant">Notifications</div>
      <div class="mt-3 font-mono text-3xl font-bold text-primary"><?= count($notifications) ?></div>
    </div>
    <div class="rounded-3xl border border-primary/10 bg-white p-5 shadow-sm">
      <div class="text-[10px] uppercase tracking-[0.2em] text-on-surface-variant">Demandes attente</div>
      <div class="mt-3 font-mono text-3xl font-bold text-primary"><?= $categories['operator_waiting'] ?? 0 ?></div>
    </div>
    <div class="rounded-3xl border border-primary/10 bg-white p-5 shadow-sm">
      <div class="text-[10px] uppercase tracking-[0.2em] text-on-surface-variant">Categories actives</div>
      <div class="mt-3 font-mono text-3xl font-bold text-primary"><?= count($categories) ?></div>
    </div>
    <div class="rounded-3xl border border-primary/10 bg-white p-5 shadow-sm">
      <div class="text-[10px] uppercase tracking-[0.2em] text-on-surface-variant">Derniere alerte</div>
      <div class="mt-3 text-lg font-bold text-primary">
        <?= !empty($notifications[0]['created_at']) ? date('d/m H:i', strtotime($notifications[0]['created_at'])) : 'Aucune' ?>
      </div>
    </div>
  </section>

  <section class="rounded-4xl border border-primary/10 bg-white p-6 shadow-sm">
    <div class="mb-6">
      <div class="text-[11px] font-bold uppercase tracking-[0.24em] text-secondary">File de messages</div>
      <h2 class="mt-2 font-headline text-3xl font-black text-primary">Centre de notifications</h2>
    </div>

    <div class="space-y-4">
      <?php if (!$notifications) : ?>
        <div class="rounded-3xl bg-surface-container-low p-8 text-center text-on-surface-variant">
          Aucune notification pour le moment.
        </div>
      <?php endif; ?>

      <?php foreach ($notifications as $notification) : ?>
        <?php
        $tone = match ($notification['category']) {
          'operator_waiting' => 'border-secondary-container bg-secondary-container/10',
          default => 'border-primary/10 bg-surface-container-low',
        };
        ?>
        <div class="rounded-3xl border px-5 py-5 <?= $tone ?>">
          <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div class="max-w-3xl">
              <div class="flex flex-wrap items-center gap-3">
                <h3 class="font-headline text-xl font-bold text-primary"><?= htmlspecialchars($notification['title']) ?></h3>
                <?php if (!$notification['is_read']) : ?>
                  <span class="rounded-full bg-primary px-3 py-1 text-[10px] font-black uppercase tracking-[0.2em] text-white">Nouveau</span>
                <?php endif; ?>
              </div>

              <p class="mt-3 text-sm leading-7 text-on-surface-variant"><?= htmlspecialchars($notification['message']) ?></p>

              <div class="mt-3 flex flex-wrap gap-4 text-[11px] font-semibold text-primary">
                <span>Categorie: <?= htmlspecialchars($notification['category']) ?></span>
                <?php if ($notification['username']) : ?>
                  <span>Utilisateur: <?= htmlspecialchars($notification['username']) ?> (<?= htmlspecialchars($notification['role'] ?? 'n/a') ?>)</span>
                <?php endif; ?>
                <span>Date: <?= date('d/m/Y H:i', strtotime($notification['created_at'])) ?></span>
              </div>
            </div>

            <div class="flex gap-2">
              <?php if (!$notification['is_read']) : ?>
                <a
                  class="rounded-2xl bg-primary px-4 py-3 text-sm font-bold uppercase tracking-[0.2em] text-white"
                  href="/admin/messages?read=<?= $notification['id'] ?>">
                  Marquer lu
                </a>
              <?php endif; ?>

              <?php if ($notification['category'] === 'operator_waiting' && $notification['username']) : ?>
                <a
                  class="rounded-2xl border border-primary/15 px-4 py-3 text-sm font-bold uppercase tracking-[0.2em] text-primary"
                  href="/admin/utilisateurs?role=operateur">
                  Affecter
                </a>
              <?php endif; ?>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </section>
</main>
