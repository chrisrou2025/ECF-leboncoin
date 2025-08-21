<?php
/**
 * Page d'affichage d'annonce
 * Affiche les détails d'une annonce spécifique
 */
?>
<div class="annonce-container show-page">
    <div class="annonce-wrapper">

        <div class="annonce-images">
            <div class="main-image">
                <?php if (!empty($annonce->getImages()) && isset($annonce->getImages()[0])): ?>
                    <?php $image = $annonce->getImages()[0]; ?>
                    <img src="<?= htmlspecialchars($image->getPath()) ?>" alt="Photo principale">
                <?php else: ?>
                    <div class="no-image-placeholder">
                        <span>📷</span>
                        <p>Aucune photo</p>
                    </div>
                <?php endif; ?>
            </div>

            <div class="secondary-images">
                <?php for ($i = 1; $i <= 2; $i++): ?>
                    <div class="secondary-image">
                        <?php if (!empty($annonce->getImages()) && isset($annonce->getImages()[$i])): ?>
                            <?php $image = $annonce->getImages()[$i]; ?>
                            <img src="<?= htmlspecialchars($image->getPath()) ?>" alt="Photo <?= $i + 1 ?>">
                        <?php else: ?>
                            <div class="no-image-placeholder small">
                                <span>📷</span>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endfor; ?>
            </div>
        </div>

        <div class="annonce-header">
            <h1 class="annonce-title"><?= htmlspecialchars($annonce->getTitre()) ?></h1>
        </div>

        <div class="annonce-price-container">
            <div class="annonce-price"><?= number_format($annonce->getPrix(), 0, ',', ' ') ?> €</div>
        </div>

        <div class="annonce-details-gauche">
            <div class="annonce-meta">
                <div class="meta-row">
                    <div class="meta-item">
                        <strong>Vendeur :</strong> <?= htmlspecialchars($annonce->getUserPseudo()) ?>
                    </div>
                    <div class="meta-item">
                        <strong>Publié :</strong>
                        Il y a <?= htmlspecialchars($timeElapsed) ?>
                    </div>
                </div>

                <div class="meta-row">
                    <div class="meta-item">
                        <strong>Localité :</strong> <?= htmlspecialchars($annonce->getLocalite()) ?>
                    </div>
                    <?php if (!empty($annonce->getEtat())): ?>
                        <div class="meta-item">
                            <strong>État :</strong>
                            <?= htmlspecialchars($etats[$annonce->getEtat()] ?? $annonce->getEtat()) ?>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if (!empty($annonce->getMarque()) && $annonce->getCategoryId() != 2): ?>
                    <div class="meta-row">
                        <div class="meta-item">
                            <strong>Marque :</strong> <?= htmlspecialchars($annonce->getMarque()) ?>
                        </div>
                        <?php if (!empty($annonce->getKilometrage())): ?>
                            <div class="meta-item">
                                <strong>Kilométrage :</strong>
                                <?= number_format($annonce->getKilometrage(), 0, ',', ' ') ?> km
                            </div>
                        <?php endif; ?>
                    </div>
                <?php elseif (!empty($annonce->getKilometrage())): ?>
                    <!-- Afficher seulement le kilométrage si pas de marque -->
                    <div class="meta-row">
                        <div class="meta-item">
                            <strong>Kilométrage :</strong>
                            <?= number_format($annonce->getKilometrage(), 0, ',', ' ') ?> km
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <div class="annonce-description">
                <h2>Description :</h2>
                <div class="description-content">
                    <?= nl2br(htmlspecialchars($annonce->getDescription())) ?>
                </div>
            </div>
        </div>
    </div>
</div>