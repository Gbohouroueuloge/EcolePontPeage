<?php

use App\Auth;
use App\ConnectionBDD;
use App\Services\AdminService;
use App\Services\AnalyticsService;

$pdo = ConnectionBDD::getPdo();
$auth = new Auth($pdo);

if (!Auth::isConnected()) {
  header('Location: /login');
  exit();
}

$user = $auth->getUser();
if (!$user) {
  header('Location: /login');
  exit();
}

if ($user->role !== 'superviseur') {
  header('Location: ' . $auth->getRedirectPath($user));
  exit();
}

$adminService = new AdminService($pdo);
$analyticsService = new AnalyticsService($pdo);
$supervisedGuichetIds = $analyticsService->getSupervisorGuichetIds((int) $user->id);

$stmt = $pdo->prepare("
  SELECT s.*, COALESCE(GROUP_CONCAT(g.emplacement ORDER BY g.id SEPARATOR ', '), '') AS guichet_labels
  FROM superviseur s
  LEFT JOIN superviseur_guichet sg ON sg.superviseur_id = s.id
  LEFT JOIN guichet g ON g.id = sg.guichet_id
  WHERE s.user_id = ?
  GROUP BY s.id, s.user_id, s.zone_nominale, s.telephone, s.created_at, s.updated_at
  LIMIT 1
");
$stmt->execute([(int) $user->id]);
$supervisorProfile = $stmt->fetch(PDO::FETCH_OBJ);

if (!$supervisorProfile) {
  $supervisorProfile = (object) [
    'zone_nominale' => null,
    'telephone' => null,
    'guichet_labels' => '',
  ];
}

$stmt = $pdo->prepare("
  SELECT g.*
  FROM guichet g
  JOIN superviseur_guichet sg ON sg.guichet_id = g.id
  JOIN superviseur s ON s.id = sg.superviseur_id
  WHERE s.user_id = ?
  ORDER BY g.id ASC
");
$stmt->execute([(int) $user->id]);
$supervisedGuichets = $stmt->fetchAll(PDO::FETCH_OBJ);
