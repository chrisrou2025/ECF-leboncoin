-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost
-- Généré le : mer. 30 juil. 2025 à 15:19
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
  `titre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '? Titre de l''annonce',
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '? Description détaillée',
  `prix` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '? Prix en euros',
  `category_id` int NOT NULL COMMENT '?️ Référence vers la catégorie',
  `user_id` int NOT NULL COMMENT '? Référence vers l''utilisateur'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `annonces`
--

INSERT INTO `annonces` (`id`, `titre`, `description`, `prix`, `category_id`, `user_id`) VALUES
(1, 'MacBook Pro 13 pouces', 'MacBook Pro en excellent état, peu servi. Livré avec chargeur et housse.', 899.00, 1, 1),
(2, 'Table basse scandinave', 'Belle table basse en bois clair, style nordique. Parfait état.', 150.00, 2, 1),
(3, 'Vélo de course Peugeot', 'Vélo vintage en bon état général. Idéal pour débuter le cyclisme.', 280.00, 4, 1);

-- --------------------------------------------------------

--
-- Structure de la table `categories`
--

CREATE TABLE `categories` (
  `id` int NOT NULL,
  `nom` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '? Nom de la catégorie',
  `description` text COLLATE utf8mb4_unicode_ci COMMENT '? Description de la catégorie',
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
(9, 'Vacances', 'Reservation, voyages, hotels', '2025-07-30 14:45:31'),
(10, 'Instruments de Musique', 'Guitares, pianos, matériel audio', '2025-07-30 14:45:31');

-- --------------------------------------------------------

--
-- Structure de la table `images`
--

CREATE TABLE `images` (
  `id` int NOT NULL,
  `annonce_id` int NOT NULL COMMENT '? Référence vers l''annonce',
  `nom_fichier` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '? Nom du fichier image',
  `chemin` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '? Chemin vers l''image',
  `ordre` int DEFAULT '1' COMMENT '? Ordre d''affichage',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `pseudo` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '? Pseudo de l''utilisateur',
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '? Email unique pour la connexion',
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '? Mot de passe hashé'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `pseudo`, `email`, `password`) VALUES
(1, 'Chris', 'c.roupioz@laposte.net', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `annonces`
--
ALTER TABLE `annonces`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_annonces_category` (`category_id`),
  ADD KEY `idx_annonces_user` (`user_id`);

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
  ADD KEY `annonce_id` (`annonce_id`);

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
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT pour la table `images`
--
ALTER TABLE `images`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
  ADD CONSTRAINT `images_ibfk_1` FOREIGN KEY (`annonce_id`) REFERENCES `annonces` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
