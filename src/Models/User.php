<?php
namespace App\Models;

class User
{
  public ?int $id;
  public ?string $username, $email, $password, $role, $is_active;
  public ?string $created_at, $updated_at, $last_login_at;
}