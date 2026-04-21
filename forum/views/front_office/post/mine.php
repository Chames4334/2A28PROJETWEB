<?php
declare(strict_types=1);
/** @var string $pageTitle */
/** @var list<array<string, mixed>> $posts */
$posts = $posts ?? [];

require dirname(__DIR__) . '/layout/header.php';
?>
<div class="page-heading">
    <h1>Mes sujets</h1>
    <p class="text-muted">Sujets actifs que vous avez créés.</p>
</div>

<?php if ($posts === []) : ?>
    <p class="card card--empty">Vous n’avez pas encore créé de sujet. <a href="create.php">En créer un</a>.</p>
<?php else : ?>
    <ul class="post-list">
        <?php foreach ($posts as $row) : ?>
            <li class="post-card">
                <div class="post-card__meta">
                    <?php if (!empty($row['is_pinned'])) : ?>
                        <span class="badge badge--pin">Épinglé</span>
                    <?php endif; ?>
                    <time datetime="<?= h((string) $row['created_at']) ?>"><?= h(date('d/m/Y H:i', strtotime((string) $row['created_at']))) ?></time>
                    <span class="post-card__replies"><?= (int) $row['reply_count'] ?> rép.</span>
                </div>
                <h2 class="post-card__title"><a href="post.php?id=<?= (int) $row['id'] ?>"><?= h((string) $row['titre']) ?></a></h2>
                <p class="post-card__excerpt"><?= h(excerpt((string) $row['contenu'])) ?></p>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<?php require dirname(__DIR__) . '/layout/footer.php'; ?>
