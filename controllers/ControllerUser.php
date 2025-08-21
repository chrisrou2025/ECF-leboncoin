<?php

/**
 * Contrôleur des Utilisateurs  
 * Gère les opérations liées aux profils utilisateurs
 */
class ControllerUser
{
    private $userModel;
    private $annonceModel;

    /**
     * Constructeur - Initialise les modèles nécessaires
     */
    public function __construct()
    {
        $this->userModel = new ModelUser();
        $this->annonceModel = new ModelAnnonce();
    }

    /**
     * Affiche la page de compte de l'utilisateur connecté.
     * Route: GET /compte
     */
    public function accountPage(): void
    {
        if (!isset($_SESSION['id'])) {
            $_SESSION['error'] = "Vous devez être connecté pour accéder à votre compte.";
            header('Location: /ECF-leboncoin/login');
            exit();
        }

        $userId = $_SESSION['id'];
        $user = $this->userModel->getUserById($userId); // Retourne ?User

        if (!$user) {
            $_SESSION['error'] = "Utilisateur non trouvé.";
            header('Location: /ECF-leboncoin/');
            exit();
        }

        // Récupérer les annonces de l'utilisateur
        $annonces = $this->annonceModel->getAnnoncesByUserId($userId); // Retourne array of Annonce
        $title = "Mon Compte - labonnetrouvaille";
        
        // Définir correctement la variable $page pour le CSS
        $page = 'compte';

        // Affichage du template base-html.php
        ob_start();
        require __DIR__ . '/../view/user/compte.php';
        $content = ob_get_clean();
        require __DIR__ . '/../view/base-html.php';
    }

    /**
     * Affiche le profil d'un utilisateur par son ID
     * Route: GET /user/[i:id]
     */
    public function oneUserById(array $params): void
    {
        $id = (int)$params['id'];

        $user = $this->userModel->getUserById($id); // Retourne ?User

        if (!$user) {
            http_response_code(404);
            $title = "Utilisateur non trouvé";
            $page = '404';

            require __DIR__ . '/../view/base-html.php';
            return;
        }

        // Récupération des annonces de cet utilisateur
        $annonces = $this->annonceModel->getAnnoncesByUserId($id);

        $title = "Profil de " . $user->getPseudo() . " - labonnetrouvaille";
        
        // Définir correctement la variable $page pour le CSS
        $page = 'compte';

        // Affichage du template base-html.php
        ob_start();
        require __DIR__ . '/../view/user/compte.php';
        $content = ob_get_clean();
        require __DIR__ . '/../view/base-html.php';
    }

    /**
     * Affiche les annonces de l'utilisateur connecté
     * Route: GET /user/mes-annonces
     */
    public function mesAnnonces(): void
    {
        // Vérification que l'utilisateur est connecté
        if (!isset($_SESSION['id'])) {
            $_SESSION['error'] = "Vous devez être connecté pour voir vos annonces.";
            header('Location: /ECF-leboncoin/login');
            exit();
        }

        $userId = $_SESSION['id'];
        $user = $this->userModel->getUserById($userId);

        if (!$user) {
            $_SESSION['error'] = "Utilisateur non trouvé.";
            header('Location: /ECF-leboncoin/');
            exit();
        }

        // Récupération des annonces de l'utilisateur
        $annonces = $this->annonceModel->getAnnoncesByUserId($userId); // Retourne array of Annonce

        $title = "Mes Annonces - labonnetrouvaille";
        
        // Définir correctement la variable $page pour le CSS
        $page = 'mes-annonces';

        // Affichage du template base-html.php
        ob_start();
        require __DIR__ . '/../view/user/mes-annonces.php';
        $content = ob_get_clean();
        require __DIR__ . '/../view/base-html.php';
    }

    /**
     * Met à jour le pseudo de l'utilisateur connecté
     * Route: POST /compte/update-pseudo
     */
    public function updatePseudo(): void
    {
        // Vérification que l'utilisateur est connecté
        if (!isset($_SESSION['id'])) {
            $_SESSION['error'] = "Vous devez être connecté pour modifier votre pseudo.";
            header('Location: /ECF-leboncoin/login');
            exit();
        }

        // Vérification que la requête est bien en POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error'] = "Méthode non autorisée.";
            header('Location: /ECF-leboncoin/compte');
            exit();
        }

        $userId = $_SESSION['id'];
        $pseudo = trim($_POST['pseudo'] ?? '');

