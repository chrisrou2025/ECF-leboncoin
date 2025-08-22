<?php

/**
 * Contrôleur des Pages
 * Gère l'affichage de la page d'accueil avec système de filtrage
 */
class ControllerPage
{
    private $annonceModel;
    private $categorieModel;

    /**
     * Constructeur - Initialise les modèles nécessaires
     */
    public function __construct()
    {
        $this->annonceModel = new ModelAnnonce();
        $this->categorieModel = new ModelCategorie();
    }

    /**
     * Affiche la page d'accueil avec les annonces pour le filtrage JavaScript
     * Route: GET /
     */
    public function homePage(): void
    {
        $recentAnnonces = $this->annonceModel->getRecentAnnonces(20);
        $categories = $this->categorieModel->getAllCategories();
        $modelAnnonce = $this->annonceModel; // Passer le modèle à la vue

        $title = "Accueil - labonnetrouvaille";
        $page = 'homepage';

        ob_start();
        require __DIR__ . '/../view/page/homepage.php';
        $content = ob_get_clean();

        require __DIR__ . '/../view/base-html.php';
    }

    /**
     * Recherche d'annonces via AJAX
     * Route: GET /recherche?q=terme
     */
    public function searchAnnonces(): void
    {
        $searchTerm = $_GET['q'] ?? '';
        $searchTerm = trim($searchTerm);

        // Si le terme est vide, retourner toutes les annonces récentes
        if (empty($searchTerm)) {
            $annonces = $this->annonceModel->getRecentAnnonces(20);
        } else {
            // Rechercher les annonces correspondantes
            $annonces = $this->annonceModel->searchAnnonces($searchTerm);
        }

        // Si c'est une requête AJAX, retourner du JSON
        if (
            !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'
        ) {

            header('Content-Type: application/json');

            $results = [];
            foreach ($annonces as $annonce) {
                $results[] = [
                    'id' => $annonce->getId(),
                    'titre' => htmlspecialchars($annonce->getTitre()),
                    'prix' => $annonce->getPrix(),
                    'localite' => htmlspecialchars($annonce->getLocalite() ?? ''),
                    'kilometrage' => $annonce->getKilometrage(),
                    'marque' => htmlspecialchars($annonce->getMarque() ?? ''),
                    'user_pseudo' => htmlspecialchars($annonce->getUserPseudo() ?? 'Utilisateur inconnu'),
                    'user_id' => $annonce->getUserId(),
                    'category_id' => $annonce->getCategoryId(),
                    'category_nom' => htmlspecialchars($annonce->getCategoryNom() ?? ''),
                    'created_at' => $annonce->getCreatedAt(),
                    'image_principale' => $annonce->getImagePrincipale() ?? '/ECF-leboncoin/asset/img/default-annonce.jpg',
                    'is_vehicule' => $annonce->isVehicule(),
                    'is_maison_jardin' => $annonce->isMaisonJardin()
                ];
            }

            echo json_encode($results);
            exit;
        }

        // Sinon, afficher la page normale
        $categories = $this->categorieModel->getAllCategories();
        $recentAnnonces = $annonces; // Utiliser les résultats de la recherche
        $modelAnnonce = $this->annonceModel;

        $title = "Recherche - labonnetrouvaille";
        $page = 'homepage';

        ob_start();
        require __DIR__ . '/../view/page/homepage.php';
        $content = ob_get_clean();

        require __DIR__ . '/../view/base-html.php';
    }
}
