<?php

require_once __DIR__ . '/../../includes/connectionDBB.php';
require_once __DIR__ . '/../../functions/auth.php';

$user = getUser();

if (!isConnected() || !isSupervisor()) {
  http_response_code(401);
  header('Location: /login.php');
  exit();
}

$stmt = $pdo->prepare("
  SELECT s.*
  FROM superviseur s
  WHERE s.user_id = :user_id
  LIMIT 1
");
$stmt->execute(['user_id' => $user->id]);
$supervisor = $stmt->fetch(PDO::FETCH_OBJ);

if (!$supervisor) {
  http_response_code(404);
  header('Location: /login.php');
  exit();
}

$stmt = $pdo->prepare("
  SELECT g.*
  FROM superviseur_guichet sg
  JOIN guichet g ON g.id = sg.guichet_id
  WHERE sg.superviseur_id = :superviseur_id
  ORDER BY g.id ASC
");
$stmt->execute(['superviseur_id' => $supervisor->id]);
$supervisedGuichets = $stmt->fetchAll(PDO::FETCH_OBJ);

$supervisedGuichetIds = array_map(fn($g) => (int)$g->id, $supervisedGuichets);

if (empty($supervisedGuichetIds)) {
  createNotif($user);
  http_response_code(302);
  header('Location: /waiting.php');
  exit();
}

function supervisorPlaceholders(array $ids): string
{
  return implode(',', array_fill(0, count($ids), '?'));
}

function supervisorBadgeClass(string $mode): string
{
  return match (strtolower($mode)) {
    'espece', 'especes', 'espaces', 'espèce', 'espèces' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
    'carte' => 'bg-blue-50 text-blue-700 border-blue-200',
    'abonnement' => 'bg-amber-50 text-amber-700 border-amber-200',
    'mobile money' => 'bg-violet-50 text-violet-700 border-violet-200',
    default => 'bg-slate-100 text-slate-700 border-slate-200',
  };
}
