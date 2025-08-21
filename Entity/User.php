<?php

/**
 * Entité User
 * Représente un utilisateur du système
 */
class User
{
    private ?int $id = null;
    private string $pseudo;
    private string $email;
    private string $password;
    private array $annonces = [];

    public function __construct(array $data = [])
    {
        $this->hydrate($data);
    }

    /**
     * Hydrate l'objet avec un tableau de données
     */
    public function hydrate(array $data): void
    {
        foreach ($data as $key => $value) {
            $method = 'set' . str_replace('_', '', ucwords($key, '_'));
            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }
    }

    /**
     * Convertit l'objet en tableau (sans le mot de passe)
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'pseudo' => $this->pseudo,
            'email' => $this->email,
            'annonces' => $this->annonces
        ];
    }

    /**
     * Convertit l'objet en tableau pour la sauvegarde
     */
    public function toArrayForSave(): array
    {
        return [
            'id' => $this->id,
            'pseudo' => $this->pseudo,
            'email' => $this->email,
            'password' => $this->password
        ];
    }

    // Getters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPseudo(): string
    {
        return $this->pseudo ?? '';
    }

    public function getEmail(): string
    {
        return $this->email ?? '';
    }

    public function getPassword(): string
    {
        return $this->password ?? '';
    }

    public function getAnnonces(): array
    {
        return $this->annonces;
    }

    // Setters
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function setPseudo(string $pseudo): void
    {
        $this->pseudo = trim($pseudo);
    }

    public function setEmail(string $email): void
    {
        $this->email = strtolower(trim($email));
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function setAnnonces(array $annonces): void
    {
        $this->annonces = $annonces;
    }

    // Méthodes métier

    /**
     * Vérifie si l'utilisateur est valide
     */
    public function isValid(): bool
    {
        return !empty($this->pseudo)
            && $this->isValidEmail()
            && strlen($this->password) >= 3;
    }

    /**
     * Retourne les erreurs de validation
     */
    public function getValidationErrors(): array
    {
        $errors = [];

        if (empty($this->pseudo)) {
            $errors[] = 'Le pseudo est obligatoire';
        } elseif (strlen($this->pseudo) < 2) {
            $errors[] = 'Le pseudo doit contenir au moins 2 caractères';
        }

        if (empty($this->email)) {
            $errors[] = 'L\'email est obligatoire';
        } elseif (!$this->isValidEmail()) {
            $errors[] = 'L\'email n\'est pas valide';
        }

        if (empty($this->password)) {
            $errors[] = 'Le mot de passe est obligatoire';
        } elseif (strlen($this->password) < 3) {
            $errors[] = 'Le mot de passe doit contenir au moins 3 caractères';
        }

        return $errors;
    }

    /**
     * Vérifie si l'email est valide
     */
    public function isValidEmail(): bool
    {
        return filter_var($this->email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Hash le mot de passe
     */
    public function hashPassword(): void
    {
        if (!empty($this->password)) {
            $this->password = password_hash($this->password, PASSWORD_DEFAULT);
        }
    }

    /**
     * Vérifie un mot de passe
     */
    public function verifyPassword(string $password): bool
    {
        return password_verify($password, $this->password);
    }

    /**
     * Compte le nombre d'annonces
     */
    public function getNombreAnnonces(): int
    {
        return count($this->annonces);
    }

    /**
     * Vérifie si l'utilisateur a des annonces
     */
    public function hasAnnonces(): bool
    {
        return !empty($this->annonces);
    }
}
