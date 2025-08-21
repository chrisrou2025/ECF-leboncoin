<?php

/**
 * Classe ModelAnnonce - Gère les opérations liées aux annonces
 */
class ModelAnnonce extends ModelBase
{
    public function __construct()
    {
        parent::__construct();
    }

    public function beginTransaction(): void
    {
        $this->db->beginTransaction();
    }

    public function commit(): void
    {
        $this->db->commit();
    }

    public function rollBack(): void
    {
        $this->db->rollBack();
    }

    /**
     * Récupère le chemin de la première image d'une annonce
     */
    public function getFirstImagePath(int $annonceId): ?string
    {
        try {
            $sql = "SELECT path FROM images WHERE annonce_id = :annonce_id LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':annonce_id', $annonceId, PDO::PARAM_INT);
            $stmt->execute();
            $image = $stmt->fetch(PDO::FETCH_ASSOC);
            return $image ? $image['path'] : '/ECF-leboncoin/asset/img/default-annonce.jpg';
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération de l'image : " . $e->getMessage());
            return '/ECF-leboncoin/asset/img/default-annonce.jpg';
        }
    }

    /**
     * Récupère une annonce par ID avec détails
     */
    public function getAnnonceById(int $id): ?Annonce
    {
        try {
            $sql = "
                SELECT a.*, c.nom AS category_nom, u.pseudo AS user_pseudo
                FROM annonces a
                JOIN categories c ON a.category_id = c.id
                JOIN users u ON a.user_id = u.id
                WHERE a.id = :id
            ";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$data) {
                return null;
            }

            $annonce = new Annonce($data);

            // Chargement des images
            $images = $this->getImagesByAnnonceId($id);
            $annonce->setImages($images);

            return $annonce;
        } catch (PDOException $e) {
            error_log("Erreur récupération annonce : " . $e->getMessage());
            return null;
        }
    }

    /**
     * Récupère les images d'une annonce
     */
    public function getImagesByAnnonceId(int $annonceId): array
    {
        try {
            $sql = "SELECT * FROM images WHERE annonce_id = :annonce_id ORDER BY `order` ASC";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':annonce_id', $annonceId, PDO::PARAM_INT);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return array_map(fn($data) => new Image($data), $results);
        } catch (PDOException $e) {
            error_log("Erreur récupération images : " . $e->getMessage());
            return [];
        }
    }

    /**
     * Récupère les annonces d'un utilisateur
     */
    public function getAnnoncesByUserId(int $userId): array
    {
        try {
            $sql = "
                SELECT a.*, c.nom AS category_nom
                FROM annonces a
                JOIN categories c ON a.category_id = c.id
                WHERE a.user_id = :user_id
                ORDER BY a.created_at DESC
            ";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return array_map(function ($data) {
                $annonce = new Annonce($data);
                $images = $this->getImagesByAnnonceId($data['id']);
                $annonce->setImages($images);
                return $annonce;
            }, $results);
        } catch (PDOException $e) {
            error_log("Erreur récupération annonces utilisateur : " . $e->getMessage());
            return [];
        }
    }

    /**
     * Recherche des annonces par terme
     */
    public function searchAnnonces(string $searchTerm): array
    {
        try {
            $query = $this->db->prepare('
                SELECT a.*, c.nom AS category_nom, u.pseudo AS user_pseudo
                FROM annonces a
                JOIN categories c ON a.category_id = c.id
                JOIN users u ON a.user_id = u.id
                WHERE a.titre LIKE :searchTerm OR a.description LIKE :searchTerm
                ORDER BY a.created_at DESC
            ');
            $searchTerm = '%' . $searchTerm . '%';
            $query->bindParam(':searchTerm', $searchTerm, PDO::PARAM_STR);
            $query->execute();
            $results = $query->fetchAll(PDO::FETCH_ASSOC);
            return array_map(function ($data) {
                $annonce = new Annonce($data);
                $images = $this->getImagesByAnnonceId($data['id']);
                $annonce->setImages($images);
                return $annonce;
            }, $results);
        } catch (PDOException $e) {
            error_log("Erreur recherche annonces : " . $e->getMessage());
            return [];
        }
    }

    /**
     * Récupère les annonces récentes
     */
    public function getRecentAnnonces(int $limit = 5): array
    {
        try {
            $query = $this->db->prepare('
                SELECT a.*, c.nom AS category_nom, u.pseudo AS user_pseudo
                FROM annonces a
                JOIN categories c ON a.category_id = c.id
                JOIN users u ON a.user_id = u.id
                ORDER BY a.created_at DESC
                LIMIT :limit
            ');
            $query->bindParam(':limit', $limit, PDO::PARAM_INT);
            $query->execute();
            $results = $query->fetchAll(PDO::FETCH_ASSOC);
            return array_map(function ($data) {
                $annonce = new Annonce($data);
                $images = $this->getImagesByAnnonceId($data['id']);
                $annonce->setImages($images);
                return $annonce;
            }, $results);
        } catch (PDOException $e) {
            error_log("Erreur récupération annonces récentes : " . $e->getMessage());
            return [];
        }
    }

    /**
     * Insère une nouvelle annonce
     */
    public function insertAnnonce(Annonce $annonce): array
    {
        try {
            $query = $this->db->prepare('
                INSERT INTO annonces (titre, description, prix, category_id, etat, localite, marque, kilometrage, user_id)
                VALUES (:titre, :description, :prix, :category_id, :etat, :localite, :marque, :kilometrage, :user_id)
            ');
            $data = $annonce->toArray();
            $query->execute([
                ':titre' => $data['titre'],
                ':description' => $data['description'],
                ':prix' => $data['prix'],
                ':category_id' => $data['category_id'],
                ':etat' => $data['etat'],
                ':localite' => $data['localite'],
                ':marque' => $data['marque'],
                ':kilometrage' => $data['kilometrage'],
                ':user_id' => $data['user_id']
            ]);
            $id = $this->db->lastInsertId();
            $annonce->setId($id);
            return [
                'success' => true,
                'id' => $id,
                'message' => 'Annonce créée avec succès.'
            ];
        } catch (PDOException $e) {
            error_log("Erreur insertion annonce : " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erreur lors de la création de l\'annonce.',
                'errors' => [$e->getMessage()]
            ];
        }
    }

    /**
     * Met à jour une annonce existante
     */
    public function updateAnnonce(Annonce $annonce): bool
    {
        try {
            $query = $this->db->prepare('
                UPDATE annonces SET
                    titre = :titre,
                    description = :description,
                    prix = :prix,
                    category_id = :category_id,
                    etat = :etat,
                    localite = :localite,
                    marque = :marque,
                    kilometrage = :kilometrage,
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = :id AND user_id = :user_id
            ');
            $data = $annonce->toArray();
            $query->execute([
                ':titre' => $data['titre'],
                ':description' => $data['description'],
                ':prix' => $data['prix'],
                ':category_id' => $data['category_id'],
                ':etat' => $data['etat'],
                ':localite' => $data['localite'],
                ':marque' => $data['marque'],
                ':kilometrage' => $data['kilometrage'],
                ':id' => $annonce->getId(),
                ':user_id' => $annonce->getUserId()
            ]);
            return $query->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Erreur mise à jour annonce : " . $e->getMessage());
            return false;
        }
    }

    /**
     * Supprime une annonce
     */
    public function deleteAnnonce(int $id, int $userId): array
    {
        try {
            $this->beginTransaction();

            // Supprimer les images associées
            $this->deleteImagesByAnnonceId($id);

            // Supprimer l'annonce
            $query = $this->db->prepare('DELETE FROM annonces WHERE id = :id AND user_id = :user_id');
            $query->execute([
                ':id' => $id,
                ':user_id' => $userId
            ]);

            if ($query->rowCount() === 0) {
                throw new Exception('Annonce non trouvée ou non autorisée.');
            }

            $this->commit();
            return ['success' => true, 'message' => 'Annonce supprimée avec succès.'];
        } catch (Exception $e) {
            $this->rollBack();
            error_log("Erreur suppression annonce : " . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Supprime toutes les images d'une annonce
     */
    public function deleteImagesByAnnonceId(int $annonceId): void
    {
        try {
            $images = $this->getImagesByAnnonceId($annonceId);
            foreach ($images as $image) {
                $this->deleteImage($image->getId());
            }
        } catch (PDOException $e) {
            error_log("Erreur suppression images : " . $e->getMessage());
        }
    }

    /**
     * Supprime une image spécifique
     */
    public function deleteImage(int $imageId): bool
    {
        try {
            // Récupérer le chemin pour supprimer le fichier
            $sql = "SELECT path FROM images WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $imageId, PDO::PARAM_INT);
            $stmt->execute();
            $imageData = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($imageData) {
                $fullPath = $_SERVER['DOCUMENT_ROOT'] . $imageData['path'];
                if (file_exists($fullPath)) {
                    unlink($fullPath);
                }
            }

            // Supprimer de la base de données
            $sql = "DELETE FROM images WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $imageId, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erreur suppression image : " . $e->getMessage());
            return false;
        }
    }

    /**
     * Ajoute une image à une annonce
     */
    public function addImage(Image $image): bool
    {
        try {
            $query = $this->db->prepare('
                INSERT INTO images (annonce_id, path, `order`)
                VALUES (:annonce_id, :path, :order)
            ');
            $data = $image->toArray();
            return $query->execute([
                ':annonce_id' => $data['annonce_id'],
                ':path' => $data['path'],
                ':order' => $data['order']
            ]);
        } catch (PDOException $e) {
            error_log("Erreur ajout image : " . $e->getMessage());
            return false;
        }
    }

    /**
     * Valide les fichiers images uploadés
     */
    public function validateImages(array $files): array
    {
        $errors = [];
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $maxFileSize = 5 * 1024 * 1024; // 5MB
        $maxFiles = 4;

        if (empty($files['name'][0])) {
            return $errors;
        }

        $fileCount = 0;
        foreach ($files['name'] as $name) {
            if (!empty($name)) {
                $fileCount++;
            }
        }

        if ($fileCount > $maxFiles) {
            $errors[] = "Vous ne pouvez uploader que $maxFiles images maximum.";
            return $errors;
        }

        for ($i = 0; $i < count($files['name']); $i++) {
            if (empty($files['name'][$i])) {
                continue;
            }

            $fileName = $files['name'][$i];
            $fileSize = $files['size'][$i];
            $fileTmpName = $files['tmp_name'][$i];
            $fileError = $files['error'][$i];

            if ($fileError !== UPLOAD_ERR_OK) {
                $errors[] = "Erreur lors de l'upload du fichier '$fileName'.";
                continue;
            }

            if ($fileSize > $maxFileSize) {
                $errors[] = "Le fichier '$fileName' dépasse la taille maximale de 5MB.";
                continue;
            }

            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $fileTmpName);
            finfo_close($finfo);

            if (!in_array($mimeType, $allowedTypes)) {
                $errors[] = "Le type de fichier '$fileName' n'est pas autorisé.";
                continue;
            }

            if (!getimagesize($fileTmpName)) {
                $errors[] = "Le fichier '$fileName' n'est pas une image valide.";
            }
        }

        return $errors;
    }

    /**
     * Retourne le temps écoulé depuis la création de l'annonce
     */
    public function getTimeElapsed(string $createdAt): string
    {
        $date = new DateTime($createdAt);
        $now = new DateTime();
        $interval = $now->diff($date);

        if ($interval->y > 0) {
            return $interval->y . ' an' . ($interval->y > 1 ? 's' : '');
        } elseif ($interval->m > 0) {
            return $interval->m . ' mois';
        } elseif ($interval->d > 0) {
            return $interval->d . ' jour' . ($interval->d > 1 ? 's' : '');
        } elseif ($interval->h > 0) {
            return $interval->h . ' heure' . ($interval->h > 1 ? 's' : '');
        } else {
            return $interval->i . ' minute' . ($interval->i > 1 ? 's' : '');
        }
    }

    /**
     * Traite l'upload des images
     */
    public function processImageUploads(array $files, int $annonceId): array
    {
        $savedPaths = [];
        $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/ECF-leboncoin/asset/uploads/';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        if (!isset($files['name']) || empty($files['name'][0])) {
            return $savedPaths;
        }

        for ($i = 0; $i < count($files['name']); $i++) {
            if (empty($files['name'][$i]) || $files['error'][$i] !== UPLOAD_ERR_OK) {
                continue;
            }

            $fileName = $files['name'][$i];
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            $uniqueName = 'photo_' . $annonceId . '_' . uniqid() . '.' . $fileExtension;
            $destinationPath = $uploadDir . $uniqueName;

            if (move_uploaded_file($files['tmp_name'][$i], $destinationPath)) {
                $relativePath = '/ECF-leboncoin/asset/uploads/' . $uniqueName;
                $savedPaths[] = $relativePath;
            }
        }

        return $savedPaths;
    }
}
