<?php

use App\Auth;
use App\ConnectionBDD;

$navLinks = [
  ['label' => "Tarifs", 'href' => "/tarifs", 'icon' => "directions_car"],
  ['label' => "Abonnements", 'href' => "/abonnements", 'icon' => "payments"],
  ['label' => "Contact", 'href' => "/contact", 'icon' => "email"],
];

$auth = new Auth(ConnectionBDD::getPdo());

$isconnected = Auth::isConnected();
$isAdmin = $auth->isAdmin();

$user = $auth->getUser();

if (isset($_GET['logout'])) {
  $auth->logout();

  header('Location: /');
  exit();
}

?>
<!DOCTYPE html>
<html lang="fr">

<?php require dirname(__DIR__) . DIRECTORY_SEPARATOR . '/layouts/head.php'; ?>

<body class="bg-surface font-body text-on-surface">
  <?= $content ?? '' ?>
</body>

</html>