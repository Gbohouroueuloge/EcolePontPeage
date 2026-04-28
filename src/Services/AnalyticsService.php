<?php

namespace App\Services;

use DateInterval;
use DatePeriod;
use DateTimeImmutable;
use PDO;

class AnalyticsService
{
  public function __construct(private PDO $pdo)
  {
  }

  public function resolvePeriod(string $preset = '30j', ?string $dateMin = null, ?string $dateMax = null): array
  {
    $today = new DateTimeImmutable('now');

    switch ($preset) {
      case '7j':
        $start = $today->modify('-6 days')->setTime(0, 0, 0);
        $end = $today->setTime(23, 59, 59);
        $label = '7 derniers jours';
        break;
      case 'trimestre':
        $start = $today->modify('first day of -2 month')->setTime(0, 0, 0);
        $end = $today->setTime(23, 59, 59);
        $label = 'Trimestre en cours';
        break;
      case 'annee':
        $start = $today->modify('first day of January this year')->setTime(0, 0, 0);
        $end = $today->setTime(23, 59, 59);
        $label = 'Annee ' . $today->format('Y');
        break;
      case 'custom':
        $start = $dateMin
          ? (new DateTimeImmutable($dateMin))->setTime(0, 0, 0)
          : $today->modify('-29 days')->setTime(0, 0, 0);
        $end = $dateMax
          ? (new DateTimeImmutable($dateMax))->setTime(23, 59, 59)
          : $today->setTime(23, 59, 59);
        $label = $start->format('d/m/Y') . ' -> ' . $end->format('d/m/Y');
        break;
      case '30j':
      default:
        $preset = '30j';
        $start = $today->modify('-29 days')->setTime(0, 0, 0);
        $end = $today->setTime(23, 59, 59);
        $label = '30 derniers jours';
        break;
    }

    return [
      'preset' => $preset,
      'start' => $start,
      'end' => $end,
      'label' => $label,
    ];
  }

