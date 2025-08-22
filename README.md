# ECF-leboncoin
site de petites annonces labonnetrouvaille

https://www.figma.com/design/Wp8NR7nyAeShTWKHTPBf6t/Site-labonnetrouvaille?node-id=0-1&p=f&t=Pg8McOEtfZ31v525-0

# La Bonne Trouvaille

Site d'annonces de vente d'objets d'occasion développé en PHP vanilla avec architecture MVC.

## Description

La Bonne Trouvaille est une plateforme web permettant aux utilisateurs de publier et consulter des annonces de vente d'objets d'occasion. Le site propose un système de catégories, de recherche, et de gestion d'images pour les annonces.

## Fonctionnalités

### Pour les visiteurs
- Consultation des annonces par catégorie
- Recherche d'annonces par mots-clés
- Affichage détaillé des annonces avec photos

### Pour les utilisateurs connectés
- Inscription et connexion sécurisée
- Publication d'annonces avec upload d'images (4 max)
- Modification et suppression de ses propres annonces
- Gestion du profil utilisateur (pseudo, email, mot de passe)

### Catégories disponibles
- Informatique
- Maison & Jardin
- Mode & Vêtements
- Véhicules
- Sports & Loisirs
- Vacances
- Instruments de Musique

## Technologies utilisées

- **Backend** : PHP 7.4+
- **Base de données** : MySQL
- **Routeur** : AltoRouter
- **Frontend** : HTML5, CSS3, JavaScript vanilla
- **Architecture** : MVC (Model-View-Controller)

## Structure du projet

```
ECF-leboncoin/
├── Entity/              # Entités (Annonce, User, Category, Image)
├── controllers/         # Contrôleurs MVC
├── models/             # Modèles pour l'accès aux données
├── view/               # Vues et templates
├── asset/              # Ressources statiques (CSS, JS, images)
│   ├── css/
│   ├── js/
│   ├── images/
│   └── uploads/        # Images uploadées par les utilisateurs
├── vendor/             # Dépendances Composer
├── composer.json       # Configuration Composer
├── index.php           # Point d'entrée avec routage
└── labonnetrouvaille.sql # Dump de la base de données
```

## Installation

### Prérequis
- PHP 7.4 ou supérieur
- MySQL 5.7 ou supérieur
- Composer
- Serveur web (Apache/Nginx) ou XAMPP/WAMP/MAMP

### Étapes d'installation

1. **Cloner le projet**
   ```bash
   git clone [URL_DU_REPOSITORY]
   cd ECF-leboncoin
   ```

2. **Installer les dépendances**
   ```bash
   composer install
   ```

3. **Configuration de la base de données**
   - Créer une base de données MySQL nommée `labonnetrouvaille`
   - Importer le fichier `labonnetrouvaille.sql`
   - Ajuster les paramètres de connexion dans `models/ModelBase.php` :
     ```php
     self::$instance = new PDO('mysql:host=localhost;dbname=labonnetrouvaille', 'votre_utilisateur', 'votre_mot_de_passe');
     ```

4. **Configuration du serveur web**
   - Placer le projet dans le répertoire web de votre serveur
   - Configurer le virtual host pour pointer vers le dossier du projet
   - S'assurer que le dossier `asset/uploads/` est accessible en écriture

5. **Accès au site**
   - Ouvrir votre navigateur et accéder à `http://localhost/ECF-leboncoin`

## Configuration

### Base de données
Modifiez les paramètres de connexion dans `models/ModelBase.php` :
```php
private static function setDb()
{
    try {
        self::$instance = new PDO('mysql:host=localhost;dbname=labonnetrouvaille', 'root', 'root');
        self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        // ...
    }
}
```

### Routage
Le routage est configuré dans `index.php`. Ajustez le base path si nécessaire :
```php
$router->setBasePath('/ECF-leboncoin');
```

## Utilisation

### Comptes de test
Vous pouvez créer un nouveau compte ou utiliser les données existantes dans la base.

### Publication d'une annonce
1. Créer un compte ou se connecter
2. Cliquer sur "Déposer une annonce"
3. Remplir le formulaire avec titre, description, prix, catégorie
4. Ajouter jusqu'à 4 photos
5. Valider l'annonce

### Gestion du profil
- Accéder via "Mon Compte" dans le menu
- Modifier pseudo, email ou mot de passe
- Gérer ses annonces publiées

## Structure de la base de données

### Tables principales
- `users` : Informations des utilisateurs
- `categories` : Catégories d'annonces
- `annonces` : Annonces avec détails
- `images` : Images associées aux annonces

### Relations
- Une annonce appartient à un utilisateur et une catégorie
- Une annonce peut avoir plusieurs images
- Les contraintes de clés étrangères assurent l'intégrité

## Fonctionnalités techniques

### Upload d'images
- Formats supportés : JPG, PNG, GIF, WebP
- Taille maximale : 5MB par image
- Maximum 4 images par annonce
- Redimensionnement automatique côté client

### Sécurité
- Mots de passe hashés avec `password_hash()`
- Protection contre les injections SQL avec requêtes préparées
- Validation des données côté serveur et client
- Contrôle d'accès pour la modification/suppression

### Recherche
- Recherche full-text dans titre, description, marque
- Filtrage par catégorie
- Tri par prix, date, titre

## Améliorations possibles

- Système de messagerie entre acheteurs/vendeurs
- Géolocalisation des annonces
- Système de favoris
- Notifications par email
- API REST pour une application mobile
- Système de modération des annonces

## Dépannage

### Problèmes courants

**Erreur de connexion base de données**
- Vérifier les paramètres dans `ModelBase.php`
- S'assurer que MySQL est démarré
- Vérifier les permissions utilisateur

**Images non affichées**
- Vérifier les permissions du dossier `asset/uploads/`
- S'assurer que le chemin dans la base correspond aux fichiers

**Erreurs de routing**
- Vérifier le `basePath` dans `index.php`
- S'assurer que mod_rewrite est activé (Apache)

## Licence

Ce projet est réalisé dans un cadre pédagogique.

## Contact

Pour toute question concernant ce projet, n'hésitez pas à ouvrir une issue sur le repository.