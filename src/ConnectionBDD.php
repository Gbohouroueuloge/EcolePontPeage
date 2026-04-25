<?php

namespace App;

use PDO;

class ConnectionBDD
{

  public static function getPdo(): PDO
  {
    return new PDO("mysql:host=localhost;dbname=pont_peage2", "root", "", [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
  }
}
