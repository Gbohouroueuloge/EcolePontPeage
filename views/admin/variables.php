<?php

use App\Auth;
use App\ConnectionBDD;
use App\Models\Agent;
use App\Models\Guichet;

$pdo = ConnectionBDD::getPdo();

$auth = new Auth($pdo);

$user = $auth->getUser();

if (!Auth::isConnected() || !$auth->isAdmin()) {
  http_response_code(401);
  header('Location: /404');
  exit();
}