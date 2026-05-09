<?php

$title = 'Superviseurs';

require __DIR__ . '/variables.php';

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'] ?? '';

  if ($action === 'create_supervisor') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $zone = trim($_POST['zone_nominale'] ?? '');
    $telephone = trim($_POST['telephone'] ?? '');
    $guichetIds = array_values(array_filter(array_map('intval', $_POST['guichets'] ?? [])));

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
            VALUES (:username, :email, :password, 'superviseur')
          ");
          $stmt->execute([
            'username' => $username,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_BCRYPT),
          ]);

          $userId = (int)$pdo->lastInsertId();

          $stmt = $pdo->prepare("
            INSERT INTO superviseur (user_id, zone_nominale, telephone)
            VALUES (:user_id, :zone_nominale, :telephone)
          ");
          $stmt->execute([
            'user_id' => $userId,
            'zone_nominale' => $zone ?: null,
            'telephone' => $telephone ?: null,
          ]);

          $supervisorId = (int)$pdo->lastInsertId();
          if (!empty($guichetIds)) {
            $insertLink = $pdo->prepare("
              INSERT INTO superviseur_guichet (superviseur_id, guichet_id)
              VALUES (:superviseur_id, :guichet_id)
            ");
            foreach ($guichetIds as $guichetId) {
              $insertLink->execute([
                'superviseur_id' => $supervisorId,
                'guichet_id' => $guichetId,
              ]);
            }
          }

          $pdo->commit();
          header('Location: /pages/admin/superviseurs.php');
          exit();
        } catch (Throwable $e) {
          $pdo->rollBack();
          $error = 'Impossible de creer ce superviseur.';
        }
      }
    }
  }

  if ($action === 'update_supervisor_lanes') {
    $supervisorId = (int)($_POST['supervisor_id'] ?? 0);
    $guichetIds = array_values(array_filter(array_map('intval', $_POST['guichets'] ?? [])));

    if ($supervisorId > 0) {
      $pdo->beginTransaction();
      try {
        $pdo->prepare("DELETE FROM superviseur_guichet WHERE superviseur_id = :id")->execute(['id' => $supervisorId]);
        if (!empty($guichetIds)) {
          $stmt = $pdo->prepare("
            INSERT INTO superviseur_guichet (superviseur_id, guichet_id)
            VALUES (:superviseur_id, :guichet_id)
          ");
          foreach ($guichetIds as $guichetId) {
            $stmt->execute([
              'superviseur_id' => $supervisorId,
              'guichet_id' => $guichetId,
            ]);
          }
        }
        $pdo->commit();
        header('Location: /pages/admin/superviseurs.php');
        exit();
      } catch (Throwable $e) {
        $pdo->rollBack();
        $error = 'La mise a jour des voies a echoue.';
      }
    }
  }
}

