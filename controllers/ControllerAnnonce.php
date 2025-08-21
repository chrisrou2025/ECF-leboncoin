<?php

/**
 * Contrôleur des Annonces
 * Gère toutes les opérations CRUD pour les annonces
 */
class ControllerAnnonce
{
    private ModelAnnonce $annonceModel;
    private ModelCategorie $categorieModel;

    private const MAX_IMAGES_PER_ANNONCE = 4;

    /**
     * Constructeur - Initialise les modèles nécessaires
     */
    public function __construct()
    {
        $this->annonceModel = new ModelAnnonce();
        $this->categorieModel = new ModelCategorie();
    }

    public function createAnnonceForm(): void
    {
        if (!isset($_SESSION['id'])) {
            $_SESSION['error'] = "Vous devez être connecté pour créer une annonce.";
            header('Location: /ECF-leboncoin/login');
            exit();
        }
        $categories = $this->categorieModel->getAllCategories();
        $annonce = new Annonce();
        $etats = [
            'neuf' => 'Neuf',
            'tres_bon' => 'Très bon état',
            'bon' => 'Bon état',
            'satisfaisant' => 'Satisfaisant'
        ];
        $title = "Créer une Annonce - labonnetrouvaille";
        $page = 'create';
        ob_start();
        require __DIR__ . '/../view/annonce/create.php';
        $content = ob_get_clean();
        require __DIR__ . '/../view/base-html.php';
    }

