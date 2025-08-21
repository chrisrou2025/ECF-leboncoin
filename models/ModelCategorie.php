<?php

/**
 * Classe ModelCategorie - Gère les opérations liées aux catégories
 */
class ModelCategorie extends ModelBase
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Récupère une catégorie par son ID
     */
    public function getCategoryById(int $categoryId): ?Category
    {
        try {
            $sql = "SELECT * FROM categories WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $categoryId, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ? new Category($result) : null;
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération de la catégorie : " . $e->getMessage());
            return null;
        }
    }

    /**
     * Récupère toutes les catégories
     */
    public function getAllCategories(): array
    {
        static $categoriesCache = null;
        if ($categoriesCache !== null) {
            return $categoriesCache;
        }
        try {
            $sql = "SELECT id, nom FROM categories ORDER BY nom ASC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $categoriesCache = array_map(fn($data) => new Category($data), $results);
            return $categoriesCache;
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération des catégories : " . $e->getMessage());
            return [];
        }
    }

    /**
     * Récupère toutes les catégories avec le nombre d'annonces
     */
    public function getCategoriesWithCount(): array
    {
        try {
            $sql = "SELECT c.id, c.nom, COUNT(a.id) as nb_annonces 
                    FROM categories c 
                    LEFT JOIN annonces a ON c.id = a.category_id 
                    GROUP BY c.id, c.nom 
                    ORDER BY c.nom ASC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return array_map(function ($data) {
                $category = new Category($data);
                $category->setAnnonces(array_fill(0, $data['nb_annonces'], null));
                return $category;
            }, $results);
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération des catégories avec compteur : " . $e->getMessage());
            return [];
        }
    }

    /**
     * Vérifie si une catégorie existe
     */
    public function categoryExists(int $categoryId): bool
    {
        try {
            $sql = "SELECT COUNT(*) FROM categories WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $categoryId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Erreur lors de la vérification de la catégorie : " . $e->getMessage());
            return false;
        }
    }

    /**
     * Récupère le nom d'une catégorie par son ID
     */
    public function getCategoryName(int $categoryId): ?string
    {
        $category = $this->getCategoryById($categoryId);
        return $category ? $category->getNom() : null;
    }

    /**
     * Invalidate le cache des catégories
     */
    protected function clearCache(): void
    {
        $categoriesCache = null;
    }
}