<?php
declare(strict_types=1);
/** @var string $pageTitle */
/** @var array<string, mixed> $post */
/** @var list<string> $errors */
require dirname(__DIR__, 2) . '/front_office/layout/header.php';
require __DIR__ . '/_shell_start.php';
?>
<div class="page-heading">
    <h1>Modifier un sujet (admin)</h1>
    <p class="text-muted">ID #<?= (int) $post['id'] ?> — statut : <?= h((string) $post['statut']) ?></p>
</div>

<?php if ($errors !== []) : ?>
    <div class="form-errors card card--error" role="alert">
        <ul><?php foreach ($errors as $err) : ?><li><?= h($err) ?></li><?php endforeach; ?></ul>
    </div>
<?php endif; ?>

<form method="post" action="admin_post_update.php?id=<?= (int) $post['id'] ?>" class="form card">
    <input type="hidden" name="_csrf" value="<?= h(csrf_token()) ?>">
    <div class="form__group">
        <label for="titre">Titre</label>
        <input type="text" id="titre" name="titre" maxlength="200" required value="<?= h((string) $post['titre']) ?>">
    </div>
    <div class="form__group">
        <label for="contenu">Contenu</label>
        <textarea id="contenu" name="contenu" rows="14" required><?= h((string) $post['contenu']) ?></textarea>
    </div>
    <button type="submit" class="btn">Enregistrer</button>
    <a class="btn btn--secondary" href="admin_posts.php">Annuler</a>
</form>

<?php
require __DIR__ . '/_shell_end.php';
require dirname(__DIR__, 2) . '/front_office/layout/footer.php';