    public function storeAnnonce(): void
    {
        if (!isset($_SESSION['id'])) {
            $_SESSION['error'] = "Vous devez être connecté pour créer une annonce.";
            header('Location: /ECF-leboncoin/login');
            exit();
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'titre' => trim($_POST['titre'] ?? ''),
                'description' => trim($_POST['description'] ?? ''),
                'prix' => floatval($_POST['prix'] ?? 0),
                'kilometrage' => isset($_POST['kilometrage']) && $_POST['kilometrage'] !== '' ? intval($_POST['kilometrage']) : null,
                'localite' => trim($_POST['localite'] ?? ''),
                'marque' => trim($_POST['marque'] ?? ''),
                'category_id' => intval($_POST['category_id'] ?? 0),
                'etat' => trim($_POST['etat'] ?? ''),
                'user_id' => $_SESSION['id']
            ];
            $annonce = new Annonce($data);
            $errors = $annonce->getValidationErrors();
            if ($annonce->getPrix() < 0) {
                $errors[] = "Le prix ne peut pas être négatif.";
            }
            if (!$this->categorieModel->categoryExists($annonce->getCategoryId())) {
                $errors[] = "Catégorie invalide.";
            }
            $imageErrors = $this->annonceModel->validateImages($_FILES['photos'] ?? []);
            if (!empty($imageErrors)) {
                $errors = array_merge($errors, $imageErrors);
            }
            if (!empty($errors)) {
                $_SESSION['error'] = "Erreurs de validation :";
                $_SESSION['validation_errors'] = $errors;
                $_SESSION['old_input'] = $_POST;
                header('Location: /ECF-leboncoin/annonce/create');
                exit();
            }
            try {
                $this->annonceModel->beginTransaction();
                $result = $this->annonceModel->insertAnnonce($annonce);
                if (!$result['success']) {
                    throw new Exception($result['message']);
                }
                $annonceId = $result['id'];
                if (isset($_FILES['photos']) && !empty($_FILES['photos']['name'][0])) {
                    $imagePaths = $this->annonceModel->processImageUploads($_FILES['photos'], $annonceId);
                    foreach ($imagePaths as $index => $path) {
                        $image = new Image([
                            'annonce_id' => $annonceId,
                            'path' => $path,
                            'order' => $index
                        ]);
                        $this->annonceModel->addImage($image);
                    }
                }
                $this->annonceModel->commit();
                $_SESSION['success'] = "Annonce créée avec succès.";
                header('Location: /ECF-leboncoin/annonce/' . $annonceId);
                exit();
            } catch (Exception $e) {
                $this->annonceModel->rollBack();
                $_SESSION['error'] = "Erreur lors de la création de l'annonce : " . $e->getMessage();
                $_SESSION['old_input'] = $_POST;
                header('Location: /ECF-leboncoin/annonce/create');
                exit();
            }
        } else {
            $_SESSION['error'] = "Méthode non autorisée.";
            header('Location: /ECF-leboncoin/annonce/create');
            exit();
        }
    }

    public function showAnnonce(array $params): void
    {
        $id = (int)$params['id'];
        $annonce = $this->annonceModel->getAnnonceById($id);
        if (!$annonce) {
            http_response_code(404);
            $title = "Annonce non trouvée";
            $page = '404';
            require __DIR__ . '/../view/base-html.php';
            return;
        }
        $images = $this->annonceModel->getImagesByAnnonceId($id);
        $timeElapsed = $this->annonceModel->getTimeElapsed($annonce->getCreatedAt());
        $etats = [
            'neuf' => 'Neuf',
            'tres_bon' => 'Très bon état',
            'bon' => 'Bon état',
            'satisfaisant' => 'Satisfaisant'
        ];
        $title = htmlspecialchars($annonce->getTitre()) . " - labonnetrouvaille";
        $page = 'show';
        ob_start();
        require __DIR__ . '/../view/annonce/show.php';
        $content = ob_get_clean();
        require __DIR__ . '/../view/base-html.php';
    }

    public function editAnnonceForm(array $params): void
    {
        $id = (int)$params['id'];
        if (!isset($_SESSION['id'])) {
            $_SESSION['error'] = "Vous devez être connecté pour modifier une annonce.";
            header('Location: /ECF-leboncoin/login');
            exit();
        }
        $annonce = $this->annonceModel->getAnnonceById($id);
        if (!$annonce) {
            $_SESSION['error'] = "Annonce non trouvée.";
            header('Location: /ECF-leboncoin/user/mes-annonces');
            exit();
        }
        if ($annonce->getUserId() != $_SESSION['id']) {
            $_SESSION['error'] = "Vous n'êtes pas autorisé à modifier cette annonce.";
            header('Location: /ECF-leboncoin/user/mes-annonces');
            exit();
        }
        $categories = $this->categorieModel->getAllCategories();
        $etats = [
            'neuf' => 'Neuf',
            'tres_bon' => 'Très bon état',
            'bon' => 'Bon état',
            'satisfaisant' => 'Satisfaisant'
        ];
        $title = "Modifier l'Annonce - labonnetrouvaille";
        $page = 'edit';
        ob_start();
        require __DIR__ . '/../view/annonce/edit.php';
        $content = ob_get_clean();
        require __DIR__ . '/../view/base-html.php';
    }

    /**
     * Met à jour une annonce existante
     * Route: POST /annonce/[i:id]/update
     */
    public function updateAnnonce(array $params): void
    {
        $id = (int)$params['id'];

        if (!isset($_SESSION['id'])) {
            $_SESSION['error'] = "Vous devez être connecté pour modifier une annonce.";
            header('Location: /ECF-leboncoin/login');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error'] = "Méthode non autorisée.";
            header('Location: /ECF-leboncoin/annonce/' . $id . '/edit');
            exit();
        }

        $annonce = $this->annonceModel->getAnnonceById($id);

        if (!$annonce) {
            $_SESSION['error'] = "Annonce non trouvée.";
            header('Location: /ECF-leboncoin/user/mes-annonces');
            exit();
        }

        if ($annonce->getUserId() != $_SESSION['id']) {
            $_SESSION['error'] = "Vous n'êtes pas autorisé à modifier cette annonce.";
            header('Location: /ECF-leboncoin/user/mes-annonces');
            exit();
        }

        $data = [
            'titre' => trim($_POST['titre'] ?? $annonce->getTitre()),
            'description' => trim($_POST['description'] ?? $annonce->getDescription()),
            'prix' => floatval($_POST['prix'] ?? $annonce->getPrix()),
            'kilometrage' => isset($_POST['kilometrage']) && $_POST['kilometrage'] !== '' ? intval($_POST['kilometrage']) : $annonce->getKilometrage(),
            'localite' => trim($_POST['localite'] ?? $annonce->getLocalite()),
            'marque' => trim($_POST['marque'] ?? $annonce->getMarque()),
            'category_id' => intval($_POST['category_id'] ?? $annonce->getCategoryId()),
            'etat' => trim($_POST['etat'] ?? $annonce->getEtat()),
            'user_id' => $_SESSION['id']
        ];

        $annonce->hydrate($data);

        $errors = $annonce->getValidationErrors();

        if ($annonce->getPrix() < 0) {
            $errors[] = "Le prix ne peut pas être négatif.";
        }
        if (!$this->categorieModel->categoryExists($annonce->getCategoryId())) {
            $errors[] = "Catégorie invalide.";
        }
        $imageErrors = $this->annonceModel->validateImages($_FILES['photos'] ?? []);
        if (!empty($imageErrors)) {
            $errors = array_merge($errors, $imageErrors);
        }

        if (!empty($errors)) {
            $_SESSION['error'] = "Erreurs de validation :";
            $_SESSION['validation_errors'] = $errors;
            $_SESSION['old_input'] = $_POST;
            header('Location: /ECF-leboncoin/annonce/' . $id . '/edit');
            exit();
        }

        try {
            $this->annonceModel->beginTransaction();
            $this->annonceModel->updateAnnonce($annonce);

            $imagesToDeleteString = $_POST['images_to_delete'] ?? '';
            
            if (!empty($imagesToDeleteString)) {
                $imagesToDelete = explode(',', $imagesToDeleteString);
                
                foreach ($imagesToDelete as $imageId) {
                    if (!empty($imageId)) { // Sécurité supplémentaire
                        $this->annonceModel->deleteImage((int)$imageId);
                    }
                }
            }

            // Ajout de nouvelles images si présentes
            if (isset($_FILES['photos']) && !empty($_FILES['photos']['name'][0])) {
                $imagePaths = $this->annonceModel->processImageUploads($_FILES['photos'], $id);
                foreach ($imagePaths as $index => $path) {
                    $image = new Image([
                        'annonce_id' => $id,
                        'path' => $path,
                        'order' => $index
                    ]);
                    $this->annonceModel->addImage($image);
                }
            }

            $this->annonceModel->commit();
            $_SESSION['success'] = "L'annonce a été modifiée avec succès.";
            header('Location: /ECF-leboncoin/annonce/' . $id);
            exit();
        } catch (Exception $e) {
            $this->annonceModel->rollBack();
            $_SESSION['error'] = "Une erreur est survenue lors de la mise à jour : " . $e->getMessage();
            error_log("Erreur lors de la mise à jour de l'annonce : " . $e->getMessage());
            header('Location: /ECF-leboncoin/annonce/' . $id . '/edit');
            exit();
        }
    }

    public function deleteAnnonce(array $params): void
    {
        $id = (int)$params['id'];
        if (!isset($_SESSION['id'])) {
            $_SESSION['error'] = "Vous devez être connecté pour supprimer une annonce.";
            header('Location: /ECF-leboncoin/login');
            exit();
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error'] = "Méthode non autorisée.";
            header('Location: /ECF-leboncoin/user/mes-annonces');
            exit();
        }
        $annonce = $this->annonceModel->getAnnonceById($id);
        if (!$annonce) {
            $_SESSION['error'] = "Annonce non trouvée.";
            header('Location: /ECF-leboncoin/user/mes-annonces');
            exit();
        }
        if ($annonce->getUserId() != $_SESSION['id']) {
            $_SESSION['error'] = "Vous n'êtes pas autorisé à supprimer cette annonce.";
            header('Location: /ECF-leboncoin/user/mes-annonces');
            exit();
        }
        try {
            $result = $this->annonceModel->deleteAnnonce($id, $_SESSION['id']);
            if ($result['success']) {
                $_SESSION['success'] = $result['message'];
            } else {
                $_SESSION['error'] = $result['message'];
            }
        } catch (Exception $e) {
            error_log("Erreur lors de la suppression de l'annonce : " . $e->getMessage());
            $_SESSION['error'] = "Une erreur est survenue lors de la suppression.";
        }
        header('Location: /ECF-leboncoin/user/mes-annonces');
        exit();
    }
}