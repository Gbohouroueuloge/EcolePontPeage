<?php

$title = 'Parametres';

require __DIR__ . '/variables.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'] ?? '';

  if ($action === 'save_settings') {
    $settings = [
      'bridge_name' => trim($_POST['bridge_name'] ?? ''),
      'bridge_code' => trim($_POST['bridge_code'] ?? ''),
      'currency' => trim($_POST['currency'] ?? ''),
      'support_email' => trim($_POST['support_email'] ?? ''),
      'support_phone' => trim($_POST['support_phone'] ?? ''),
      'timezone' => trim($_POST['timezone'] ?? ''),
      'dashboard_refresh_seconds' => trim($_POST['dashboard_refresh_seconds'] ?? ''),
      'waiting_message' => trim($_POST['waiting_message'] ?? ''),
    ];

    $stmt = $pdo->prepare("
      INSERT INTO system_settings (setting_key, setting_value, updated_by)
      VALUES (:setting_key, :setting_value, :updated_by)
      ON DUPLICATE KEY UPDATE
        setting_value = VALUES(setting_value),
        updated_by = VALUES(updated_by)
    ");

    foreach ($settings as $key => $value) {
      $stmt->execute([
        'setting_key' => $key,
        'setting_value' => $value,
        'updated_by' => $user->id,
      ]);
    }

    header('Location: /pages/admin/parametres.php');
    exit();
  }

  if ($action === 'toggle_guichet') {
    $id = (int)($_POST['guichet_id'] ?? 0);
    $current = (int)($_POST['is_active'] ?? 0);
    if ($id > 0) {
      $next = $current ? 0 : 1;
      $pdo->beginTransaction();
      try {
        $pdo->prepare("UPDATE guichet SET is_active = :next WHERE id = :id")
          ->execute(['next' => $next, 'id' => $id]);

        if ($next === 0) {
          $pdo->prepare("
            UPDATE agent
            SET fin = CASE WHEN guichet_id = :id AND fin IS NULL THEN NOW() ELSE fin END,
                guichet_id = NULL,
                date_assignation = NULL,
                debut = NULL
            WHERE guichet_id = :id
          ")->execute(['id' => $id]);
        }

        $pdo->commit();
      } catch (Throwable $e) {
        $pdo->rollBack();
        header('Location: /pages/admin/parametres.php');
        exit();
      }

      header('Location: /pages/admin/parametres.php');
      exit();
    }
  }
}


$settingsRows = $pdo->query("SELECT setting_key, setting_value FROM system_settings")->fetchAll(PDO::FETCH_OBJ);
$settings = [];
foreach ($settingsRows as $row) {
  $settings[$row->setting_key] = $row->setting_value;
}

$guichets = $pdo->query("
  SELECT g.*, COUNT(p.id) AS total_passages, COALESCE(SUM(p.montant), 0) AS revenu_total
  FROM guichet g
  LEFT JOIN paiement p ON p.guichet_id = g.id
  GROUP BY g.id, g.slug, g.emplacement, g.is_active, g.created_at, g.updated_at
  ORDER BY g.id
")->fetchAll(PDO::FETCH_OBJ);

$totalUsers = (int)$pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$pendingOperators = (int)$pdo->query("
  SELECT COUNT(*)
  FROM users u
  LEFT JOIN agent a ON a.user_id = u.id
  WHERE u.role = 'operateur' AND (a.id IS NULL OR a.guichet_id IS NULL)
")->fetchColumn();

$pendingSupervisors = (int)$pdo->query("
  SELECT s.id
  FROM superviseur s
  JOIN users u ON u.id = s.user_id
  LEFT JOIN superviseur_guichet sg ON sg.superviseur_id = s.id
  WHERE u.role = 'superviseur'
  GROUP BY s.id
  HAVING COUNT(sg.guichet_id) = 0
")->rowCount();

$pendingAccounts = $pendingOperators + $pendingSupervisors;
?>

<!DOCTYPE html>
<html lang="fr">
<?php require __DIR__ . '/../../includes/head.php'; ?>
<?php require __DIR__ . '/../../layouts/headerAdmin.php'; ?>

<main class="md:ml-72 pt-20 px-6 md:px-8 pb-12">
  <div class="mt-4 mb-10 flex flex-col xl:flex-row xl:items-end xl:justify-between gap-6">
    <div>
      <p class="text-secondary font-mono text-xs font-bold tracking-[0.3em] uppercase mb-2">Systeme</p>
      <h1 class="text-5xl font-headline font-black tracking-tight text-primary">Configuration generale</h1>
      <p class="text-on-surface-variant mt-3">Tous les reglages fonctionnels du pont, du support et du message d'attente.</p>
    </div>
    <div class="flex flex-wrap gap-3">
      <span class="rounded-xl bg-surface-container-lowest px-4 py-3 text-xs font-bold uppercase tracking-[0.22em] text-primary"><?= $totalUsers ?> comptes</span>
      <span class="rounded-xl bg-surface-container-lowest px-4 py-3 text-xs font-bold uppercase tracking-[0.22em] text-primary"><?= $pendingAccounts ?> comptes a traiter</span>
    </div>
  </div>

  <div class="grid grid-cols-1 xl:grid-cols-12 gap-8">
    <section class="xl:col-span-7 rounded-4xl bg-surface-container-lowest p-8 shadow-sm border border-outline-variant/10">
      <h2 class="text-2xl font-headline font-bold text-primary mb-8">Identite et support</h2>
      <form method="post" class="grid md:grid-cols-2 gap-5">
        <input type="hidden" name="action" value="save_settings">
        <div>
          <label class="mb-2 block text-xs font-bold uppercase tracking-[0.22em] text-on-surface-variant">Nom du pont</label>
          <input name="bridge_name" value="<?= htmlspecialchars($settings['bridge_name'] ?? '') ?>" class="w-full rounded-xl border border-outline-variant/20 px-4 py-4">
        </div>
        <div>
          <label class="mb-2 block text-xs font-bold uppercase tracking-[0.22em] text-on-surface-variant">Code exploitation</label>
          <input name="bridge_code" value="<?= htmlspecialchars($settings['bridge_code'] ?? '') ?>" class="w-full rounded-xl border border-outline-variant/20 px-4 py-4">
        </div>
        <div>
          <label class="mb-2 block text-xs font-bold uppercase tracking-[0.22em] text-on-surface-variant">Devise</label>
          <input name="currency" value="<?= htmlspecialchars($settings['currency'] ?? '') ?>" class="w-full rounded-xl border border-outline-variant/20 px-4 py-4">
        </div>
        <div>
          <label class="mb-2 block text-xs font-bold uppercase tracking-[0.22em] text-on-surface-variant">Fuseau horaire</label>
          <input name="timezone" value="<?= htmlspecialchars($settings['timezone'] ?? 'UTC') ?>" class="w-full rounded-xl border border-outline-variant/20 px-4 py-4">
        </div>
        <div>
          <label class="mb-2 block text-xs font-bold uppercase tracking-[0.22em] text-on-surface-variant">Email support</label>
          <input type="email" name="support_email" value="<?= htmlspecialchars($settings['support_email'] ?? '') ?>" class="w-full rounded-xl border border-outline-variant/20 px-4 py-4">
        </div>
        <div>
          <label class="mb-2 block text-xs font-bold uppercase tracking-[0.22em] text-on-surface-variant">Telephone support</label>
          <input name="support_phone" value="<?= htmlspecialchars($settings['support_phone'] ?? '') ?>" class="w-full rounded-xl border border-outline-variant/20 px-4 py-4">
        </div>
        <div>
          <label class="mb-2 block text-xs font-bold uppercase tracking-[0.22em] text-on-surface-variant">Refresh dashboard (sec)</label>
          <input name="dashboard_refresh_seconds" value="<?= htmlspecialchars($settings['dashboard_refresh_seconds'] ?? '30') ?>" class="w-full rounded-xl border border-outline-variant/20 px-4 py-4">
        </div>
        <div class="md:col-span-2">
          <label class="mb-2 block text-xs font-bold uppercase tracking-[0.22em] text-on-surface-variant">Message d'attente</label>
          <textarea name="waiting_message" rows="5" class="w-full rounded-xl border border-outline-variant/20 px-4 py-4"><?= htmlspecialchars($settings['waiting_message'] ?? '') ?></textarea>
        </div>
        <div class="md:col-span-2 flex items-center justify-end">
          <button type="submit" class="rounded-xl bg-primary px-6 py-4 text-xs font-bold uppercase tracking-[0.22em] text-white">
            Enregistrer les parametres
          </button>
        </div>
      </form>
    </section>

    <aside class="xl:col-span-5 rounded-4xl bg-primary p-8 text-white shadow-xl">
      <h2 class="text-2xl font-headline font-bold">Etat du socle</h2>
      <p class="mt-3 text-sm leading-6 text-slate-300">Vue rapide des informations critiques exposees par la plateforme.</p>
      <div class="mt-8 space-y-4">
        <div class="rounded-xl border border-white/10 bg-white/5 p-4">
          <p class="text-[10px] font-bold uppercase tracking-[0.22em] text-slate-300">Bridge name</p>
          <p class="mt-2 text-lg font-semibold"><?= htmlspecialchars($settings['bridge_name'] ?? 'Pont a Peage Atlantique') ?></p>
        </div>
        <div class="rounded-xl border border-white/10 bg-white/5 p-4">
          <p class="text-[10px] font-bold uppercase tracking-[0.22em] text-slate-300">Support</p>
          <p class="mt-2 text-sm font-semibold"><?= htmlspecialchars($settings['support_email'] ?? 'support@peage.local') ?></p>
          <p class="text-sm text-slate-300"><?= htmlspecialchars($settings['support_phone'] ?? '+225 01 23 45 67 89') ?></p>
        </div>
        <div class="rounded-xl border border-white/10 bg-white/5 p-4">
          <p class="text-[10px] font-bold uppercase tracking-[0.22em] text-slate-300">Timeout dashboard</p>
          <p class="mt-2 text-lg font-semibold"><?= htmlspecialchars($settings['dashboard_refresh_seconds'] ?? '30') ?> secondes</p>
        </div>
      </div>
    </aside>

    <section class="xl:col-span-12 rounded-4xl bg-surface-container-lowest p-8 shadow-sm border border-outline-variant/10">
      <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 mb-8">
        <div>
          <h2 class="text-2xl font-headline font-bold text-primary">Etat des guichets</h2>
          <p class="text-sm text-on-surface-variant mt-2">Activez ou suspendez une voie depuis ce tableau d'exploitation.</p>
        </div>
      </div>
      <div class="grid md:grid-cols-2 xl:grid-cols-4 gap-4">
        <?php foreach ($guichets as $guichet) : ?>
          <form method="post" class="rounded-2xl border border-outline-variant/10 bg-surface-container-low p-5">
            <input type="hidden" name="action" value="toggle_guichet">
            <input type="hidden" name="guichet_id" value="<?= $guichet->id ?>">
            <input type="hidden" name="is_active" value="<?= (int)$guichet->is_active ?>">
            <div class="flex items-start justify-between gap-3">
              <div>
                <p class="text-[10px] font-bold uppercase tracking-[0.22em] text-on-surface-variant">Voie <?= $guichet->id ?></p>
                <h3 class="mt-2 text-xl font-headline font-bold text-primary"><?= htmlspecialchars($guichet->emplacement) ?></h3>
              </div>
              <span class="rounded-full px-3 py-1 text-[10px] font-bold uppercase tracking-[0.22em] <?= $guichet->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700' ?>">
                <?= $guichet->is_active ? 'Active' : 'Fermee' ?>
              </span>
            </div>
            <div class="mt-5 space-y-2 text-sm">
              <div class="flex items-center justify-between">
                <span class="text-on-surface-variant">Passages</span>
                <span class="font-mono font-bold text-primary"><?= (int)$guichet->total_passages ?></span>
              </div>
              <div class="flex items-center justify-between">
                <span class="text-on-surface-variant">Revenus</span>
                <span class="font-mono font-bold text-primary"><?= number_format((float)$guichet->revenu_total, 0, ',', ' ') ?> FCFA</span>
              </div>
            </div>
            <button type="submit" class="mt-5 w-full rounded-xl bg-primary px-4 py-3 text-xs font-bold uppercase tracking-[0.22em] text-white">
              <?= $guichet->is_active ? 'Desactiver la voie' : 'Reactiver la voie' ?>
            </button>
          </form>
        <?php endforeach; ?>
      </div>
    </section>
  </div>
</main>

<?php require __DIR__ . '/../../layouts/footerAdmin.php'; ?>
</html>
