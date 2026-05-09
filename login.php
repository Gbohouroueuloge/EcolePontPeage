<?php

require __DIR__ . '/includes/connectionDBB.php';
require __DIR__ . '/functions/auth.php';

$error = null;

if (isAdmin()) {
  header('Location: /pages/admin');
  exit();
} elseif (isConnected()) {
  $currentUser = getUser();

  if (userWaiting($currentUser)) {
    createNotif($currentUser);
    header('Location: /waiting.php');
    exit();
  }

  if (isSupervisor()) {
    header('Location: /pages/supervisor');
    exit();
  }

  header('Location: /pages/operator');
  exit();
}

if (!empty($_POST)) {
  $user = login($_POST['email'] ?? '', $_POST['password'] ?? '');

  if ($user) {
    if (userWaiting($user)) {
      createNotif($user);
      header('Location: /waiting.php');
      exit();
    }

    if ($user->role === 'admin') {
      header('Location: /pages/admin');
      exit();
    }

    if ($user->role === 'superviseur') {
      header('Location: /pages/supervisor');
      exit();
    }

    header('Location: /pages/operator');
    exit();
  } else {
    $error = 'Adresse e-mail ou mot de passe incorrect.';
  }
}
?>
<!DOCTYPE html>
<html lang="fr">

<?php require __DIR__ . '/includes/head.php'; ?>

<body class="bg-surface font-body text-on-surface">
  <main class="min-h-screen bg-[radial-gradient(circle_at_top_left,rgba(254,190,73,0.18),transparent_38%),linear-gradient(135deg,#fffdf7_0%,#f7f0e4_45%,#eef2f8_100%)]">
    <div class="w-full flex items-center px-6 py-10 sm:px-10 lg:px-14 xl:px-20">
      <div class="z-10 mx-auto w-fit relative">
        <div class="rounded-4xl border border-white/80 bg-white/88 p-6 shadow-[0_28px_80px_rgba(0,7,25,0.12)] backdrop-blur sm:p-8">
          <div class="mb-8">
            <div class="flex items-center justify-center px-2 py-1 mb-4">
              <img src="/assets/svg/peage_bridge_logo_africain.svg" alt="Logo Peage Africain" class="h-10 w-10">
              <h1 class="ml-2 font-headline text-3xl font-black text-primary">Peage Bridge</h1>
            </div>
            <h2 class="mt-4 font-headline text-3xl font-black text-primary">
              Connectez-vous
            </h2>
            <p class="mt-2 text-sm leading-6 text-on-surface-variant">
              Utilisez votre adresse professionnelle pour acceder au bon espace: administration, supervision ou voie affectee.
            </p>
          </div>

          <?php if ($error) : ?>
            <div class="mb-4 rounded-2xl bg-error/20 p-4">
              <p class="text-sm font-semibold leading-6 text-error"><?= $error ?></p>
            </div>
          <?php endif; ?>

          <form class="space-y-5" action="" method="post">
            <div>
              <label class="mb-2 block text-sm font-semibold text-primary" for="email">Adresse e-mail</label>
              <input
                class="w-full rounded-2xl border border-outline-variant/40 bg-surface-container-lowest px-4 py-4 text-base outline-none transition focus:border-secondary focus:ring-2 focus:ring-secondary-container/60"
                id="email"
                name="email"
                placeholder="operateur@peage.com"
                type="email"
                value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            </div>

            <div>
              <div class="mb-2 flex items-center justify-between">
                <label class="block text-sm font-semibold text-primary" for="password">Mot de passe</label>
              </div>
              <input
                class="w-full rounded-2xl border border-outline-variant/40 bg-surface-container-lowest px-4 py-4 text-base outline-none transition focus:border-secondary focus:ring-2 focus:ring-secondary-container/60"
                id="password"
                name="password"
                placeholder="Votre mot de passe"
                type="password">
            </div>

            <button
              class="w-full rounded-2xl bg-primary px-5 py-4 text-sm font-bold uppercase tracking-[0.24em] text-white shadow-[0_18px_40px_rgba(0,7,25,0.22)] transition hover:-translate-y-0.5 hover:bg-primary-container"
              type="submit">
              Se connecter
            </button>
          </form>

          <div class="mt-6 flex items-center justify-between text-xs text-on-surface-variant">
            <span>Support: support@peage.local</span>
            <span><?= date('d/m/Y') ?></span>
          </div>
        </div>
      </div>
    </div>
  </main>
</body>

</html>
