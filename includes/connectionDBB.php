<?php

$pdo = new PDO("mysql:host=localhost;dbname=pont_peage2", "root", "", [
  PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);
