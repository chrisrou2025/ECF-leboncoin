<?php
/**
 * Page de création d'annonce
 * Affiche le formulaire pour créer une nouvelle annonce
 */

// Récupération des données précédemment saisies ou des données d'une annonce existante
$oldInput = $_SESSION['old_input'] ?? $annonce->toArray();
$validationErrors = $_SESSION['validation_errors'] ?? [];

// Nettoyage des données de session
unset($_SESSION['old_input'], $_SESSION['validation_errors']);
?>

<div class="form-container-create">
    <div class="page-title-container">
        <h1 class="page-title" style="font-size: 24px;">Déposer une annonce</h1>
    </div>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="error-message">
            <strong><?= htmlspecialchars($_SESSION['error']) ?></strong>
            <?php if (!empty($validationErrors)): ?>
                <div class="validation-errors">
                    <ul>
                        <?php foreach ($validationErrors as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <form id="annonce-form" method="POST" action="/ECF-leboncoin/annonce/store" enctype="multipart/form-data">
        <div class="main-form-content">
            <div class="form-and-photo-container">

                <div class="form-section">
                    <h2 class="form-main-title">Commençons par le plus important !</h2>
                    <p class="required-note">*Champs obligatoires</p>

                    <div class="form-group-create">
                        <label for="titre" class="form-label-create">Titre de l'annonce <span class="required">*</span></label>
                        <input type="text" id="titre" name="titre" class="form-input-create"
                            value="<?= htmlspecialchars($oldInput['titre'] ?? '') ?>" required
                            placeholder="Ex: Superbe vélo de course">
                    </div>

                    <div class="form-group-create">
                        <label for="category_id" class="form-label-create">Catégorie <span class="required">*</span></label>
                        <select id="category_id" name="category_id" class="form-select-create" required>
                            <option value="">-- Choisir une catégorie --</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= $category->getId() ?>" <?= (isset($oldInput['category_id']) && $oldInput['category_id'] == $category->getId()) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($category->getNom()) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-row">
                        <div class="form-group-half">
                            <label for="prix" class="form-label-create">Prix <span class="required">*</span></label>
                            <input type="number" id="prix" name="prix" class="form-input-half"
                                value="<?= htmlspecialchars($oldInput['prix'] ?? '') ?>" required
                                placeholder="Prix en €" min="0" step="1">
                        </div>
                        <div class="form-group-half">
                            <label for="etat" class="form-label-create">État</label>
                            <select id="etat" name="etat" class="form-input-state">
                                <option value="">Non spécifié</option>
                                <?php
                                // Assurez-vous que la variable $etats est bien passée depuis votre contrôleur
                                $etats = ['neuf' => 'Neuf', 'tres_bon' => 'Très bon état', 'bon' => 'Bon état', 'satisfaisant' => 'Satisfaisant'];
                                foreach ($etats as $value => $label): ?>
                                    <option value="<?= $value ?>" <?= ($oldInput['etat'] ?? '') === $value ? 'selected' : '' ?>>
                                        <?= $label ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-row" id="marque-row" style="display: flex;">
                        <div class="form-group-half">
                            <label for="marque" class="form-label-create">Marque</label>
                            <input type="text" id="marque" name="marque" class="form-input-half"
                                value="<?= htmlspecialchars($oldInput['marque'] ?? '') ?>"
                                placeholder="Ex: Apple, Renault..." maxlength="100">
                        </div>
                    </div>

                    <div class="form-row" id="vehicule-fields" style="display: none;">
                        <div class="form-group-half">
                            <label for="kilometrage" class="form-label-create">Kilométrage <span class="required">*</span></label>
                            <input type="number" id="kilometrage" name="kilometrage" class="form-input-half"
                                value="<?= htmlspecialchars($oldInput['kilometrage'] ?? '') ?>"
                                placeholder="Ex: 90000" min="0">
                        </div>
                    </div>

                    <div class="form-group-create">
                        <label for="localite" class="form-label-create">Localité <span class="required">*</span></label>
                        <input type="text" id="localite" name="localite" class="form-input-create"
                            value="<?= htmlspecialchars($oldInput['localite'] ?? '') ?>" required
                            placeholder="Ex: Paris">
                    </div>

                    <div class="form-group-create">
                        <label for="description" class="form-label-create">Description <span class="required">*</span></label>
                        <textarea id="description" name="description" class="form-textarea-create" required
                            placeholder="Décrivez votre annonce en détail..."><?= htmlspecialchars($oldInput['description'] ?? '') ?></textarea>
                    </div>
                </div>

                <div class="photo-section">
                    <div class="photo-container">
                        <div class="photo-upload-container" id="photo-upload-trigger" title="Ajouter jusqu'à 4 photos">
                            <img src="/ECF-leboncoin/asset/icones/ajouter-une-photo.png" alt="Ajouter une photo" class="photo-upload-icon">
                            <span class="photo-upload-text">Ajouter des photos</span>
                            <small class="photo-upload-info">4 photos max (JPG, PNG, GIF, WebP)</small>
                        </div>
                        <input type="file" id="photo-input" name="photos[]" accept="image/jpeg,image/png,image/gif,image/webp" multiple style="display: none;">
                        <div class="photo-thumbnails"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-buttons">
            <a href="/ECF-leboncoin/" class="btn-cancel">Annuler</a>
            <button type="submit" class="btn-validate">Valider l'annonce</button>
        </div>
    </form>
</div>