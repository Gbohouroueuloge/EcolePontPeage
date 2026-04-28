<?php

use App\Auth;
use App\ConnectionBDD;
use App\Services\AdminService;

$pdo = ConnectionBDD::getPdo();
$auth = new Auth($pdo);

if (!Auth::isConnected()) {
  header('Location: /login');
  exit();
}

$user = $auth->getUser();
if (!$user) {
  header('Location: /login');
  exit();
}

if ($user->role === 'admin') {
  header('Location: /admin');
  exit();
}

if ($user->role === 'superviseur') {
  header('Location: /superviseur');
  exit();
}

if ($auth->operatorHasAssignedGuichet((int) $user->id)) {
  header('Location: /operator');
  exit();
}

$adminService = new AdminService($pdo);
$settings = $adminService->getSettings();

$stmt = $pdo->prepare("
  SELECT created_at
  FROM admin_notifications
  WHERE category = 'operator_waiting'
    AND user_id = ?
  ORDER BY created_at DESC
  LIMIT 1
");
$stmt->execute([(int) $user->id]);
$notificationTime = $stmt->fetchColumn();
?>

<main class="min-h-screen bg-[linear-gradient(180deg,#fef9f1_0%,#f5efe4_100%)] px-6 py-10">
  <div class="mx-auto flex min-h-[calc(100vh-5rem)] max-w-5xl items-center">
    <div class="grid w-full gap-8 lg:grid-cols-[0.9fr_1.1fr]">
      <section class="rounded-4xl bg-primary p-8 text-white shadow-[0_26px_80px_rgba(0,7,25,0.28)]">
        <span class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1 text-[11px] font-bold uppercase tracking-[0.24em] text-secondary-container">
          <span class="h-2 w-2 rounded-full bg-secondary-container"></span>
          En attente
        </span>
        <h1 class="mt-6 font-headline text-4xl font-black leading-none">Affectation en cours</h1>
        <p class="mt-4 text-sm leading-7 text-white/72">
          Votre compte existe bien, mais il n'est pas encore rattache a un guichet. L'administrateur a ete notifie de votre tentative de connexion.
        </p>

        <div class="mt-8 grid gap-4 sm:grid-cols-2">
          <div class="rounded-3xl bg-white/8 p-5">
            <div class="text-[10px] uppercase tracking-[0.2em] text-white/60">Compte</div>
            <div class="mt-2 text-lg font-bold"><?= htmlspecialchars($user->username) ?></div>
            <div class="mt-1 text-sm text-white/60"><?= htmlspecialchars($user->email) ?></div>
          </div>
          <div class="rounded-3xl bg-white/8 p-5">
            <div class="text-[10px] uppercase tracking-[0.2em] text-white/60">Derniere alerte</div>
            <div class="mt-2 text-lg font-bold">
              <?= $notificationTime ? date('d/m/Y H:i', strtotime($notificationTime)) : 'Alerte envoyee' ?>
            </div>
            <div class="mt-1 text-sm text-white/60">Equipe administration</div>
          </div>
        </div>
      </section>

      <section class="rounded-4xl border border-primary/10 bg-white/90 p-8 shadow-[0_22px_70px_rgba(0,7,25,0.12)] backdrop-blur">
        <div class="flex items-start gap-4">
          <div class="rounded-3xl bg-secondary-container/20 p-3 text-primary">
            <span class="material-symbols-outlined text-4xl">hourglass_top</span>
          </div>
          <div>
            <h2 class="font-headline text-3xl font-black text-primary">Prochaine etape</h2>
            <p class="mt-3 text-sm leading-7 text-on-surface-variant">
              <?= htmlspecialchars($settings['waiting_message']) ?>
            </p>
          </div>
        </div>

        <div class="mt-8 space-y-4">
          <div class="rounded-3xl bg-surface-container-low p-5">
            <div class="text-[11px] font-bold uppercase tracking-[0.2em] text-primary">Ce que l admin va faire</div>
            <p class="mt-2 text-sm text-on-surface-variant">Verifier votre profil, vous affecter a une voie puis activer votre acces operateur.</p>
          </div>
          <div class="rounded-3xl bg-surface-container-low p-5">
            <div class="text-[11px] font-bold uppercase tracking-[0.2em] text-primary">Astuce</div>
            <p class="mt-2 text-sm text-on-surface-variant">Laissez cette page ouverte ou rechargez-la dans quelques instants. La redirection vers votre guichet sera automatique des qu'une affectation existera.</p>
          </div>
        </div>

        <div class="mt-8 flex flex-wrap gap-3">
          <a
            class="inline-flex items-center gap-2 rounded-2xl bg-primary px-5 py-3 text-sm font-bold uppercase tracking-[0.2em] text-white"
            href="/attente">
            Actualiser
          </a>
          <a
            class="inline-flex items-center gap-2 rounded-2xl border border-outline-variant/40 px-5 py-3 text-sm font-bold uppercase tracking-[0.2em] text-primary"
            href="?logout=1">
            Se deconnecter
          </a>
        </div>
      </section>
    </div>
  </div>
</main>

<script>
  setTimeout(() => window.location.reload(), 20000);
</script>
