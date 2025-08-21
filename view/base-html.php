<?php
/**
 * Page de base
 * Contient la structure HTML de base pour toutes les pages
 */
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'labonnetrouvaille' ?></title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Jaldi:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <link rel="stylesheet" href="/ECF-leboncoin/asset/css/styles.css">

    <?php if (isset($page) && $page === 'create'): ?>
        <link rel="stylesheet" href="/ECF-leboncoin/asset/css/styles-create.css">
    <?php endif; ?>
    <?php if (isset($page) && $page === 'homepage'): ?>
        <link rel="stylesheet" href="/ECF-leboncoin/asset/css/styles-homepage.css">
    <?php endif; ?>
    <?php if (isset($page) && ($page === 'show' || $page === 'edit')): ?>
        <link rel="stylesheet" href="/ECF-leboncoin/asset/css/styles-show-edit.css">
    <?php endif; ?>
    <?php if (isset($page) && ($page === 'compte' || $page === 'mes-annonces')): ?>
        <link rel="stylesheet" href="/ECF-leboncoin/asset/css/styles-auth.css">
    <?php endif; ?>

</head>

<body class="<?= $page ?? '' ?>">
    <div id="toast-container"></div>

    <?php if (isset($_SESSION['error'])) : ?>
        <div class="error-message">
            <strong>Erreur : </strong>
            <span><?php echo $_SESSION['error']; ?></span>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <!-- Overlay du menu mobile -->
    <div class="menu-overlay" id="menu-overlay"></div>

    <!-- Menu burger mobile -->
    <div class="mobile-menu" id="mobile-menu">
        <div class="mobile-menu-content">
            <!-- Section Authentification -->
            <div class="mobile-menu-section">
                <h3 class="mobile-menu-title">Bienvenue</h3>

                <?php if (isset($_SESSION['id'])): ?>
                    <a href="/ECF-leboncoin/annonce/create" class="mobile-menu-item auth-item primary">
                        Déposer une annonce
                    </a>
                    <a href="/ECF-leboncoin/compte" class="mobile-menu-item auth-item">
                        Mon Compte
                    </a>
                    <a href="/ECF-leboncoin/logout" class="mobile-menu-item auth-item">
                        Se déconnecter
                    </a>
                <?php else: ?>
                    <a href="/ECF-leboncoin/login" class="mobile-menu-item auth-item primary">
                        Se connecter
                    </a>
                    <a href="/ECF-leboncoin/register" class="mobile-menu-item auth-item">
                        S'inscrire
                    </a>
                    <a href="/ECF-leboncoin/login" class="mobile-menu-item auth-item">
                        Déposer une annonce
                    </a>
                <?php endif; ?>
            </div>

            <!-- Section Catégories -->
            <div class="mobile-menu-section">
                <h3 class="mobile-menu-title">Catégories</h3>
                <a href="#" onclick="filterByCategory('1'); closeMobileMenu();" class="mobile-menu-item">
                    Informatique
                </a>
                <a href="#" onclick="filterByCategory('2'); closeMobileMenu();" class="mobile-menu-item">
                    Maison & Jardin
                </a>
                <a href="#" onclick="filterByCategory('3'); closeMobileMenu();" class="mobile-menu-item">
                    Mode & Vêtements
                </a>
                <a href="#" onclick="filterByCategory('4'); closeMobileMenu();" class="mobile-menu-item">
                    Véhicules
                </a>
                <a href="#" onclick="filterByCategory('6'); closeMobileMenu();" class="mobile-menu-item">
                    Sports & Loisirs
                </a>
                <a href="#" onclick="filterByCategory('9'); closeMobileMenu();" class="mobile-menu-item">
                    Vacances
                </a>
                <a href="#" onclick="filterByCategory('10'); closeMobileMenu();" class="mobile-menu-item">
                    Instruments de Musique
                </a>
            </div>
        </div>
    </div>

    <header class="header">
        <nav class="nav-container">
            <a href="/ECF-leboncoin" class="logo">labonnetrouvaille</a>

            <!-- Burger menu (mobile uniquement) -->
            <button class="burger-menu-btn" id="burger-menu-btn" aria-label="Menu">
                <span></span>
                <span></span>
                <span></span>
            </button>

            <!-- Éléments droite (desktop) -->
            <div class="header-right-elements">
                <?php if (isset($_SESSION['id'])): ?>
                    <a href="/ECF-leboncoin/annonce/create" class="depot-button-header">
                        <img src="/ECF-leboncoin/asset/icones/add_box.svg" alt="Ajouter" class="depot-button-header-icon" width="20" height="20">
                        Déposer une annonce
                    </a>
                <?php else: ?>
                    <a href="/ECF-leboncoin/login" class="depot-button-header">
                        <img src="/ECF-leboncoin/asset/icones/add_box.svg" alt="Ajouter" class="depot-button-header-icon" width="20" height="20">
                        Déposer une annonce
                    </a>
                <?php endif; ?>

                <div class="search-container">
                    <input type="text" class="search-input" placeholder="Rechercher sur labonnetrouvaille">
                    <button class="search-button">
                        <img src="/ECF-leboncoin/asset/icones/loupe.png" alt="Rechercher" class="search-icon">
                    </button>
                </div>

                <?php if (isset($_SESSION['id'])): ?>
                    <a href="/ECF-leboncoin/compte" class="btn-compte">
                        <img src="/ECF-leboncoin/asset/icones/manage_accounts.svg" alt="Compte" class="auth-icon">
                        Compte
                    </a>
                    <a href="/ECF-leboncoin/logout" class="btn-logout">
                        <img src="/ECF-leboncoin/asset/icones/se-deconnecter.png" alt="Déconnexion" class="auth-icon">
                        Se déconnecter
                    </a>
                <?php else: ?>
                    <a href="/ECF-leboncoin/register" class="btn-register">
                        <img src="/ECF-leboncoin/asset/icones/person_add.svg" alt="S'inscrire" class="auth-icon">
                        S'inscrire
                    </a>
                    <a href="/ECF-leboncoin/login" class="btn-login">
                        <img src="/ECF-leboncoin/asset/icones/person.svg" alt="Se connecter" class="auth-icon">
                        Se connecter
                    </a>
                <?php endif; ?>
            </div>

            <div class="search-container mobile-search">
                <input type="text" class="search-input" placeholder="Rechercher">
                <button class="search-button">
                    <img src="/ECF-leboncoin/asset/icones/loupe.png" alt="Rechercher" class="search-icon">
                </button>
            </div>
        </nav>
    </header>

    <nav class="categories-nav">
        <div class="categories-container">

            <div class="category-item">
                <a href="#" onclick="filterByCategory('1'); return false;" class="category-link">Informatique</a>
            </div>

            <span class="category-separator">-</span>

            <div class="category-item">
                <a href="#" onclick="filterByCategory('2'); return false;" class="category-link">Maison & Jardin</a>
            </div>

            <span class="category-separator">-</span>

            <div class="category-item">
                <a href="#" onclick="filterByCategory('3'); return false;" class="category-link">Mode & Vêtements</a>
            </div>

            <span class="category-separator">-</span>

            <div class="category-item">
                <a href="#" onclick="filterByCategory('4'); return false;" class="category-link">Véhicules</a>
            </div>

            <span class="category-separator">-</span>

            <div class="category-item">
                <a href="#" onclick="filterByCategory('6'); return false;" class="category-link">Sports & Loisirs</a>
            </div>

            <span class="category-separator">-</span>

            <div class="category-item">
                <a href="#" onclick="filterByCategory('9'); return false;" class="category-link">Vacances</a>
            </div>

            <span class="category-separator">-</span>

            <div class="category-item">
                <a href="#" onclick="filterByCategory('10'); return false;" class="category-link">Instruments de Musique</a>
            </div>

        </div>
    </nav>

    <main class="main-content">
        <?= $content ?? '<p class="no-content">Pas de contenu à afficher.</p>' ?>
    </main>

    <footer class="footer">
        <p>&copy; <?= date('Y') ?> labonnetrouvaille. Tous droits réservés.</p>
    </footer>

    <script src="/ECF-leboncoin/asset/js/main.js"></script>

    <?php if (isset($_SESSION['success'])) : ?>
        <script>
            // Affiche le message de succès dès le chargement de la page
            displaySessionMessage('<?php echo addslashes($_SESSION['success']); ?>', 'success');
        </script>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

</body>

</html>