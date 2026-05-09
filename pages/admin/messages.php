<?php

$title = 'Messages';

require __DIR__ . '/variables.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'] ?? '';

  if ($action === 'mark_one_read') {
    $id = (int)($_POST['notification_id'] ?? 0);
    if ($id > 0) {
      $pdo->prepare("UPDATE admin_notifications SET is_read = 1 WHERE id = :id")->execute(['id' => $id]);
      header('Location: /pages/admin/messages.php');
      exit();
    }
  }

  if ($action === 'mark_all_read') {
    $pdo->exec("UPDATE admin_notifications SET is_read = 1 WHERE is_read = 0");
    header('Location: /pages/admin/messages.php');
    exit();
  }
}

$notifications = $pdo->query("
  SELECT n.*, u.username, u.role
  FROM admin_notifications n
  LEFT JOIN users u ON u.id = n.user_id
  ORDER BY n.created_at DESC
")->fetchAll(PDO::FETCH_OBJ);

$unreadCount = count(array_filter($notifications, fn($n) => (int)$n->is_read === 0));
$pendingOperators = $pdo->query("
  SELECT u.username, u.email
  FROM users u
  LEFT JOIN agent a ON a.user_id = u.id
  WHERE u.role = 'operateur' AND (a.id IS NULL OR a.guichet_id IS NULL)
  ORDER BY u.created_at DESC
")->fetchAll(PDO::FETCH_OBJ);

$pendingSupervisors = $pdo->query("
  SELECT u.username, u.email
  FROM users u
  LEFT JOIN superviseur s ON s.user_id = u.id
  LEFT JOIN superviseur_guichet sg ON sg.superviseur_id = s.id
  WHERE u.role = 'superviseur'
  GROUP BY u.id, u.username, u.email
  HAVING COUNT(sg.guichet_id) = 0
  ORDER BY MAX(u.created_at) DESC
")->fetchAll(PDO::FETCH_OBJ);

function adminMessageLink(object $notification): string
{
  return str_contains($notification->category, 'supervisor')
    ? '/pages/admin/superviseurs.php'
    : '/pages/admin/operateurs.php';
}
?>

<!DOCTYPE html>
<html lang="fr">
<?php require __DIR__ . '/../../includes/head.php'; ?>
<?php require __DIR__ . '/../../layouts/headerAdmin.php'; ?>

<main class="md:ml-72 pt-20 px-6 md:px-8 pb-12">
  <div class="mt-4 mb-10 flex flex-col xl:flex-row xl:items-end xl:justify-between gap-6">
    <div>
      <p class="text-secondary font-mono text-xs font-bold tracking-[0.3em] uppercase mb-2">Flux admin</p>
      <h1 class="text-5xl font-headline font-black tracking-tight text-primary">Messages et alertes</h1>
      <p class="text-on-surface-variant mt-3">Notifications d'affectation, points de blocage et suivi des connexions.</p>
    </div>
    <?php if($unreadCount > 0): ?>
    <form method="post">
      <input type="hidden" name="action" value="mark_all_read">
      <button type="submit" class="rounded-xl bg-primary px-5 py-4 text-xs font-bold uppercase tracking-[0.22em] text-white">
        Tout marquer comme lu
      </button>
    </form>
    <?php endif ?>
  </div>

  <section class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-surface-container-lowest rounded-2xl p-6 shadow-sm border-l-4 border-primary">
      <p class="text-xs font-bold uppercase tracking-widest text-on-surface-variant">Messages</p>
      <p class="mt-3 font-mono text-4xl font-bold text-primary"><?= count($notifications) ?></p>
    </div>
    <div class="bg-surface-container-lowest rounded-2xl p-6 shadow-sm border-l-4 border-secondary">
      <p class="text-xs font-bold uppercase tracking-widest text-on-surface-variant">Non lus</p>
      <p class="mt-3 font-mono text-4xl font-bold text-primary"><?= $unreadCount ?></p>
    </div>
    <div class="bg-surface-container-lowest rounded-2xl p-6 shadow-sm border-l-4 border-error">
      <p class="text-xs font-bold uppercase tracking-widest text-on-surface-variant">Comptes en attente</p>
      <p class="mt-3 font-mono text-4xl font-bold text-error"><?= count($pendingOperators) + count($pendingSupervisors) ?></p>
    </div>
  </section>

  <section class="grid grid-cols-1 xl:grid-cols-12 gap-8">
    <div class="xl:col-span-8 space-y-4">
      <?php foreach ($notifications as $notification) : ?>
        <article class="rounded-2xl border p-6 shadow-sm <?= $notification->is_read ? 'border-outline-variant/15 bg-surface-container-lowest' : 'border-secondary/20 bg-secondary/5' ?>">
          <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
            <div>
              <div class="flex items-center gap-3 flex-wrap">
                <h2 class="text-xl font-headline font-bold text-primary"><?= htmlspecialchars($notification->title) ?></h2>
                <?php if (!(int)$notification->is_read) : ?>
                  <span class="rounded-full bg-secondary px-2 py-1 text-[10px] font-bold uppercase tracking-widest text-primary">Nouveau</span>
                <?php endif; ?>
              </div>
              <p class="mt-3 text-sm leading-7 text-on-surface-variant"><?= htmlspecialchars($notification->message) ?></p>
              <p class="mt-3 text-[10px] uppercase tracking-[0.22em] text-on-surface-variant">
                <?= $notification->username ? htmlspecialchars($notification->username) . ' • ' : '' ?><?= date('d/m/Y H:i', strtotime($notification->created_at)) ?>
              </p>
            </div>
            <div class="flex flex-col gap-2 min-w-44">
              <a href="<?= adminMessageLink($notification) ?>" class="rounded-xl border border-outline-variant/20 px-4 py-3 text-center text-xs font-bold uppercase tracking-[0.22em] text-primary">
                Ouvrir la gestion
              </a>
              <?php if (!(int)$notification->is_read) : ?>
                <form method="post">
                  <input type="hidden" name="action" value="mark_one_read">
                  <input type="hidden" name="notification_id" value="<?= $notification->id ?>">
                  <button type="submit" class="w-full rounded-xl bg-primary px-4 py-3 text-xs font-bold uppercase tracking-[0.22em] text-white">
                    Marquer comme lu
                  </button>
                </form>
              <?php endif; ?>
            </div>
          </div>
        </article>
      <?php endforeach; ?>
    </div>

    <aside class="xl:col-span-4 space-y-6">
      <div class="rounded-2xl bg-surface-container-lowest p-6 shadow-sm border border-outline-variant/10">
        <h2 class="text-xl font-headline font-bold text-primary">Agents en attente</h2>
        <div class="mt-5 space-y-3">
          <?php foreach ($pendingOperators as $pending) : ?>
            <div class="rounded-xl bg-surface-container-low p-4">
              <p class="font-semibold text-primary"><?= htmlspecialchars($pending->username) ?></p>
              <p class="text-xs text-on-surface-variant"><?= htmlspecialchars($pending->email) ?></p>
            </div>
          <?php endforeach; ?>
          <?php if (empty($pendingOperators)) : ?>
            <p class="text-sm text-on-surface-variant">Aucun operateur en attente.</p>
          <?php endif; ?>
        </div>
      </div>

      <div class="rounded-2xl bg-surface-container-lowest p-6 shadow-sm border border-outline-variant/10">
        <h2 class="text-xl font-headline font-bold text-primary">Superviseurs en attente</h2>
        <div class="mt-5 space-y-3">
          <?php foreach ($pendingSupervisors as $pending) : ?>
            <div class="rounded-xl bg-surface-container-low p-4">
              <p class="font-semibold text-primary"><?= htmlspecialchars($pending->username) ?></p>
              <p class="text-xs text-on-surface-variant"><?= htmlspecialchars($pending->email) ?></p>
            </div>
          <?php endforeach; ?>
          <?php if (empty($pendingSupervisors)) : ?>
            <p class="text-sm text-on-surface-variant">Aucun superviseur en attente.</p>
          <?php endif; ?>
        </div>
      </div>
    </aside>
  </section>
</main>

<?php require __DIR__ . '/../../layouts/footerAdmin.php'; ?>
</html>
