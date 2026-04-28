<?php

use App\Auth;
use App\ConnectionBDD;
use App\Models\Agent;
use App\Models\Guichet;

$pdo = ConnectionBDD::getPdo();
$auth = new Auth($pdo);

if (!Auth::isConnected()) {
  header('Location: /login');
  exit();
}

if ($auth->isAdmin()) {
  header('Location: /admin');
  exit();
}

if ($auth->isSupervisor()) {
  header('Location: /superviseur');
  exit();
}

$user = $auth->getUser();

if (!$user) {
  header('Location: /login');
  exit();
}

if (!$auth->operatorHasAssignedGuichet((int) $user->id)) {
  header('Location: /attente');
  exit();
}

$query = $pdo->prepare("
  SELECT a.id AS agent_real_id, a.*, u.username, u.email
  FROM agent a
  JOIN users u ON a.user_id = u.id
  WHERE u.id = :id
");
$query->execute(['id' => $user->id]);
$agent = $query->fetchObject(Agent::class);

if (!$agent) {
  http_response_code(302);
  header('Location: /attente');
  exit();
}

$query = $pdo->prepare("
  SELECT g.*, a.date_assignation
  FROM agent a
  JOIN guichet g ON a.guichet_id = g.id
  WHERE a.id = :id
");
$query->execute(['id' => $agent->id]);
$guichet = $query->fetchObject(Guichet::class);

if (!$guichet) {
  http_response_code(302);
  header('Location: /attente');
  exit();
}
