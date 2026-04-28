-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : mar. 28 avr. 2026 à 21:04
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `pont_peage2`
--

-- --------------------------------------------------------

--
-- Structure de la table `agent`
--

CREATE TABLE `agent` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `guichet_id` int(10) UNSIGNED DEFAULT NULL,
  `debut` datetime DEFAULT NULL,
  `fin` datetime DEFAULT NULL,
  `date_assignation` datetime DEFAULT NULL,
  `created_at` timestamp(3) NOT NULL DEFAULT current_timestamp(3),
  `updated_at` timestamp(3) NOT NULL DEFAULT current_timestamp(3) ON UPDATE current_timestamp(3)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `agent`
--

INSERT INTO `agent` (`id`, `user_id`, `guichet_id`, `debut`, `fin`, `date_assignation`, `created_at`, `updated_at`) VALUES
(1, 2, 1, '2026-04-28 19:02:32', NULL, '2026-04-26 19:16:05', '2026-04-25 01:30:18.500', '2026-04-28 19:02:32.322');

-- --------------------------------------------------------

--
-- Structure de la table `guichet`
--

CREATE TABLE `guichet` (
  `id` int(10) UNSIGNED NOT NULL,
  `slug` varchar(50) NOT NULL,
  `emplacement` varchar(150) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp(3) NOT NULL DEFAULT current_timestamp(3),
  `updated_at` timestamp(3) NOT NULL DEFAULT current_timestamp(3) ON UPDATE current_timestamp(3)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `guichet`
--

INSERT INTO `guichet` (`id`, `slug`, `emplacement`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'voie_01_nord', 'Nord', 1, '2026-04-25 01:30:18.478', '2026-04-26 19:44:07.849'),
(2, 'voie_02_sud', 'Sud', 1, '2026-04-25 01:30:18.483', '2026-04-25 21:46:24.529'),
(3, 'voie_03_ouest', 'Ouest', 1, '2026-04-25 01:30:18.485', '2026-04-26 18:53:10.382'),
(4, 'voie_04_est', 'Est', 1, '2026-04-25 01:30:18.490', '2026-04-25 21:46:24.545'),
(5, 'voie_05_nord_est', 'Nord-Est', 1, '2026-04-25 01:30:18.492', '2026-04-26 18:50:27.295'),
(6, 'voie_06_nord_ouest', 'Nord-Ouest', 1, '2026-04-25 01:30:18.494', '2026-04-26 19:44:38.626'),
(7, 'voie_07_sud_est', 'Sud-Est', 1, '2026-04-25 01:30:18.496', '2026-04-25 21:46:24.571'),
(8, 'voie_08_sud_ouest', 'Sud-Ouest', 1, '2026-04-25 01:30:18.498', '2026-04-26 18:54:45.399');

-- --------------------------------------------------------

--
-- Structure de la table `incident`
--

CREATE TABLE `incident` (
  `id` int(10) UNSIGNED NOT NULL,
  `vehicule_id` int(10) UNSIGNED NOT NULL,
  `guichet_id` int(10) UNSIGNED NOT NULL,
  `type` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `url_image` varchar(255) DEFAULT NULL,
  `created_at` timestamp(3) NOT NULL DEFAULT current_timestamp(3),
  `updated_at` timestamp(3) NOT NULL DEFAULT current_timestamp(3) ON UPDATE current_timestamp(3)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `incident`
--

INSERT INTO `incident` (`id`, `vehicule_id`, `guichet_id`, `type`, `description`, `url_image`, `created_at`, `updated_at`) VALUES
(1, 25, 1, 'Panne', 'Il y\'a une voiture en panne', '/uploads/incidents/incident_69ee68224643a.png', '2026-04-26 19:31:46.291', '2026-04-26 19:31:46.291'),
(2, 7, 1, 'Urgence', NULL, '/uploads/incidents/incident_69ef6bad597a1.jpeg', '2026-04-27 13:59:09.368', '2026-04-27 13:59:09.368');

-- --------------------------------------------------------

--
-- Structure de la table `paiement`
--

CREATE TABLE `paiement` (
  `id` int(10) UNSIGNED NOT NULL,
  `vehicule_id` int(10) UNSIGNED NOT NULL,
  `guichet_id` int(10) UNSIGNED NOT NULL,
  `mode_paiement` varchar(50) NOT NULL,
  `montant` decimal(10,2) NOT NULL DEFAULT 0.00,
  `is_valide` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp(3) NOT NULL DEFAULT current_timestamp(3),
  `updated_at` timestamp(3) NOT NULL DEFAULT current_timestamp(3) ON UPDATE current_timestamp(3)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `paiement`
--

INSERT INTO `paiement` (`id`, `vehicule_id`, `guichet_id`, `mode_paiement`, `montant`, `is_valide`, `created_at`, `updated_at`) VALUES
(1, 2, 3, 'Abonnement', 4548.00, 1, '2026-04-25 01:30:18.558', '2026-04-25 01:30:18.558'),
(2, 21, 3, 'Abonnement', 4057.00, 1, '2026-04-25 01:30:18.560', '2026-04-25 01:30:18.560'),
(3, 13, 4, 'Espece', 3416.00, 1, '2026-04-25 01:30:18.562', '2026-04-25 01:30:18.562'),
(4, 18, 1, 'Abonnement', 3713.00, 1, '2026-04-25 01:30:18.567', '2026-04-25 01:30:18.567'),
(5, 25, 5, 'Carte', 3005.00, 1, '2026-04-25 01:30:18.569', '2026-04-25 01:30:18.569'),
(6, 10, 8, 'Carte', 3530.00, 1, '2026-04-25 01:30:18.571', '2026-04-25 01:30:18.571'),
(7, 6, 5, 'Espece', 3316.00, 1, '2026-04-25 01:30:18.574', '2026-04-25 01:30:18.574'),
(8, 16, 1, 'Carte', 4078.00, 1, '2026-04-25 01:30:18.576', '2026-04-25 01:30:18.576'),
(9, 23, 5, 'Carte', 2095.00, 1, '2026-04-25 01:30:18.578', '2026-04-25 01:30:18.578'),
(10, 14, 4, 'Mobile Money', 5164.00, 1, '2026-04-25 01:30:18.580', '2026-04-25 01:30:18.580'),
(11, 6, 8, 'Mobile Money', 2759.00, 1, '2026-04-25 01:30:18.582', '2026-04-25 01:30:18.582'),
(12, 4, 4, 'Carte', 1800.00, 1, '2026-04-25 01:30:18.584', '2026-04-25 01:30:18.584'),
(13, 22, 7, 'Mobile Money', 2133.00, 1, '2026-04-25 01:30:18.585', '2026-04-25 01:30:18.585'),
(14, 20, 4, 'Mobile Money', 3779.00, 1, '2026-04-25 01:30:18.587', '2026-04-25 01:30:18.587'),
(15, 10, 5, 'Abonnement', 3313.00, 1, '2026-04-25 01:30:18.588', '2026-04-25 01:30:18.588'),
(16, 11, 7, 'Carte', 5420.00, 1, '2026-04-25 01:30:18.590', '2026-04-25 01:30:18.590'),
(17, 12, 6, 'Mobile Money', 4706.00, 1, '2026-04-25 01:30:18.592', '2026-04-25 01:30:18.592'),
(18, 2, 2, 'Mobile Money', 3069.00, 1, '2026-04-25 01:30:18.594', '2026-04-25 01:30:18.594'),
(19, 6, 8, 'Mobile Money', 3477.00, 1, '2026-04-25 01:30:18.595', '2026-04-25 01:30:18.595'),
(20, 21, 1, 'Abonnement', 3192.00, 1, '2026-04-25 01:30:18.597', '2026-04-25 01:30:18.597'),
(21, 2, 3, 'Carte', 4010.00, 1, '2026-04-25 01:30:18.599', '2026-04-25 01:30:18.599'),
(22, 17, 6, 'Mobile Money', 1489.00, 1, '2026-04-25 01:30:18.601', '2026-04-25 01:30:18.601'),
(23, 7, 1, 'Espece', 3905.00, 1, '2026-04-25 01:30:18.603', '2026-04-25 01:30:18.603'),
(24, 25, 1, 'Carte', 3051.00, 1, '2026-04-25 01:30:18.605', '2026-04-25 01:30:18.605'),
(25, 2, 4, 'Espece', 524.00, 1, '2026-04-25 01:30:18.607', '2026-04-25 01:30:18.607'),
(26, 1, 6, 'Mobile Money', 670.00, 1, '2026-04-25 01:30:18.609', '2026-04-25 01:30:18.609'),
(27, 18, 3, 'Abonnement', 4252.00, 1, '2026-04-25 01:30:18.611', '2026-04-25 01:30:18.611'),
(28, 9, 2, 'Espece', 3072.00, 1, '2026-04-25 01:30:18.612', '2026-04-25 01:30:18.612'),
(29, 2, 8, 'Carte', 1941.00, 1, '2026-04-25 01:30:18.614', '2026-04-25 01:30:18.614'),
(30, 9, 3, 'Carte', 3913.00, 1, '2026-04-25 01:30:18.616', '2026-04-25 01:30:18.616'),
(31, 23, 5, 'Espece', 652.00, 1, '2026-04-25 01:30:18.618', '2026-04-25 01:30:18.618'),
(32, 9, 7, 'Carte', 1843.00, 1, '2026-04-25 01:30:18.620', '2026-04-25 01:30:18.620'),
(33, 8, 4, 'Espece', 5144.00, 1, '2026-04-25 01:30:18.622', '2026-04-25 01:30:18.622'),
(34, 17, 3, 'Carte', 2273.00, 1, '2026-04-25 01:30:18.624', '2026-04-25 01:30:18.624'),
(35, 14, 7, 'Carte', 1558.00, 1, '2026-04-25 01:30:18.625', '2026-04-25 01:30:18.625');

-- --------------------------------------------------------

--
-- Structure de la table `typevehicule`
--

CREATE TABLE `typevehicule` (
  `id` int(10) UNSIGNED NOT NULL,
  `libelle` varchar(150) NOT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp(3) NOT NULL DEFAULT current_timestamp(3),
  `updated_at` timestamp(3) NOT NULL DEFAULT current_timestamp(3) ON UPDATE current_timestamp(3)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `typevehicule`
--

INSERT INTO `typevehicule` (`id`, `libelle`, `price`, `created_at`, `updated_at`) VALUES
(1, 'Moto', 1000.00, '2026-04-25 01:29:53.561', '2026-04-25 21:14:03.576'),
(2, 'Voiture', 500.00, '2026-04-25 01:29:53.561', '2026-04-25 01:29:53.561'),
(3, 'Van/SUV', 500.00, '2026-04-25 01:29:53.561', '2026-04-25 01:29:53.561'),
(4, 'Poids Moyen', 1000.00, '2026-04-25 01:29:53.561', '2026-04-25 01:29:53.561'),
(5, 'Poids Lourd', 1500.00, '2026-04-25 01:29:53.561', '2026-04-25 01:29:53.561');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` text NOT NULL,
  `role` enum('operateur','admin') NOT NULL DEFAULT 'operateur',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `last_login_at` timestamp(3) NOT NULL DEFAULT current_timestamp(3) ON UPDATE current_timestamp(3),
  `created_at` timestamp(3) NOT NULL DEFAULT current_timestamp(3),
  `updated_at` timestamp(3) NOT NULL DEFAULT current_timestamp(3) ON UPDATE current_timestamp(3)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `is_active`, `last_login_at`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin@peage.com', '$2y$10$LMFl/NAeo9hVBtbbP8hylemNBA9aOY.zVR6jmBqDyJJCSjzcZR1Wy', 'admin', 1, '2026-04-25 01:30:18.474', '2026-04-25 01:30:18.474', '2026-04-25 01:30:18.474'),
(2, 'Alex', 'alex@gmail.com', '$2y$10$QRipLOqT2HedsSULpQ22IOJsrytqrFbTlLOnQl6Qy1OBuSWiP5CUC', 'operateur', 1, '2026-04-25 01:30:18.476', '2026-04-25 01:30:18.476', '2026-04-25 01:30:18.476');

-- --------------------------------------------------------

--
-- Structure de la table `vehicule`
--

CREATE TABLE `vehicule` (
  `id` int(10) UNSIGNED NOT NULL,
  `immatriculation` varchar(50) NOT NULL,
  `type_vehicule_id` int(10) UNSIGNED NOT NULL,
  `marque` varchar(50) DEFAULT NULL,
  `modele` varchar(50) DEFAULT NULL,
  `couleur` varchar(50) DEFAULT NULL,
  `created_at` timestamp(3) NOT NULL DEFAULT current_timestamp(3),
  `updated_at` timestamp(3) NOT NULL DEFAULT current_timestamp(3) ON UPDATE current_timestamp(3)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `vehicule`
--

INSERT INTO `vehicule` (`id`, `immatriculation`, `type_vehicule_id`, `marque`, `modele`, `couleur`, `created_at`, `updated_at`) VALUES
(1, 'nh-253-nt', 4, 'Joly S.A.', 'S.A.R.L.', 'Gueules', '2026-04-25 01:30:18.503', '2026-04-25 01:30:18.503'),
(2, 'du-331-ht', 2, 'Gillet', 'SAS', 'Pelure d\'oignon', '2026-04-25 01:30:18.505', '2026-04-25 01:30:18.505'),
(3, 'dh-810-hl', 4, 'Techer', 'et Fils', 'Lapis-lazuli', '2026-04-25 01:30:18.507', '2026-04-25 01:30:18.507'),
(4, 'tk-136-kx', 3, 'Hernandez', 'SAS', 'Vert tilleul', '2026-04-25 01:30:18.509', '2026-04-25 01:30:18.509'),
(5, 'hw-310-nm', 4, 'Lelievre S.A.', 'et Fils', 'Gris souris', '2026-04-25 01:30:18.511', '2026-04-25 01:30:18.511'),
(6, 'cu-402-ow', 2, 'Blin', 'SA', 'Bleu céleste', '2026-04-25 01:30:18.513', '2026-04-25 01:30:18.513'),
(7, 'gl-827-fk', 4, 'Chevallier', 'S.A.R.L.', 'Rouge indien', '2026-04-25 01:30:18.515', '2026-04-25 01:30:18.515'),
(8, 'lp-518-yr', 5, 'Lecoq SA', 'SARL', 'Grenadine', '2026-04-25 01:30:18.518', '2026-04-25 01:30:18.518'),
(9, 'qx-517-my', 1, 'Meyer S.A.', 'S.A.S.', 'Or (couleur)', '2026-04-25 01:30:18.520', '2026-04-25 01:30:18.520'),
(10, 'jf-609-ib', 1, 'Gauthier', 'S.A.R.L.', 'Vert printemps', '2026-04-25 01:30:18.522', '2026-04-25 01:30:18.522'),
(11, 'uc-146-jr', 3, 'Tanguy Pelletier S.A.S.', 'SARL', 'Pourpre (héraldique)', '2026-04-25 01:30:18.524', '2026-04-25 01:30:18.524'),
(12, 'sa-333-ha', 4, 'Lenoir Cousin S.A.', 'S.A.S.', 'Tourterelle', '2026-04-25 01:30:18.526', '2026-04-25 01:30:18.526'),
(13, 'ri-925-jm', 1, 'Roger', 'SAS', 'Maïs', '2026-04-25 01:30:18.528', '2026-04-25 01:30:18.528'),
(14, 'kz-393-oq', 3, 'Hebert', 'S.A.S.', 'Pelure d\'oignon', '2026-04-25 01:30:18.531', '2026-04-25 01:30:18.531'),
(15, 'gi-493-ri', 5, 'Marty', 'SA', 'Bleu charrette', '2026-04-25 01:30:18.532', '2026-04-25 01:30:18.532'),
(16, 'jo-276-pv', 3, 'Rousseau', 'S.A.S.', 'Vert épinard', '2026-04-25 01:30:18.534', '2026-04-25 01:30:18.534'),
(17, 'gm-766-yv', 5, 'Dumas Remy SA', 'S.A.R.L.', 'Turquoise', '2026-04-25 01:30:18.537', '2026-04-25 01:30:18.537'),
(18, 'do-042-cs', 1, 'Fleury', 'S.A.', 'Carmin', '2026-04-25 01:30:18.540', '2026-04-25 01:30:18.540'),
(19, 'tc-234-dg', 1, 'Pons Wagner S.A.R.L.', 'S.A.', 'Bronze', '2026-04-25 01:30:18.542', '2026-04-25 01:30:18.542'),
(20, 'km-362-fn', 5, 'Remy Denis SAS', 'S.A.R.L.', 'Indigo', '2026-04-25 01:30:18.544', '2026-04-25 01:30:18.544'),
(21, 'no-105-eb', 5, 'Durand', 'et Fils', 'Chartreuse', '2026-04-25 01:30:18.546', '2026-04-25 01:30:18.546'),
(22, 'qw-177-ct', 5, 'Toussaint', 'S.A.R.L.', 'Vert printemps', '2026-04-25 01:30:18.548', '2026-04-25 01:30:18.548'),
(23, 'ri-719-zs', 2, 'Martin', 'S.A.R.L.', 'Moutarde', '2026-04-25 01:30:18.551', '2026-04-25 01:30:18.551'),
(24, 'wa-520-ba', 4, 'Boucher S.A.R.L.', 'SARL', 'Fuchsia', '2026-04-25 01:30:18.553', '2026-04-25 01:30:18.553'),
(25, 'os-387-cl', 4, 'Jacquet Maurice S.A.', 'S.A.S.', 'Jaune d\'or', '2026-04-25 01:30:18.555', '2026-04-25 01:30:18.555');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `agent`
--
ALTER TABLE `agent`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD KEY `fk_agent_guichet` (`guichet_id`);

--
-- Index pour la table `guichet`
--
ALTER TABLE `guichet`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `incident`
--
ALTER TABLE `incident`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_incident_vehicule` (`vehicule_id`),
  ADD KEY `fk_incident_guichet` (`guichet_id`);

--
-- Index pour la table `paiement`
--
ALTER TABLE `paiement`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_paiement_vehicule` (`vehicule_id`),
  ADD KEY `fk_paiement_guichet` (`guichet_id`);

--
-- Index pour la table `typevehicule`
--
ALTER TABLE `typevehicule`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `vehicule`
--
ALTER TABLE `vehicule`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_vehicule_type` (`type_vehicule_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `agent`
--
ALTER TABLE `agent`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT pour la table `guichet`
--
ALTER TABLE `guichet`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pour la table `incident`
--
ALTER TABLE `incident`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `paiement`
--
ALTER TABLE `paiement`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT pour la table `typevehicule`
--
ALTER TABLE `typevehicule`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `vehicule`
--
ALTER TABLE `vehicule`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `agent`
--
ALTER TABLE `agent`
  ADD CONSTRAINT `fk_agent` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_agent_guichet` FOREIGN KEY (`guichet_id`) REFERENCES `guichet` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `incident`
--
ALTER TABLE `incident`
  ADD CONSTRAINT `fk_incident_guichet` FOREIGN KEY (`guichet_id`) REFERENCES `guichet` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_incident_vehicule` FOREIGN KEY (`vehicule_id`) REFERENCES `vehicule` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `paiement`
--
ALTER TABLE `paiement`
  ADD CONSTRAINT `fk_paiement_guichet` FOREIGN KEY (`guichet_id`) REFERENCES `guichet` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_paiement_vehicule` FOREIGN KEY (`vehicule_id`) REFERENCES `vehicule` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `vehicule`
--
ALTER TABLE `vehicule`
  ADD CONSTRAINT `fk_vehicule_type` FOREIGN KEY (`type_vehicule_id`) REFERENCES `typevehicule` (`id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
