-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : mer. 15 avr. 2026 à 10:42
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
-- Base de données : `pont_peage`
--

-- --------------------------------------------------------

--
-- Structure de la table `agent`
--

CREATE TABLE `agent` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `debut` datetime DEFAULT NULL,
  `fin` datetime DEFAULT NULL,
  `created_at` timestamp(3) NOT NULL DEFAULT current_timestamp(3),
  `updated_at` timestamp(3) NOT NULL DEFAULT current_timestamp(3) ON UPDATE current_timestamp(3)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `agent`
--

INSERT INTO `agent` (`id`, `user_id`, `debut`, `fin`, `created_at`, `updated_at`) VALUES
(1, 2, '2026-04-15 08:25:23', NULL, '2026-04-10 00:28:24.082', '2026-04-15 08:25:23.306'),
(6, 3, NULL, '2026-04-14 08:33:18', '2026-04-11 16:33:01.000', '2026-04-14 08:33:18.413'),
(7, 4, NULL, '2026-04-14 12:39:14', '2026-04-11 16:43:44.384', '2026-04-14 12:39:14.453'),
(17, 6, '2026-04-14 08:29:37', '2026-04-14 08:32:26', '2026-04-14 08:24:33.760', '2026-04-14 08:32:26.542'),
(22, 7, NULL, NULL, '2026-04-14 12:37:42.337', '2026-04-14 12:37:42.337');

-- --------------------------------------------------------

--
-- Structure de la table `agent_guichet`
--

