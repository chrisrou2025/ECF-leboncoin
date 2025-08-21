-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost
-- Généré le : mer. 20 août 2025 à 13:35
-- Version du serveur : 9.1.0
-- Version de PHP : 8.1.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `labonnetrouvaille`
--

-- --------------------------------------------------------

--
-- Structure de la table `annonces`
--

CREATE TABLE `annonces` (
  `id` int NOT NULL,
  `titre` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '? Titre de l''annonce',
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '? Description détaillée',
  `prix` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '? Prix en euros',
  `kilometrage` int DEFAULT NULL,
  `localite` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `marque` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Marque du produit',
  `etat` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'bon',
  `category_id` int NOT NULL COMMENT '?️ Référence vers la catégorie',
  `user_id` int NOT NULL COMMENT '? Référence vers l''utilisateur',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `annonces`
--

INSERT INTO `annonces` (`id`, `titre`, `description`, `prix`, `kilometrage`, `localite`, `marque`, `etat`, `category_id`, `user_id`, `created_at`, `updated_at`) VALUES
(21, 'Console Playstation 1', 'vieille console', 50.00, NULL, 'oyonnax', 'Sony', 'satisfaisant', 6, 4, '2025-08-15 09:44:26', '2025-08-20 13:15:43'),
(22, 'Superbe Batterie', 'Très belle batterie', 100.00, NULL, 'Oyonnax', NULL, 'tres_bon', 10, 4, '2025-08-15 10:51:51', '2025-08-19 18:59:08'),
(23, 'Vends voitures d\'occasions', 'Plusieurs marques de véhicules pas cher', 3000.00, 90000, 'Oyonnax', 'Toutes marques', 'satisfaisant', 4, 4, '2025-08-15 16:17:41', '2025-08-20 13:15:03'),
(25, 'Villa à louer', 'Disponible à partir de décembre !', 1500.00, NULL, 'Marseille', NULL, '', 2, 3, '2025-08-16 10:57:10', '2025-08-16 16:29:56'),
(28, 'Superbe ZOE', 'Vends ZOE pour piéces détachées', 50.00, 150000, 'Oyonnax', 'Renault', 'satisfaisant', 4, 4, '2025-08-20 13:13:50', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `categories`
--

CREATE TABLE `categories` (
  `id` int NOT NULL,
  `nom` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '? Nom de la catégorie',
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT '? Description de la catégorie',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '? Date de création'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `categories`
--

INSERT INTO `categories` (`id`, `nom`, `description`, `created_at`) VALUES
(1, 'Informatique', 'Ordinateurs, smartphones, accessoires high-tech', '2025-07-30 14:45:31'),
(2, 'Maison & Jardin', 'Meubles, décoration, outils de jardinage', '2025-07-30 14:45:31'),
(3, 'Mode & Vêtements', 'Vêtements, chaussures, accessoires', '2025-07-30 14:45:31'),
(4, 'Véhicules', 'Voitures, motos, vélos', '2025-07-30 14:45:31'),
(6, 'Sports & Loisirs', 'Équipements sportifs, jeux, instruments', '2025-07-30 14:45:31'),
(9, 'Vacances', 'Locations, voyages, hotels', '2025-07-30 14:45:31'),
(10, 'Instruments de Musique', 'Guitares, pianos, matériel audio', '2025-07-30 14:45:31');

-- --------------------------------------------------------

--
-- Structure de la table `images`
--

CREATE TABLE `images` (
  `id` int NOT NULL,
  `annonce_id` int NOT NULL COMMENT '? Référence vers l''annonce',
  `nom_fichier` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '? Nom du fichier image',
  `path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `images`
--

INSERT INTO `images` (`id`, `annonce_id`, `nom_fichier`, `path`, `order`, `created_at`) VALUES
(61, 22, '', '/ECF-leboncoin/asset/uploads/photo_22_689f1147a95c3.jpg', 1, '2025-08-15 10:51:51'),
(72, 25, '', '/ECF-leboncoin/asset/uploads/photo_25_68a0b204c5502.jpg', 0, '2025-08-16 16:29:56'),
(73, 25, '', '/ECF-leboncoin/asset/uploads/photo_25_68a0b204c55a3.jpg', 0, '2025-08-16 16:29:56'),
(74, 25, '', '/ECF-leboncoin/asset/uploads/photo_25_68a0b23c6a376.jpg', 0, '2025-08-16 16:30:52'),
(76, 22, '', '/ECF-leboncoin/asset/uploads/photo_22_68a0b39d8ccbc.jpg', 0, '2025-08-16 16:36:45'),
(89, 21, '', '/ECF-leboncoin/asset/uploads/photo_21_68a30305d9bee.jpg', 0, '2025-08-18 10:40:05'),
(90, 21, '', '/ECF-leboncoin/asset/uploads/photo_21_68a30305d9c94.jpg', 0, '2025-08-18 10:40:05'),
(91, 21, '', '/ECF-leboncoin/asset/uploads/photo_21_68a30305d9cfb.jpg', 0, '2025-08-18 10:40:05'),
(97, 23, '', '/ECF-leboncoin/asset/uploads/photo_23_68a4450392b99.jpg', 0, '2025-08-19 09:33:55'),
(98, 23, '', '/ECF-leboncoin/asset/uploads/photo_23_68a4450392c62.jpg', 0, '2025-08-19 09:33:55'),
(99, 23, '', '/ECF-leboncoin/asset/uploads/photo_23_68a4450392ce8.jpg', 0, '2025-08-19 09:33:55'),
(100, 22, '', '/ECF-leboncoin/asset/uploads/photo_22_68a4c97c800db.jpg', 0, '2025-08-19 18:59:08'),
(101, 28, '', '/ECF-leboncoin/asset/uploads/photo_28_68a5ca0eb7675.jpg', 0, '2025-08-20 13:13:50'),
(102, 28, '', '/ECF-leboncoin/asset/uploads/photo_28_68a5ca0eb7739.jpg', 1, '2025-08-20 13:13:50'),
(103, 28, '', '/ECF-leboncoin/asset/uploads/photo_28_68a5ca0eb77a3.jpg', 2, '2025-08-20 13:13:50'),
(104, 28, '', '/ECF-leboncoin/asset/uploads/photo_28_68a5ca0eb7801.jpg', 3, '2025-08-20 13:13:50');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `pseudo` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '? Pseudo de l''utilisateur',
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '? Email unique pour la connexion',
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '? Mot de passe hashé'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `pseudo`, `email`, `password`) VALUES
(3, 'dede', 'dede@laposte.net', '$2y$10$ww7EKPEiWHFU7zhkSuyMV.BHvkmubWy7VjoNBYlV8cYaBW0gn8wMq'),
(4, 'chrisrou', 'chr.roupioz@laposte.net', '$2y$10$lWhK8J4UyUtsSQNjipmIieKTCh/HDOzM.KAzYcU63lwZmaHeFVe.e');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `annonces`
--
ALTER TABLE `annonces`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_annonces_category` (`category_id`),
  ADD KEY `idx_annonces_user` (`user_id`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Index pour la table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `images`
--
ALTER TABLE `images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `annonce_id` (`annonce_id`),
  ADD KEY `idx_annonce_id` (`annonce_id`),
  ADD KEY `idx_ordre` (`annonce_id`,`order`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `annonces`
--
ALTER TABLE `annonces`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT pour la table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT pour la table `images`
--
ALTER TABLE `images`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=105;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `annonces`
--
ALTER TABLE `annonces`
  ADD CONSTRAINT `annonces_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE RESTRICT,
  ADD CONSTRAINT `annonces_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `images`
--
ALTER TABLE `images`
  ADD CONSTRAINT `fk_images_annonce` FOREIGN KEY (`annonce_id`) REFERENCES `annonces` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
