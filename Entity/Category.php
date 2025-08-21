<?php

/**
 * Entité Category
 * Représente une catégorie d'annonces
 */
class Category
{
    private ?int $id = null;
    private string $nom;
    private ?string $description = null;
    private ?string $createdAt = null;
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
     * Convertit l'objet en tableau
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'nom' => $this->nom,
            'description' => $this->description,
            'created_at' => $this->createdAt,
            'annonces' => $this->annonces
        ];
    }

    // Getters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): string
    {
        return $this->nom ?? '';
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
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

    public function setNom(string $nom): void
    {
        $this->nom = trim($nom);
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description ? trim($description) : null;
    }

    public function setCreatedAt(?string $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function setAnnonces(array $annonces): void
    {
        $this->annonces = $annonces;
    }

    // Méthodes métier

    /**
     * Vérifie si la catégorie est valide
     */
    public function isValid(): bool
    {
        return !empty($this->nom);
    }

    /**
     * Retourne les erreurs de validation
     */
    public function getValidationErrors(): array
    {
        $errors = [];

        if (empty($this->nom)) {
            $errors[] = 'Le nom de la catégorie est obligatoire';
        } elseif (strlen($this->nom) < 2) {
            $errors[] = 'Le nom doit contenir au moins 2 caractères';
        }

        return $errors;
    }

    /**
     * Compte le nombre d'annonces dans cette catégorie
     */
    public function getNombreAnnonces(): int
    {
        return count($this->annonces);
    }

    /**
     * Vérifie si la catégorie a des annonces
     */
    public function hasAnnonces(): bool
    {
        return !empty($this->annonces);
    }

    /**
     * Vérifie si c'est la catégorie Véhicules
     */
    public function isVehicules(): bool
    {
        return $this->id === 4;
    }

    /**
     * Vérifie si c'est la catégorie Maison & Jardin
     */
    public function isMaisonJardin(): bool
    {
        return $this->id === 2;
    }
}
