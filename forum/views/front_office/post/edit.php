<?php
declare(strict_types=1);
/** @var string $pageTitle */
/** @var array<string, mixed> $post */
/** @var list<string> $errors */
require dirname(__DIR__) . '/layout/header.php';
?>
<div class="page-heading">
    <h1>Modifier le sujet</h1>
</div>

<?php if ($errors !== []) : ?>
    <div class="form-errors card card--error" role="alert">
        <ul>
            <?php foreach ($errors as $err) : ?>
                <li><?= h($err) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form method="post" action="post_update.php?id=<?= (int) $post['id'] ?>" class="form card" novalidate>
    <input type="hidden" name="_csrf" value="<?= h(csrf_token()) ?>">
    <div class="form__group">
        <label for="titre">Titre</label>
        <input type="text" id="titre" name="titre" maxlength="200" required value="<?= h((string) $post['titre']) ?>">
    </div>
    <div class="form__group">
        <label for="contenu">Contenu</label>
        <textarea id="contenu" name="contenu" rows="12" required><?= h((string) $post['contenu']) ?></textarea>
    </div>
    <button type="submit" class="btn">Enregistrer</button>
    <a class="btn btn--secondary" href="post.php?id=<?= (int) $post['id'] ?>">Annuler</a>
</form>

<?php require dirname(__DIR__) . '/layout/footer.php'; ?>
