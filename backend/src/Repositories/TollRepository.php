<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Support\Format;
use DateInterval;
use DateTimeImmutable;
use PDO;

class TollRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function getUserByEmail(string $email): ?array
    {
        $statement = $this->pdo->prepare(
            'SELECT users.*, booths.name AS booth_name, booths.lane_code, booths.zone
             FROM users
             LEFT JOIN booths ON booths.id = users.booth_id
             WHERE email = :email
             LIMIT 1'
        );
        $statement->execute(['email' => $email]);
        $user = $statement->fetch();

        return $user ?: null;
    }

    public function publicUser(array $user): array
    {
        return [
            'id' => (int) $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role'],
            'booth' => $user['booth_id'] ? [
                'id' => (int) $user['booth_id'],
                'name' => $user['booth_name'],
                'lane_code' => $user['lane_code'],
                'zone' => $user['zone'],
            ] : null,
        ];
    }

    public function createToken(int $userId): string
    {
        $token = bin2hex(random_bytes(24));
        $statement = $this->pdo->prepare(
            'INSERT INTO api_tokens (user_id, token_hash, created_at, last_used_at)
             VALUES (:user_id, :token_hash, :created_at, :last_used_at)'
        );
        $now = (new DateTimeImmutable())->format(DATE_ATOM);
        $statement->execute([
            'user_id' => $userId,
            'token_hash' => hash('sha256', $token),
            'created_at' => $now,
            'last_used_at' => $now,
        ]);

        return $token;
    }

    public function getUserByToken(string $token): ?array
    {
        $statement = $this->pdo->prepare(
            'SELECT users.*, booths.name AS booth_name, booths.lane_code, booths.zone
             FROM api_tokens
             JOIN users ON users.id = api_tokens.user_id
             LEFT JOIN booths ON booths.id = users.booth_id
             WHERE api_tokens.token_hash = :token_hash
             LIMIT 1'
        );
        $statement->execute(['token_hash' => hash('sha256', $token)]);
        $user = $statement->fetch();

        if ($user) {
            $this->pdo->prepare(
                'UPDATE api_tokens SET last_used_at = :used_at WHERE token_hash = :token_hash'
            )->execute([
                'used_at' => (new DateTimeImmutable())->format(DATE_ATOM),
                'token_hash' => hash('sha256', $token),
            ]);
        }

        return $user ?: null;
    }

    public function deleteToken(string $token): void
    {
        $this->pdo->prepare('DELETE FROM api_tokens WHERE token_hash = :token_hash')
            ->execute(['token_hash' => hash('sha256', $token)]);
    }

    public function allTariffs(): array
    {
        $rows = $this->pdo->query('SELECT * FROM tariffs ORDER BY priority ASC, id ASC')->fetchAll();

        return array_map(fn (array $row) => [
            'id' => (int) $row['id'],
            'label' => $row['label'],
            'description' => $row['description'],
            'price' => (int) $row['price'],
            'price_formatted' => Format::currency((int) $row['price']),
        ], $rows);
    }

    public function updateTariffs(array $items): void
    {
        $statement = $this->pdo->prepare(
            'UPDATE tariffs SET label = :label, description = :description, price = :price WHERE id = :id'
        );

        foreach ($items as $item) {
            $statement->execute([
                'id' => (int) ($item['id'] ?? 0),
                'label' => trim((string) ($item['label'] ?? '')),
                'description' => trim((string) ($item['description'] ?? '')),
                'price' => (int) ($item['price'] ?? 0),
            ]);
        }
    }

    public function adminDashboard(): array
    {
        $todayRevenue = (int) $this->pdo->query("SELECT COALESCE(SUM(amount), 0) FROM passages WHERE date(created_at) = date('now')")->fetchColumn();
        $yesterdayRevenue = (int) $this->pdo->query("SELECT COALESCE(SUM(amount), 0) FROM passages WHERE date(created_at) = date('now', '-1 day')")->fetchColumn();
        $todayPassages = (int) $this->pdo->query("SELECT COUNT(*) FROM passages WHERE date(created_at) = date('now')")->fetchColumn();
        $yesterdayPassages = (int) $this->pdo->query("SELECT COUNT(*) FROM passages WHERE date(created_at) = date('now', '-1 day')")->fetchColumn();
        $activeOperators = (int) $this->pdo->query("SELECT COUNT(*) FROM users WHERE role = 'operateur'")->fetchColumn();
        $activeSubscribers = (int) $this->pdo->query("SELECT COUNT(*) FROM subscribers WHERE status = 'Actif' AND expires_at >= datetime('now')")->fetchColumn();

        $metrics = [
            [
                'label' => 'Revenus du jour',
                'value' => Format::currency($todayRevenue),
                'change' => $this->change($todayRevenue, $yesterdayRevenue),
                'tone' => 'secondary',
            ],
            [
                'label' => 'Passages du jour',
                'value' => (string) $todayPassages,
                'change' => $this->change($todayPassages, $yesterdayPassages),
                'tone' => 'primary',
            ],
            [
                'label' => 'Operateurs mobilises',
                'value' => (string) $activeOperators,
                'change' => '+0,0%',
                'tone' => 'accent',
            ],
            [
                'label' => 'Abonnements actifs',
                'value' => (string) $activeSubscribers,
                'change' => '+4,6%',
                'tone' => 'success',
            ],
        ];

        $revenueTrend = $this->pdo->query(
            "SELECT strftime('%d/%m', created_at) AS label, COALESCE(SUM(amount), 0) AS amount
             FROM passages
             WHERE created_at >= datetime('now', '-6 day')
             GROUP BY date(created_at)
             ORDER BY date(created_at) ASC"
        )->fetchAll();

        $laneTraffic = $this->pdo->query(
            "SELECT booths.lane_code AS name,
                    (SELECT COUNT(*) FROM passages WHERE passages.booth_id = booths.id AND passages.created_at >= datetime('now', '-14 day')) AS passages,
                    (SELECT COUNT(*) FROM incidents WHERE incidents.booth_id = booths.id AND incidents.reported_at >= datetime('now', '-14 day')) AS incidents
             FROM booths
             ORDER BY booths.id"
        )->fetchAll();

        $paymentMix = $this->pdo->query(
            "SELECT payment_mode AS name, COUNT(*) AS value, SUM(amount) AS amount
             FROM passages
             GROUP BY payment_mode
             ORDER BY SUM(amount) DESC"
        )->fetchAll();

        $recentPassages = $this->passagesQuery('ORDER BY passages.created_at DESC LIMIT 8');

        $operators = $this->pdo->query(
            "SELECT users.id,
                    users.name,
                    booths.lane_code || ' - ' || booths.name AS booth,
                    CASE WHEN MAX(passages.created_at) >= datetime('now', '-18 hour') THEN 'Actif' ELSE 'Hors ligne' END AS status,
                    MIN(CASE WHEN date(passages.created_at) = date('now') THEN passages.created_at END) AS shift_start,
                    SUM(CASE WHEN date(passages.created_at) = date('now') THEN 1 ELSE 0 END) AS passages_today,
                    COALESCE(SUM(CASE WHEN date(passages.created_at) = date('now') THEN passages.amount ELSE 0 END), 0) AS revenue_today
             FROM users
             LEFT JOIN booths ON booths.id = users.booth_id
             LEFT JOIN passages ON passages.operator_id = users.id
             WHERE users.role = 'operateur'
             GROUP BY users.id
             ORDER BY users.name ASC"
        )->fetchAll();

        foreach ($operators as &$operator) {
            $operator['id'] = (int) $operator['id'];
            $operator['passages_today'] = (int) $operator['passages_today'];
            $operator['revenue_today'] = (int) $operator['revenue_today'];
        }

        $alerts = $this->pdo->query(
            "SELECT id, title, description, severity, reported_at AS time
             FROM incidents
             ORDER BY reported_at DESC
             LIMIT 4"
        )->fetchAll();

        return compact('metrics', 'revenueTrend', 'laneTraffic', 'paymentMix', 'recentPassages', 'operators', 'alerts');
    }

    public function operatorDashboard(int $userId): array
    {
        $user = $this->getUserById($userId);
        $booth = [
            'id' => (int) $user['booth_id'],
            'name' => $user['booth_name'],
            'lane_code' => $user['lane_code'],
            'zone' => $user['zone'],
            'status' => 'Ouverte',
        ];

        $statement = $this->pdo->prepare(
            "SELECT COUNT(*) AS passages,
                    COALESCE(SUM(amount), 0) AS collected,
                    MIN(created_at) AS start_at
             FROM passages
             WHERE operator_id = :operator_id
               AND date(created_at) = date('now')"
        );
        $statement->execute(['operator_id' => $userId]);
        $shift = $statement->fetch();

        $shiftData = [
            'start_at' => $shift['start_at'] ?: (new DateTimeImmutable('today 06:00'))->format(DATE_ATOM),
            'passages' => (int) $shift['passages'],
            'collected' => (int) $shift['collected'],
            'collected_formatted' => Format::currency((int) $shift['collected']),
            'target' => 180000,
            'target_formatted' => Format::currency(180000),
        ];

        $incidentStatement = $this->pdo->prepare(
            "SELECT incidents.id, incidents.title, incidents.description, incidents.severity, incidents.status, incidents.reported_at,
                    booths.lane_code || ' - ' || booths.name AS booth,
                    users.name AS operator
             FROM incidents
             LEFT JOIN booths ON booths.id = incidents.booth_id
             LEFT JOIN users ON users.id = incidents.operator_id
             WHERE incidents.booth_id = :booth_id
             ORDER BY incidents.reported_at DESC
             LIMIT 5"
        );
        $incidentStatement->execute(['booth_id' => $user['booth_id']]);

        return [
            'booth' => $booth,
            'shift' => $shiftData,
            'kpis' => [],
            'tariffs' => $this->allTariffs(),
            'recentPassages' => $this->recentPassagesForOperator($userId),
            'incidents' => $incidentStatement->fetchAll(),
        ];
    }

    public function allOperators(): array
    {
        $operators = $this->pdo->query(
            "SELECT users.id,
                    users.name,
                    users.email,
                    booths.lane_code || ' - ' || booths.name AS booth,
                    CASE WHEN MAX(passages.created_at) >= datetime('now', '-18 hour') THEN 'Actif' ELSE 'Hors ligne' END AS status,
                    MIN(CASE WHEN date(passages.created_at) = date('now') THEN passages.created_at END) AS shift_start,
                    SUM(CASE WHEN date(passages.created_at) = date('now') THEN 1 ELSE 0 END) AS passages_today,
                    COALESCE(SUM(CASE WHEN date(passages.created_at) = date('now') THEN passages.amount ELSE 0 END), 0) AS revenue_today
             FROM users
             LEFT JOIN booths ON booths.id = users.booth_id
             LEFT JOIN passages ON passages.operator_id = users.id
             WHERE users.role = 'operateur'
             GROUP BY users.id
             ORDER BY users.name ASC"
        )->fetchAll();

        foreach ($operators as &$operator) {
            $operator['id'] = (int) $operator['id'];
            $operator['passages_today'] = (int) $operator['passages_today'];
            $operator['revenue_today'] = (int) $operator['revenue_today'];
            $operator['recent_passages'] = $this->recentPassagesForOperator((int) $operator['id'], 5);
        }

        return $operators;
    }

    public function subscribers(): array
    {
        $items = $this->pdo->query('SELECT * FROM subscribers ORDER BY expires_at ASC')->fetchAll();
        $monthlyRevenue = (int) $this->pdo->query('SELECT COALESCE(SUM(monthly_fee), 0) FROM subscribers WHERE status = "Actif"')->fetchColumn();
        $critical = (int) $this->pdo->query("SELECT COUNT(*) FROM subscribers WHERE expires_at <= datetime('now', '+10 day')")->fetchColumn();
        $active = (int) $this->pdo->query("SELECT COUNT(*) FROM subscribers WHERE status = 'Actif'")->fetchColumn();
        $discount = (float) $this->pdo->query('SELECT COALESCE(AVG(discount_rate), 0) FROM subscribers')->fetchColumn();

        return [
            'summary' => [
                'active' => $active,
                'critical' => $critical,
                'monthly_revenue' => $monthlyRevenue,
                'monthly_revenue_formatted' => Format::currency($monthlyRevenue),
                'average_discount' => round($discount, 1),
            ],
            'items' => $items,
        ];
    }

    public function createSubscriber(array $payload): array
    {
        $company = trim((string) ($payload['company'] ?? ''));
        $contact = trim((string) ($payload['contact_name'] ?? ''));
        $plate = strtoupper(trim((string) ($payload['plate'] ?? '')));
        $plan = trim((string) ($payload['plan'] ?? 'Mensuel'));

        if ($company === '' || $contact === '' || $plate === '') {
            throw new \RuntimeException('Tous les champs de l abonnement sont requis.');
        }

        $months = match ($plan) {
            'Annuel' => 12,
            'Trimestriel' => 3,
            default => 1,
        };

        $fee = match ($plan) {
            'Annuel' => 210000,
            'Trimestriel' => 62000,
            default => 22000,
        };

        $discount = match ($plan) {
            'Annuel' => 22,
            'Trimestriel' => 12,
            default => 7,
        };

        $expiresAt = (new DateTimeImmutable())->add(new DateInterval("P{$months}M"))->format(DATE_ATOM);
        $now = (new DateTimeImmutable())->format(DATE_ATOM);

        $statement = $this->pdo->prepare(
            'INSERT INTO subscribers (company, contact_name, plate, plan, discount_rate, monthly_fee, expires_at, status, notes, created_at)
             VALUES (:company, :contact_name, :plate, :plan, :discount_rate, :monthly_fee, :expires_at, :status, :notes, :created_at)'
        );
        $statement->execute([
            'company' => $company,
            'contact_name' => $contact,
            'plate' => $plate,
            'plan' => $plan,
            'discount_rate' => $discount,
            'monthly_fee' => $fee,
            'expires_at' => $expiresAt,
            'status' => 'Actif',
            'notes' => 'Cree depuis l interface React.',
            'created_at' => $now,
        ]);

        return [
            'id' => (int) $this->pdo->lastInsertId(),
            'plate' => $plate,
            'company' => $company,
            'contact_name' => $contact,
            'plan' => $plan,
            'monthly_fee' => $fee,
            'expires_at' => $expiresAt,
        ];
    }

    public function renewSubscriber(int $id): array
    {
        $statement = $this->pdo->prepare('SELECT * FROM subscribers WHERE id = :id LIMIT 1');
        $statement->execute(['id' => $id]);
        $subscriber = $statement->fetch();

        if (!$subscriber) {
            throw new \RuntimeException('Abonne introuvable.');
        }

        $months = match ($subscriber['plan']) {
            'Annuel' => 12,
            'Trimestriel' => 3,
            default => 1,
        };

        $baseDate = new DateTimeImmutable(max($subscriber['expires_at'], (new DateTimeImmutable())->format(DATE_ATOM)));
        $expiresAt = $baseDate->add(new DateInterval("P{$months}M"))->format(DATE_ATOM);

        $this->pdo->prepare('UPDATE subscribers SET expires_at = :expires_at, status = :status WHERE id = :id')
            ->execute([
                'expires_at' => $expiresAt,
                'status' => 'Actif',
                'id' => $id,
            ]);

        return [
            'id' => $id,
            'expires_at' => $expiresAt,
        ];
    }

    public function history(): array
    {
        $items = $this->passagesQuery('ORDER BY passages.created_at DESC LIMIT 40');
        $totalRevenue = (int) $this->pdo->query('SELECT COALESCE(SUM(amount), 0) FROM passages')->fetchColumn();
        $totalPassages = (int) $this->pdo->query('SELECT COUNT(*) FROM passages')->fetchColumn();
        $paymentModes = (int) $this->pdo->query('SELECT COUNT(DISTINCT payment_mode) FROM passages')->fetchColumn();

        return [
            'summary' => [
                'total_revenue' => $totalRevenue,
                'total_revenue_formatted' => Format::currency($totalRevenue),
                'total_passages' => $totalPassages,
                'payment_modes' => $paymentModes,
            ],
            'items' => $items,
        ];
    }

    public function reports(): array
    {
        $monthlyRevenue = $this->pdo->query(
            "SELECT strftime('%m/%Y', created_at) AS label, COALESCE(SUM(amount), 0) AS amount
             FROM passages
             WHERE created_at >= datetime('now', '-5 months')
             GROUP BY strftime('%Y-%m', created_at)
             ORDER BY strftime('%Y-%m', created_at) ASC"
        )->fetchAll();

        $vehicleBreakdown = $this->pdo->query(
            "SELECT tariffs.label AS name,
                    COUNT(passages.id) AS value,
                    'Tarif moyen ' || tariffs.price || ' FCFA' AS description
             FROM tariffs
             LEFT JOIN passages ON passages.tariff_id = tariffs.id
             GROUP BY tariffs.id
             ORDER BY COUNT(passages.id) DESC"
        )->fetchAll();

        $boothPerformance = $this->pdo->query(
            "SELECT booths.lane_code AS name,
                    (SELECT COUNT(*) FROM passages WHERE passages.booth_id = booths.id) AS passages,
                    (SELECT COUNT(*) FROM incidents WHERE incidents.booth_id = booths.id) AS incidents
             FROM booths
             ORDER BY booths.id"
        )->fetchAll();

        $paymentMix = $this->pdo->query(
            "SELECT payment_mode AS name, COUNT(*) AS value, SUM(amount) AS amount
             FROM passages
             GROUP BY payment_mode
             ORDER BY COUNT(*) DESC"
        )->fetchAll();

        return compact('monthlyRevenue', 'vehicleBreakdown', 'boothPerformance', 'paymentMix');
    }

    public function settings(): array
    {
        $rows = $this->pdo->query('SELECT key, value FROM settings')->fetchAll();
        $result = [];

        foreach ($rows as $row) {
            $result[$row['key']] = $row['value'];
        }

        return $result;
    }

    public function updateSettings(array $payload): array
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO settings (key, value) VALUES (:key, :value)
             ON CONFLICT(key) DO UPDATE SET value = excluded.value'
        );

        foreach ($payload as $key => $value) {
            $statement->execute([
                'key' => (string) $key,
                'value' => (string) $value,
            ]);
        }

        return $this->settings();
    }

    public function incidents(): array
    {
        return $this->pdo->query(
            "SELECT incidents.id, incidents.title, incidents.description, incidents.severity, incidents.status, incidents.reported_at,
                    booths.lane_code || ' - ' || booths.name AS booth,
                    users.name AS operator
             FROM incidents
             LEFT JOIN booths ON booths.id = incidents.booth_id
             LEFT JOIN users ON users.id = incidents.operator_id
             ORDER BY incidents.reported_at DESC"
        )->fetchAll();
    }

    public function createIncident(array $payload, int $operatorId): array
    {
        $operator = $this->getUserById($operatorId);
        $title = trim((string) ($payload['title'] ?? ''));
        $description = trim((string) ($payload['description'] ?? ''));
        $severity = trim((string) ($payload['severity'] ?? 'medium'));

        if ($title === '' || $description === '') {
            throw new \RuntimeException('Titre et description obligatoires.');
        }

        $reportedAt = (new DateTimeImmutable())->format(DATE_ATOM);
        $statement = $this->pdo->prepare(
            'INSERT INTO incidents (booth_id, operator_id, title, description, severity, status, reported_at)
             VALUES (:booth_id, :operator_id, :title, :description, :severity, :status, :reported_at)'
        );
        $statement->execute([
            'booth_id' => $operator['booth_id'],
            'operator_id' => $operatorId,
            'title' => $title,
            'description' => $description,
            'severity' => $severity,
            'status' => 'Ouvert',
            'reported_at' => $reportedAt,
        ]);

        return [
            'id' => (int) $this->pdo->lastInsertId(),
            'title' => $title,
            'description' => $description,
            'severity' => $severity,
            'status' => 'Ouvert',
            'reported_at' => $reportedAt,
        ];
    }

    public function createPassage(array $payload, int $operatorId): array
    {
        $operator = $this->getUserById($operatorId);
        $plate = strtoupper(trim((string) ($payload['plate'] ?? '')));
        $tariffId = (int) ($payload['tariff_id'] ?? 0);
        $paymentMode = trim((string) ($payload['payment_mode'] ?? 'Carte'));

        if ($plate === '' || $tariffId === 0) {
            throw new \RuntimeException('Plaque et categorie obligatoires.');
        }

        $tariffStatement = $this->pdo->prepare('SELECT * FROM tariffs WHERE id = :id LIMIT 1');
        $tariffStatement->execute(['id' => $tariffId]);
        $tariff = $tariffStatement->fetch();

        if (!$tariff) {
            throw new \RuntimeException('Tarif introuvable.');
        }

        $vehicleStatement = $this->pdo->prepare('SELECT * FROM vehicles WHERE plate = :plate LIMIT 1');
        $vehicleStatement->execute(['plate' => $plate]);
        $vehicle = $vehicleStatement->fetch();

        if (!$vehicle) {
            $this->pdo->prepare(
                'INSERT INTO vehicles (plate, tariff_id, brand, model, color)
                 VALUES (:plate, :tariff_id, :brand, :model, :color)'
            )->execute([
                'plate' => $plate,
                'tariff_id' => $tariffId,
                'brand' => 'Inconnu',
                'model' => 'Transit',
                'color' => 'Gris',
            ]);
            $vehicleId = (int) $this->pdo->lastInsertId();
        } else {
            $vehicleId = (int) $vehicle['id'];
            $this->pdo->prepare('UPDATE vehicles SET tariff_id = :tariff_id WHERE id = :id')
                ->execute([
                    'tariff_id' => $tariffId,
                    'id' => $vehicleId,
                ]);
        }

        $amount = (int) $tariff['price'];
        $subscriber = $this->pdo->prepare(
            "SELECT * FROM subscribers
             WHERE plate = :plate AND status = 'Actif' AND expires_at >= datetime('now')
             LIMIT 1"
        );
        $subscriber->execute(['plate' => $plate]);
        $subscription = $subscriber->fetch();

        if ($paymentMode === 'Abonnement' && $subscription) {
            $amount = max(0, (int) round($amount * (1 - ((int) $subscription['discount_rate'] / 100))));
        }

        $createdAt = (new DateTimeImmutable())->format(DATE_ATOM);
        $statement = $this->pdo->prepare(
            'INSERT INTO passages (vehicle_id, booth_id, operator_id, tariff_id, payment_mode, amount, status, source, created_at)
             VALUES (:vehicle_id, :booth_id, :operator_id, :tariff_id, :payment_mode, :amount, :status, :source, :created_at)'
        );
        $statement->execute([
            'vehicle_id' => $vehicleId,
            'booth_id' => $operator['booth_id'],
            'operator_id' => $operatorId,
            'tariff_id' => $tariffId,
            'payment_mode' => $paymentMode,
            'amount' => $amount,
            'status' => 'Valide',
            'source' => 'manual',
            'created_at' => $createdAt,
        ]);

        return [
            'id' => (int) $this->pdo->lastInsertId(),
            'plate' => $plate,
            'amount' => $amount,
            'created_at' => $createdAt,
        ];
    }

    private function getUserById(int $id): array
    {
        $statement = $this->pdo->prepare(
            'SELECT users.*, booths.name AS booth_name, booths.lane_code, booths.zone
             FROM users
             LEFT JOIN booths ON booths.id = users.booth_id
             WHERE users.id = :id
             LIMIT 1'
        );
        $statement->execute(['id' => $id]);
        $user = $statement->fetch();

        if (!$user) {
            throw new \RuntimeException('Utilisateur introuvable.');
        }

        return $user;
    }

    private function passagesQuery(string $suffix): array
    {
        return $this->pdo->query(
            "SELECT passages.id, passages.amount, passages.payment_mode, passages.status, passages.created_at,
                    vehicles.plate,
                    tariffs.label AS tariff_label,
                    booths.lane_code || ' - ' || booths.name AS booth,
                    users.name AS operator
             FROM passages
             JOIN vehicles ON vehicles.id = passages.vehicle_id
             JOIN tariffs ON tariffs.id = passages.tariff_id
             JOIN booths ON booths.id = passages.booth_id
             JOIN users ON users.id = passages.operator_id
             {$suffix}"
        )->fetchAll();
    }

    private function recentPassagesForOperator(int $operatorId, int $limit = 8): array
    {
        $statement = $this->pdo->prepare(
            "SELECT passages.id, passages.amount, passages.payment_mode, passages.status, passages.created_at,
                    vehicles.plate,
                    tariffs.label AS tariff_label,
                    booths.lane_code || ' - ' || booths.name AS booth
             FROM passages
             JOIN vehicles ON vehicles.id = passages.vehicle_id
             JOIN tariffs ON tariffs.id = passages.tariff_id
             JOIN booths ON booths.id = passages.booth_id
             WHERE passages.operator_id = :operator_id
             ORDER BY passages.created_at DESC
             LIMIT {$limit}"
        );
        $statement->execute(['operator_id' => $operatorId]);

        return $statement->fetchAll();
    }

    private function change(int $current, int $previous): string
    {
        if ($previous === 0) {
            return '+100%';
        }

        return Format::percent((($current - $previous) / $previous) * 100);
    }
}
