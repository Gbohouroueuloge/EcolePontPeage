<?php

declare(strict_types=1);

function seed(PDO $pdo): void
{
    $pdo->beginTransaction();

    $booths = [
        ['Cabine Nord 01', 'Nord', 'N-01'],
        ['Cabine Nord 02', 'Nord', 'N-02'],
        ['Cabine Sud 01', 'Sud', 'S-01'],
        ['Cabine Sud 02', 'Sud', 'S-02'],
        ['Cabine Est 01', 'Est', 'E-01'],
        ['Cabine Ouest 01', 'Ouest', 'O-01'],
        ['Cabine Express 01', 'Express', 'X-01'],
        ['Cabine Express 02', 'Express', 'X-02'],
    ];

    $boothStatement = $pdo->prepare('INSERT INTO booths (name, zone, lane_code, status) VALUES (?, ?, ?, ?)');
    foreach ($booths as $booth) {
        $boothStatement->execute([$booth[0], $booth[1], $booth[2], 'Ouverte']);
    }

    $now = (new DateTimeImmutable())->format(DATE_ATOM);
    $users = [
        ['Aminata Konan', 'admin@pontpeage.local', 'admin', null, 'admin123'],
        ['Jean Amani', 'agent@pontpeage.local', 'operateur', 1, 'operator123'],
        ['Nadia Koffi', 'nadia@pontpeage.local', 'operateur', 2, 'operator123'],
        ['Marc Yao', 'marc@pontpeage.local', 'operateur', 3, 'operator123'],
        ['Elodie Nguessan', 'elodie@pontpeage.local', 'operateur', 4, 'operator123'],
    ];

    $userStatement = $pdo->prepare(
        'INSERT INTO users (name, email, password, role, booth_id, created_at) VALUES (?, ?, ?, ?, ?, ?)'
    );

    foreach ($users as $user) {
        $userStatement->execute([
            $user[0],
            $user[1],
            password_hash($user[4], PASSWORD_BCRYPT),
            $user[2],
            $user[3],
            $now,
        ]);
    }

    $tariffs = [
        ['Moto', 'MOTO', 'Deux roues legeres', 800, 'amber', 1],
        ['Voiture', 'CAR', 'Vehicule particulier standard', 1500, 'sky', 2],
        ['SUV / Van', 'SUV', 'Vehicule familial ou utilitaire leger', 2400, 'indigo', 3],
        ['Poids moyen', 'MID', 'Transport mixte ou petit camion', 3900, 'emerald', 4],
        ['Poids lourd', 'HVY', 'Transport longue distance et fret', 5600, 'rose', 5],
    ];

    $tariffStatement = $pdo->prepare(
        'INSERT INTO tariffs (label, code, description, price, accent, priority) VALUES (?, ?, ?, ?, ?, ?)'
    );
    foreach ($tariffs as $tariff) {
        $tariffStatement->execute($tariff);
    }

    $plates = [
        'AB-123-CD', 'BE-445-RT', 'CI-778-ZA', 'DK-123-XY', 'LM-909-FR', 'AA-777-KK', 'TT-621-JH',
        'CI-010-TR', 'GH-981-KA', 'TR-121-OO', 'LV-441-PA', 'SM-800-TA', 'HT-743-DB', 'CE-120-ZX',
        'UU-975-LM', 'RE-336-AA', 'PO-118-LE', 'KB-995-TR', 'FG-440-JJ', 'MM-100-RT',
    ];

    $vehicleStatement = $pdo->prepare(
        'INSERT INTO vehicles (plate, tariff_id, brand, model, color) VALUES (?, ?, ?, ?, ?)'
    );

    $vehicleIds = [];
    foreach ($plates as $index => $plate) {
        $tariffId = ($index % 5) + 1;
        $vehicleStatement->execute([$plate, $tariffId, 'Marque ' . ($index + 1), 'Modele ' . ($index + 1), 'Bleu']);
        $vehicleIds[] = (int) $pdo->lastInsertId();
    }

    $subscriberStatement = $pdo->prepare(
        'INSERT INTO subscribers (company, contact_name, plate, plan, discount_rate, monthly_fee, expires_at, status, notes, created_at)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
    );

    $subscriberData = [
        ['Logisud', 'Mireille Ahou', 'AB-123-CD', 'Mensuel', 8, 22000, '+14 days'],
        ['TransAfrica', 'Roger Seka', 'BE-445-RT', 'Trimestriel', 14, 62000, '+7 days'],
        ['Cacao Freight', 'Linda Assi', 'CI-778-ZA', 'Annuel', 22, 210000, '+120 days'],
        ['Atlas Transit', 'Boris Kanga', 'DK-123-XY', 'Mensuel', 9, 22000, '+4 days'],
        ['Rive Ouest', 'Aicha Toure', 'LM-909-FR', 'Annuel', 20, 210000, '+220 days'],
    ];

    foreach ($subscriberData as $subscriber) {
        $expiresAt = (new DateTimeImmutable())->modify($subscriber[6])->format(DATE_ATOM);
        $subscriberStatement->execute([
            $subscriber[0],
            $subscriber[1],
            $subscriber[2],
            $subscriber[3],
            $subscriber[4],
            $subscriber[5],
            $expiresAt,
            'Actif',
            'Contrat demonstration',
            $now,
        ]);
    }

    $paymentModes = ['Carte', 'Especes', 'Mobile Money', 'Abonnement'];
    $passageStatement = $pdo->prepare(
        'INSERT INTO passages (vehicle_id, booth_id, operator_id, tariff_id, payment_mode, amount, status, source, created_at)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
    );

    mt_srand(42);
    for ($i = 0; $i < 180; $i++) {
        $vehicleId = $vehicleIds[array_rand($vehicleIds)];
        $tariffId = (($vehicleId - 1) % 5) + 1;
        $boothId = (($i % 4) + 1);
        $operatorId = (($i % 4) + 2);
        $mode = $paymentModes[array_rand($paymentModes)];
        $basePrice = $tariffs[$tariffId - 1][3];
        $amount = $mode === 'Abonnement' ? (int) round($basePrice * 0.85) : $basePrice;
        $daysAgo = 170 - $i;
        $hour = str_pad((string) (6 + ($i % 12)), 2, '0', STR_PAD_LEFT);
        $createdAt = (new DateTimeImmutable("-{$daysAgo} days"))->setTime((int) $hour, ($i * 7) % 60)->format(DATE_ATOM);

        $passageStatement->execute([
            $vehicleId,
            $boothId,
            $operatorId,
            $tariffId,
            $mode,
            $amount,
            'Valide',
            'seed',
            $createdAt,
        ]);
    }

    $incidentStatement = $pdo->prepare(
        'INSERT INTO incidents (booth_id, operator_id, title, description, severity, status, reported_at)
         VALUES (?, ?, ?, ?, ?, ?, ?)'
    );

    $incidents = [
        [1, 2, 'Barriere lente', 'Temps d ouverture superieur a la normale sur la voie Nord.', 'medium', 'Ouvert', '-2 hours'],
        [2, 3, 'Capteur de lecture instable', 'Lecture de plaques a verifier sur deux vehicules consecutifs.', 'high', 'Ouvert', '-5 hours'],
        [3, 4, 'File d attente inhabituelle', 'Flux dense en sortie Sud apres pic de circulation.', 'low', 'Surveillance', '-1 day'],
        [4, 5, 'TPE redemarre', 'Terminal carte redemarre puis revient en ligne.', 'medium', 'Ouvert', '-2 days'],
    ];

    foreach ($incidents as $incident) {
        $incidentStatement->execute([
            $incident[0],
            $incident[1],
            $incident[2],
            $incident[3],
            $incident[4],
            $incident[5],
            (new DateTimeImmutable($incident[6]))->format(DATE_ATOM),
        ]);
    }

    $settings = [
        'site_name' => 'Pont Peage Atlantique',
        'currency' => 'XOF',
        'timezone' => 'UTC',
        'dashboard_mode' => 'live',
        'alert_threshold' => '3',
        'refresh_interval' => '30',
    ];

    $settingStatement = $pdo->prepare('INSERT INTO settings (key, value) VALUES (?, ?)');
    foreach ($settings as $key => $value) {
        $settingStatement->execute([$key, $value]);
    }

    $pdo->commit();
}
