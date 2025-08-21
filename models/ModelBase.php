<?php

/**
 * Classe Modelbase - Classe de base pour les modèles, gère la connexion à la base de données
 */
abstract class ModelBase
{
    // Propriété protégée accessible aux classes enfants
    protected $db;

    // Propriété statique pour éviter les connexions multiples
    private static $instance = null;

    public function __construct()
    {
        // Initialise la connexion lors de la création de l'objet
        $this->db = $this->getDb();
    }

    private static function setDb()
    {
        try {
            self::$instance = new PDO('mysql:host=localhost;dbname=labonnetrouvaille', 'root', 'root');
            self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "Erreur de connexion à la base de données : " . $e->getMessage();
            error_log($e->getMessage());
            exit();
        }
    }

    protected function getDb()
    {
        // Si pas encore de connexion, on la crée
        if (self::$instance === null) {
            self::setDb();
        }

        return self::$instance;
    }
}
