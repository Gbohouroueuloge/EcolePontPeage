<?php

require_once __DIR__ . '/../../includes/connectionDBB.php';
require_once __DIR__ . '/../../functions/auth.php';

$user = getUser();

if ($user && getStatusOperator($user) === 'closed') {
  createNotif($user);
  http_response_code(302);
  header('Location: /waiting.php');
  exit();
}

$query = $pdo->prepare("SELECT a.id AS agent_real_id, a.*, u.username, u.email FROM agent a JOIN users u ON a.user_id = u.id WHERE u.id = :id");
$query->execute(['id' => $user->id]);
$agent = $query->fetch(PDO::FETCH_OBJ);

if (!$agent) {
  createNotif($user);
  http_response_code(302);
  header('Location: /waiting.php');
  exit();
}

$query = $pdo->prepare(
  "SELECT g.*, a.date_assignation 
  FROM agent as a
  JOIN guichet g ON a.guichet_id = g.id
  WHERE a.id = :id"
);
$query->execute(['id' => $agent->id]);
$guichet = $query->fetch(PDO::FETCH_OBJ);


if (!$guichet) {
  createNotif($user);
  http_response_code(302);
  header('Location: /waiting.php');
  exit();
}