  public function getOverview(DateTimeImmutable $start, DateTimeImmutable $end, array $guichetIds = []): array
  {
    [$paymentWhere, $paymentParams] = $this->buildPeriodClause('p', $start, $end, $guichetIds);
    [$incidentWhere, $incidentParams] = $this->buildPeriodClause('i', $start, $end, $guichetIds);

    $stmt = $this->pdo->prepare("
      SELECT
        COUNT(*) AS total_passages,
        COALESCE(SUM(p.montant), 0) AS revenu_total,
        COALESCE(AVG(p.montant), 0) AS ticket_moyen,
        COUNT(DISTINCT p.guichet_id) AS voies_couvertes
      FROM paiement p
      {$paymentWhere}
    ");
    $stmt->execute($paymentParams);
    $payments = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

    $stmt = $this->pdo->prepare("
      SELECT COUNT(*) AS total_incidents
      FROM incident i
      {$incidentWhere}
    ");
    $stmt->execute($incidentParams);
    $incidents = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

    $activeAgents = (int) $this->pdo->query("
      SELECT COUNT(*)
      FROM agent a
      JOIN users u ON u.id = a.user_id
      WHERE u.role = 'operateur'
        AND a.guichet_id IS NOT NULL
        AND a.fin IS NULL
    ")->fetchColumn();

    $activeGuichets = (int) $this->pdo->query("
      SELECT COUNT(*)
      FROM guichet
      WHERE is_active = 1
    ")->fetchColumn();

    return [
      'total_passages' => (int) ($payments['total_passages'] ?? 0),
      'revenu_total' => (float) ($payments['revenu_total'] ?? 0),
      'ticket_moyen' => (float) ($payments['ticket_moyen'] ?? 0),
      'voies_couvertes' => (int) ($payments['voies_couvertes'] ?? 0),
      'total_incidents' => (int) ($incidents['total_incidents'] ?? 0),
      'agents_actifs' => $activeAgents,
      'guichets_actifs' => $activeGuichets,
    ];
  }

  public function getDailyTrafficSeries(int $days = 7, array $guichetIds = []): array
  {
    $days = max(1, $days);
    $start = (new DateTimeImmutable('today'))->sub(new DateInterval('P' . ($days - 1) . 'D'))->setTime(0, 0, 0);
    $end = (new DateTimeImmutable('today'))->setTime(23, 59, 59);

    [$paymentWhere, $paymentParams] = $this->buildPeriodClause('p', $start, $end, $guichetIds);
    [$incidentWhere, $incidentParams] = $this->buildPeriodClause('i', $start, $end, $guichetIds);

    $stmt = $this->pdo->prepare("
      SELECT
        DATE(p.created_at) AS date_key,
        COUNT(*) AS passages,
        COALESCE(SUM(p.montant), 0) AS revenu
      FROM paiement p
      {$paymentWhere}
      GROUP BY DATE(p.created_at)
      ORDER BY DATE(p.created_at) ASC
    ");
    $stmt->execute($paymentParams);
    $payments = [];
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
      $payments[$row['date_key']] = $row;
    }

    $stmt = $this->pdo->prepare("
      SELECT
        DATE(i.created_at) AS date_key,
        COUNT(*) AS incidents
      FROM incident i
      {$incidentWhere}
      GROUP BY DATE(i.created_at)
      ORDER BY DATE(i.created_at) ASC
    ");
    $stmt->execute($incidentParams);
    $incidents = [];
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
      $incidents[$row['date_key']] = $row;
    }

    $series = [];
    $period = new DatePeriod($start, new DateInterval('P1D'), $end->modify('+1 day'));
    foreach ($period as $date) {
      $dateKey = $date->format('Y-m-d');
      $paymentRow = $payments[$dateKey] ?? [];
      $incidentRow = $incidents[$dateKey] ?? [];

      $series[] = [
        'date_key' => $dateKey,
        'label' => $date->format('d M'),
        'label_short' => $date->format('D'),
        'passages' => (int) ($paymentRow['passages'] ?? 0),
        'revenu' => (float) ($paymentRow['revenu'] ?? 0),
        'incidents' => (int) ($incidentRow['incidents'] ?? 0),
      ];
    }

    return $series;
  }

  public function getMonthlyRevenueSeries(int $months = 6, array $guichetIds = []): array
  {
    $months = max(1, $months);
    $start = (new DateTimeImmutable('first day of this month'))->sub(new DateInterval('P' . ($months - 1) . 'M'))->setTime(0, 0, 0);
    $end = (new DateTimeImmutable('last day of this month'))->setTime(23, 59, 59);

    [$where, $params] = $this->buildPeriodClause('p', $start, $end, $guichetIds);

    $stmt = $this->pdo->prepare("
      SELECT
        DATE_FORMAT(p.created_at, '%Y-%m-01') AS month_key,
        SUM(p.montant) AS revenu,
        COUNT(*) AS passages
      FROM paiement p
      {$where}
      GROUP BY DATE_FORMAT(p.created_at, '%Y-%m-01')
      ORDER BY month_key ASC
    ");
    $stmt->execute($params);
    $rows = [];
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
      $rows[$row['month_key']] = $row;
    }

    $series = [];
    $cursor = $start;
    for ($index = 0; $index < $months; $index++) {
      $monthKey = $cursor->format('Y-m-01');
      $row = $rows[$monthKey] ?? [];

      $series[] = [
        'month_key' => $monthKey,
        'label' => $cursor->format('M Y'),
        'revenu' => (float) ($row['revenu'] ?? 0),
        'passages' => (int) ($row['passages'] ?? 0),
      ];

      $cursor = $cursor->modify('+1 month');
    }

    return $series;
  }

  public function getRevenueByMode(DateTimeImmutable $start, DateTimeImmutable $end, array $guichetIds = []): array
  {
    [$where, $params] = $this->buildPeriodClause('p', $start, $end, $guichetIds);

    $stmt = $this->pdo->prepare("
      SELECT
        p.mode_paiement,
        COUNT(*) AS passages,
        COALESCE(SUM(p.montant), 0) AS revenu
      FROM paiement p
      {$where}
      GROUP BY p.mode_paiement
      ORDER BY revenu DESC, passages DESC
    ");
    $stmt->execute($params);

    return array_map(function (array $row): array {
      return [
        'mode' => $row['mode_paiement'],
        'passages' => (int) $row['passages'],
        'revenu' => (float) $row['revenu'],
      ];
    }, $stmt->fetchAll(PDO::FETCH_ASSOC));
  }

  public function getRevenueByGuichet(DateTimeImmutable $start, DateTimeImmutable $end, array $guichetIds = []): array
  {
    [$where, $params] = $this->buildPeriodClause('p', $start, $end, $guichetIds);

    $stmt = $this->pdo->prepare("
      SELECT
        g.id,
        g.emplacement,
        COUNT(p.id) AS passages,
        COALESCE(SUM(p.montant), 0) AS revenu
      FROM paiement p
      JOIN guichet g ON g.id = p.guichet_id
      {$where}
      GROUP BY g.id, g.emplacement
      ORDER BY revenu DESC, passages DESC, g.id ASC
    ");
    $stmt->execute($params);

    return array_map(function (array $row): array {
      return [
        'guichet_id' => (int) $row['id'],
        'emplacement' => $row['emplacement'],
        'passages' => (int) $row['passages'],
        'revenu' => (float) $row['revenu'],
      ];
    }, $stmt->fetchAll(PDO::FETCH_ASSOC));
  }

  public function getVehicleTypeMix(DateTimeImmutable $start, DateTimeImmutable $end, array $guichetIds = []): array
  {
    [$where, $params] = $this->buildPeriodClause('p', $start, $end, $guichetIds);

    $stmt = $this->pdo->prepare("
      SELECT
        t.libelle,
        COUNT(p.id) AS passages,
        COALESCE(SUM(p.montant), 0) AS revenu,
        COALESCE(AVG(p.montant), 0) AS ticket_moyen
      FROM paiement p
      JOIN vehicule v ON v.id = p.vehicule_id
      JOIN typevehicule t ON t.id = v.type_vehicule_id
      {$where}
      GROUP BY t.id, t.libelle
      ORDER BY revenu DESC, passages DESC
    ");
    $stmt->execute($params);

    return array_map(function (array $row): array {
      return [
        'libelle' => $row['libelle'],
        'passages' => (int) $row['passages'],
        'revenu' => (float) $row['revenu'],
        'ticket_moyen' => (float) $row['ticket_moyen'],
      ];
    }, $stmt->fetchAll(PDO::FETCH_ASSOC));
  }

  public function getIncidentsByType(DateTimeImmutable $start, DateTimeImmutable $end, array $guichetIds = []): array
  {
    [$where, $params] = $this->buildPeriodClause('i', $start, $end, $guichetIds);

    $stmt = $this->pdo->prepare("
      SELECT
        i.type,
        COUNT(*) AS total
      FROM incident i
      {$where}
      GROUP BY i.type
      ORDER BY total DESC, i.type ASC
    ");
    $stmt->execute($params);

    return array_map(function (array $row): array {
      return [
        'type' => $row['type'],
        'total' => (int) $row['total'],
      ];
    }, $stmt->fetchAll(PDO::FETCH_ASSOC));
  }

  public function getRecentActivity(int $limit = 10, array $guichetIds = []): array
  {
    $limit = max(1, min(50, $limit));

    [$paymentWhere, $paymentParams] = $this->buildOptionalGuichetClause('p', $guichetIds);
    [$incidentWhere, $incidentParams] = $this->buildOptionalGuichetClause('i', $guichetIds);

    $sql = "
      SELECT *
      FROM (
        SELECT
          'payment' AS activity_type,
          p.created_at,
          v.immatriculation,
          p.mode_paiement AS label,
          p.montant,
          g.id AS guichet_id,
          g.emplacement,
          CONCAT('Paiement ', p.mode_paiement) AS title,
          NULL AS description
        FROM paiement p
        JOIN vehicule v ON v.id = p.vehicule_id
        JOIN guichet g ON g.id = p.guichet_id
        {$paymentWhere}

        UNION ALL

        SELECT
          'incident' AS activity_type,
          i.created_at,
          v.immatriculation,
          i.type AS label,
          NULL AS montant,
          g.id AS guichet_id,
          g.emplacement,
          CONCAT('Incident ', i.type) AS title,
          COALESCE(i.description, 'Signalement en attente de traitement') AS description
        FROM incident i
        JOIN vehicule v ON v.id = i.vehicule_id
        JOIN guichet g ON g.id = i.guichet_id
        {$incidentWhere}
      ) activity_feed
      ORDER BY created_at DESC
      LIMIT {$limit}
    ";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute(array_merge($paymentParams, $incidentParams));

    return array_map(function (array $row): array {
      return [
        'activity_type' => $row['activity_type'],
        'created_at' => $row['created_at'],
        'immatriculation' => $row['immatriculation'],
        'label' => $row['label'],
        'montant' => $row['montant'] === null ? null : (float) $row['montant'],
        'guichet_id' => (int) $row['guichet_id'],
        'emplacement' => $row['emplacement'],
        'title' => $row['title'],
        'description' => $row['description'],
      ];
    }, $stmt->fetchAll(PDO::FETCH_ASSOC));
  }

  public function getDetailedTransactions(DateTimeImmutable $start, DateTimeImmutable $end, array $guichetIds = []): array
  {
    [$where, $params] = $this->buildPeriodClause('p', $start, $end, $guichetIds);

    $stmt = $this->pdo->prepare("
      SELECT
        p.id,
        p.created_at,
        v.immatriculation,
        t.libelle AS type_vehicule,
        g.id AS guichet_id,
        g.emplacement AS guichet,
        p.mode_paiement,
        p.montant,
        p.is_valide
      FROM paiement p
      JOIN vehicule v ON v.id = p.vehicule_id
      JOIN typevehicule t ON t.id = v.type_vehicule_id
      JOIN guichet g ON g.id = p.guichet_id
      {$where}
      ORDER BY p.created_at DESC, p.id DESC
    ");
    $stmt->execute($params);

    return array_map(function (array $row): array {
      return [
        'id' => (int) $row['id'],
        'created_at' => $row['created_at'],
        'immatriculation' => $row['immatriculation'],
        'type_vehicule' => $row['type_vehicule'],
        'guichet_id' => (int) $row['guichet_id'],
        'guichet' => $row['guichet'],
        'mode_paiement' => $row['mode_paiement'],
        'montant' => (float) $row['montant'],
        'is_valide' => (int) $row['is_valide'],
      ];
    }, $stmt->fetchAll(PDO::FETCH_ASSOC));
  }

  public function getDetailedIncidents(DateTimeImmutable $start, DateTimeImmutable $end, array $guichetIds = []): array
  {
    [$where, $params] = $this->buildPeriodClause('i', $start, $end, $guichetIds);

    $stmt = $this->pdo->prepare("
      SELECT
        i.id,
        i.created_at,
        i.type,
        COALESCE(i.description, '') AS description,
        v.immatriculation,
        g.id AS guichet_id,
        g.emplacement AS guichet
      FROM incident i
      JOIN vehicule v ON v.id = i.vehicule_id
      JOIN guichet g ON g.id = i.guichet_id
      {$where}
      ORDER BY i.created_at DESC, i.id DESC
    ");
    $stmt->execute($params);

    return array_map(function (array $row): array {
      return [
        'id' => (int) $row['id'],
        'created_at' => $row['created_at'],
        'type' => $row['type'],
        'description' => $row['description'],
        'immatriculation' => $row['immatriculation'],
        'guichet_id' => (int) $row['guichet_id'],
        'guichet' => $row['guichet'],
      ];
    }, $stmt->fetchAll(PDO::FETCH_ASSOC));
  }

  public function getSupervisorGuichetIds(int $userId): array
  {
    $stmt = $this->pdo->prepare("
      SELECT sg.guichet_id
      FROM superviseur s
      JOIN superviseur_guichet sg ON sg.superviseur_id = s.id
      WHERE s.user_id = ?
      ORDER BY sg.guichet_id ASC
    ");
    $stmt->execute([$userId]);

    return array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));
  }

  private function buildPeriodClause(string $alias, DateTimeImmutable $start, DateTimeImmutable $end, array $guichetIds = []): array
  {
    $where = "WHERE {$alias}.created_at BETWEEN ? AND ?";
    $params = [$start->format('Y-m-d H:i:s'), $end->format('Y-m-d H:i:s')];

    if (!empty($guichetIds)) {
      $guichetIds = array_values(array_unique(array_map('intval', $guichetIds)));
      $placeholders = implode(', ', array_fill(0, count($guichetIds), '?'));
      $where .= " AND {$alias}.guichet_id IN ({$placeholders})";
      $params = array_merge($params, $guichetIds);
    }

    return [$where, $params];
  }

  private function buildOptionalGuichetClause(string $alias, array $guichetIds = []): array
  {
    if (empty($guichetIds)) {
      return ['', []];
    }

    $guichetIds = array_values(array_unique(array_map('intval', $guichetIds)));
    $placeholders = implode(', ', array_fill(0, count($guichetIds), '?'));

    return ["WHERE {$alias}.guichet_id IN ({$placeholders})", $guichetIds];
  }
}
