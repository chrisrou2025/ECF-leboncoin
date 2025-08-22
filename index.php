<?php
header('Content-Type: text/html; charset=UTF-8');
session_start();
require __DIR__ . '/vendor/autoload.php';

$router = new AltoRouter();
$router->setBasePath('/ECF-leboncoin');

// Définition des routes
$router->map('GET', '/', 'ControllerPage#homePage', 'homepage');
$router->map('GET', '/recherche', 'ControllerPage#searchAnnonces', 'search_annonces');
$router->map('GET', '/annonce/create', 'ControllerAnnonce#createAnnonceForm', 'annonce_create_form');
$router->map('POST', '/annonce/store', 'ControllerAnnonce#storeAnnonce', 'annonce_store');
$router->map('GET', '/annonce/[i:id]', 'ControllerAnnonce#showAnnonce', 'annonce_show');
$router->map('GET', '/annonce/[i:id]/edit', 'ControllerAnnonce#editAnnonceForm', 'annonce_edit_form');
$router->map('POST', '/annonce/[i:id]/update', 'ControllerAnnonce#updateAnnonce', 'annonce_update');
$router->map('POST', '/annonce/[i:id]/delete', 'ControllerAnnonce#deleteAnnonce', 'annonce_delete');
$router->map('GET', '/user/[i:id]', 'ControllerUser#oneUserById', 'user_profile');
$router->map('GET', '/user/mes-annonces', 'ControllerUser#mesAnnonces', 'user_annonces');
$router->map('GET|POST', '/register', 'ControllerAuth#register', 'user_register');
$router->map('GET|POST', '/login', 'ControllerAuth#login', 'user_login');
$router->map('GET', '/logout', 'ControllerAuth#logout', 'user_logout');
$router->map('GET', '/compte', 'ControllerUser#accountPage', 'user_account');
$router->map('POST', '/compte/update-pseudo', 'ControllerUser#updatePseudo', 'user_update_pseudo');
$router->map('POST', '/compte/update-email', 'ControllerUser#updateEmail', 'user_update_email');
$router->map('POST', '/compte/update-password', 'ControllerUser#updatePassword', 'user_update_password');
$router->map('GET', '/user/[i:id]', 'ControllerUser#oneUserById', 'user_show');

// Cherche une correspondance pour l'URL actuelle
$match = $router->match();

// Définir $page après le match
$page = is_array($match) ? $match['name'] : null;

// Vérifie si une route correspondante a été trouvée
if (is_array($match)) {
    list($controller, $action) = explode('#', $match['target']);
    $obj = new $controller();
    if (is_callable(array($obj, $action))) {
        call_user_func_array(array($obj, $action), array($match['params']));
    } else {
        http_response_code(404);
        $page = '404'; // Définir $page pour la page 404
        $title = "Erreur 404 - Page non trouvée";
        $content = "<h1>Erreur : Action non trouvée dans le contrôleur.</h1>";
        require __DIR__ . '/view/base-html.php';
    }
} else {
    http_response_code(404);
    $page = '404'; // Définir $page pour la page 404
    $title = "Erreur 404 - Page non trouvée";
    $content = "<h1>Erreur 404 : Page non trouvée.</h1>";
    require __DIR__ . '/view/base-html.php';
}
