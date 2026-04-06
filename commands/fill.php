<?php

declare(strict_types=1);

use App\ConnectionBDD;

require_once dirname(__DIR__) . '/vendor/autoload.php';

$faker = Faker\Factory::create('fr_Fr');

$pdo = ConnectionBDD::getPdo();

$pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");

$pdo->exec("TRUNCATE TABLE users");
$pdo->exec("TRUNCATE TABLE typevehicule");
$pdo->exec("TRUNCATE TABLE vehicule");
$pdo->exec("TRUNCATE TABLE agent");
$pdo->exec("TRUNCATE TABLE guichet");
$pdo->exec("TRUNCATE TABLE agent_guichet");

$pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");

$pswdHash = password_hash("admin123", PASSWORD_BCRYPT);
$pswdHash2 = password_hash("Azerty098", PASSWORD_BCRYPT);
$pdo->exec("INSERT INTO users (username, email, password, role) VALUES ('admin', 'admin@peage.com', '$pswdHash', 'admin')");

$pdo->exec("INSERT INTO users (username, email, password, role) VALUES ('Alex', 'alex@gmail.com', '$pswdHash2', 'operateur')");

$pdo->exec("INSERT INTO agent (user_id) VALUES (2)");

for ($i = 0; $i < 8; $i++) {
  $direction = ['Nord', 'Sud', 'Ouest', 'Est', 'Nord-Est', 'Nord-Ouest', 'Sud-Est', 'Sud-Ouest'];

  $pdo->exec("INSERT INTO guichet (emplacement) VALUES ('{$direction[$i]}')");
}

for ($i = 1; $i <= 5; ++$i) {
  $price = rand(500, 5500);
  $libelle = "category $i";

  // Utilisation d'une requête préparée (fortement recommandé)
  $stmt = $pdo->exec("INSERT INTO typevehicule (libelle, price) VALUES ('$libelle', $price)");
}

$pdo->exec("INSERT INTO agent_guichet (agent_id, guichet_id) VALUES (1, 1)");

$stmt = $pdo->prepare("INSERT INTO vehicule (immatriculation, type_vehicule_id, marque, modele, couleur) 
                      VALUES (:imma, :type, :marque, :modele, :color)");

for ($i = 0; $i < 25; $i++) {
  // Génération des données
  $imma = $faker->bothify('??-###-??'); // Format NSIIV Côte d'Ivoire
  $type = rand(1, 5);
  $marque = $faker->company();
  $modele = $faker->companySuffix();
  $color = $faker->colorName();

  // 2. On exécute avec les données (PDO s'occupe de protéger les apostrophes)
  $stmt->execute([
    'imma'   => $imma,
    'type'   => $type,
    'marque' => $marque,
    'modele' => $modele,
    'color'  => $color
  ]);
}
