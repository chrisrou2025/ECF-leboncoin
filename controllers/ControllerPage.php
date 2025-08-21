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
}
