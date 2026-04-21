<?php
declare(strict_types=1);
/** @var string $pageTitle */
/** @var array<string, mixed> $reply */
/** @var array<string, mixed> $post */
/** @var list<string> $errors */
require dirname(__DIR__) . '/layout/header.php';
?>
<div class="page-heading">
    <h1>Modifier ma réponse</h1>
    <p class="text-muted">Sujet : <a href="post.php?id=<?= (int) $post['id'] ?>"><?= h((string) $post['titre']) ?></a></p>
</div>

<?php if ($errors !== []) : ?>
    <div class="form-errors card card--error" role="alert">
        <ul><?php foreach ($errors as $err) : ?><li><?= h($err) ?></li><?php endforeach; ?></ul>
    </div>
<?php endif; ?>

<form method="post" action="reply_update.php?id=<?= (int) $reply['id'] ?>" class="form card">
    <input type="hidden" name="_csrf" value="<?= h(csrf_token()) ?>">
    <div class="form__group">
        <label for="contenu">Message</label>
        <textarea id="contenu" name="contenu" rows="10" required><?= h((string) $reply['contenu']) ?></textarea>
    </div>
    <button type="submit" class="btn">Enregistrer</button>
    <a class="btn btn--secondary" href="post.php?id=<?= (int) $post['id'] ?>">Annuler</a>
</form>

<?php require dirname(__DIR__) . '/layout/footer.php'; ?>
