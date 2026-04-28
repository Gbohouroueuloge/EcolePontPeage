<?php

use App\Services\AdminService;

$title = 'Utilisateurs';

require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'admin/variables.php';

/** @var AdminService $adminService */
$adminService = $adminService;

$selectedRole = $_GET['role'] ?? 'all';
$editingId = !empty($_GET['edit']) ? (int) $_GET['edit'] : null;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $userId = !empty($_POST['user_id']) ? (int) $_POST['user_id'] : null;
  $result = $adminService->saveUser($_POST, $userId);

  if ($result['success']) {
    header('Location: /admin/utilisateurs?saved=1');
    exit;
  }

  $errors = $result['errors'];
  $editingId = $userId;
}

$stats = $adminService->getUserStats();
$guichets = $adminService->getGuichets();
$users = $adminService->getUsers($selectedRole === 'all' ? null : $selectedRole);
$editingUser = $editingId ? $adminService->getUserById($editingId) : null;

$formUser = [
  'id' => $editingUser['id'] ?? null,
  'username' => $editingUser['username'] ?? '',
  'email' => $editingUser['email'] ?? '',
  'role' => $_POST['role'] ?? ($editingUser['role'] ?? 'operateur'),
  'is_active' => (int) ($_POST['is_active'] ?? ($editingUser['is_active'] ?? 1)),
  'guichet_id' => $_POST['guichet_id'] ?? ($editingUser['agent_guichet_id'] ?? ''),
  'zone_nominale' => $_POST['zone_nominale'] ?? ($editingUser['zone_nominale'] ?? ''),
  'telephone' => $_POST['telephone'] ?? ($editingUser['superviseur_telephone'] ?? ''),
  'supervisor_guichets' => $_POST['supervisor_guichets'] ?? ($editingUser['supervisor_guichet_ids'] ?? []),
];
?>

