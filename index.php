<?php

require __DIR__ . '/includes/connectionDBB.php';
require __DIR__ . '/functions/auth.php';

if (isAdmin()) {
  header('Location: /pages/admin');
  exit();
} elseif (isConnected()) {
  $user = getUser();

  if (userWaiting($user)) {
    createNotif($user);
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

header('Location: /login.php');
exit;
