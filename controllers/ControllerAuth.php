<?php

/**
 * Contrôleur d'Authentification
 * Gère l'inscription, la connexion et la déconnexion des utilisateurs
 */
class ControllerAuth
{
    private $userModel;

    /**
     * Constructeur - Initialise le modèle User
     */
    public function __construct()
    {
        $this->userModel = new ModelUser();
    }

    /**
     * Méthode pour gérer l'inscription d'un nouvel utilisateur
     * Route: GET|POST /register
     */
    public function register(): void
    {
        // Si l'utilisateur est déjà connecté, redirection vers l'accueil
        if (isset($_SESSION['id'])) {
            header('Location: /ECF-leboncoin/');
            exit();
        }

        // Vérification si la requête est de type POST (formulaire soumis)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // Validation : vérifier que tous les champs obligatoires sont remplis
            if (empty($_POST['pseudo']) || empty($_POST['email']) || empty($_POST['password'])) {
                $_SESSION['error'] = "Tous les champs doivent être remplis !";
                $_SESSION['old_input'] = $_POST;
                header('Location: /ECF-leboncoin/register');
                exit();
            }

            // Nettoyage des données reçues
            $pseudo = trim($_POST['pseudo']);
            $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
            $password = trim($_POST['password']);
            $confirmPassword = trim($_POST['confirm_password'] ?? '');

            // Création de l'entité User
            $user = new User([
                'pseudo' => $pseudo,
                'email' => $email,
                'password' => $password
            ]);

            // Validations supplémentaires
            $errors = $user->getValidationErrors(); // Utilise la validation de l'entité

            // Vérification du format de l'email
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Format d'email invalide !";
            }

            // Vérification de la longueur du pseudo
            if (strlen($pseudo) < 3) {
                $errors[] = "Le pseudo doit contenir au moins 3 caractères.";
            }

            // Vérification de la longueur du mot de passe
            if (strlen($password) < 3) {
                $errors[] = "Le mot de passe doit contenir au moins 3 caractères.";
            }

            // Vérification de la confirmation du mot de passe
            if ($password !== $confirmPassword) {
                $errors[] = "Les mots de passe ne correspondent pas.";
            }

            // Si des erreurs sont présentes
            if (!empty($errors)) {
                $_SESSION['error'] = "Erreurs de validation :";
                $_SESSION['validation_errors'] = $errors;
                $_SESSION['old_input'] = $_POST;
                header('Location: /ECF-leboncoin/register');
                exit();
            }

            // Tentative de création du nouvel utilisateur via le modèle
            $result = $this->userModel->register($user);

            // Vérification du succès de l'inscription
            if ($result['success']) {
                $_SESSION['success'] = $result['message'];
                header('Location: /ECF-leboncoin/login');
                exit();
            } else {
                $_SESSION['error'] = $result['message'];
                $_SESSION['old_input'] = $_POST;
                header('Location: /ECF-leboncoin/register');
                exit();
            }
        }

        require __DIR__ . '/../view/auth/register.php';
    }

    /**
     * Méthode pour gérer la connexion d'un utilisateur
     * Route: GET|POST /login
     */
    public function login(): void
    {
        // Si l'utilisateur est déjà connecté, redirection vers l'accueil
        if (isset($_SESSION['id'])) {
            header('Location: /ECF-leboncoin/');
            exit();
        }

        // Vérification si la requête est de type POST (formulaire soumis)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validation : vérifier que tous les champs obligatoires sont remplis
            if (empty($_POST['email']) || empty($_POST['password'])) {
                $_SESSION['error'] = "Tous les champs doivent être remplis !";
                $_SESSION['old_input'] = $_POST;
                header('Location: /ECF-leboncoin/login');
                exit();
            }

            // Nettoyage des données reçues
            $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
            $password = trim($_POST['password']);

            // Validation du format email
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $_SESSION['error'] = "Format d'email invalide !";
                $_SESSION['old_input'] = $_POST;
                header('Location: /ECF-leboncoin/login');
                exit();
            }

            // Tentative de connexion via le modèle
            $result = $this->userModel->login($email, $password);

            // Vérification du résultat de l'authentification
            if ($result['success']) {
                // Authentification réussie, on stocke les informations dans la session
                $user = $result['user']; // Objet User
                $_SESSION['success'] = "Heureux de vous revoir " . $user->getPseudo() . " !";
                $_SESSION['id'] = $user->getId();
                $_SESSION['pseudo'] = $user->getPseudo();
                $_SESSION['email'] = $user->getEmail();

                // Redirection vers la page d'accueil ou page demandée
                $redirectTo = $_SESSION['redirect_after_login'] ?? '/ECF-leboncoin/';
                unset($_SESSION['redirect_after_login']);

                header('Location: ' . $redirectTo);
                exit();
            } else {
                // Échec de l'authentification
                $_SESSION['error'] = $result['message'];
                $_SESSION['old_input'] = $_POST;
                header('Location: /ECF-leboncoin/login');
                exit();
            }
        }

        require __DIR__ . '/../view/auth/login.php';
    }

    /**
     * Méthode pour gérer la déconnexion
     * Détruit la session et redirige l'utilisateur
     * Route: GET /logout
     */
    public function logout(): void
    {
        // Sauvegarde du pseudo pour le message d'au revoir
        $pseudo = $_SESSION['pseudo'] ?? 'Utilisateur';

        // Détruire toutes les données de session
        session_unset();
        session_destroy();

        // Redémarrer une nouvelle session pour afficher le message de succès
        session_start();
        $_SESSION['success'] = "Au revoir {$pseudo} !";

        // Redirection vers la page d'accueil
        header('Location: /ECF-leboncoin/');
        exit();
    }

    /**
     * Méthode utilitaire pour vérifier si un utilisateur est connecté
     * Utilisée par d'autres contrôleurs pour protéger les routes
     */
    public static function requireLogin(string $redirectTo = '/ECF-leboncoin/login'): void
    {
        if (!isset($_SESSION['id'])) {
            $_SESSION['error'] = "Vous devez être connecté pour accéder à cette page.";
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'] ?? '/ECF-leboncoin/';
            header('Location: ' . $redirectTo);
            exit();
        }
    }

    /**
     * Méthode utilitaire pour vérifier la propriété d'une ressource
     * Vérifie que l'utilisateur connecté est bien le propriétaire
     */
    public static function requireOwnership(int $resourceOwnerId, string $errorMessage = "Vous n'êtes pas autorisé à effectuer cette action."): void
    {
        if (!isset($_SESSION['id']) || $_SESSION['id'] != $resourceOwnerId) {
            $_SESSION['error'] = $errorMessage;
            header('Location: /ECF-leboncoin/');
            exit();
        }
    }
}