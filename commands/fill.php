<?php

declare(strict_types=1);

use App\ConnectionBDD;

require_once dirname(__DIR__) . '/vendor/autoload.php';

$faker = Faker\Factory::create('fr_Fr');

$pdo = ConnectionBDD::getPdo();

$pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");

$pdo->exec("TRUNCATE TABLE admin_notifications");
$pdo->exec("TRUNCATE TABLE system_settings");
$pdo->exec("TRUNCATE TABLE superviseur_guichet");
$pdo->exec("TRUNCATE TABLE superviseur");
$pdo->exec("TRUNCATE TABLE users");
$pdo->exec("TRUNCATE TABLE vehicule");
$pdo->exec("TRUNCATE TABLE agent");
$pdo->exec("TRUNCATE TABLE guichet");

$pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");

$pswdHash = password_hash("admin123", PASSWORD_BCRYPT);
$pswdHash2 = password_hash("Azerty098", PASSWORD_BCRYPT);
$pswdHash3 = password_hash("Super123", PASSWORD_BCRYPT);
$pdo->exec("INSERT INTO users (username, email, password, role) VALUES ('admin', 'admin@peage.com', '$pswdHash', 'admin')");

$pdo->exec("INSERT INTO users (username, email, password, role) VALUES ('Alex', 'alex@gmail.com', '$pswdHash2', 'operateur')");
$pdo->exec("INSERT INTO users (username, email, password, role) VALUES ('Nadia', 'nadia@peage.com', '$pswdHash3', 'superviseur')");

for ($i = 0; $i < 8; $i++) {
  $direction = ['Nord', 'Sud', 'Ouest', 'Est', 'Nord-Est', 'Nord-Ouest', 'Sud-Est', 'Sud-Ouest'];

  $pdo->exec("INSERT INTO guichet (emplacement) VALUES ('{$direction[$i]}')");
}

$pdo->exec("INSERT INTO agent (user_id) VALUES (2)");
$pdo->exec("INSERT INTO superviseur (user_id, zone_nominale, telephone) VALUES (3, 'Zone Nord-Est', '+2250102030405')");
$pdo->exec("INSERT INTO superviseur_guichet (superviseur_id, guichet_id) VALUES (1, 1), (1, 2), (1, 5)");

$stmtSettings = $pdo->prepare("INSERT INTO system_settings (setting_key, setting_value) VALUES (:key, :value)");
foreach ([
  'bridge_name' => 'Peage Bridge',
  'bridge_code' => 'PPA-01',
  'currency' => 'FCFA (XOF)',
  'support_email' => 'support@peage.local',
  'support_phone' => '+225 01 23 45 67 89',
  'timezone' => 'UTC',
  'dashboard_refresh_seconds' => '30',
  'waiting_message' => 'Votre compte a bien ete cree. Un administrateur doit encore vous affecter a un guichet avant votre premiere vacation.',
] as $key => $value) {
  $stmtSettings->execute(['key' => $key, 'value' => $value]);
}

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

$stmt = $pdo->prepare("INSERT INTO paiement (vehicule_id, guichet_id, mode_paiement, montant) 
            VALUES (:vehicule_id, :guichet_id, :mode_paiement, :montant)");

$typeMode = ['Espece', 'Mobile Money', 'Carte', 'Abonnement'];

for ($i = 0; $i < 35; $i++) {
  $vehicule = (int)rand(1, 25);
  $guichet = (int)rand(1, 8);
  $mode = $typeMode[rand(0, 3)];
  $montant = (float)rand(500, 5500);

  $stmt->execute([
    'vehicule_id'    => $vehicule,
    'guichet_id'     => $guichet,
    'mode_paiement'  => $mode,
    'montant'        => $montant
  ]);
}
