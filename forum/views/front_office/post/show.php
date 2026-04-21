<?php
declare(strict_types=1);
/** @var string $pageTitle */
/** @var array<string, mixed> $post */
/** @var list<array<string, mixed>> $replies */
/** @var bool $canEdit */
/** @var array<string, mixed> $reactionSummary */
$reactionSummary = $reactionSummary ?? [
    'post'    => ['likes' => 0, 'dislikes' => 0, 'user' => null],
    'replies' => [],
];
$rp = $reactionSummary['post'];
require dirname(__DIR__) . '/layout/header.php';
?>
<article class="post-detail card">
    <header class="post-detail__head">
        <?php if (!empty($post['is_pinned'])) : ?>
            <span class="badge badge--pin">Épinglé</span>
        <?php endif; ?>
        <h1><?= h((string) $post['titre']) ?></h1>
        <div class="post-detail__meta text-muted">
            <span><?= h(trim((string) $post['auteur_nom'] . ' ' . (string) $post['auteur_prenom'])) ?></span>
            <span> · </span>
            <time datetime="<?= h((string) $post['created_at']) ?>">Publié le <?= h(date('d/m/Y à H:i', strtotime((string) $post['created_at']))) ?></time>
            <?php if (($post['updated_at'] ?? '') !== ($post['created_at'] ?? '')) : ?>
                <span> — modifié le <?= h(date('d/m/Y à H:i', strtotime((string) $post['updated_at']))) ?></span>
            <?php endif; ?>
        </div>
        <div class="reaction-bar reaction-bar--post" aria-label="Réactions au sujet">
            <span class="reaction-counts" title="Votes">👍 <?= (int) $rp['likes'] ?> · 👎 <?= (int) $rp['dislikes'] ?></span>
            <?php if ($currentForumUser !== null) : ?>
                <form class="reaction-form" method="post" action="reaction_store.php">
                    <input type="hidden" name="_csrf" value="<?= h(csrf_token()) ?>">
                    <input type="hidden" name="target" value="post">
                    <input type="hidden" name="target_id" value="<?= (int) $post['id'] ?>">
                    <input type="hidden" name="post_id" value="<?= (int) $post['id'] ?>">
                    <input type="hidden" name="type" value="like">
                    <button type="submit" class="btn btn--reaction <?= ($rp['user'] ?? null) === 'like' ? 'is-active' : '' ?>">👍 J’aime</button>
                </form>
                <form class="reaction-form" method="post" action="reaction_store.php">
                    <input type="hidden" name="_csrf" value="<?= h(csrf_token()) ?>">
                    <input type="hidden" name="target" value="post">
                    <input type="hidden" name="target_id" value="<?= (int) $post['id'] ?>">
                    <input type="hidden" name="post_id" value="<?= (int) $post['id'] ?>">
                    <input type="hidden" name="type" value="dislike">
                    <button type="submit" class="btn btn--reaction <?= ($rp['user'] ?? null) === 'dislike' ? 'is-active' : '' ?>">👎 Je n’aime pas</button>
                </form>
            <?php endif; ?>
        </div>
        <?php if ($canEdit) : ?>
            <div class="post-detail__actions">
                <a class="btn btn--secondary btn--small" href="post_edit.php?id=<?= (int) $post['id'] ?>">Modifier</a>
                <form class="inline-form" method="post" action="post_delete.php" data-confirm="Retirer ce sujet du forum ?">
                    <input type="hidden" name="_csrf" value="<?= h(csrf_token()) ?>">
                    <input type="hidden" name="id" value="<?= (int) $post['id'] ?>">
                    <button type="submit" class="btn btn--danger btn--small">Retirer</button>
                </form>
            </div>
        <?php endif; ?>
    </header>
    <div class="post-detail__body">
        <?= nl2br(h((string) $post['contenu']), false) ?>
    </div>
</article>