        // Validation des données
        if (empty($pseudo)) {
            $_SESSION['error'] = "Le pseudo ne peut pas être vide.";
            header('Location: /ECF-leboncoin/compte');
            exit();
        }

        if (strlen($pseudo) < 3) {
            $_SESSION['error'] = "Le pseudo doit contenir au moins 3 caractères.";
            header('Location: /ECF-leboncoin/compte');
            exit();
        }

        // Récupérer l'utilisateur actuel
        $user = $this->userModel->getUserById($userId);
        if (!$user) {
            $_SESSION['error'] = "Utilisateur non trouvé.";
            header('Location: /ECF-leboncoin/compte');
            exit();
        }

        // Mettre à jour le pseudo dans l'entité
        $user->setPseudo($pseudo);

        // Mise à jour via le modèle
        $result = $this->userModel->updateUser($user);

        if ($result['success']) {
            $_SESSION['success'] = $result['message'];
            // Mettre à jour le pseudo en session
            $_SESSION['pseudo'] = $pseudo;
        } else {
            $_SESSION['error'] = $result['message'];
        }

        header('Location: /ECF-leboncoin/compte');
        exit();
    }

    /**
     * Met à jour l'email de l'utilisateur connecté
     * Route: POST /compte/update-email
     */
    public function updateEmail(): void
    {
        // Vérification que l'utilisateur est connecté
        if (!isset($_SESSION['id'])) {
            $_SESSION['error'] = "Vous devez être connecté pour modifier votre email.";
            header('Location: /ECF-leboncoin/login');
            exit();
        }

        // Vérification que la requête est bien en POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error'] = "Méthode non autorisée.";
            header('Location: /ECF-leboncoin/compte');
            exit();
        }

        $userId = $_SESSION['id'];
        $email = trim($_POST['email'] ?? '');

        // Validation des données
        if (empty($email)) {
            $_SESSION['error'] = "L'email ne peut pas être vide.";
            header('Location: /ECF-leboncoin/compte');
            exit();
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = "Format d'email invalide.";
            header('Location: /ECF-leboncoin/compte');
            exit();
        }

        // Récupérer l'utilisateur actuel
        $user = $this->userModel->getUserById($userId);
        if (!$user) {
            $_SESSION['error'] = "Utilisateur non trouvé.";
            header('Location: /ECF-leboncoin/compte');
            exit();
        }

        // Mettre à jour l'email dans l'entité
        $user->setEmail($email);

        // Mise à jour via le modèle
        $result = $this->userModel->updateUser($user);

        if ($result['success']) {
            $_SESSION['success'] = $result['message'];
            // Mettre à jour l'email en session
            $_SESSION['email'] = $email;
        } else {
            $_SESSION['error'] = $result['message'];
        }

        header('Location: /ECF-leboncoin/compte');
        exit();
    }

    /**
     * Met à jour le mot de passe de l'utilisateur connecté
     * Route: POST /compte/update-password
     */
    public function updatePassword(): void
    {
        // Vérification que l'utilisateur est connecté
        if (!isset($_SESSION['id'])) {
            $_SESSION['error'] = "Vous devez être connecté pour modifier votre mot de passe.";
            header('Location: /ECF-leboncoin/login');
            exit();
        }

        // Vérification que la requête est bien en POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error'] = "Méthode non autorisée.";
            header('Location: /ECF-leboncoin/compte');
            exit();
        }

        $userId = $_SESSION['id'];
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';

        // Validation des données
        if (empty($currentPassword)) {
            $_SESSION['error'] = "Le mot de passe actuel est requis.";
            header('Location: /ECF-leboncoin/compte');
            exit();
        }

        if (empty($newPassword)) {
            $_SESSION['error'] = "Le nouveau mot de passe est requis.";
            header('Location: /ECF-leboncoin/compte');
            exit();
        }

        if (strlen($newPassword) < 6) {
            $_SESSION['error'] = "Le nouveau mot de passe doit contenir au moins 6 caractères.";
            header('Location: /ECF-leboncoin/compte');
            exit();
        }

        // Mise à jour via le modèle
        $result = $this->userModel->updatePassword($userId, $currentPassword, $newPassword);

        if ($result['success']) {
            $_SESSION['success'] = $result['message'];
        } else {
            $_SESSION['error'] = $result['message'];
        }

        header('Location: /ECF-leboncoin/compte');
        exit();
    }
}