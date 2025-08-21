<?php

/**
 * Entité Image
 * Représente une image associée à une annonce
 */
class Image
{
    private ?int $id = null;
    private int $annonceId = 0;
    private string $nomFichier = '';
    private string $path = '';
    private int $order = 0;
    private ?string $createdAt = null;

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
            'annonce_id' => $this->annonceId,
            'nom_fichier' => $this->nomFichier,
            'path' => $this->path,
            'order' => $this->order,
            'created_at' => $this->createdAt
        ];
    }

    // Getters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAnnonceId(): int
    {
        return $this->annonceId;
    }

    public function getNomFichier(): string
    {
        return $this->nomFichier;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getOrder(): int
    {
        return $this->order;
    }

    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    // Setters
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function setAnnonceId(int $annonceId): void
    {
        $this->annonceId = $annonceId;
    }

    public function setNomFichier(string $nomFichier): void
    {
        $this->nomFichier = trim($nomFichier);
    }

    public function setPath(string $path): void
    {
        $this->path = trim($path);
    }

    public function setOrder(int $order): void
    {
        $this->order = max(0, $order);
    }

    public function setCreatedAt(?string $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    // Méthodes métier
    public function isValid(): bool
    {
        return $this->annonceId > 0
            && !empty($this->path)
            && $this->isValidImagePath();
    }

    public function getValidationErrors(): array
    {
        $errors = [];

        if ($this->annonceId <= 0) {
            $errors[] = 'Une annonce doit être associée';
        }

        if (empty($this->path)) {
            $errors[] = 'Le chemin de l\'image est obligatoire';
        } elseif (!$this->isValidImagePath()) {
            $errors[] = 'Le chemin de l\'image n\'est pas valide';
        }

        return $errors;
    }

    public function isValidImagePath(): bool
    {
        $validExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $extension = strtolower(pathinfo($this->path, PATHINFO_EXTENSION));
        return in_array($extension, $validExtensions);
    }

    public function getExtension(): string
    {
        return strtolower(pathinfo($this->path, PATHINFO_EXTENSION));
    }

    public function isPrincipale(): bool
    {
        return $this->order === 0;
    }

    public function getBaseName(): string
    {
        return basename($this->path);
    }

    public function exists(): bool
    {
        $fullPath = $_SERVER['DOCUMENT_ROOT'] . $this->path;
        return file_exists($fullPath);
    }

    public function deleteFile(): bool
    {
        if ($this->exists()) {
            $fullPath = $_SERVER['DOCUMENT_ROOT'] . $this->path;
            return unlink($fullPath);
        }
        return true;
    }

    public function getFileSize(): int
    {
        if ($this->exists()) {
            $fullPath = $_SERVER['DOCUMENT_ROOT'] . $this->path;
            return filesize($fullPath);
        }
        return 0;
    }

    public function getFormattedFileSize(): string
    {
        $bytes = $this->getFileSize();

        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return round($bytes / 1024, 2) . ' KB';
        }

        return $bytes . ' bytes';
    }
}
