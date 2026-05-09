<?php

require_once dirname(__DIR__) . '/includes/connectionDBB.php';

function isConnected(): bool
{
  if (session_status() !== PHP_SESSION_ACTIVE) session_start();
  return isset($_SESSION['user']);
}

function getUser()
{
  global $pdo;

  if (session_status() !== PHP_SESSION_ACTIVE) session_start();
  if (!isConnected()) return null;

  $req = $pdo->prepare("SELECT * FROM users WHERE id = ?");
  $req->execute([$_SESSION['user']]);
  return $req->fetch(PDO::FETCH_OBJ);
}

function getUserRole(): ?string
{
  $user = getUser();
  return $user->role ?? null;
}

function hasRole(string $role): bool
{
  global $pdo;

  if (session_status() !== PHP_SESSION_ACTIVE) session_start();
  if (!isConnected()) return false;

  $req = $pdo->prepare("SELECT role FROM users WHERE id = ?");
  $req->execute([$_SESSION['user']]);
  $user = $req->fetch(PDO::FETCH_OBJ);

  return $user && $user->role === $role;
}

function isAdmin(): bool
{
  return hasRole('admin');
}

function isSupervisor(): bool
{
  return hasRole('superviseur');
}

function login(string $email, string $password)
{
  global $pdo;

  $req = $pdo->prepare("SELECT * FROM users WHERE email = :email");
  $req->execute(["email" => $email]);

  $user = $req->fetch(PDO::FETCH_OBJ);

  if ($user && password_verify($password, $user->password)) {
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    $_SESSION['user'] = $user->id;

    if ($user->role === 'operateur') {
      $laneStatus = getStatusOperator($user);

      if ($laneStatus === 'active') {
        $query = $pdo->prepare("INSERT INTO agent (user_id, debut, fin) VALUES (:user_id, NOW(), NULL) ON DUPLICATE KEY UPDATE debut = NOW(), fin = NULL");
        $query->execute(["user_id" => $user->id]);
      }
    }

    return $user;
  }
  return null;
}

function register(array $data): bool
{
  global $pdo;

  $req = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (:username, :email, :password, :role)");
  $req->execute([
    "username" => $data['username'],
    "email"    => $data['email'],
    "password" => password_hash($data['password'], PASSWORD_BCRYPT),
    "role"     => 'operateur',
  ]);

  $id = $pdo->lastInsertId();

  $query = $pdo->prepare("INSERT INTO agent (user_id, debut, fin) VALUES (:user_id, NOW(), NULL) ON DUPLICATE KEY UPDATE debut = NOW(), fin = NULL");
  $query->execute(["user_id" => $id]);

  if (session_status() !== PHP_SESSION_ACTIVE) session_start();
  $_SESSION['user'] = $id;

  return true;
}

function logout(): void
{
  global $pdo;

  if (session_status() !== PHP_SESSION_ACTIVE) session_start();
  $user = getUser();
  if ($user && $user->role === 'operateur') {
    $pdo->prepare("UPDATE agent SET fin = NOW() WHERE user_id = :id")->execute(['id' => $user->id]);
  }
  session_destroy();
}

function getSystemSetting(string $key, ?string $default = null): ?string
{
  global $pdo;

  $stmt = $pdo->prepare("SELECT setting_value FROM system_settings WHERE setting_key = :key LIMIT 1");
  $stmt->execute(['key' => $key]);
  $value = $stmt->fetchColumn();

  return $value !== false ? $value : $default;
}

function userWaiting($user = null): bool
{
  global $pdo;

  $user = $user ?: getUser();
  if (!$user) {
    return false;
  }

  if ($user->role === 'operateur') {
    return getStatusOperator($user) !== 'active';
  }

  if ($user->role === 'superviseur') {
    $stmt = $pdo->prepare("
      SELECT COUNT(*)
      FROM superviseur s
      JOIN superviseur_guichet sg ON sg.superviseur_id = s.id
      WHERE s.user_id = :user_id
    ");
    $stmt->execute(['user_id' => $user->id]);
    return (int)$stmt->fetchColumn() === 0;
  }

  return false;
}

function getStatusOperator($user = null): string
{
  global $pdo;

  $user = $user ?: getUser();
  if (!$user || $user->role !== 'operateur') {
    return 'none';
  }

  $stmt = $pdo->prepare("
    SELECT a.guichet_id, g.is_active
    FROM agent a
    LEFT JOIN guichet g ON g.id = a.guichet_id
    WHERE a.user_id = :user_id
    LIMIT 1
  ");
  $stmt->execute(['user_id' => $user->id]);
  $row = $stmt->fetch(PDO::FETCH_OBJ);

  if (!$row || !$row->guichet_id) {
    return 'none';
  }

  return (int)$row->is_active === 1 ? 'active' : 'closed';
}

function createNotif($user = null): void
{
  global $pdo;

  $user = $user ?: getUser();
  if (!$user || !in_array($user->role, ['operateur', 'superviseur'], true)) {
    return;
  }

  $category = $user->role === 'operateur' ? 'operator_waiting' : 'supervisor_waiting';
  $title = $user->role === 'operateur'
    ? 'Operateur en attente d affectation'
    : 'Superviseur en attente d affectation';
  $message = $user->role === 'operateur'
    ? "{$user->username} ({$user->email}) a essaye de se connecter sans guichet assigne. Merci de l affecter a une voie."
    : "{$user->username} ({$user->email}) a essaye de se connecter sans zone assignee. Merci de lui affecter au moins un guichet.";

  if ($user->role === 'operateur' && getStatusOperator($user) === 'closed') {
    $category = 'operator_lane_closed';
    $title = 'Operateur bloque par voie fermee';
    $message = "{$user->username} ({$user->email}) a essaye de se connecter alors que sa voie est actuellement fermee. Merci de reaffecter l agent ou de reactiver la voie.";
  }

  $check = $pdo->prepare("
    SELECT id
    FROM admin_notifications
    WHERE user_id = :user_id AND category = :category AND is_read = 0
    LIMIT 1
  ");
  $check->execute([
    'user_id' => $user->id,
    'category' => $category,
  ]);

  if ($check->fetchColumn()) {
    return;
  }

  $stmt = $pdo->prepare("
    INSERT INTO admin_notifications (category, title, message, user_id, is_read)
    VALUES (:category, :title, :message, :user_id, 0)
  ");
  $stmt->execute([
    'category' => $category,
    'title' => $title,
    'message' => $message,
    'user_id' => $user->id,
  ]);
}
