<?php

$title = 'Operateurs';

require __DIR__ . '/variables.php';

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'] ?? '';

  if ($action === 'create_operator') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username && $email && $password) {
      $exists = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
      $exists->execute(['email' => $email]);

      if ((int)$exists->fetchColumn() > 0) {
        $error = 'Cette adresse e-mail existe deja.';
      } else {
        $pdo->beginTransaction();
        try {
          $stmt = $pdo->prepare("
            INSERT INTO users (username, email, password, role)
            VALUES (:username, :email, :password, 'operateur')
          ");
          $stmt->execute([
            'username' => $username,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_BCRYPT),
          ]);

          $userId = (int)$pdo->lastInsertId();
          $pdo->prepare("INSERT INTO agent (user_id) VALUES (:user_id)")
            ->execute(['user_id' => $userId]);

          $pdo->commit();
          header('Location: /pages/admin/operateurs.php');
          exit();
        } catch (Throwable $e) {
          $pdo->rollBack();

          $error = 'Impossible de creer cet operateur.';
        }
      }
    }
  }

  if ($action === 'assign_operator') {
    $agentId = (int)($_POST['agent_id'] ?? 0);
    $guichetId = (int)($_POST['guichet_id'] ?? 0);

    if ($agentId > 0) {
      if ($guichetId > 0) {
        $laneStmt = $pdo->prepare("SELECT is_active FROM guichet WHERE id = :id LIMIT 1");
        $laneStmt->execute(['id' => $guichetId]);
        $laneIsActive = $laneStmt->fetchColumn();

        if ((int)$laneIsActive !== 1) {
          header('Location: /pages/admin/operateurs.php');
          exit();
        }
      }

      $stmt = $pdo->prepare("
        UPDATE agent
        SET guichet_id = :guichet_id,
            date_assignation = CASE WHEN :guichet_id = 0 THEN NULL ELSE NOW() END,
            debut = CASE WHEN :guichet_id = 0 THEN NULL ELSE COALESCE(debut, NOW()) END,
            fin = CASE WHEN :guichet_id = 0 THEN NULL ELSE fin END
        WHERE id = :agent_id
      ");
      $stmt->execute([
        'guichet_id' => $guichetId ?: null,
        'agent_id' => $agentId,
      ]);

      header('Location: /pages/admin/operateurs.php');
      exit();
    }
  }
}

