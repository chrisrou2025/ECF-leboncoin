<?php
/**
 * Page de gestion des annonces de l'utilisateur
 * Affiche les annonces publiées par l'utilisateur connecté
 */
?>
<div class="annonces-dashboard">
    <div class="page-title-container">
        <h1 class="page-title">Mes annonces</h1>
    </div>
    <!-- Messages de feedback -->
    <?php if (isset($_SESSION['error'])): ?>
        <div class="error-message">
            <strong><?= $_SESSION['error'] ?></strong>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="success-message">
            <strong><?= $_SESSION['success'] ?></strong>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <div class="annonces-content">
        <?php if (!empty($annonces)): ?>
            <div class="annonces-grid">
                <?php foreach ($annonces as $annonce): ?>
                    <div class="annonce-card">
                        <div class="annonce-image-wrapper">
                            <?php
                            $mainImage = '/ECF-leboncoin/asset/images/image.png';
                            if (!empty($annonce->getImages()) && isset($annonce->getImages()[0])) {
                                $mainImage = $annonce->getImages()[0]->getPath();
                            }
                            ?>
                            <img src="<?= htmlspecialchars($mainImage) ?>" alt="Photo de l'annonce" class="annonce-image">
                        </div>

                        <div class="annonce-content">
                            <h3 class="annonce-title"><?= htmlspecialchars($annonce->getTitre()) ?></h3>
                            <p class="annonce-description">
                                <?= htmlspecialchars($annonce->getDescriptionExtrait(100)) ?>
                            </p>
                            <div class="annonce-price">
                                <?= number_format($annonce->getPrix(), 0, ',', ' ') ?> €
                            </div>
                            <div class="annonce-date">
                                Publié le <?= date('d/m/Y', strtotime($annonce->getCreatedAt())) ?>
                            </div>
                        </div>

                        <div class="annonce-actions">
                            <a href="/ECF-leboncoin/annonce/<?= $annonce->getId() ?>/edit" class="action-btn edit-btn" title="Modifier">
                                Modifier
                            </a>
                            <form method="POST" action="/ECF-leboncoin/annonce/<?= $annonce->getId() ?>/delete"
                                class="delete-form"
                                onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette annonce ? Cette action est irréversible.');">
                                <button type="submit" class="action-btn delete-btn" title="Supprimer">
                                    Supprimer
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <div class="empty-state-icon">
                </div>
                <h3 class="empty-state-title">Aucune annonce publiée</h3>
                <p class="empty-state-description">
                    Vous n'avez pas encore publié d'annonces sur labonnetrouvaille.
                    Commencez dès maintenant et vendez vos objets !
                </p>
                <a href="/ECF-leboncoin/annonce/create" class="empty-state-action">
                    Publier ma première annonce
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>