<?php

require_once __DIR__ . '/../../includes/connectionDBB.php';
require_once __DIR__ . '/../../functions/auth.php';

$user = getUser();

if (!isConnected() || !isAdmin()) {
  http_response_code(401);
  header('Location: /login.php');
  exit();
}
