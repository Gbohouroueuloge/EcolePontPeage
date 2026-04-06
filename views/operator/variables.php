<?php

use App\Auth;
use App\ConnectionBDD;
use App\Models\Agent;
use App\Models\Guichet;

$pdo = ConnectionBDD::getPdo();

$auth = new Auth($pdo);

$user = $auth->getUser();

$query = $pdo->prepare("SELECT a.id AS agent_real_id, a.*, u.username, u.email FROM agent a JOIN users u ON a.user_id = u.id WHERE u.id = :id");
$query->execute(['id' => $params['id']]);
$agent = $query->fetchObject(Agent::class);

if (!$agent) {
  http_response_code(301);
  header('Location: /404');
  exit();
}

$query = $pdo->prepare(
  "SELECT g.*, ag.date_assignation 
  FROM agent_guichet ag
  JOIN guichet g ON ag.guichet_id = g.id
  WHERE ag.agent_id = :id"
);
$query->execute(['id' => $agent->id]);
$guichet = $query->fetchObject(Guichet::class);