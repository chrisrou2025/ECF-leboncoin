<?php

/**
 * Classe ModelUser - Gère les opérations liées aux utilisateurs
 */
class ModelUser extends ModelBase
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Inscription d'un nouvel utilisateur
     */
    public function register(User $user): array
    {
        try {
            if ($this->emailExists($user->getEmail())) {
                return [
                    'success' => false,
                    'message' => 'Cet email est déjà enregistré.'
                ];
            }

            $hashedPassword = password_hash($user->getPassword(), PASSWORD_DEFAULT);
            $sql = "INSERT INTO users (pseudo, email, password) VALUES (:pseudo, :email, :password)";
            $stmt = $this->db->prepare($sql);
            $data = $user->toArray();
            $stmt->execute([
                ':pseudo' => $data['pseudo'],
                ':email' => $data['email'],
                ':password' => $hashedPassword
            ]);

            $user->setId($this->db->lastInsertId());
            return [
                'success' => true,
                'message' => 'Inscription réussie ! Vous pouvez maintenant vous connecter.',
                'user_id' => $user->getId()
            ];
        } catch (PDOException $e) {
            error_log("Erreur lors de l'inscription : " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erreur lors de l\'inscription : ' . $e->getMessage()
            ];
        }
    }

    /**
     * Connexion d'un utilisateur
     */
    public function login(string $email, string $password): array
    {
        try {
            $sql = "SELECT * FROM users WHERE email = :email";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            $userData = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($userData && password_verify($password, $userData['password'])) {
                $user = new User($userData);
                return [
                    'success' => true,
                    'message' => 'Connexion réussie !',
                    'user' => $user
                ];
            }
            return [
                'success' => false,
                'message' => 'Email ou mot de passe incorrect'
            ];
        } catch (PDOException $e) {
            error_log("Erreur de connexion : " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erreur de connexion : ' . $e->getMessage()
            ];
        }
    }

    /**
     * Vérifier si un email existe déjà
     */
    private function emailExists(string $email, ?int $excludeUserId = null): bool
    {
        try {
            $sql = "SELECT COUNT(*) FROM users WHERE email = :email";
            $params = ['email' => $email];
            if ($excludeUserId) {
                $sql .= " AND id != :user_id";
                $params['user_id'] = $excludeUserId;
            }
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Erreur lors de la vérification de l'email : " . $e->getMessage());
            return false;
        }
    }

    /**
     * Récupérer un utilisateur par ID
     */
    public function getUserById(int $id): ?User
    {
        try {
            $sql = "SELECT * FROM users WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $userData = $stmt->fetch(PDO::FETCH_ASSOC);
            return $userData ? new User($userData) : null;
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération de l'utilisateur : " . $e->getMessage());
            return null;
        }
    }

    /**
     * Mettre à jour les informations d'un utilisateur
     */
    public function updateUser(User $user): array
    {
        try {
            $data = $user->toArray();
            if ($this->emailExists($data['email'], $data['id'])) {
                return ['success' => false, 'message' => 'Cette adresse email est déjà utilisée par un autre utilisateur.'];
            }

            $sql = "UPDATE users SET pseudo = :pseudo, email = :email WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':pseudo' => $data['pseudo'],
                ':email' => $data['email'],
                ':id' => $data['id']
            ]);
            return ['success' => true, 'message' => 'Mise à jour réussie.'];
        } catch (PDOException $e) {
            error_log("Erreur lors de la mise à jour : " . $e->getMessage());
            return ['success' => false, 'message' => 'Erreur lors de la mise à jour : ' . $e->getMessage()];
        }
    }

    /**
     * Mettre à jour le mot de passe de l'utilisateur
     */
    public function updatePassword(int $userId, string $currentPassword, string $newPassword): array
    {
        try {
            $sql = "SELECT password FROM users WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
            $stmt->execute();
            $userData = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$userData || !password_verify($currentPassword, $userData['password'])) {
                return ['success' => false, 'message' => 'Le mot de passe actuel est incorrect.'];
            }

            $hashedNewPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $sql = "UPDATE users SET password = :password WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':password' => $hashedNewPassword,
                ':id' => $userId
            ]);
            return ['success' => true, 'message' => 'Mot de passe mis à jour avec succès.'];
        } catch (PDOException $e) {
            error_log("Erreur lors de la mise à jour du mot de passe : " . $e->getMessage());
            return ['success' => false, 'message' => 'Erreur lors de la mise à jour du mot de passe : ' . $e->getMessage()];
        }
    }
}