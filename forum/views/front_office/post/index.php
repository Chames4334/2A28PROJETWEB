<?php
declare(strict_types=1);
/** @var string $pageTitle */
/** @var list<array<string, mixed>> $posts */
/** Sécurise l’accès direct au gabarit sans extract() (hors scripts dans views/*.php). */
$posts = $posts ?? [];

require dirname(__DIR__) . '/layout/header.php';
?>
<div class="page-heading">
    <h1>Sujets récents</h1>
    <p class="text-muted">Échanges de la communauté — seuls les sujets actifs sont affichés.</p>
</div>

<?php if ($posts === []) : ?>
    <p class="card card--empty">Aucun sujet pour le moment. Soyez le premier à en créer un !</p>
<?php else : ?>
    <ul class="post-list">
        <?php foreach ($posts as $row) : ?>
            <li class="post-card">
                <div class="post-card__meta">
                    <?php if (!empty($row['is_pinned'])) : ?>
                        <span class="badge badge--pin">Épinglé</span>
                    <?php endif; ?>
                    <time datetime="<?= h((string) $row['created_at']) ?>"><?= h(date('d/m/Y H:i', strtotime((string) $row['created_at']))) ?></time>
                    <span class="post-card__author"><?= h(trim((string) $row['auteur_nom'] . ' ' . (string) $row['auteur_prenom'])) ?></span>
                    <span class="post-card__replies"><?= (int) $row['reply_count'] ?> rép.</span>
                </div>
                <h2 class="post-card__title"><a href="post.php?id=<?= (int) $row['id'] ?>"><?= h((string) $row['titre']) ?></a></h2>
                <p class="post-card__excerpt"><?= h(excerpt((string) $row['contenu'])) ?></p>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<?php require dirname(__DIR__) . '/layout/footer.php'; ?>