CREATE TABLE `agent_guichet` (
  `agent_id` int(10) UNSIGNED NOT NULL,
  `guichet_id` int(10) UNSIGNED NOT NULL,
  `date_assignation` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `agent_guichet`
--

INSERT INTO `agent_guichet` (`agent_id`, `guichet_id`, `date_assignation`) VALUES
(1, 1, '2026-04-14 12:52:31');

-- --------------------------------------------------------

--
-- Structure de la table `guichet`
--

CREATE TABLE `guichet` (
  `id` int(10) UNSIGNED NOT NULL,
  `emplacement` varchar(150) NOT NULL,
  `created_at` timestamp(3) NOT NULL DEFAULT current_timestamp(3),
  `updated_at` timestamp(3) NOT NULL DEFAULT current_timestamp(3) ON UPDATE current_timestamp(3)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `guichet`
--

INSERT INTO `guichet` (`id`, `emplacement`, `created_at`, `updated_at`) VALUES
(1, 'Nord', '2026-04-10 00:28:24.087', '2026-04-10 00:28:24.087'),
(2, 'Sud', '2026-04-10 00:28:24.089', '2026-04-10 00:28:24.089'),
(3, 'Ouest', '2026-04-10 00:28:24.091', '2026-04-10 00:28:24.091'),
(4, 'Est', '2026-04-10 00:28:24.096', '2026-04-10 00:28:24.096'),
(5, 'Nord-Est', '2026-04-10 00:28:24.098', '2026-04-10 00:28:24.098'),
(6, 'Nord-Ouest', '2026-04-10 00:28:24.100', '2026-04-10 00:28:24.100'),
(7, 'Sud-Est', '2026-04-10 00:28:24.102', '2026-04-10 00:28:24.102'),
(8, 'Sud-Ouest', '2026-04-10 00:28:24.103', '2026-04-10 00:28:24.103');

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
  `created_at` timestamp(3) NOT NULL DEFAULT current_timestamp(3),
  `updated_at` timestamp(3) NOT NULL DEFAULT current_timestamp(3) ON UPDATE current_timestamp(3)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `paiement`
--

INSERT INTO `paiement` (`id`, `vehicule_id`, `guichet_id`, `mode_paiement`, `montant`, `created_at`, `updated_at`) VALUES
(1, 20, 3, 'Carte', 1515.00, '2026-04-10 00:28:24.176', '2026-04-10 00:28:24.176'),
(2, 16, 7, 'Espece', 4846.00, '2026-04-10 00:28:24.178', '2026-04-10 00:28:24.178'),
(3, 17, 3, 'Mobile Money', 2786.00, '2026-04-10 00:28:24.180', '2026-04-10 00:28:24.180'),
(4, 14, 3, 'Abonnement', 1102.00, '2026-04-10 00:28:24.182', '2026-04-10 00:28:24.182'),
(5, 17, 5, 'Carte', 2269.00, '2026-04-10 00:28:24.184', '2026-04-10 00:28:24.184'),
(6, 5, 8, 'Carte', 3263.00, '2026-04-10 00:28:24.185', '2026-04-10 00:28:24.185'),
(7, 6, 7, 'Mobile Money', 3680.00, '2026-04-10 00:28:24.187', '2026-04-10 00:28:24.187'),
(8, 11, 7, 'Mobile Money', 4325.00, '2026-04-10 00:28:24.189', '2026-04-10 00:28:24.189'),
(9, 3, 7, 'Mobile Money', 4831.00, '2026-04-10 00:28:24.191', '2026-04-10 00:28:24.191'),
(10, 15, 4, 'Carte', 4219.00, '2026-04-10 00:28:24.192', '2026-04-10 00:28:24.192'),
(11, 9, 2, 'Abonnement', 3545.00, '2026-04-10 00:28:24.194', '2026-04-10 00:28:24.194'),
(12, 20, 3, 'Abonnement', 4832.00, '2026-04-10 00:28:24.196', '2026-04-10 00:28:24.196'),
(13, 14, 8, 'Carte', 1127.00, '2026-04-10 00:28:24.198', '2026-04-10 00:28:24.198'),
(14, 10, 4, 'Carte', 1492.00, '2026-04-10 00:28:24.200', '2026-04-10 00:28:24.200'),
(15, 7, 7, 'Espece', 3472.00, '2026-04-10 00:28:24.202', '2026-04-10 00:28:24.202'),
(16, 12, 6, 'Abonnement', 2982.00, '2026-04-10 00:28:24.203', '2026-04-10 00:28:24.203'),
(17, 3, 7, 'Abonnement', 2193.00, '2026-04-10 00:28:24.205', '2026-04-10 00:28:24.205'),
(18, 9, 2, 'Espece', 2455.00, '2026-04-10 00:28:24.207', '2026-04-10 00:28:24.207'),
(19, 10, 3, 'Espece', 2431.00, '2026-04-10 00:28:24.209', '2026-04-10 00:28:24.209'),
(20, 25, 1, 'Abonnement', 4507.00, '2026-04-10 00:28:24.211', '2026-04-10 00:28:24.211'),
(21, 4, 6, 'Mobile Money', 3199.00, '2026-04-10 00:28:24.213', '2026-04-10 00:28:24.213'),
(22, 9, 5, 'Carte', 2111.00, '2026-04-10 00:28:24.214', '2026-04-10 00:28:24.214'),
(23, 12, 7, 'Carte', 3466.00, '2026-04-10 00:28:24.216', '2026-04-10 00:28:24.216'),
(24, 18, 6, 'Mobile Money', 2454.00, '2026-04-10 00:28:24.218', '2026-04-10 00:28:24.218'),
(25, 22, 3, 'Mobile Money', 2039.00, '2026-04-10 00:28:24.220', '2026-04-10 00:28:24.220'),
(26, 9, 4, 'Espece', 2265.00, '2026-04-10 00:28:24.222', '2026-04-10 00:28:24.222'),
(27, 4, 3, 'Mobile Money', 1572.00, '2026-04-10 00:28:24.223', '2026-04-10 00:28:24.223'),
(28, 15, 2, 'Carte', 3608.00, '2026-04-10 00:28:24.225', '2026-04-10 00:28:24.225'),
(29, 9, 4, 'Mobile Money', 663.00, '2026-04-10 00:28:24.227', '2026-04-10 00:28:24.227'),
(30, 17, 5, 'Carte', 588.00, '2026-04-10 00:28:24.228', '2026-04-10 00:28:24.228'),
(31, 21, 3, 'Mobile Money', 1448.00, '2026-04-10 00:28:24.230', '2026-04-10 00:28:24.230'),
(32, 24, 8, 'Carte', 1050.00, '2026-04-10 00:28:24.232', '2026-04-10 00:28:24.232'),
(33, 6, 7, 'Espece', 3075.00, '2026-04-10 00:28:24.234', '2026-04-10 00:28:24.234'),
(34, 23, 6, 'Espece', 4804.00, '2026-04-10 00:28:24.235', '2026-04-10 00:28:24.235'),
(35, 20, 8, 'Espece', 3598.00, '2026-04-10 00:28:24.237', '2026-04-10 00:28:24.237'),
(39, 27, 1, 'Carte', 4810.00, '2026-04-15 00:39:54.940', '2026-04-15 00:39:54.940');

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
('Moto', 500.00),
('Voiture', 500.00),
('Van/SUV', 500.00),
('Poids Moyen', 1000.00),
('Poids Lourd', 1500.00);

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` text NOT NULL,
  `role` varchar(50) NOT NULL DEFAULT 'operateur',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `last_login_at` timestamp(3) NOT NULL DEFAULT current_timestamp(3) ON UPDATE current_timestamp(3),
  `created_at` timestamp(3) NOT NULL DEFAULT current_timestamp(3),
  `updated_at` timestamp(3) NOT NULL DEFAULT current_timestamp(3) ON UPDATE current_timestamp(3)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `is_active`, `last_login_at`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin@peage.com', '$2y$10$VicZhviO6LRdqu0PmDL/u.Mntq23dp8..k0/5L6T37EpCGU4jIjlq', 'admin', 1, '2026-04-10 00:28:24.078', '2026-04-10 00:28:24.078', '2026-04-10 00:28:24.078'),
(2, 'Alex', 'alex@gmail.com', '$2y$10$rZdakEbx14WD.Hx/APdUzO5JZ8Sj.bkFRKcVMqsNE.h97IRHjKjkW', 'operateur', 1, '2026-04-10 00:28:24.080', '2026-04-10 00:28:24.080', '2026-04-10 00:28:24.080'),
(3, 'Marc', 'marc@gmail.com', '$2y$10$PhjrT3ALpex724W4ON0WcO.u8LePhYkdPeVwMlgReAr3xiE4gl2..', 'operateur', 1, '2026-04-11 16:29:03.382', '2026-04-11 16:29:03.382', '2026-04-11 16:29:03.382'),
(4, 'Sam', 'sam@gmail.com', '$2y$10$jLV2Go4hubdMvOigUaktaeNNvSGskvDFOAImuP1WYIlGy5Vz3mRbC', 'operateur', 1, '2026-04-11 16:43:44.380', '2026-04-11 16:43:44.380', '2026-04-11 16:43:44.380'),
(6, 'Koh Emmanuel', 'emmanuek@gmail.com', '$2y$10$pRlI2q7Vio/Jx779IUSObOlM74pcReJSSgurO99gVTmbBwKVVXSRe', 'operateur', 1, '2026-04-14 08:24:33.755', '2026-04-14 08:24:33.755', '2026-04-14 08:24:33.755'),
(7, 'moayé kouadio', 'moak@gmail.com', '$2y$10$cNtvYCBjeDzuec4N4mV.Jum/bSEl1gpaRbfFJN7MRHoEG.KeCeqBi', 'operateur', 1, '2026-04-14 12:37:42.331', '2026-04-14 12:37:42.331', '2026-04-14 12:37:42.331');

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
(1, 'bz-642-pr', 1, 'Robin S.A.', 'S.A.', 'Bouton d\'or', '2026-04-10 00:28:24.118', '2026-04-10 00:28:24.118'),
(2, 'rw-425-zl', 4, 'Chauveau', 'SARL', 'Vert sauge', '2026-04-10 00:28:24.120', '2026-04-10 00:28:24.120'),
(3, 'mf-068-ka', 2, 'Michel Francois S.A.R.L.', 'S.A.', 'Lie de vin', '2026-04-10 00:28:24.122', '2026-04-10 00:28:24.122'),
(4, 'xx-272-wy', 1, 'Lefevre Royer S.A.', 'et Fils', 'Violet d\'évêque', '2026-04-10 00:28:24.127', '2026-04-10 00:28:24.127'),
(5, 'dm-714-or', 3, 'Rodrigues', 'SA', 'Basané', '2026-04-10 00:28:24.130', '2026-04-10 00:28:24.130'),
(6, 'zs-935-xc', 2, 'Vidal S.A.', 'SARL', 'Bronze', '2026-04-10 00:28:24.132', '2026-04-10 00:28:24.132'),
(7, 'wo-559-id', 1, 'Verdier SA', 'SARL', 'Cachou', '2026-04-10 00:28:24.134', '2026-04-10 00:28:24.134'),
(8, 'dc-466-mj', 4, 'Garcia Imbert SA', 'SARL', 'Brique', '2026-04-10 00:28:24.136', '2026-04-10 00:28:24.136'),
(9, 'cm-283-qz', 3, 'Lopes', 'et Fils', 'Jaune mimosa', '2026-04-10 00:28:24.138', '2026-04-10 00:28:24.138'),
(10, 'ri-046-yi', 3, 'Hebert SA', 'S.A.R.L.', 'Viride', '2026-04-10 00:28:24.140', '2026-04-10 00:28:24.140'),
(11, 'sq-429-fo', 5, 'Philippe', 'S.A.R.L.', 'Vert bouteille', '2026-04-10 00:28:24.142', '2026-04-10 00:28:24.142'),
(12, 'wc-918-yx', 5, 'Simon Laine SAS', 'S.A.S.', 'Vert lichen', '2026-04-10 00:28:24.145', '2026-04-10 00:28:24.145'),
(13, 'id-998-tt', 1, 'Le Roux', 'S.A.', 'Prasin', '2026-04-10 00:28:24.147', '2026-04-10 00:28:24.147'),
(14, 'vt-600-yd', 1, 'Charrier', 'S.A.', 'Amande', '2026-04-10 00:28:24.152', '2026-04-10 00:28:24.152'),
(15, 'pm-184-oo', 4, 'Turpin Etienne et Fils', 'SAS', 'Or (couleur)', '2026-04-10 00:28:24.154', '2026-04-10 00:28:24.154'),
(16, 'ko-141-cq', 4, 'Bigot Delannoy S.A.', 'SAS', 'Rouge anglais', '2026-04-10 00:28:24.156', '2026-04-10 00:28:24.156'),
(17, 'wj-977-nh', 3, 'Brun', 'S.A.S.', 'Corail', '2026-04-10 00:28:24.158', '2026-04-10 00:28:24.158'),
(18, 'gh-707-ta', 3, 'Pichon', 'SAS', 'Sépia', '2026-04-10 00:28:24.160', '2026-04-10 00:28:24.160'),
(19, 'lz-097-vc', 4, 'Imbert SA', 'SARL', 'Magenta', '2026-04-10 00:28:24.162', '2026-04-10 00:28:24.162'),
(20, 'we-023-oy', 4, 'Valentin', 'SAS', 'Cyan', '2026-04-10 00:28:24.164', '2026-04-10 00:28:24.164'),
(21, 'wo-281-ue', 3, 'Lebreton Benoit S.A.', 'SA', 'Capucine', '2026-04-10 00:28:24.166', '2026-04-10 00:28:24.166'),
(22, 'uk-047-ca', 4, 'Jacquot', 'S.A.S.', 'Lavallière', '2026-04-10 00:28:24.168', '2026-04-10 00:28:24.168'),
(23, 'ma-238-fi', 3, 'Toussaint', 'S.A.S.', 'Héliotrope', '2026-04-10 00:28:24.169', '2026-04-10 00:28:24.169'),
(24, 'bk-785-ed', 1, 'Bouvier Devaux et Fils', 'S.A.R.L.', 'Rouge cerise', '2026-04-10 00:28:24.172', '2026-04-10 00:28:24.172'),
(25, 'to-693-uj', 4, 'Launay Pages SARL', 'S.A.S.', 'Sable', '2026-04-10 00:28:24.174', '2026-04-10 00:28:24.174'),
(27, 'AS-12-FA', 4, NULL, NULL, NULL, '2026-04-15 00:39:54.938', '2026-04-15 00:39:54.938');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `agent`
--
ALTER TABLE `agent`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Index pour la table `agent_guichet`
--
ALTER TABLE `agent_guichet`
  ADD PRIMARY KEY (`agent_id`,`guichet_id`),
  ADD KEY `fk_agent_guichet_guichet` (`guichet_id`);

--
-- Index pour la table `guichet`
--
ALTER TABLE `guichet`
  ADD PRIMARY KEY (`id`);

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
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_email` (`email`);

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
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT pour la table `guichet`
--
ALTER TABLE `guichet`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pour la table `paiement`
--
ALTER TABLE `paiement`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT pour la table `typevehicule`
--
ALTER TABLE `typevehicule`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pour la table `vehicule`
--
ALTER TABLE `vehicule`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `agent`
--
ALTER TABLE `agent`
  ADD CONSTRAINT `fk_agent` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `agent_guichet`
--
ALTER TABLE `agent_guichet`
  ADD CONSTRAINT `fk_agent_guichet_agent` FOREIGN KEY (`agent_id`) REFERENCES `agent` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_agent_guichet_guichet` FOREIGN KEY (`guichet_id`) REFERENCES `guichet` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

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