<section class="replies-section">
    <h2>Réponses (<?= count($replies) ?>)</h2>
    <?php if ($replies === []) : ?>
        <p class="text-muted">Pas encore de réponse.</p>
    <?php else : ?>
        <ul class="reply-list">
            <?php foreach ($replies as $r) : ?>
                <?php
                $isAuthor = current_user_id() !== null && (int) $r['user_id'] === (int) current_user_id();
                $rr = $reactionSummary['replies'][$r['id']] ?? ['likes' => 0, 'dislikes' => 0, 'user' => null];
                ?>
                <li class="reply-card card card--compact">
                    <div class="reply-card__meta text-muted">
                        <?= h(trim((string) $r['auteur_nom'] . ' ' . (string) $r['auteur_prenom'])) ?>
                        · <time datetime="<?= h((string) $r['created_at']) ?>"><?= h(date('d/m/Y H:i', strtotime((string) $r['created_at']))) ?></time>
                    </div>
                    <div class="reply-card__body"><?= nl2br(h((string) $r['contenu']), false) ?></div>
                    <div class="reaction-bar reaction-bar--reply" aria-label="Réactions à cette réponse">
                        <span class="reaction-counts">👍 <?= (int) $rr['likes'] ?> · 👎 <?= (int) $rr['dislikes'] ?></span>
                        <?php if ($currentForumUser !== null) : ?>
                            <form class="reaction-form" method="post" action="reaction_store.php">
                                <input type="hidden" name="_csrf" value="<?= h(csrf_token()) ?>">
                                <input type="hidden" name="target" value="reply">
                                <input type="hidden" name="target_id" value="<?= (int) $r['id'] ?>">
                                <input type="hidden" name="post_id" value="<?= (int) $post['id'] ?>">
                                <input type="hidden" name="type" value="like">
                                <button type="submit" class="btn btn--reaction btn--small <?= ($rr['user'] ?? null) === 'like' ? 'is-active' : '' ?>">👍</button>
                            </form>
                            <form class="reaction-form" method="post" action="reaction_store.php">
                                <input type="hidden" name="_csrf" value="<?= h(csrf_token()) ?>">
                                <input type="hidden" name="target" value="reply">
                                <input type="hidden" name="target_id" value="<?= (int) $r['id'] ?>">
                                <input type="hidden" name="post_id" value="<?= (int) $post['id'] ?>">
                                <input type="hidden" name="type" value="dislike">
                                <button type="submit" class="btn btn--reaction btn--small <?= ($rr['user'] ?? null) === 'dislike' ? 'is-active' : '' ?>">👎</button>
                            </form>
                        <?php endif; ?>
                    </div>
                    <?php if ($isAuthor) : ?>
                        <div class="reply-card__owner-actions">
                            <a class="btn btn--secondary btn--small" href="reply_edit.php?id=<?= (int) $r['id'] ?>">Modifier</a>
                            <form class="inline-form" method="post" action="reply_delete.php" data-confirm="Retirer cette réponse ?">
                                <input type="hidden" name="_csrf" value="<?= h(csrf_token()) ?>">
                                <input type="hidden" name="post_id" value="<?= (int) $post['id'] ?>">
                                <input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
                                <button type="submit" class="btn btn--link">Retirer ma réponse</button>
                            </form>
                        </div>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</section>

<?php if ($currentForumUser !== null) : ?>
    <section class="reply-form-section card">
        <h2>Votre réponse</h2>
        <form method="post" action="reply_store.php" class="form" novalidate>
            <input type="hidden" name="_csrf" value="<?= h(csrf_token()) ?>">
            <input type="hidden" name="post_id" value="<?= (int) $post['id'] ?>">
            <div class="form__group">
                <label for="contenu">Message</label>
                <textarea id="contenu" name="contenu" rows="5" required></textarea>
            </div>
            <button type="submit" class="btn">Publier la réponse</button>
        </form>
    </section>
<?php else : ?>
    <p class="card card--muted"><a href="login.php">Connectez-vous</a> pour répondre ou réagir.</p>
<?php endif; ?>

<?php require dirname(__DIR__) . '/layout/footer.php'; ?>