<main class="md:ml-64 px-6 pb-12 pt-24 md:px-8">
  <section class="mb-10 flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
    <div>
      <div class="text-[11px] font-bold uppercase tracking-[0.3em] text-secondary">Administration</div>
      <h1 class="mt-2 font-headline text-5xl font-black tracking-tight text-primary">Gestion des utilisateurs</h1>
      <p class="mt-3 max-w-3xl text-sm leading-7 text-on-surface-variant">
        Creez, modifiez et activez les comptes administrateur, operateur et superviseur depuis un meme point de controle.
      </p>
    </div>

    <div class="rounded-3xl border border-primary/10 bg-white/80 px-5 py-4 shadow-sm">
      <div class="text-[10px] uppercase tracking-[0.2em] text-on-surface-variant">Comptes actifs</div>
      <div class="mt-2 font-mono text-3xl font-bold text-primary"><?= $stats['actifs'] ?></div>
    </div>
  </section>

  <?php if (!empty($_GET['saved'])) : ?>
    <div class="mb-6 rounded-3xl border border-brand-success/20 bg-brand-success/10 px-5 py-4 text-sm font-semibold text-brand-success">
      Le profil utilisateur a ete enregistre avec succes.
    </div>
  <?php endif; ?>

  <?php if ($errors) : ?>
    <div class="mb-6 rounded-3xl border border-error/20 bg-error/10 px-5 py-4 text-sm text-error">
      <?php foreach ($errors as $error) : ?>
        <div><?= htmlspecialchars($error) ?></div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <section class="mb-8 grid gap-4 md:grid-cols-2 xl:grid-cols-5">
    <div class="rounded-3xl border border-primary/10 bg-white p-5 shadow-sm">
      <div class="text-[10px] uppercase tracking-[0.2em] text-on-surface-variant">Total</div>
      <div class="mt-3 font-mono text-3xl font-bold text-primary"><?= $stats['total'] ?></div>
    </div>
    <div class="rounded-3xl border border-primary/10 bg-white p-5 shadow-sm">
      <div class="text-[10px] uppercase tracking-[0.2em] text-on-surface-variant">Admins</div>
      <div class="mt-3 font-mono text-3xl font-bold text-primary"><?= $stats['admins'] ?></div>
    </div>
    <div class="rounded-3xl border border-primary/10 bg-white p-5 shadow-sm">
      <div class="text-[10px] uppercase tracking-[0.2em] text-on-surface-variant">Superviseurs</div>
      <div class="mt-3 font-mono text-3xl font-bold text-primary"><?= $stats['superviseurs'] ?></div>
    </div>
    <div class="rounded-3xl border border-primary/10 bg-white p-5 shadow-sm">
      <div class="text-[10px] uppercase tracking-[0.2em] text-on-surface-variant">Operateurs</div>
      <div class="mt-3 font-mono text-3xl font-bold text-primary"><?= $stats['operateurs'] ?></div>
    </div>
    <div class="rounded-3xl border border-primary/10 bg-white p-5 shadow-sm">
      <div class="text-[10px] uppercase tracking-[0.2em] text-on-surface-variant">En attente</div>
      <div class="mt-3 font-mono text-3xl font-bold text-primary"><?= $stats['pending_assignments'] ?></div>
    </div>
  </section>

  <section class="grid gap-8 xl:grid-cols-[400px_minmax(0,1fr)]">
    <div class="rounded-4xl border border-primary/10 bg-white p-6 shadow-sm">
      <div class="mb-6">
        <div class="text-[11px] font-bold uppercase tracking-[0.24em] text-secondary">
          <?= $formUser['id'] ? 'Edition' : 'Nouveau compte' ?>
        </div>
        <h2 class="mt-2 font-headline text-3xl font-black text-primary">
          <?= $formUser['id'] ? 'Modifier un utilisateur' : 'Ajouter un utilisateur' ?>
        </h2>
      </div>

      <form method="post" class="space-y-5">
        <input type="hidden" name="user_id" value="<?= htmlspecialchars((string) ($formUser['id'] ?? '')) ?>">

        <div>
          <label class="mb-2 block text-sm font-semibold text-primary">Nom utilisateur</label>
          <input
            class="w-full rounded-2xl border border-outline-variant/40 bg-surface-container-low px-4 py-3 outline-none focus:border-secondary"
            type="text"
            name="username"
            value="<?= htmlspecialchars($formUser['username']) ?>"
            required>
        </div>

        <div>
          <label class="mb-2 block text-sm font-semibold text-primary">Adresse e-mail</label>
          <input
            class="w-full rounded-2xl border border-outline-variant/40 bg-surface-container-low px-4 py-3 outline-none focus:border-secondary"
            type="email"
            name="email"
            value="<?= htmlspecialchars($formUser['email']) ?>"
            required>
        </div>

        <div>
          <label class="mb-2 block text-sm font-semibold text-primary">Mot de passe <?= $formUser['id'] ? '(laisser vide pour conserver)' : '' ?></label>
          <input
            class="w-full rounded-2xl border border-outline-variant/40 bg-surface-container-low px-4 py-3 outline-none focus:border-secondary"
            type="password"
            name="password">
        </div>

        <div>
          <label class="mb-2 block text-sm font-semibold text-primary">Role</label>
          <select id="role-field" class="w-full rounded-2xl border border-outline-variant/40 bg-surface-container-low px-4 py-3 outline-none focus:border-secondary" name="role">
            <option value="operateur" <?= $formUser['role'] === 'operateur' ? 'selected' : '' ?>>Operateur</option>
            <option value="superviseur" <?= $formUser['role'] === 'superviseur' ? 'selected' : '' ?>>Superviseur</option>
            <option value="admin" <?= $formUser['role'] === 'admin' ? 'selected' : '' ?>>Administrateur</option>
          </select>
        </div>

        <div id="operator-fields" class="space-y-5">
          <div>
            <label class="mb-2 block text-sm font-semibold text-primary">Guichet affecte (optionnel)</label>
            <select class="w-full rounded-2xl border border-outline-variant/40 bg-surface-container-low px-4 py-3 outline-none focus:border-secondary" name="guichet_id">
              <option value="">Laisser en attente d affectation</option>
              <?php foreach ($guichets as $guichet) : ?>
                <option value="<?= $guichet['id'] ?>" <?= (string) $formUser['guichet_id'] === (string) $guichet['id'] ? 'selected' : '' ?>>
                  Voie <?= $guichet['id'] ?> - <?= htmlspecialchars($guichet['emplacement']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <div id="supervisor-fields" class="space-y-5">
          <div>
            <label class="mb-2 block text-sm font-semibold text-primary">Zone nominale</label>
            <input
              class="w-full rounded-2xl border border-outline-variant/40 bg-surface-container-low px-4 py-3 outline-none focus:border-secondary"
              type="text"
              name="zone_nominale"
              value="<?= htmlspecialchars($formUser['zone_nominale']) ?>"
              placeholder="Zone nord, axe est, etc.">
          </div>
          <div>
            <label class="mb-2 block text-sm font-semibold text-primary">Telephone</label>
            <input
              class="w-full rounded-2xl border border-outline-variant/40 bg-surface-container-low px-4 py-3 outline-none focus:border-secondary"
              type="text"
              name="telephone"
              value="<?= htmlspecialchars($formUser['telephone']) ?>"
              placeholder="+225 ...">
          </div>
          <div>
            <label class="mb-2 block text-sm font-semibold text-primary">Voies supervisees</label>
            <div class="grid gap-3 sm:grid-cols-2">
              <?php foreach ($guichets as $guichet) : ?>
                <?php $checked = in_array((string) $guichet['id'], array_map('strval', (array) $formUser['supervisor_guichets']), true); ?>
                <label class="flex items-center gap-3 rounded-2xl border border-outline-variant/20 bg-surface-container-low px-4 py-3">
                  <input type="checkbox" name="supervisor_guichets[]" value="<?= $guichet['id'] ?>" <?= $checked ? 'checked' : '' ?>>
                  <span class="text-sm text-primary">Voie <?= $guichet['id'] ?> - <?= htmlspecialchars($guichet['emplacement']) ?></span>
                </label>
              <?php endforeach; ?>
            </div>
          </div>
        </div>

        <label class="flex items-center gap-3 rounded-2xl border border-outline-variant/20 bg-surface-container-low px-4 py-3">
          <input type="checkbox" name="is_active" value="1" <?= $formUser['is_active'] ? 'checked' : '' ?>>
          <span class="text-sm font-semibold text-primary">Compte actif</span>
        </label>

        <div class="flex gap-3">
          <button class="flex-1 rounded-2xl bg-primary px-4 py-3 text-sm font-bold uppercase tracking-[0.2em] text-white" type="submit">
            <?= $formUser['id'] ? 'Mettre a jour' : 'Creer le compte' ?>
          </button>
          <a class="rounded-2xl border border-outline-variant/30 px-4 py-3 text-sm font-bold uppercase tracking-[0.2em] text-primary" href="/admin/utilisateurs">
            Reinitialiser
          </a>
        </div>
      </form>
    </div>

    <div class="rounded-4xl border border-primary/10 bg-white p-6 shadow-sm">
      <div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div>
          <div class="text-[11px] font-bold uppercase tracking-[0.24em] text-secondary">Repertoire</div>
          <h2 class="mt-2 font-headline text-3xl font-black text-primary">Comptes de la plateforme</h2>
        </div>

        <div class="flex flex-wrap gap-2">
          <?php foreach (['all' => 'Tous', 'admin' => 'Admins', 'superviseur' => 'Superviseurs', 'operateur' => 'Operateurs'] as $roleValue => $roleLabel) : ?>
            <a
              href="/admin/utilisateurs?role=<?= $roleValue ?>"
              class="rounded-full px-4 py-2 text-xs font-bold uppercase tracking-[0.2em] <?= $selectedRole === $roleValue ? 'bg-primary text-white' : 'bg-surface-container-low text-primary' ?>">
              <?= $roleLabel ?>
            </a>
          <?php endforeach; ?>
        </div>
      </div>

      <div class="space-y-4">
        <?php foreach ($users as $entry) : ?>
          <?php
          $badgeCss = match ($entry['role']) {
            'admin' => 'bg-primary text-white',
            'superviseur' => 'bg-brand-indigo text-white',
            default => 'bg-secondary-container text-primary',
          };
          $context = match ($entry['role']) {
            'admin' => 'Acces global back-office',
            'superviseur' => ($entry['supervisor_guichet_labels'] ?: 'Aucune voie supervisee'),
            default => ($entry['agent_guichet_label'] ? 'Guichet ' . $entry['agent_guichet_label'] : 'En attente d affectation'),
          };
          ?>
          <div class="rounded-3xl border border-outline-variant/15 bg-surface-container-low p-5">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
              <div class="flex items-start gap-4">
                <div class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-white shadow-sm">
                  <span class="font-mono text-xl font-black uppercase text-primary"><?= htmlspecialchars(substr($entry['username'], 0, 2)) ?></span>
                </div>
                <div>
                  <div class="flex flex-wrap items-center gap-3">
                    <h3 class="font-headline text-xl font-bold text-primary"><?= htmlspecialchars($entry['username']) ?></h3>
                    <span class="rounded-full px-3 py-1 text-[10px] font-black uppercase tracking-[0.2em] <?= $badgeCss ?>">
                      <?= htmlspecialchars($entry['role']) ?>
                    </span>
                    <?php if (!$entry['is_active']) : ?>
                      <span class="rounded-full bg-error/10 px-3 py-1 text-[10px] font-black uppercase tracking-[0.2em] text-error">Inactif</span>
                    <?php endif; ?>
                  </div>
                  <p class="mt-2 text-sm text-on-surface-variant"><?= htmlspecialchars($entry['email']) ?></p>
                  <p class="mt-2 text-sm font-semibold text-primary"><?= htmlspecialchars($context) ?></p>
                  <p class="mt-1 text-[11px] text-on-surface-variant">
                    Derniere connexion:
                    <?= $entry['last_login_at'] ? date('d/m/Y H:i', strtotime($entry['last_login_at'])) : 'Jamais' ?>
                  </p>
                </div>
              </div>

              <div class="flex gap-2">
                <a
                  class="rounded-2xl border border-primary/15 px-4 py-3 text-sm font-bold uppercase tracking-[0.2em] text-primary"
                  href="/admin/utilisateurs?role=<?= urlencode($selectedRole) ?>&edit=<?= $entry['id'] ?>">
                  Modifier
                </a>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
</main>

<script>
  const roleField = document.getElementById('role-field');
  const operatorFields = document.getElementById('operator-fields');
  const supervisorFields = document.getElementById('supervisor-fields');

  const syncRoleFields = () => {
    const role = roleField.value;
    operatorFields.style.display = role === 'operateur' ? 'block' : 'none';
    supervisorFields.style.display = role === 'superviseur' ? 'block' : 'none';
  };

  syncRoleFields();
  roleField.addEventListener('change', syncRoleFields);
</script>
