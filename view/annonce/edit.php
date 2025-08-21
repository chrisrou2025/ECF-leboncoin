<?php
/**
 * Page d'édition d'annonce
 * Affiche le formulaire pour modifier une annonce existante
 */

// Récupération des données précédemment saisies ou données actuelles
$oldInput = $_SESSION['old_input'] ?? $annonce->toArray();
$validationErrors = $_SESSION['validation_errors'] ?? [];

// Nettoyage des données de session
unset($_SESSION['old_input'], $_SESSION['validation_errors']);
?>

<div class="annonce-container edit-mode">
    <div class="annonce-wrapper">
        <form action="/ECF-leboncoin/annonce/<?= $annonce->getId() ?>/update" method="POST" enctype="multipart/form-data" class="edit-form">

            <!-- Champ caché pour les suppressions d'images -->
            <input type="hidden" name="images_to_delete" id="images-to-delete" value="">

            <!-- Messages d'erreur -->
            <?php if (!empty($validationErrors)): ?>
                <div class="alert alert-error">
                    <h3>Erreurs de validation</h3>
                    <ul>
                        <?php foreach ($validationErrors as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- Section images (identique au show mais avec édition) -->
            <div class="annonce-images edit-images">
                <!-- Image principale -->
                <div class="main-image" id="thumb-0-container">
                    <?php if (!empty($annonce->getImages()) && isset($annonce->getImages()[0])): ?>
                        <?php $image = $annonce->getImages()[0]; ?>
                        <img src="<?= htmlspecialchars($image->getPath()) ?>" alt="Photo principale" id="thumb-0-display">
                        <button type="button" class="remove-btn" onclick="removeExistingImage(<?= $image->getId() ?>, 'thumb-0')">
                            ✕
                        </button>
                    <?php else: ?>
                        <div class="no-image-placeholder clickable" onclick="triggerImageUpload('thumb-0')">
                            <span>📷</span>
                            <p>Cliquez pour ajouter<br>la photo principale</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Images secondaires à droite -->
                <div class="secondary-images">
                    <?php for ($i = 1; $i <= 2; $i++): ?>
                        <div class="secondary-image" id="thumb-<?= $i ?>-container">
                            <?php if (!empty($annonce->getImages()) && isset($annonce->getImages()[$i])): ?>
                                <?php $image = $annonce->getImages()[$i]; ?>
                                <img src="<?= htmlspecialchars($image->getPath()) ?>" alt="Photo <?= $i + 1 ?>" id="thumb-<?= $i ?>-display">
                                <button type="button" class="remove-btn" onclick="removeExistingImage(<?= $image->getId() ?>, 'thumb-<?= $i ?>')">
                                    ✕
                                </button>
                            <?php else: ?>
                                <div class="no-image-placeholder small clickable" onclick="triggerImageUpload('thumb-<?= $i ?>')">
                                    <span>📷</span>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endfor; ?>
                </div>
            </div>

            <!-- Input file caché pour l'ajout d'images -->
            <input type="file" id="image-upload" name="images[]" multiple accept="image/*" style="display: none;">

            <!-- Titre de l'annonce -->
            <div class="form-group-row">
                <div class="form-group full">
                    <label for="titre">Titre de l'annonce</label>
                    <input type="text" id="titre" name="titre" value="<?= htmlspecialchars($oldInput['titre'] ?? '') ?>" required maxlength="100">
                </div>
            </div>

            <!-- Prix et Catégorie -->
            <div class="form-group-row">
                <div class="form-group">
                    <label for="prix">Prix (€)</label>
                    <input type="number" id="prix" name="prix" value="<?= htmlspecialchars($oldInput['prix'] ?? '') ?>" required min="0" step="0.01">
                </div>
                <div class="form-group">
                    <label for="category_id">Catégorie</label>
                    <select id="category_id" name="category_id" required>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= $category->getId() ?>" <?= ($oldInput['category_id'] ?? '') == $category->getId() ? 'selected' : '' ?>>
                                <?= htmlspecialchars($category->getNom()) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- État et Localité -->
            <div class="form-group-row">
                <div class="form-group">
                    <label for="etat">État</label>
                    <select id="etat" name="etat">
                        <?php foreach (['neuf' => 'Neuf', 'tres_bon' => 'Très bon état', 'bon' => 'Bon état', 'satisfaisant' => 'Satisfaisant'] as $value => $label): ?>
                            <option value="<?= $value ?>" <?= ($oldInput['etat'] ?? '') == $value ? 'selected' : '' ?>>
                                <?= $label ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="localite">Localité</label>
                    <input type="text" id="localite" name="localite" value="<?= htmlspecialchars($oldInput['localite'] ?? '') ?>" required maxlength="100">
                </div>
            </div>

            <div class="form-group-row" id="marque-row"
                style="<?= ($oldInput['category_id'] ?? 0) == 2 ? 'display: none;' : 'display: flex;' ?>">
                <div class="form-group">
                    <label for="marque">Marque</label>
                    <input type="text" id="marque" name="marque"
                        value="<?= htmlspecialchars($oldInput['marque'] ?? '') ?>"
                        maxlength="100">
                </div>
                <div class="form-group"></div> <!-- Espace pour alignement -->
            </div>

            <!-- Kilométrage (conditionnel) -->
            <div class="form-group-row" id="kilometrage-row"
                style="<?= ($oldInput['category_id'] ?? 0) == 4 ? 'display: flex;' : 'display: none;' ?>">
                <div class="form-group">
                    <label for="kilometrage">Kilométrage (km)</label>
                    <input type="number" id="kilometrage" name="kilometrage"
                        value="<?= htmlspecialchars($oldInput['kilometrage'] ?? '') ?>"
                        min="0" max="999999">
                </div>
                <div class="form-group"></div> <!-- Espace pour alignement -->
            </div>

            <!-- Description -->
            <div class="form-group-row">
                <div class="form-group full">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="6" required><?= htmlspecialchars($oldInput['description'] ?? '') ?></textarea>
                </div>
            </div>

            <!-- Boutons d'action -->
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    Enregistrer les modifications
                </button>
                <a href="/ECF-leboncoin/annonce/<?= $annonce->getId() ?>" class="btn btn-secondary">
                    Annuler
                </a>
            </div>
        </form>
    </div>
</div>