<?php

/**
 * Entité Annonce
 * Représente une annonce avec ses propriétés et méthodes métier
 */
class Annonce
{
    private ?int $id = null;
    private string $titre = ''; // Valeur par défaut
    private string $description = ''; // Valeur par défaut
    private float $prix = 0.0; // Valeur par défaut
    private ?int $kilometrage = null;
    private ?string $localite = null;
    private ?string $marque = null;
    private string $etat = ''; // Valeur par défaut
    private int $categoryId = 0; // Valeur par défaut
    private int $userId = 0; // Valeur par défaut
    private ?string $createdAt = null;
    private ?string $updatedAt = null;

    // Propriétés pour les relations
    private ?string $categoryNom = null;
    private ?string $userPseudo = null;
    private array $images = [];

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
            'titre' => $this->titre,
            'description' => $this->description,
            'prix' => $this->prix,
            'kilometrage' => $this->kilometrage,
            'localite' => $this->localite,
            'marque' => $this->marque,
            'etat' => $this->etat,
            'category_id' => $this->categoryId,
            'user_id' => $this->userId,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
            'category_nom' => $this->categoryNom,
            'user_pseudo' => $this->userPseudo,
            'images' => $this->images
        ];
    }

    // Getters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): string
    {
        return $this->titre;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getPrix(): float
    {
        return $this->prix;
    }

    public function getKilometrage(): ?int
    {
        return $this->kilometrage;
    }

    public function getLocalite(): ?string
    {
        return $this->localite;
    }

    public function getMarque(): ?string
    {
        return $this->marque;
    }

    public function getEtat(): string
    {
        return $this->etat;
    }

    public function getCategoryId(): int
    {
        return $this->categoryId;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?string
    {
        return $this->updatedAt;
    }

    public function getCategoryNom(): ?string
    {
        return $this->categoryNom;
    }

    public function getUserPseudo(): ?string
    {
        return $this->userPseudo;
    }

    public function getImages(): array
    {
        return $this->images;
    }

    // Setters
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function setTitre(string $titre): void
    {
        $this->titre = trim($titre);
    }

    public function setDescription(string $description): void
    {
        $this->description = trim($description);
    }

    public function setPrix(float $prix): void
    {
        $this->prix = $prix;
    }

    public function setKilometrage(?int $kilometrage): void
    {
        $this->kilometrage = $kilometrage;
    }

    public function setLocalite(?string $localite): void
    {
        $this->localite = $localite;
    }

    public function setMarque(?string $marque): void
    {
        $this->marque = $marque;
    }

    public function setEtat(string $etat): void
    {
        $this->etat = $etat;
    }

    public function setCategoryId(int $categoryId): void
    {
        $this->categoryId = $categoryId;
    }

    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    public function setCreatedAt(?string $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function setUpdatedAt(?string $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function setCategoryNom(?string $categoryNom): void
    {
        $this->categoryNom = $categoryNom;
    }

    public function setUserPseudo(?string $userPseudo): void
    {
        $this->userPseudo = $userPseudo;
    }

    public function setImages(array $images): void
    {
        $this->images = $images;
    }

    // Méthodes métier

    /**
     * Vérifie si l'annonce est valide pour la sauvegarde
     */
    public function isValid(): bool
    {
        return !empty($this->titre)
            && !empty($this->description)
            && $this->prix >= 0
            && $this->categoryId > 0
            && $this->userId > 0;
    }

    /**
     * Retourne les erreurs de validation
     */
    public function getValidationErrors(): array
    {
        $errors = [];

        if (empty($this->titre)) {
            $errors[] = 'Le titre est obligatoire';
        }

        if (empty($this->description)) {
            $errors[] = 'La description est obligatoire';
        }

        if ($this->prix < 0) {
            $errors[] = 'Le prix doit être positif';
        }

        if ($this->categoryId <= 0) {
            $errors[] = 'Une catégorie doit être sélectionnée';
        }

        if ($this->userId <= 0) {
            $errors[] = 'Un utilisateur doit être associé';
        }

        return $errors;
    }

    /**
     * Retourne le prix formaté
     */
    public function getPrixFormate(): string
    {
        return number_format($this->prix, 2, ',', ' ') . ' €';
    }

    /**
     * Vérifie si l'annonce concerne un véhicule
     */
    public function isVehicule(): bool
    {
        return $this->categoryId === 4;
    }

    /**
     * Vérifie si l'annonce concerne la maison & jardin
     */
    public function isMaisonJardin(): bool
    {
        return $this->categoryId === 2;
    }

    /**
     * Retourne l'image principale
     */
    public function getImagePrincipale(): ?string
    {
        if (!empty($this->images)) {
            if ($this->images[0] instanceof Image) {
                return $this->images[0]->getPath();
            } elseif (is_array($this->images[0])) {
                return $this->images[0]['path'] ?? null;
            } elseif (is_string($this->images[0])) {
                return $this->images[0];
            }
        }
        return null;
    }

    /**
     * Retourne un extrait de la description
     */
    public function getDescriptionExtrait(int $longueur = 100): string
    {
        if (strlen($this->description) <= $longueur) {
            return $this->description;
        }
        return substr($this->description, 0, $longueur) . '...';
    }
}