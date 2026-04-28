<?php

declare(strict_types=1);

use App\ConnectionBDD;

require_once dirname(__DIR__) . '/vendor/autoload.php';

$pdo = ConnectionBDD::getPdo();

$statements = [
  "ALTER TABLE users MODIFY role ENUM('operateur','admin','superviseur') NOT NULL DEFAULT 'operateur'",
  "CREATE TABLE IF NOT EXISTS admin_notifications (
      id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
      category VARCHAR(80) NOT NULL,
      title VARCHAR(180) NOT NULL,
      message TEXT NOT NULL,
      user_id INT(10) UNSIGNED DEFAULT NULL,
      is_read TINYINT(1) NOT NULL DEFAULT 0,
      created_at TIMESTAMP(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
      updated_at TIMESTAMP(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3) ON UPDATE CURRENT_TIMESTAMP(3),
      PRIMARY KEY (id),
      KEY idx_notifications_user (user_id),
      KEY idx_notifications_read (is_read)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci",
  "CREATE TABLE IF NOT EXISTS system_settings (
      setting_key VARCHAR(100) NOT NULL,
      setting_value TEXT NOT NULL,
      updated_by INT(10) UNSIGNED DEFAULT NULL,
      created_at TIMESTAMP(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
      updated_at TIMESTAMP(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3) ON UPDATE CURRENT_TIMESTAMP(3),
      PRIMARY KEY (setting_key),
      KEY idx_settings_updated_by (updated_by)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci",
  "CREATE TABLE IF NOT EXISTS superviseur (
      id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
      user_id INT(10) UNSIGNED NOT NULL,
      zone_nominale VARCHAR(120) DEFAULT NULL,
      telephone VARCHAR(50) DEFAULT NULL,
      created_at TIMESTAMP(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
      updated_at TIMESTAMP(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3) ON UPDATE CURRENT_TIMESTAMP(3),
      PRIMARY KEY (id),
      UNIQUE KEY uniq_superviseur_user (user_id),
      CONSTRAINT fk_superviseur_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci",
  "CREATE TABLE IF NOT EXISTS superviseur_guichet (
      id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
      superviseur_id INT(10) UNSIGNED NOT NULL,
      guichet_id INT(10) UNSIGNED NOT NULL,
      created_at TIMESTAMP(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
      PRIMARY KEY (id),
      UNIQUE KEY uniq_superviseur_guichet (superviseur_id, guichet_id),
      KEY idx_superviseur_guichet_guichet (guichet_id),
      CONSTRAINT fk_superviseur_guichet_superviseur FOREIGN KEY (superviseur_id) REFERENCES superviseur (id) ON DELETE CASCADE ON UPDATE CASCADE,
      CONSTRAINT fk_superviseur_guichet_guichet FOREIGN KEY (guichet_id) REFERENCES guichet (id) ON DELETE CASCADE ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci",
];

try {
  foreach ($statements as $statement) {
    $pdo->exec($statement);
  }

  $settings = [
    'bridge_name' => 'Pont a Peage Atlantique',
    'bridge_code' => 'PPA-01',
    'currency' => 'FCFA (XOF)',
    'support_email' => 'support@peage.local',
    'support_phone' => '+225 01 23 45 67 89',
    'timezone' => 'UTC',
    'dashboard_refresh_seconds' => '30',
    'waiting_message' => 'Votre compte a bien ete cree. Un administrateur doit encore vous affecter a un guichet avant votre premiere vacation.',
  ];

  $stmt = $pdo->prepare("
    INSERT INTO system_settings (setting_key, setting_value)
    VALUES (?, ?)
    ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)
  ");

  foreach ($settings as $key => $value) {
    $stmt->execute([$key, $value]);
  }

  echo "Migration 20260428 appliquee avec succes.\n";
} catch (Throwable $exception) {
  fwrite(STDERR, $exception->getMessage() . PHP_EOL);
  exit(1);
}
