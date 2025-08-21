<?php
/**
 * Page de modification du compte utilisateur
 * Affiche les options pour modifier le pseudo, l'email, le mot de passe et gérer les annonces
 */
?>
<div class="account-dashboard">
    <div class="page-title-container">
        <h1 class="page-title">Modifier mon compte</h1>
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

        <!-- Section principale avec cartes -->
        <div class="account-cards-container">
            <!-- Carte: Modifier le pseudo -->
            <div class="account-card">
                <div class="card-header">
                    <h2 class="card-title">
                        <i class="fas fa-user"></i>
                        Changer de Pseudo
                    </h2>
                </div>
                <div class="card-content">
                    <form method="POST" action="/ECF-leboncoin/compte/update-pseudo">
                        <div class="form-group">
                            <label for="pseudo" class="form-label">
                                Nouveau pseudo <span class="required">*</span>
                            </label>
                            <input type="text" id="pseudo" name="pseudo" class="form-input"
                                value="<?= htmlspecialchars($user->getPseudo()) ?>" required>
                        </div>
                        <button type="submit" class="submit-button">Mettre à jour le pseudo</button>
                    </form>
                </div>
            </div>

            <!-- Carte: Modifier l'email -->
            <div class="account-card">
                <div class="card-header">
                    <h2 class="card-title">
                        <i class="fas fa-envelope"></i>
                        Changer d'Email
                    </h2>
                </div>
                <div class="card-content">
                    <form method="POST" action="/ECF-leboncoin/compte/update-email">
                        <div class="form-group">
                            <label for="email" class="form-label">
                                Nouvel email <span class="required">*</span>
                            </label>
                            <input type="email" id="email" name="email" class="form-input"
                                value="<?= htmlspecialchars($user->getEmail()) ?>" required>
                        </div>
                        <button type="submit" class="submit-button">Mettre à jour l'email</button>
                    </form>
                </div>
            </div>

            <!-- Carte: Modifier le mot de passe -->
            <div class="account-card">
                <div class="card-header">
                    <h2 class="card-title">
                        <i class="fas fa-lock"></i>
                        Changer de Mot de Passe
                    </h2>
                </div>
                <div class="card-content">
                    <form method="POST" action="/ECF-leboncoin/compte/update-password">
                        <div class="form-group">
                            <label for="current_password" class="form-label">
                                Mot de passe actuel <span class="required">*</span>
                            </label>
                            <div class="input-with-icon">
                                <input type="password" id="current_password" name="current_password"
                                    class="form-input" required>
                                <button type="button" class="toggle-password">
                                    <i class="fa fa-eye-slash"></i>
                                </button>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="new_password" class="form-label">
                                Nouveau mot de passe <span class="required">*</span>
                            </label>
                            <div class="input-with-icon">
                                <input type="password" id="new_password" name="new_password"
                                    class="form-input" required>
                                <button type="button" class="toggle-password">
                                    <i class="fa fa-eye-slash"></i>
                                </button>
                            </div>
                        </div>
                        <button type="submit" class="submit-button">Mettre à jour le mot de passe</button>
                    </form>
                </div>
            </div>

            <!-- Carte: Gérer mes annonces -->
            <div class="account-card action-card">
                <div class="card-header">
                    <h2 class="card-title">
                        <i class="fas fa-bullhorn"></i>
                        Mes Annonces
                    </h2>
                </div>
                <div class="card-content">
                    <p class="card-description">
                        Consultez et gérez toutes vos annonces déposées sur labonnetrouvaille.
                    </p>
                    <a href="/ECF-leboncoin/user/mes-annonces" class="action-link">
                        <span>Gérer mes annonces</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>