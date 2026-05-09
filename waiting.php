<?php

require_once __DIR__ . '/includes/connectionDBB.php';
require_once __DIR__ . '/functions/auth.php';

if (!isConnected()) {
  header('Location: /login.php');
  exit();
}

$user = getUser();

if ($user->role === 'admin') {
  header('Location: /pages/admin/');
  exit();
}

if (!userWaiting($user)) {
  if ($user->role === 'superviseur') {
    header('Location: /pages/supervisor/');
    exit();
  }

  header('Location: /pages/operator/');
  exit();
}

createNotif($user);

$title = 'Affectation en attente';
$waitingMessage = getSystemSetting(
  'waiting_message',
  'Votre compte a bien ete cree. Un administrateur doit encore vous affecter a un guichet avant votre premiere vacation.'
);
$supportEmail = getSystemSetting('support_email', 'support@peage.local');
$supportPhone = getSystemSetting('support_phone', '+225 01 23 45 67 89');
$bridgeName = getSystemSetting('bridge_name', 'Pont a Peage Atlantique');
$operatorLaneStatus = $user->role === 'operateur' ? getStatusOperator($user) : null;
$waitingTitle = $operatorLaneStatus === 'closed' ? 'Voie actuellement fermee' : 'Affectation en attente';
$waitingLead = $operatorLaneStatus === 'closed'
  ? 'Votre compte est bien affecte, mais la voie associee est fermee pour le moment. Un administrateur doit reactiver la voie ou vous reassigner.'
  : $waitingMessage;

if (isset($_GET['logout'])) {
  logout();
  header('Location: /');
  exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<?php require __DIR__ . '/includes/head.php'; ?>
<body class="bg-surface font-body text-on-surface">
  <main class="min-h-screen bg-[radial-gradient(circle_at_top_left,rgba(254,190,73,0.16),transparent_30%),linear-gradient(135deg,#fffdf7_0%,#f6efe3_50%,#eef4fb_100%)] px-6 py-10">
    <div class="mx-auto max-w-4xl">
      <div class="mb-12 flex items-center justify-center gap-3">
        <img src="/assets/svg/peage_bridge_logo_africain.svg" alt="Logo Peage Bridge" class="h-11 w-11">
        <div>
          <p class="font-headline text-2xl font-black text-primary">Peage Bridge</p>
          <p class="text-xs uppercase tracking-[0.26em] text-on-surface-variant font-bold"><?= htmlspecialchars($bridgeName) ?></p>
        </div>
      </div>

      <section class="rounded-4xl border border-white/80 bg-white/90 p-8 shadow-[0_28px_80px_rgba(0,7,25,0.12)] backdrop-blur md:p-10">
        <div class="grid gap-10 lg:grid-cols-[1.2fr_0.8fr] lg:items-start">
          <div>
            <span class="inline-flex items-center gap-2 rounded-full bg-amber-100 px-4 py-2 text-[10px] font-bold uppercase tracking-[0.24em] text-amber-700">
              <span class="material-symbols-outlined text-sm">schedule</span>
              <?= $operatorLaneStatus === 'closed' ? 'Voie fermee' : 'Validation en attente' ?>
            </span>
            <h1 class="mt-6 font-headline text-4xl font-black tracking-tight text-primary md:text-5xl">
              <?= htmlspecialchars($waitingTitle) ?>
            </h1>
            <p class="mt-4 max-w-2xl text-base leading-7 text-on-surface-variant">
              <?= htmlspecialchars($waitingLead) ?>
            </p>

            <div class="mt-8 grid gap-4 sm:grid-cols-2">
              <div class="rounded-2xl bg-surface-container-low p-5">
                <p class="text-[10px] font-bold uppercase tracking-[0.24em] text-on-surface-variant">Compte</p>
                <p class="mt-3 text-xl font-bold text-primary"><?= htmlspecialchars($user->username) ?></p>
                <p class="mt-1 text-sm text-on-surface-variant"><?= htmlspecialchars($user->email) ?></p>
              </div>
              <div class="rounded-2xl bg-surface-container-low p-5">
                <p class="text-[10px] font-bold uppercase tracking-[0.24em] text-on-surface-variant">Profil</p>
                <p class="mt-3 text-xl font-bold text-primary"><?= htmlspecialchars(ucfirst($user->role)) ?></p>
                <p class="mt-1 text-sm text-on-surface-variant">Un administrateur doit finaliser votre affectation.</p>
              </div>
            </div>
          </div>

          <aside class="rounded-3xl bg-primary p-7 text-white shadow-xl">
            <h2 class="font-headline text-2xl font-bold">Besoin d'aide ?</h2>
            <p class="mt-3 text-sm leading-6 text-slate-300">
              Votre tentative de connexion a ete notifiee a l'administration. Vous pouvez aussi contacter le support pour accelerer la mise en service.
            </p>

            <div class="mt-8 space-y-4">
              <div class="rounded-xl border border-white/10 bg-white/5 p-4">
                <p class="text-[10px] font-bold uppercase tracking-[0.24em] text-slate-300">Email support</p>
                <p class="mt-2 text-sm font-semibold"><?= htmlspecialchars($supportEmail) ?></p>
              </div>
              <div class="rounded-xl border border-white/10 bg-white/5 p-4">
                <p class="text-[10px] font-bold uppercase tracking-[0.24em] text-slate-300">Telephone</p>
                <p class="mt-2 text-sm font-semibold"><?= htmlspecialchars($supportPhone) ?></p>
              </div>
            </div>

            <div class="mt-8 flex flex-col gap-3">
              <a href="/waiting.php" class="rounded-xl bg-secondary px-5 py-4 text-center text-xs font-bold uppercase tracking-[0.24em] text-primary">
                Actualiser le statut
              </a>
              <a href="?logout" class="rounded-xl border border-white/15 px-5 py-4 text-center text-xs font-bold uppercase tracking-[0.24em] text-white/90 hover:bg-white/10">
                Se deconnecter
              </a>
            </div>
          </aside>
        </div>
      </section>
    </div>
  </main>
</body>
</html>
