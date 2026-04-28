<?php

namespace App;

use App\Models\User;
use PDO;

class Auth
{
  private PDO $pdo;

  public function __construct(PDO $pdo)
  {
    $this->pdo = $pdo;
  }

  public static function isConnected(): bool
  {
    if (session_status() !== PHP_SESSION_ACTIVE) {
      session_start();
    }

    return isset($_SESSION['user']);
  }

  public function isAdmin(): bool
  {
    if (session_status() !== PHP_SESSION_ACTIVE) {
      session_start();
    }

    if (!self::isConnected()) {
      return false;
    }

    $req = $this->pdo->prepare("SELECT role FROM users WHERE id = ?");
    $req->execute([$_SESSION['user']]);
    $user = $req->fetch(PDO::FETCH_ASSOC);

    return ($user['role'] ?? null) === 'admin';
  }

  public function isSupervisor(): bool
  {
    if (session_status() !== PHP_SESSION_ACTIVE) {
      session_start();
    }

    if (!self::isConnected()) {
      return false;
    }

    $req = $this->pdo->prepare("SELECT role FROM users WHERE id = ?");
    $req->execute([$_SESSION['user']]);
    $user = $req->fetch(PDO::FETCH_ASSOC);

    return ($user['role'] ?? null) === 'superviseur';
  }

  public function getUser(): ?User
  {
    if (session_status() !== PHP_SESSION_ACTIVE) {
      session_start();
    }

    if (!self::isConnected()) {
      return null;
    }

    $req = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
    $req->execute([$_SESSION['user']]);

    return $req->fetchObject(User::class) ?: null;
  }

  public function login(string $email, string $password): ?User
  {
    $req = $this->pdo->prepare("SELECT * FROM users WHERE email = :email");
    $req->execute(["email" => $email]);

    $user = $req->fetchObject(User::class);

    if (!$user || !password_verify($password, $user->password)) {
      return null;
    }

    if (session_status() !== PHP_SESSION_ACTIVE) {
      session_start();
    }

    $_SESSION['user'] = $user->id;
    $this->touchLastLogin((int) $user->id);

    if ($user->role === 'operateur') {
      $this->ensureAgentRow((int) $user->id);

      if ($this->operatorHasAssignedGuichet((int) $user->id)) {
        $query = $this->pdo->prepare("
          UPDATE agent
          SET debut = NOW(), fin = NULL
          WHERE user_id = :user_id
        ");
        $query->execute(["user_id" => $user->id]);
      } else {
        $this->notifyAdminWaitingAssignment($user);
      }
    }

    return $user;
  }

  public function register(array $data): bool
  {
    $req = $this->pdo->prepare("
      INSERT INTO users (username, email, password, role)
      VALUES (:username, :email, :password, :role)
    ");
    $req->execute([
      "username" => $data['username'],
      "email" => $data['email'],
      "password" => password_hash($data['password'], PASSWORD_BCRYPT),
      "role" => 'operateur',
    ]);

    $id = (int) $this->pdo->lastInsertId();

    $query = $this->pdo->prepare("
      INSERT INTO agent (user_id, debut, fin)
      VALUES (:user_id, NULL, NULL)
      ON DUPLICATE KEY UPDATE user_id = VALUES(user_id)
    ");
    $query->execute(["user_id" => $id]);

    if (session_status() !== PHP_SESSION_ACTIVE) {
      session_start();
    }

    $_SESSION['user'] = $id;

    $user = $this->getUser();
    if ($user) {
      $this->notifyAdminWaitingAssignment($user);
    }

    return true;
  }

  public function logout(): void
  {
    if (session_status() !== PHP_SESSION_ACTIVE) {
      session_start();
    }

    $user = $this->getUser();
    if ($user && $user->role === 'operateur') {
      $this->pdo->prepare("UPDATE agent SET fin = NOW() WHERE user_id = :id")->execute(['id' => $user->id]);
    }

    session_destroy();
    unset($_SESSION['user']);
  }

  public function operatorHasAssignedGuichet(int $userId): bool
  {
    $stmt = $this->pdo->prepare("
      SELECT guichet_id
      FROM agent
      WHERE user_id = ?
      LIMIT 1
    ");
    $stmt->execute([$userId]);
    $guichetId = $stmt->fetchColumn();

    return $guichetId !== false && $guichetId !== null;
  }

  public function getRedirectPath(?User $user = null): string
  {
    $user = $user ?: $this->getUser();

    if (!$user) {
      return '/login';
    }

    if ($user->role === 'admin') {
      return '/admin';
    }

    if ($user->role === 'superviseur') {
      return '/superviseur';
    }

    if ($user->role === 'operateur') {
      return $this->operatorHasAssignedGuichet((int) $user->id) ? '/operator' : '/attente';
    }

    return '/login';
  }

  private function ensureAgentRow(int $userId): void
  {
    $query = $this->pdo->prepare("
      INSERT INTO agent (user_id, debut, fin)
      VALUES (:user_id, NULL, NULL)
      ON DUPLICATE KEY UPDATE user_id = VALUES(user_id)
    ");
    $query->execute(["user_id" => $userId]);
  }

  private function touchLastLogin(int $userId): void
  {
    $stmt = $this->pdo->prepare("UPDATE users SET last_login_at = CURRENT_TIMESTAMP(3) WHERE id = ?");
    $stmt->execute([$userId]);
  }

  private function notifyAdminWaitingAssignment(User $user): void
  {
    $stmt = $this->pdo->prepare("
      SELECT COUNT(*)
      FROM admin_notifications
      WHERE category = 'operator_waiting'
        AND user_id = ?
        AND is_read = 0
    ");
    $stmt->execute([(int) $user->id]);

    if ((int) $stmt->fetchColumn() > 0) {
      return;
    }

    $stmt = $this->pdo->prepare("
      INSERT INTO admin_notifications (category, title, message, user_id, is_read)
      VALUES (?, ?, ?, ?, 0)
    ");
    $stmt->execute([
      'operator_waiting',
      'Operateur en attente d affectation',
      sprintf(
        '%s (%s) a essaye de se connecter sans guichet assigne. Merci de l affecter a une voie.',
        $user->username,
        $user->email
      ),
      (int) $user->id,
    ]);
  }
}