$guichets = $pdo->query("SELECT * FROM guichet ORDER BY id ASC")->fetchAll(PDO::FETCH_OBJ);
$supervisors = $pdo->query("
  SELECT s.id AS supervisor_id, s.zone_nominale, s.telephone, u.username, u.email,
         COUNT(DISTINCT sg.guichet_id) AS lane_count,
         GROUP_CONCAT(CONCAT('Voie ', g.id, ' - ', g.emplacement) ORDER BY g.id SEPARATOR ' | ') AS lanes
  FROM superviseur s
  JOIN users u ON u.id = s.user_id
  LEFT JOIN superviseur_guichet sg ON sg.superviseur_id = s.id
  LEFT JOIN guichet g ON g.id = sg.guichet_id
  GROUP BY s.id, s.zone_nominale, s.telephone, u.username, u.email
  ORDER BY s.created_at DESC
")->fetchAll(PDO::FETCH_OBJ);

$assignments = [];
$assignmentRows = $pdo->query("SELECT superviseur_id, guichet_id FROM superviseur_guichet")->fetchAll(PDO::FETCH_OBJ);
foreach ($assignmentRows as $row) {
  $assignments[(int)$row->superviseur_id][] = (int)$row->guichet_id;
}

$showModal = ($_GET['modal'] ?? '') === 'superviseur';
$assignSupervisorId = (int)($_GET['assign'] ?? 0);
$assignedSupervisor = null;

if ($assignSupervisorId > 0) {
  foreach ($supervisors as $supervisorItem) {
    if ((int)$supervisorItem->supervisor_id === $assignSupervisorId) {
      $assignedSupervisor = $supervisorItem;
      break;
    }
  }
}
?>

<!DOCTYPE html>
<html lang="fr">
<?php require __DIR__ . '/../../includes/head.php'; ?>
<?php require __DIR__ . '/../../layouts/headerAdmin.php'; ?>

<main class="md:ml-72 pt-20 px-6 md:px-8 pb-12">
  <div class="mt-4 mb-10 flex flex-col xl:flex-row xl:items-end xl:justify-between gap-6">
    <div>
      <p class="text-secondary font-mono text-xs font-bold tracking-[0.3em] uppercase mb-2">Gestion</p>
      <h1 class="text-5xl font-headline font-black tracking-tight text-primary">Gestion des superviseurs</h1>
      <p class="text-on-surface-variant mt-3">Organisation des responsables de zone et des voies qu'ils couvrent.</p>
    </div>
    <a href="?modal=superviseur" class="rounded-xl bg-primary px-5 py-4 text-xs font-bold uppercase tracking-[0.22em] text-white">
      Ajouter un superviseur
    </a>
  </div>

  <section class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-surface-container-lowest rounded-2xl p-6 shadow-sm border-l-4 border-primary">
      <p class="text-xs font-bold uppercase tracking-widest text-on-surface-variant">Superviseurs</p>
      <p class="mt-3 font-mono text-4xl font-bold text-primary"><?= count($supervisors) ?></p>
    </div>
    <div class="bg-surface-container-lowest rounded-2xl p-6 shadow-sm border-l-4 border-secondary">
      <p class="text-xs font-bold uppercase tracking-widest text-on-surface-variant">Voies couvertes</p>
      <p class="mt-3 font-mono text-4xl font-bold text-primary"><?= array_sum(array_map(fn($s) => (int)$s->lane_count, $supervisors)) ?></p>
    </div>
    <div class="bg-surface-container-lowest rounded-2xl p-6 shadow-sm border-l-4 border-tertiary">
      <p class="text-xs font-bold uppercase tracking-widest text-on-surface-variant">Zones a clarifier</p>
      <p class="mt-3 font-mono text-4xl font-bold text-primary"><?= count(array_filter($supervisors, fn($s) => (int)$s->lane_count === 0)) ?></p>
    </div>
  </section>

  <div class="bg-surface-container-lowest rounded-2xl shadow-sm overflow-hidden">
    <table class="w-full hidden xl:table text-left border-collapse">
      <thead>
        <tr class="bg-surface-container-low">
          <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-500">Superviseur</th>
          <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-500">Zone</th>
          <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-500">Telephone</th>
          <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-500">Etat</th>
          <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-500">Voies suivies</th>
          <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-500">Affectation</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-surface-container-low">
        <?php foreach ($supervisors as $supervisor) : ?>
          <tr class="hover:bg-surface-container-low/40 align-top">
            <td class="px-6 py-4">
              <div class="flex items-center gap-4">
                <div class="h-10 w-10 rounded-full bg-surface-container-high flex items-center justify-center font-bold text-primary">
                  <?= strtoupper(substr($supervisor->username, 0, 2)) ?>
                </div>
                <div>
                  <div class="font-bold text-primary text-sm uppercase"><?= htmlspecialchars($supervisor->username) ?></div>
                  <div class="font-mono text-[10px] text-slate-400"><?= htmlspecialchars($supervisor->email) ?></div>
                </div>
              </div>
            </td>
            <td class="px-6 py-4 text-sm font-semibold text-primary">
              <?= htmlspecialchars($supervisor->zone_nominale ?: 'Zone non renseignee') ?>
            </td>
            <td class="px-6 py-4 text-sm text-primary">
              <?= htmlspecialchars($supervisor->telephone ?: 'Non renseigne') ?>
            </td>
            <td class="px-6 py-4">
              <span class="px-2 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest <?= (int)$supervisor->lane_count > 0 ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' ?>">
                <?= (int)$supervisor->lane_count > 0 ? 'couverte' : 'Affectation incomplete' ?>
              </span>
            </td>
            <td class="px-6 py-4">
              <div class="max-w-sm">
                <p class="text-sm font-semibold text-primary leading-6"><?= htmlspecialchars($supervisor->lanes ?: 'Aucune voie assignee') ?></p>
                <p class="mt-2 text-[10px] uppercase tracking-[0.22em] text-on-surface-variant">
                  <?= (int)$supervisor->lane_count ?> voie<?= (int)$supervisor->lane_count > 1 ? 's' : '' ?>
                </p>
              </div>
            </td>
            <td class="px-6 py-4">
              <a
                href="?assign=<?= $supervisor->supervisor_id ?>"
                class="inline-flex items-center gap-2 rounded-xl bg-primary px-4 py-3 text-[10px] font-bold uppercase tracking-[0.22em] text-white shadow-[0_12px_28px_rgba(0,7,25,0.12)]">
                <span class="material-symbols-outlined text-sm">alt_route</span>
                Assigner les voies
              </a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <div class="xl:hidden p-4 space-y-4">
      <?php foreach ($supervisors as $supervisor) : ?>
        <div class="rounded-2xl border border-surface-variant bg-white p-4">
          <div class="flex items-center justify-between gap-3">
            <div>
              <p class="font-bold text-primary uppercase"><?= htmlspecialchars($supervisor->username) ?></p>
              <p class="font-mono text-[10px] text-slate-400"><?= htmlspecialchars($supervisor->email) ?></p>
            </div>
            <span class="px-2 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest <?= (int)$supervisor->lane_count > 0 ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' ?>">
              <?= (int)$supervisor->lane_count > 0 ? 'Couvert' : 'Attente' ?>
            </span>
          </div>

          <div class="mt-4 space-y-2 text-sm">
            <div class="flex justify-between gap-4">
              <span class="font-semibold text-primary text-right"><?= htmlspecialchars($supervisor->zone_nominale ?: 'Zone non renseignee') ?></span>
            </div>
            <div class="flex justify-between gap-4">
              <span class="text-on-surface-variant">Telephone</span>
              <span class="font-semibold text-primary text-right"><?= htmlspecialchars($supervisor->telephone ?: 'Non renseigne') ?></span>
            </div>
            <div>
              <p class="text-on-surface-variant mb-2">Voies suivies</p>
              <p class="font-semibold text-primary"><?= htmlspecialchars($supervisor->lanes ?: 'Aucune voie assignee') ?></p>
            </div>
          </div>

          <a
            href="?assign=<?= $supervisor->supervisor_id ?>"
            class="mt-4 inline-flex w-full items-center justify-center gap-2 rounded-xl bg-primary px-4 py-3 text-xs font-bold uppercase tracking-[0.22em] text-white">
            <span class="material-symbols-outlined text-sm">alt_route</span>
            Assigner les voies
          </a>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <?php if ($assignedSupervisor) : ?>
    <div class="fixed inset-0 z-60 overflow-y-auto bg-black/50 backdrop-blur-sm px-4 py-8">
      <div class="mx-auto w-full max-w-3xl rounded-[2rem] border border-white/70 bg-[linear-gradient(145deg,#fffdfa_0%,#f4efe4_42%,#eef4fb_100%)] p-8 shadow-[0_32px_90px_rgba(0,7,25,0.22)]">
        <div class="flex items-center justify-between mb-8">
          <div>
            <p class="text-[10px] font-bold uppercase tracking-[0.24em] text-secondary">Affectation</p>
            <h2 class="text-4xl font-headline font-black text-primary">Assigner des voies</h2>
            <p class="mt-2 text-sm text-on-surface-variant">
              <?= htmlspecialchars($assignedSupervisor->username) ?> • <?= htmlspecialchars($assignedSupervisor->zone_nominale ?: 'Zone non renseignee') ?>
            </p>
          </div>
          <a href="/pages/admin/superviseurs.php" class="rounded-full border border-outline-variant/15 bg-white/70 p-2 hover:bg-white">
            <span class="material-symbols-outlined text-primary">close</span>
          </a>
        </div>

        <form method="post" class="space-y-6">
          <input type="hidden" name="action" value="update_supervisor_lanes">
          <input type="hidden" name="supervisor_id" value="<?= $assignedSupervisor->supervisor_id ?>">

          <div>
            <label class="mb-3 block text-xs font-bold uppercase tracking-[0.22em] text-on-surface-variant">Voies a affecter</label>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <?php foreach ($guichets as $guichet) : ?>
                <?php $isSelected = in_array((int)$guichet->id, $assignments[(int)$assignedSupervisor->supervisor_id] ?? [], true); ?>
                <label class="group block cursor-pointer">
                  <input
                    type="checkbox"
                    name="guichets[]"
                    value="<?= $guichet->id ?>"
                    class="peer sr-only"
                    <?= $isSelected ? 'checked' : '' ?>>
                  <div class="rounded-[1.5rem] border border-outline-variant/20 bg-white/85 p-5 shadow-sm transition-all peer-checked:border-secondary peer-checked:bg-secondary/10 peer-checked:shadow-[0_16px_36px_rgba(201,144,26,0.18)] group-hover:-translate-y-0.5 group-hover:border-secondary/35">
                    <div class="flex items-start justify-between gap-4">
                      <div>
                        <p class="text-[10px] font-bold uppercase tracking-[0.22em] text-on-surface-variant">Voie <?= $guichet->id ?></p>
                        <h3 class="mt-2 text-lg font-headline font-bold text-primary"><?= htmlspecialchars($guichet->emplacement) ?></h3>
                      </div>
                      <div class="flex h-6 w-6 items-center justify-center rounded-full border border-outline-variant/25 bg-white text-transparent peer-checked:border-secondary peer-checked:bg-secondary peer-checked:text-primary">
                        <span class="material-symbols-outlined text-sm">check</span>
                      </div>
                    </div>
                    <p class="mt-4 text-xs text-on-surface-variant"><?= htmlspecialchars($guichet->slug) ?></p>
                  </div>
                </label>
              <?php endforeach; ?>
            </div>
          </div>

          <div class="flex items-center justify-end gap-3">
            <a href="/pages/admin/superviseurs.php" class="rounded-2xl border border-outline-variant/20 bg-white/70 px-5 py-3 text-xs font-bold uppercase tracking-[0.22em] text-primary">Annuler</a>
            <button type="submit" class="rounded-2xl bg-primary px-5 py-3 text-xs font-bold uppercase tracking-[0.22em] text-white shadow-[0_14px_32px_rgba(0,7,25,0.18)]">
              Enregistrer l'affectation
            </button>
          </div>
        </form>
      </div>
    </div>
  <?php endif; ?>

  <?php if ($showModal) : ?>
    <div class="fixed inset-0 z-60 overflow-y-auto bg-black/50 backdrop-blur-sm px-4 py-8">
      <div class="mx-auto w-full max-w-4xl rounded-[2rem] border border-white/70 bg-[linear-gradient(145deg,#fffdfa_0%,#f4efe4_42%,#eef4fb_100%)] p-8 shadow-[0_32px_90px_rgba(0,7,25,0.22)]">
        <div class="flex items-center justify-between mb-8">
          <div>
            <p class="text-[10px] font-bold uppercase tracking-[0.24em] text-secondary">Creation</p>
            <h2 class="text-4xl font-headline font-black text-primary">Nouveau superviseur</h2>
            <p class="mt-2 text-sm text-on-surface-variant">Creation d'un profil de supervision avec zone et voies rattachees.</p>
          </div>
          <a href="/pages/admin/superviseurs.php" class="rounded-full border border-outline-variant/15 bg-white/70 p-2 hover:bg-white">
            <span class="material-symbols-outlined text-primary">close</span>
          </a>
        </div>

        <form method="post" class="grid md:grid-cols-2 gap-5">
          <input type="hidden" name="action" value="create_supervisor">
          <div class="md:col-span-2 rounded-[1.5rem] bg-primary p-5 text-white">
            <div class="flex items-center gap-4">
              <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-white/10">
                <span class="material-symbols-outlined text-3xl text-secondary-container">supervisor_account</span>
              </div>
              <div>
                <p class="text-sm font-bold uppercase tracking-[0.22em]">Compte supervision</p>
                <p class="text-sm text-slate-300">Un superviseur sans voie assignee sera automatiquement redirige vers la page d'attente.</p>
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
          <div>
            <label class="mb-2 block text-xs font-bold uppercase tracking-[0.22em] text-on-surface-variant">Mot de passe</label>
            <input type="password" name="password" class="w-full rounded-2xl border border-outline-variant/20 bg-white/80 px-4 py-4 shadow-sm focus:border-secondary focus:ring-2 focus:ring-secondary/20" required>
          </div>
          <div>
            <label class="mb-2 block text-xs font-bold uppercase tracking-[0.22em] text-on-surface-variant">Telephone</label>
            <input name="telephone" class="w-full rounded-2xl border border-outline-variant/20 bg-white/80 px-4 py-4 shadow-sm focus:border-secondary focus:ring-2 focus:ring-secondary/20">
          </div>
          <div class="md:col-span-2">
            <label class="mb-2 block text-xs font-bold uppercase tracking-[0.22em] text-on-surface-variant">Zone nominale</label>
            <input name="zone_nominale" class="w-full rounded-2xl border border-outline-variant/20 bg-white/80 px-4 py-4 shadow-sm focus:border-secondary focus:ring-2 focus:ring-secondary/20">
          </div>
          <div class="md:col-span-2">
            <label class="mb-3 block text-xs font-bold uppercase tracking-[0.22em] text-on-surface-variant">Voies a affecter</label>
            <div class="grid md:grid-cols-3 gap-3 rounded-[1.5rem] border border-outline-variant/10 bg-white/60 p-4">
              <?php foreach ($guichets as $guichet) : ?>
                <label class="flex items-center gap-2 rounded-2xl border border-outline-variant/15 bg-surface-container-low px-4 py-3 text-sm font-semibold text-primary transition hover:border-secondary/30 hover:bg-secondary/5">
                  <input type="checkbox" name="guichets[]" value="<?= $guichet->id ?>" class="rounded border-outline-variant/30 text-primary">
                  Voie <?= $guichet->id ?> - <?= htmlspecialchars($guichet->emplacement) ?>
                </label>
              <?php endforeach; ?>
            </div>
          </div>
          <div class="md:col-span-2 flex items-center justify-end gap-3 mt-2">
            <a href="/pages/admin/superviseurs.php" class="rounded-2xl border border-outline-variant/20 bg-white/70 px-5 py-3 text-xs font-bold uppercase tracking-[0.22em] text-primary">Annuler</a>
            <button type="submit" class="rounded-2xl bg-primary px-5 py-3 text-xs font-bold uppercase tracking-[0.22em] text-white shadow-[0_14px_32px_rgba(0,7,25,0.18)]">Creer le superviseur</button>
          </div>
        </form>
      </div>
    </div>
  <?php endif; ?>
</main>

<?php require __DIR__ . '/../../layouts/footerAdmin.php'; ?>
</html>
