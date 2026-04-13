<?php
declare(strict_types=1);
/** @var string $pageTitle */
/** @var string|null $error */
require dirname(__DIR__) . '/layout/header.php';
?>
<div class="page-center">
<div class="auth-box card card--elevated">
    <h1>Connexion</h1>
    <?php if ($error !== null && $error !== '') : ?>
        <p class="form-error" role="alert"><?= h($error) ?></p>
    <?php endif; ?>
    <form method="post" action="login.php" class="form" novalidate>
        <input type="hidden" name="_csrf" value="<?= h(csrf_token()) ?>">
        <div class="form__group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required autocomplete="username" value="">
        </div>
        <div class="form__group">
            <label for="password">Mot de passe</label>
            <input type="password" id="password" name="password" required autocomplete="current-password">
        </div>
        <button type="submit" class="btn">Se connecter</button>
    </form>
    <p class="auth-box__footer text-muted">Pas encore de compte ? <a href="register.php">S’inscrire</a></p>
</div>
</div>
<?php require dirname(__DIR__) . '/layout/footer.php'; ?>
