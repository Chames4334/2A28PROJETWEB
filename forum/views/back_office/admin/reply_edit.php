<?php
declare(strict_types=1);
/** @var string $pageTitle */
/** @var array<string, mixed> $reply */
/** @var list<string> $errors */
require dirname(__DIR__, 2) . '/front_office/layout/header.php';
require __DIR__ . '/_shell_start.php';
?>
<div class="page-heading">
    <h1>Modifier une réponse (admin)</h1>
    <p class="text-muted">Réponse #<?= (int) $reply['id'] ?> — sujet : <?= h((string) $reply['post_titre']) ?> — statut <?= h((string) $reply['statut']) ?></p>
</div>

<?php if ($errors !== []) : ?>
    <div class="form-errors card card--error" role="alert">
        <ul><?php foreach ($errors as $err) : ?><li><?= h($err) ?></li><?php endforeach; ?></ul>
    </div>
<?php endif; ?>

<form method="post" action="admin_reply_update.php?id=<?= (int) $reply['id'] ?>" class="form card">
    <input type="hidden" name="_csrf" value="<?= h(csrf_token()) ?>">
    <div class="form__group">
        <label for="contenu">Contenu</label>
        <textarea id="contenu" name="contenu" rows="10" required><?= h((string) $reply['contenu']) ?></textarea>
    </div>
    <button type="submit" class="btn">Enregistrer</button>
    <a class="btn btn--secondary" href="admin_replies.php">Annuler</a>
</form>

<?php
require __DIR__ . '/_shell_end.php';
require dirname(__DIR__, 2) . '/front_office/layout/footer.php';
