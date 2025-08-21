<?php

/**
 * Page d'accueil
 * Affiche le bandeau principal et les annonces
 * avec système de filtrage JavaScript.
 */

// Créer une instance du modèle pour utiliser la méthode getTimeElapsed
$modelAnnonce = new ModelAnnonce();

if (!empty($recentAnnonces)) {
    shuffle($recentAnnonces);
}

?>

<section class="depot-banner">
    <div class="depot-content">
        <h1 class="depot-title">C'est le moment de vendre</h1>
        <?php if (isset($_SESSION['id'])): ?>
            <a href="/ECF-leboncoin/annonce/create" class="depot-button">
                <img src="/ECF-leboncoin/asset/icones/add_box.svg" alt="Ajouter" class="depot-button-icon" width="20" height="20">
                Déposer une annonce
            </a>
        <?php else: ?>
            <a href="/ECF-leboncoin/login" class="depot-button">
                <img src="/ECF-leboncoin/asset/icones/add_box.svg" alt="Ajouter" class="depot-button-icon" width="20" height="20">
                Déposer une annonce
            </a>
        <?php endif; ?>
    </div>
</section>

<section class="latest-annonces">
    <div class="container">
        <h2 class="section-title">En ce moment sur labonnetrouvaille</h2>

        <div class="filter-controls">
            <div class="filter-group">
                <label for="category-filter" class="filter-label">Catégorie :</label>
                <select id="category-filter" class="filter-select">
                    <option value="">Toutes les catégories</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= $category->getId() ?>">
                            <?= htmlspecialchars($category->getNom()) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="filter-group">
                <label for="sort-filter" class="filter-label">Trier par :</label>
                <select id="sort-filter" class="filter-select">
                    <option value="random">Aléatoire</option>
                    <option value="date-desc">Plus récentes</option>
                    <option value="date-asc">Plus anciennes</option>
                    <option value="price-asc">Prix croissant</option>
                    <option value="price-desc">Prix décroissant</option>
                    <option value="title-asc">Titre A-Z</option>
                    <option value="title-desc">Titre Z-A</option>
                </select>
            </div>
        </div>

        <?php if (!empty($recentAnnonces)): ?>
            <div id="annonces-container" class="annonces-random-container">
                <?php foreach ($recentAnnonces as $annonce): ?>
                    <div class="annonce-card"
                        data-category-id="<?= $annonce->getCategoryId() ?>"
                        data-category-name="<?= htmlspecialchars($annonce->getCategoryNom()) ?>"
                        data-price="<?= $annonce->getPrix() ?>"
                        data-date="<?= $annonce->getCreatedAt() ?>"
                        data-title="<?= htmlspecialchars(strtolower($annonce->getTitre())) ?>">

                        <p class="annonce-author">
                            <?= htmlspecialchars($annonce->getUserPseudo() ?? 'Utilisateur inconnu') ?>
                        </p>

                        <div class="annonce-image">
                            <?php if (isset($_SESSION['id']) && $_SESSION['id'] == $annonce->getUserId()): ?>
                                <a href="/ECF-leboncoin/annonce/<?= $annonce->getId() ?>/edit" class="annonce-link">
                            <?php else: ?>
                                <a href="/ECF-leboncoin/annonce/<?= $annonce->getId() ?>" class="annonce-link">
                            <?php endif; ?>
                                    <img src="<?= htmlspecialchars($annonce->getImagePrincipale() ?? '/ECF-leboncoin/asset/img/default-annonce.jpg') ?>"
                                         alt="<?= htmlspecialchars($annonce->getTitre()) ?>"
                                         class="annonce-image-img">
                                </a>
                        </div>

                        <div class="annonce-info">
                            <h4 class="annonce-title">
                                <?= htmlspecialchars($annonce->getTitre()) ?>
                            </h4>

                            <?php if (!empty($annonce->getMarque()) && !$annonce->isMaisonJardin()): ?>
                                <p class="annonce-marque">
                                    <?= htmlspecialchars($annonce->getMarque()) ?>
                                </p>
                            <?php endif; ?>

                            <p class="annonce-price">
                                <?= number_format($annonce->getPrix(), 0, ',', ' ') ?> €
                            </p>

                            <p class="annonce-details">
                                <?php
                                // Affichage du kilométrage pour les véhicules, sinon localité
                                if ($annonce->isVehicule() && !empty($annonce->getKilometrage())): ?>
                                    <?= number_format($annonce->getKilometrage(), 0, ',', ' ') ?> km
                                <?php elseif (!empty($annonce->getLocalite())): ?>
                                    <?= htmlspecialchars($annonce->getLocalite()) ?>
                                <?php else: ?>
                                    &nbsp;
                                <?php endif; ?>
                            </p>

                            <p class="annonce-time-elapsed">
                                il y a <?= $modelAnnonce->getTimeElapsed($annonce->getCreatedAt()) ?>
                            </p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div id="no-results-message" class="no-annonces" style="display: none;">
                <p>Aucune annonce ne correspond à vos critères de recherche.</p>
                <p>Essayez de modifier vos filtres ou</p>
                <?php if (isset($_SESSION['id'])): ?>
                    <a href="/ECF-leboncoin/annonce/create" class="btn-primary">
                        Soyez le premier à déposer une annonce dans cette catégorie !
                    </a>
                <?php else: ?>
                    <p>
                        <a href="/ECF-leboncoin/register">Inscrivez-vous</a>
                        pour déposer votre première annonce !
                    </p>
                <?php endif; ?>
            </div>

        <?php else: ?>
            <div class="no-annonces">
                <p>Aucune annonce disponible pour le moment.</p>
                <?php if (isset($_SESSION['id'])): ?>
                    <a href="/ECF-leboncoin/annonce/create" class="btn-primary">
                        Soyez le premier à déposer une annonce !
                    </a>
                <?php else: ?>
                    <p>
                        <a href="/ECF-leboncoin/register">Inscrivez-vous</a>
                        pour déposer votre première annonce !
                    </p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</section>