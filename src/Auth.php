<?php

namespace App;

use App\Models\User;

class Auth
{
  private $pdo;

  public function __construct(\PDO $pdo)
  {
    $this->pdo = $pdo;
  }

  public static function isConnected(): bool
  {
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    return isset($_SESSION['user']);
  }

  public function isAdmin(): bool
  {
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    if (!self::isConnected()) return false;

    $req = $this->pdo->prepare("SELECT role FROM users WHERE id = ?");
    $req->execute([$_SESSION['user']]);
    $user = $req->fetch();

    return $user['role'] === 'admin';
  }

  public function getUser(): ?User
  {
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    if (!self::isConnected()) return null;

    $req = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
    $req->execute([$_SESSION['user']]);
    return $req->fetchObject(User::class);
  }

  public function login(string $email, string $password): ?User
  {
    $req = $this->pdo->prepare("SELECT * FROM users WHERE email = :email");
    $req->execute(["email" => $email]);

    $user = $req->fetchObject(User::class);

    if ($user && password_verify($password, $user->password)) {
      if (session_status() !== PHP_SESSION_ACTIVE) session_start();
      $_SESSION['user'] = $user->id;

      if ($user->role === 'operateur') {
        $query = $this->pdo->prepare("
        INSERT INTO agent (user_id, debut, fin) 
        VALUES (:user_id, NOW(), NULL)
        ON DUPLICATE KEY UPDATE 
            debut = NOW(), 
            fin = NULL
        ");
        $query->execute(["user_id" => $user->id]);
      }

      return $user;
    }
    return null;
  }

  public function register(User $user): bool
  {
    $req = $this->pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (:name, :email, :password, :role)");
    $req->execute([
      "name" => $user->username,
      "email" => $user->email,
      "password" => password_hash($user->password, PASSWORD_BCRYPT),
      "role" => $user->role
    ]);

    if ($user->role === 'operateur') {
      $query = $this->pdo->prepare("
        INSERT INTO agent (user_id, debut, fin) 
        VALUES (:user_id, NOW(), NULL)
        ON DUPLICATE KEY UPDATE 
            debut = NOW(), 
            fin = NULL
        ");
      $query->execute(["user_id" => $user->id]);
    }

    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    $_SESSION['user'] = $user->id;

    return true;
  }

  public function logout(): void
  {
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    session_destroy();
    unset($_SESSION['user']);
  }
}
