<?php

namespace App\Services;

use PDO;

class AdminService
{
  private array $defaultSettings = [
    'bridge_name' => 'Peage Bridge',
    'bridge_code' => 'PPA-01',
    'currency' => 'FCFA (XOF)',
    'support_email' => 'support@peage.local',
    'support_phone' => '+225 01 23 45 67 89',
    'timezone' => 'UTC',
    'dashboard_refresh_seconds' => '30',
    'waiting_message' => 'Votre compte a bien ete cree. Un administrateur doit encore vous affecter a un guichet avant votre premiere vacation.',
  ];

  public function __construct(private PDO $pdo)
  {
  }

  public function getUserStats(): array
  {
    $stmt = $this->pdo->query("
      SELECT
        COUNT(*) AS total,
        SUM(CASE WHEN role = 'admin' THEN 1 ELSE 0 END) AS admins,
        SUM(CASE WHEN role = 'superviseur' THEN 1 ELSE 0 END) AS superviseurs,
        SUM(CASE WHEN role = 'operateur' THEN 1 ELSE 0 END) AS operateurs,
        SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) AS actifs
      FROM users
    ");
    $users = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

    $pendingAssignments = (int) $this->pdo->query("
      SELECT COUNT(*)
      FROM users u
      LEFT JOIN agent a ON a.user_id = u.id
      WHERE u.role = 'operateur'
        AND (a.guichet_id IS NULL OR a.guichet_id = 0)
    ")->fetchColumn();

    return [
      'total' => (int) ($users['total'] ?? 0),
      'admins' => (int) ($users['admins'] ?? 0),
      'superviseurs' => (int) ($users['superviseurs'] ?? 0),
      'operateurs' => (int) ($users['operateurs'] ?? 0),
      'actifs' => (int) ($users['actifs'] ?? 0),
      'pending_assignments' => $pendingAssignments,
      'messages_non_lus' => $this->countUnreadNotifications(),
    ];
  }

  public function getUsers(?string $role = null): array
  {
    $where = '';
    $params = [];

    if ($role) {
      $where = 'WHERE u.role = ?';
      $params[] = $role;
    }

    $stmt = $this->pdo->prepare("
      SELECT
        u.*,
        a.id AS agent_id,
        a.guichet_id AS agent_guichet_id,
        a.date_assignation,
        g.emplacement AS agent_guichet_label,
        sup.superviseur_id,
        sup.zone_nominale,
        sup.telephone AS superviseur_telephone,
        sup.guichet_ids,
        sup.guichet_labels
      FROM users u
      LEFT JOIN agent a ON a.user_id = u.id
      LEFT JOIN guichet g ON g.id = a.guichet_id
      LEFT JOIN (
        SELECT
          s.user_id,
          s.id AS superviseur_id,
          s.zone_nominale,
          s.telephone,
          GROUP_CONCAT(sg.guichet_id ORDER BY sg.guichet_id SEPARATOR ',') AS guichet_ids,
          GROUP_CONCAT(g2.emplacement ORDER BY g2.id SEPARATOR ', ') AS guichet_labels
        FROM superviseur s
        LEFT JOIN superviseur_guichet sg ON sg.superviseur_id = s.id
        LEFT JOIN guichet g2 ON g2.id = sg.guichet_id
        GROUP BY s.user_id, s.id, s.zone_nominale, s.telephone
      ) sup ON sup.user_id = u.id
      {$where}
      ORDER BY
        CASE u.role
          WHEN 'admin' THEN 1
          WHEN 'superviseur' THEN 2
          ELSE 3
        END,
        u.created_at DESC,
        u.id DESC
    ");
    $stmt->execute($params);

    return array_map(function (array $row): array {
      $guichetIds = [];
      if (!empty($row['guichet_ids'])) {
        $guichetIds = array_map('intval', explode(',', $row['guichet_ids']));
      }

      return [
        'id' => (int) $row['id'],
        'username' => $row['username'],
        'email' => $row['email'],
        'role' => $row['role'],
        'is_active' => (int) $row['is_active'],
        'created_at' => $row['created_at'],
        'updated_at' => $row['updated_at'],
        'last_login_at' => $row['last_login_at'],
        'agent_id' => $row['agent_id'] === null ? null : (int) $row['agent_id'],
        'agent_guichet_id' => $row['agent_guichet_id'] === null ? null : (int) $row['agent_guichet_id'],
        'agent_guichet_label' => $row['agent_guichet_label'],
        'date_assignation' => $row['date_assignation'],
        'superviseur_id' => $row['superviseur_id'] === null ? null : (int) $row['superviseur_id'],
        'zone_nominale' => $row['zone_nominale'],
        'superviseur_telephone' => $row['superviseur_telephone'],
        'supervisor_guichet_ids' => $guichetIds,
        'supervisor_guichet_labels' => $row['guichet_labels'] ?? '',
      ];
    }, $stmt->fetchAll(PDO::FETCH_ASSOC));
  }

  public function getUserById(int $id): ?array
  {
    foreach ($this->getUsers() as $user) {
      if ($user['id'] === $id) {
        return $user;
      }
    }

    return null;
  }

  public function getGuichets(): array
  {
    $stmt = $this->pdo->query("
      SELECT id, slug, emplacement, is_active
      FROM guichet
      ORDER BY id ASC
    ");

    return array_map(function (array $row): array {
      return [
        'id' => (int) $row['id'],
        'slug' => $row['slug'],
        'emplacement' => $row['emplacement'],
        'is_active' => (int) $row['is_active'],
      ];
    }, $stmt->fetchAll(PDO::FETCH_ASSOC));
  }

  public function saveUser(array $data, ?int $id = null): array
  {
    $username = trim((string) ($data['username'] ?? ''));
    $email = strtolower(trim((string) ($data['email'] ?? '')));
    $password = (string) ($data['password'] ?? '');
    $role = (string) ($data['role'] ?? 'operateur');
    $isActive = !empty($data['is_active']) ? 1 : 0;
    $guichetId = !empty($data['guichet_id']) ? (int) $data['guichet_id'] : null;
    $zoneNominale = trim((string) ($data['zone_nominale'] ?? ''));
    $telephone = trim((string) ($data['telephone'] ?? ''));
    $supervisorGuichetIds = array_map('intval', $data['supervisor_guichets'] ?? []);

    $errors = [];

    if ($username === '') {
      $errors[] = 'Le nom utilisateur est obligatoire.';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $errors[] = 'Adresse e-mail invalide.';
    }

    if (!in_array($role, ['admin', 'operateur', 'superviseur'], true)) {
      $errors[] = 'Role utilisateur invalide.';
    }

    if ($id === null && $password === '') {
      $errors[] = 'Le mot de passe est obligatoire a la creation.';
    }

    if ($role === 'superviseur' && empty($supervisorGuichetIds)) {
      $errors[] = 'Selectionnez au moins un guichet supervise.';
    }

    $stmt = $this->pdo->prepare("
      SELECT id
      FROM users
      WHERE email = ?
        AND (? IS NULL OR id <> ?)
      LIMIT 1
    ");
    $stmt->execute([$email, $id, $id]);
    if ($stmt->fetchColumn()) {
      $errors[] = 'Cette adresse e-mail est deja utilisee.';
    }

    if ($errors) {
      return ['success' => false, 'errors' => $errors];
    }

    $this->pdo->beginTransaction();

    try {
      if ($id === null) {
        $stmt = $this->pdo->prepare("
          INSERT INTO users (username, email, password, role, is_active)
          VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([
          $username,
          $email,
          password_hash($password, PASSWORD_BCRYPT),
          $role,
          $isActive,
        ]);
        $id = (int) $this->pdo->lastInsertId();
      } else {
        if ($password !== '') {
          $stmt = $this->pdo->prepare("
            UPDATE users
            SET username = ?, email = ?, password = ?, role = ?, is_active = ?
            WHERE id = ?
          ");
          $stmt->execute([
            $username,
            $email,
            password_hash($password, PASSWORD_BCRYPT),
            $role,
            $isActive,
            $id,
          ]);
        } else {
          $stmt = $this->pdo->prepare("
            UPDATE users
            SET username = ?, email = ?, role = ?, is_active = ?
            WHERE id = ?
          ");
          $stmt->execute([
            $username,
            $email,
            $role,
            $isActive,
            $id,
          ]);
        }
      }

      if ($role === 'operateur') {
        $this->removeSupervisorProfile($id);
        $this->ensureAgentProfile($id);
        $this->assignOperatorToGuichet($id, $guichetId);
      } elseif ($role === 'superviseur') {
        $this->clearAgentAssignment($id);
        $this->ensureSupervisorProfile($id, $zoneNominale, $telephone);
        $this->syncSupervisorGuichets($id, $supervisorGuichetIds);
      } else {
        $this->clearAgentAssignment($id);
        $this->removeSupervisorProfile($id);
      }

      $this->pdo->commit();

      return ['success' => true, 'user_id' => $id, 'errors' => []];
    } catch (\Throwable $exception) {
      $this->pdo->rollBack();
      return ['success' => false, 'errors' => [$exception->getMessage()]];
    }
  }

  public function getSettings(): array
  {
    $settings = $this->defaultSettings;

    $stmt = $this->pdo->query("
      SELECT setting_key, setting_value
      FROM system_settings
    ");

    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
      $settings[$row['setting_key']] = $row['setting_value'];
    }

    return $settings;
  }

  public function saveSettings(array $settings, ?int $updatedBy = null): void
  {
    $payload = [
      'bridge_name' => trim((string) ($settings['bridge_name'] ?? $this->defaultSettings['bridge_name'])),
      'bridge_code' => trim((string) ($settings['bridge_code'] ?? $this->defaultSettings['bridge_code'])),
      'currency' => trim((string) ($settings['currency'] ?? $this->defaultSettings['currency'])),
      'support_email' => trim((string) ($settings['support_email'] ?? $this->defaultSettings['support_email'])),
      'support_phone' => trim((string) ($settings['support_phone'] ?? $this->defaultSettings['support_phone'])),
      'timezone' => trim((string) ($settings['timezone'] ?? $this->defaultSettings['timezone'])),
      'dashboard_refresh_seconds' => (string) max(10, (int) ($settings['dashboard_refresh_seconds'] ?? 30)),
      'waiting_message' => trim((string) ($settings['waiting_message'] ?? $this->defaultSettings['waiting_message'])),
    ];

    $stmt = $this->pdo->prepare("
      INSERT INTO system_settings (setting_key, setting_value, updated_by)
      VALUES (?, ?, ?)
      ON DUPLICATE KEY UPDATE
        setting_value = VALUES(setting_value),
        updated_by = VALUES(updated_by),
        updated_at = CURRENT_TIMESTAMP(3)
    ");

    foreach ($payload as $key => $value) {
      $stmt->execute([$key, $value, $updatedBy]);
    }
  }

  public function getNotifications(bool $onlyUnread = false, int $limit = 50): array
  {
    $limit = max(1, min(200, $limit));
    $where = $onlyUnread ? 'WHERE n.is_read = 0' : '';

    $stmt = $this->pdo->query("
      SELECT
        n.*,
        u.username,
        u.email,
        u.role
      FROM admin_notifications n
      LEFT JOIN users u ON u.id = n.user_id
      {$where}
      ORDER BY n.created_at DESC, n.id DESC
      LIMIT {$limit}
    ");

    return array_map(function (array $row): array {
      return [
        'id' => (int) $row['id'],
        'category' => $row['category'],
        'title' => $row['title'],
        'message' => $row['message'],
        'is_read' => (int) $row['is_read'],
        'created_at' => $row['created_at'],
        'username' => $row['username'],
        'email' => $row['email'],
        'role' => $row['role'],
      ];
    }, $stmt->fetchAll(PDO::FETCH_ASSOC));
  }

  public function markNotificationRead(int $notificationId): void
  {
    $stmt = $this->pdo->prepare("
      UPDATE admin_notifications
      SET is_read = 1
      WHERE id = ?
    ");
    $stmt->execute([$notificationId]);
  }

  public function countUnreadNotifications(): int
  {
    return (int) $this->pdo->query("
      SELECT COUNT(*)
      FROM admin_notifications
      WHERE is_read = 0
    ")->fetchColumn();
  }

  public function createAdminNotification(string $category, string $title, string $message, ?int $userId = null): void
  {
    $stmt = $this->pdo->prepare("
      INSERT INTO admin_notifications (category, title, message, user_id, is_read)
      VALUES (?, ?, ?, ?, 0)
    ");
    $stmt->execute([$category, $title, $message, $userId]);
  }

  public function getSupervisorSummary(): array
  {
    $stmt = $this->pdo->query("
      SELECT
        s.id,
        s.user_id,
        s.zone_nominale,
        s.telephone,
        u.username,
        u.email,
        COUNT(sg.guichet_id) AS total_voies,
        GROUP_CONCAT(g.emplacement ORDER BY g.id SEPARATOR ', ') AS voies
      FROM superviseur s
      JOIN users u ON u.id = s.user_id
      LEFT JOIN superviseur_guichet sg ON sg.superviseur_id = s.id
      LEFT JOIN guichet g ON g.id = sg.guichet_id
      GROUP BY s.id, s.user_id, s.zone_nominale, s.telephone, u.username, u.email
      ORDER BY u.username ASC
    ");

    return array_map(function (array $row): array {
      return [
        'id' => (int) $row['id'],
        'user_id' => (int) $row['user_id'],
        'username' => $row['username'],
        'email' => $row['email'],
        'zone_nominale' => $row['zone_nominale'],
        'telephone' => $row['telephone'],
        'total_voies' => (int) $row['total_voies'],
        'voies' => $row['voies'] ?? '',
      ];
    }, $stmt->fetchAll(PDO::FETCH_ASSOC));
  }

  private function ensureAgentProfile(int $userId): void
  {
    $stmt = $this->pdo->prepare("
      INSERT INTO agent (user_id, guichet_id, debut, fin, date_assignation)
      VALUES (?, NULL, NULL, NULL, NULL)
      ON DUPLICATE KEY UPDATE user_id = VALUES(user_id)
    ");
    $stmt->execute([$userId]);
  }

  private function assignOperatorToGuichet(int $userId, ?int $guichetId): void
  {
    if ($guichetId === null) {
      $stmt = $this->pdo->prepare("
        UPDATE agent
        SET guichet_id = NULL,
            date_assignation = NULL,
            debut = NULL,
            fin = NULL
        WHERE user_id = ?
      ");
      $stmt->execute([$userId]);
      return;
    }

    $stmt = $this->pdo->prepare("
      UPDATE agent
      SET guichet_id = ?,
          date_assignation = NOW(),
          debut = NULL,
          fin = NULL
      WHERE user_id = ?
    ");
    $stmt->execute([$guichetId, $userId]);
  }

  private function clearAgentAssignment(int $userId): void
  {
    $stmt = $this->pdo->prepare("
      UPDATE agent
      SET guichet_id = NULL,
          date_assignation = NULL,
          debut = NULL,
          fin = NULL
      WHERE user_id = ?
    ");
    $stmt->execute([$userId]);
  }

  private function ensureSupervisorProfile(int $userId, string $zoneNominale, string $telephone): void
  {
    $stmt = $this->pdo->prepare("
      INSERT INTO superviseur (user_id, zone_nominale, telephone)
      VALUES (?, ?, ?)
      ON DUPLICATE KEY UPDATE
        zone_nominale = VALUES(zone_nominale),
        telephone = VALUES(telephone)
    ");
    $stmt->execute([$userId, $zoneNominale ?: 'Zone centrale', $telephone ?: null]);
  }

  private function syncSupervisorGuichets(int $userId, array $guichetIds): void
  {
    $stmt = $this->pdo->prepare("
      SELECT id
      FROM superviseur
      WHERE user_id = ?
      LIMIT 1
    ");
    $stmt->execute([$userId]);
    $superviseurId = (int) $stmt->fetchColumn();

    $stmt = $this->pdo->prepare("
      DELETE FROM superviseur_guichet
      WHERE superviseur_id = ?
    ");
    $stmt->execute([$superviseurId]);

    if (empty($guichetIds)) {
      return;
    }

    $stmt = $this->pdo->prepare("
      INSERT INTO superviseur_guichet (superviseur_id, guichet_id)
      VALUES (?, ?)
    ");

    foreach (array_values(array_unique(array_map('intval', $guichetIds))) as $guichetId) {
      $stmt->execute([$superviseurId, $guichetId]);
    }
  }

  private function removeSupervisorProfile(int $userId): void
  {
    $stmt = $this->pdo->prepare("
      SELECT id
      FROM superviseur
      WHERE user_id = ?
      LIMIT 1
    ");
    $stmt->execute([$userId]);
    $superviseurId = $stmt->fetchColumn();

    if (!$superviseurId) {
      return;
    }

    $stmt = $this->pdo->prepare("
      DELETE FROM superviseur_guichet
      WHERE superviseur_id = ?
    ");
    $stmt->execute([$superviseurId]);

    $stmt = $this->pdo->prepare("
      DELETE FROM superviseur
      WHERE id = ?
    ");
    $stmt->execute([$superviseurId]);
  }
}