$guichets = $pdo->query("SELECT * FROM guichet ORDER BY id ASC")->fetchAll(PDO::FETCH_OBJ);
$operators = $pdo->query("
  SELECT a.id AS agent_real_id, a.guichet_id, a.debut, a.fin, a.date_assignation,
         u.username, u.email,
         g.emplacement,
         COUNT(p.id) AS total_passages,
         COALESCE(SUM(p.montant), 0) AS revenu_total
  FROM agent a
  JOIN users u ON u.id = a.user_id
  LEFT JOIN guichet g ON g.id = a.guichet_id
  LEFT JOIN paiement p ON p.guichet_id = a.guichet_id
  GROUP BY a.id, a.guichet_id, a.debut, a.fin, a.date_assignation, u.username, u.email, g.emplacement
  ORDER BY a.created_at DESC
")->fetchAll(PDO::FETCH_OBJ);

$activeCount = count(array_filter($operators, fn($operator) => $operator->guichet_id !== null && $operator->debut !== null && $operator->fin === null));
$waitingCount = count(array_filter($operators, fn($operator) => $operator->guichet_id === null));
$showModal = ($_GET['modal'] ?? '') === 'agent';
?>

<!DOCTYPE html>
<html lang="fr">
<?php require __DIR__ . '/../../includes/head.php'; ?>
<?php require __DIR__ . '/../../layouts/headerAdmin.php'; ?>

<main class="md:ml-72 pt-20 px-6 md:px-8 pb-12">
  <div class="mt-4 mb-10 flex flex-col xl:flex-row xl:items-end xl:justify-between gap-6">
    <div>
      <p class="text-secondary font-mono text-xs font-bold tracking-[0.3em] uppercase mb-2">Gestion</p>
      <h1 class="text-5xl font-headline font-black tracking-tight text-primary">Gestion des operateurs</h1>
      <p class="text-on-surface-variant mt-3">Creation des comptes et affectation des equipes aux voies.</p>
    </div>
    <a href="?modal=agent" class="rounded-xl bg-primary px-5 py-4 text-xs font-bold uppercase tracking-[0.22em] text-white">
      Ajouter un operateur
    </a>
  </div>

  <section class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-surface-container-lowest rounded-2xl p-6 shadow-sm border-l-4 border-primary">
      <p class="text-xs font-bold uppercase tracking-widest text-on-surface-variant">Total staff</p>
      <p class="mt-3 font-mono text-4xl font-bold text-primary"><?= count($operators) ?></p>
    </div>
    <div class="bg-surface-container-lowest rounded-2xl p-6 shadow-sm border-l-4 border-secondary">
      <p class="text-xs font-bold uppercase tracking-widest text-on-surface-variant">En service</p>
      <p class="mt-3 font-mono text-4xl font-bold text-primary"><?= $activeCount ?></p>
    </div>
    <div class="bg-surface-container-lowest rounded-2xl p-6 shadow-sm border-l-4 border-error">
      <p class="text-xs font-bold uppercase tracking-widest text-on-surface-variant">En attente</p>
      <p class="mt-3 font-mono text-4xl font-bold text-error"><?= $waitingCount ?></p>
    </div>
  </section>

  <div class="bg-surface-container-lowest rounded-2xl shadow-sm overflow-hidden">
    <table class="w-full hidden xl:table text-left border-collapse">
      <thead>
        <tr class="bg-surface-container-low">
          <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-500">Operateur</th>
          <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-500">Statut</th>
          <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-500">Voie</th>
          <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-500 text-right">Passages</th>
          <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-500 text-right">Revenus</th>
          <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-500">Affectation</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-surface-container-low">
        <?php foreach ($operators as $operator) : ?>
          <tr class="hover:bg-surface-container-low/40">
            <td class="px-6 py-4">
              <div class="flex items-center gap-4">
                <div class="h-10 w-10 rounded-full bg-surface-container-high flex items-center justify-center font-bold text-primary">
                  <?= strtoupper(substr($operator->username, 0, 2)) ?>
                </div>
                <div>
                  <div class="font-bold text-primary text-sm uppercase"><?= htmlspecialchars($operator->username) ?></div>
                  <div class="font-mono text-[10px] text-slate-400"><?= htmlspecialchars($operator->email) ?></div>
                </div>
              </div>
            </td>
            <td class="px-6 py-4">
              <span class="px-2 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest <?= $operator->guichet_id !== null && $operator->debut !== null && $operator->fin === null ? 'bg-emerald-50 text-emerald-700' : ($operator->guichet_id !== null ? 'bg-amber-50 text-amber-700' : 'bg-slate-100 text-slate-500') ?>">
                <?= $operator->guichet_id === null ? 'En attente' : ($operator->debut !== null && $operator->fin === null ? 'En service' : 'Assigne') ?>
              </span>
            </td>
            <td class="px-6 py-4 text-sm font-semibold text-primary"><?= $operator->guichet_id ? 'Voie ' . $operator->guichet_id . ' - ' . htmlspecialchars($operator->emplacement) : 'Aucune voie' ?></td>
            <td class="px-6 py-4 text-right font-mono text-sm font-bold text-primary"><?= (int)$operator->total_passages ?></td>
            <td class="px-6 py-4 text-right font-mono text-sm font-bold text-primary"><?= number_format((float)$operator->revenu_total, 0, ',', ' ') ?> FCFA</td>
            <td class="px-6 py-4">
              <form method="post" class="flex items-center gap-2">
                <input type="hidden" name="action" value="assign_operator">
                <input type="hidden" name="agent_id" value="<?= $operator->agent_real_id ?>">
                <select name="guichet_id" class="rounded-lg border border-outline-variant/20 px-3 py-2 text-xs font-semibold text-primary">
                  <option value="0">Aucune voie</option>
                  <?php foreach ($guichets as $guichet) : ?>
                    <option
                      value="<?= $guichet->id ?>"
                      <?= (int)$operator->guichet_id === (int)$guichet->id ? 'selected' : '' ?>
                      <?= !(int)$guichet->is_active ? 'disabled' : '' ?>>
                      Voie <?= $guichet->id ?> - <?= htmlspecialchars($guichet->emplacement) ?><?= !(int)$guichet->is_active ? ' (fermee)' : '' ?>
                    </option>
                  <?php endforeach; ?>
                </select>
                <button type="submit" class="rounded-lg bg-primary px-3 py-2 text-[10px] font-bold uppercase tracking-widest text-white">Sauver</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <div class="xl:hidden p-4 space-y-4">
      <?php foreach ($operators as $operator) : ?>
        <div class="rounded-2xl border border-surface-variant bg-white p-4">
          <div class="flex items-center justify-between gap-3">
            <div>
              <p class="font-bold text-primary uppercase"><?= htmlspecialchars($operator->username) ?></p>
              <p class="font-mono text-[10px] text-slate-400"><?= htmlspecialchars($operator->email) ?></p>
            </div>
            <span class="px-2 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest <?= $operator->guichet_id ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500' ?>">
              <?= $operator->guichet_id ? 'Assigne' : 'En attente' ?>
            </span>
          </div>
          <form method="post" class="mt-4 space-y-3">
            <input type="hidden" name="action" value="assign_operator">
            <input type="hidden" name="agent_id" value="<?= $operator->agent_real_id ?>">
            <select name="guichet_id" class="w-full rounded-lg border border-outline-variant/20 px-3 py-3 text-sm text-primary">
              <option value="0">Aucune voie</option>
              <?php foreach ($guichets as $guichet) : ?>
                <option
                  value="<?= $guichet->id ?>"
                  <?= (int)$operator->guichet_id === (int)$guichet->id ? 'selected' : '' ?>
                  <?= !(int)$guichet->is_active ? 'disabled' : '' ?>>
                  Voie <?= $guichet->id ?> - <?= htmlspecialchars($guichet->emplacement) ?><?= !(int)$guichet->is_active ? ' (fermee)' : '' ?>
                </option>
              <?php endforeach; ?>
            </select>
            <button type="submit" class="w-full rounded-lg bg-primary px-4 py-3 text-xs font-bold uppercase tracking-[0.22em] text-white">Mettre a jour</button>
          </form>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <?php if ($showModal) : ?>
    <div class="fixed inset-0 z-60 overflow-y-auto bg-black/50 backdrop-blur-sm px-4 py-8">
      <div class="mx-auto w-full max-w-3xl rounded-[2rem] border border-white/70 bg-[linear-gradient(145deg,#fffdfa_0%,#f4efe4_42%,#eef4fb_100%)] p-8 shadow-[0_32px_90px_rgba(0,7,25,0.22)]">
        <div class="flex items-center justify-between mb-8">
          <div>
            <p class="text-[10px] font-bold uppercase tracking-[0.24em] text-secondary">Creation</p>
            <h2 class="text-4xl font-headline font-black text-primary">Nouvel operateur</h2>
            <p class="mt-2 text-sm text-on-surface-variant">Creation d'un compte terrain pret pour affectation.</p>
          </div>
          <a href="/pages/admin/operateurs.php" class="rounded-full border border-outline-variant/15 bg-white/70 p-2 hover:bg-white">
            <span class="material-symbols-outlined text-primary">close</span>
          </a>
        </div>

        <form method="post" class="grid md:grid-cols-2 gap-5">
          <input type="hidden" name="action" value="create_operator">
          <div class="md:col-span-2 rounded-[1.5rem] bg-primary p-5 text-white">
            <div class="flex items-center gap-4">
              <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-white/10">
                <span class="material-symbols-outlined text-3xl text-secondary-container">engineering</span>
              </div>
              <div>
                <p class="text-sm font-bold uppercase tracking-[0.22em]">Compte operateur</p>
                <p class="text-sm text-slate-300">Le compte sera cree en attente jusqu'a l'affectation d'une voie.</p>
              </div>
            </div>
          </div>
          <div>
            <label class="mb-2 block text-xs font-bold uppercase tracking-[0.22em] text-on-surface-variant">Nom complet</label>
            <input name="username" class="w-full rounded-2xl border border-outline-variant/20 bg-white/80 px-4 py-4 shadow-sm focus:border-secondary focus:ring-2 focus:ring-secondary/20" required>
          </div>
          <div>
            <label class="mb-2 block text-xs font-bold uppercase tracking-[0.22em] text-on-surface-variant">Adresse e-mail</label>
            <input type="email" name="email" class="w-full rounded-2xl border border-outline-variant/20 bg-white/80 px-4 py-4 shadow-sm focus:border-secondary focus:ring-2 focus:ring-secondary/20" required>
          </div>
          <div class="md:col-span-2">
            <label class="mb-2 block text-xs font-bold uppercase tracking-[0.22em] text-on-surface-variant">Mot de passe temporaire</label>
            <input type="password" name="password" class="w-full rounded-2xl border border-outline-variant/20 bg-white/80 px-4 py-4 shadow-sm focus:border-secondary focus:ring-2 focus:ring-secondary/20" required>
            <p class="mt-2 text-xs text-on-surface-variant">L'operateur pourra se connecter mais sera redirige vers l'ecran d'attente tant qu'aucune voie ne lui est assignee.</p>
          </div>
          <div class="md:col-span-2 flex items-center justify-end gap-3 mt-2">
            <a href="/pages/admin/operateurs.php" class="rounded-2xl border border-outline-variant/20 bg-white/70 px-5 py-3 text-xs font-bold uppercase tracking-[0.22em] text-primary">Annuler</a>
            <button type="submit" class="rounded-2xl bg-primary px-5 py-3 text-xs font-bold uppercase tracking-[0.22em] text-white shadow-[0_14px_32px_rgba(0,7,25,0.18)]">Creer l'operateur</button>
          </div>
        </form>
      </div>
    </div>
  <?php endif; ?>
</main>

<?php require __DIR__ . '/../../layouts/footerAdmin.php'; ?>
</html>
