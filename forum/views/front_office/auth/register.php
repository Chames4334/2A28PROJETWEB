<?php
declare(strict_types=1);
/** @var string $pageTitle */
/** @var list<string> $errors */
/** @var array{nom?: string, prenom?: string, email?: string} $old */
require dirname(__DIR__) . '/layout/header.php';
?>
<div class="page-center">
<div class="auth-box card card--elevated">
    <h1>Inscription</h1>
    <?php if ($errors !== []) : ?>
        <div class="form-errors" role="alert">
            <ul>
                <?php foreach ($errors as $err) : ?>
                    <li><?= h($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    <form method="post" action="register.php" class="form" novalidate>
        <input type="hidden" name="_csrf" value="<?= h(csrf_token()) ?>">
        <div class="form__group">
            <label for="nom">Nom</label>
            <input type="text" id="nom" name="nom" required maxlength="100" value="<?= h((string) ($old['nom'] ?? '')) ?>">
        </div>
        <div class="form__group">
            <label for="prenom">Prénom</label>
            <input type="text" id="prenom" name="prenom" required maxlength="100" value="<?= h((string) ($old['prenom'] ?? '')) ?>">
        </div>
        <div class="form__group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required value="<?= h((string) ($old['email'] ?? '')) ?>">
        </div>
        <div class="form__group">
            <label for="password">Mot de passe (min. 8 caractères)</label>
            <input type="password" id="password" name="password" required minlength="8" autocomplete="new-password">
        </div>
        <div class="form__group">
            <label for="password_confirm">Confirmer le mot de passe</label>
            <input type="password" id="password_confirm" name="password_confirm" required minlength="8" autocomplete="new-password">
        </div>
        <button type="submit" class="btn">Créer mon compte</button>
    </form>
    <p class="auth-box__footer text-muted">Déjà inscrit ? <a href="login.php">Connexion</a></p>
</div>
</div>
<?php require dirname(__DIR__) . '/layout/footer.php'; ?>
