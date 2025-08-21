<?php
/**
 * Page d'inscription
 * Affiche le formulaire pour créer un nouveau compte utilisateur
 */
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - labonnetrouvaille</title>
    <link href="https://fonts.googleapis.com/css2?family=Jaldi:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/ECF-leboncoin/asset/css/styles-auth.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>

<body>
    <header class="header-logo">
        <a href="/ECF-leboncoin/" class="logo">labonnetrouvaille</a>
    </header>

    <!-- Conteneur principal -->
    <main class="main-container">
        <div class="form-container">
            <div class="form-content">
                <!-- Titre principal -->
                <h1 class="form-title">
                    Créez votre compte<br>labonnetrouvaille
                </h1>
                <!-- Section du formulaire -->
                <div class="form-section">
                    <!-- Messages d'erreur -->
                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="error-message">
                            <strong><?= $_SESSION['error'] ?></strong>
                            <?php if (isset($_SESSION['validation_errors'])): ?>
                                <div class="validation-errors">
                                    <ul>
                                        <?php foreach ($_SESSION['validation_errors'] as $error): ?>
                                            <li><?= $error ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                                <?php unset($_SESSION['validation_errors']); ?>
                            <?php endif; ?>
                        </div>
                        <?php unset($_SESSION['error']); ?>
                    <?php endif; ?>

                    <!-- Messages de succès -->
                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="success-message">
                            <strong><?= $_SESSION['success'] ?></strong>
                        </div>
                        <?php unset($_SESSION['success']); ?>
                    <?php endif; ?>

                    <!-- Formulaire d'inscription -->
                    <form method="POST" action="/ECF-leboncoin/register">
                        <!-- Champ Pseudo -->
                        <div class="form-group">
                            <label for="pseudo" class="form-label">
                                Pseudo <span class="required">*</span>
                            </label>
                            <input
                                type="text"
                                id="pseudo"
                                name="pseudo"
                                class="form-input"
                                value="<?= isset($_SESSION['old_input']['pseudo']) ? htmlspecialchars($_SESSION['old_input']['pseudo']) : '' ?>"
                                required
                                placeholder="Votre pseudo"
                                minlength="3">
                        </div>

                        <!-- Champ Email -->
                        <div class="form-group">
                            <label for="email" class="form-label">
                                Email <span class="required">*</span>
                            </label>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                class="form-input"
                                value="<?= isset($_SESSION['old_input']['email']) ? htmlspecialchars($_SESSION['old_input']['email']) : '' ?>"
                                required
                                placeholder="votre.email@exemple.com">
                        </div>

                        <!-- Champ Mot de passe -->
                        <div class="form-group password-group">
                            <label for="password" class="form-label">
                                Mot de passe <span class="required">*</span>
                            </label>
                            <div class="input-with-icon">
                                <input
                                    type="password"
                                    id="password"
                                    name="password"
                                    class="form-input"
                                    required
                                    placeholder="Votre mot de passe"
                                    minlength="3">
                                <button type="button" class="toggle-password"><i class="fas fa-eye"></i></button>
                            </div>
                        </div>

                        <div class="form-group password-group">
                            <label for="confirm_password" class="form-label">
                                Vérification du mot de passe <span class="required">*</span>
                            </label>
                            <div class="input-with-icon">
                                <input
                                    type="password"
                                    id="confirm_password"
                                    name="confirm_password"
                                    class="form-input"
                                    required
                                    placeholder="Confirmez votre mot de passe"
                                    minlength="3">
                                <button type="button" class="toggle-password"><i class="fas fa-eye"></i></button>
                            </div>
                        </div>

                        <!-- Bouton de validation -->
                        <button type="submit" class="submit-button">
                            S'inscrire
                        </button>
                        <!-- Bouton Annuler -->
                        <button type="button" class="cancel-button" onclick="window.location.href='/ECF-leboncoin/'">
                            Annuler
                        </button>
                    </form>
                </div>
            </div>
            <!-- Section de l'image -->
            <div class="image-section">
                <img src="/ECF-leboncoin/asset/images/image.png"
                    alt="Inscription labonnetrouvaille"
                    class="register-image">
            </div>
        </div>
    </main>

    <footer class="footer">
        <p>&copy; <?= date('Y') ?> labonnetrouvaille. Tous droits réservés.</p>
    </footer>

    <?php
    // Nettoyer les anciennes données du formulaire
    if (isset($_SESSION['old_input'])) {
        unset($_SESSION['old_input']);
    }
    ?>

    <script src="/ECF-leboncoin/asset/js/main.js"></script>
</body>

</html>