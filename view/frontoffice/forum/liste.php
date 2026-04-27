<?php
// view/frontoffice/forum/liste.php
include_once __DIR__ . '/../../../controller/ControlPost.php';
include_once __DIR__ . '/../../assets/forum_nav.php';
if (session_status() === PHP_SESSION_NONE) session_start();

$ctrl  = new ControlPost();
$posts = $ctrl->listePosts();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Forum Communautaire - Green Assurance</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="/view/assets/forum.css">
</head>
<body>

<?php forumNav('front', 'liste'); ?>

<div class="forum-wrapper">

    <!-- HERO -->
    <div class="forum-hero">
        <h1><i class="fas fa-comments"></i> Forum Communautaire</h1>
        <p>Échangez, posez vos questions et partagez vos expériences avec la communauté Green Assurance.</p>
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="/view/frontoffice/forum/ajout.php" class="btn-new"><i class="fas fa-plus"></i> Créer un post</a>
        <?php else: ?>
            <a href="/view/auth/login.php" class="btn-new"><i class="fas fa-sign-in-alt"></i> Connectez-vous pour participer</a>
        <?php endif; ?>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($_SESSION['success']) ?></div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <!-- SEARCH -->
    <div class="search-wrap">
        <form action="/view/frontoffice/forum/search.php" method="GET" style="display: flex; align-items: center;">
            <i class="fas fa-search"></i>
            <input type="text" name="q" id="forumSearch" placeholder="Rechercher un sujet, un mot-clé..." value="">
            <button type="submit" style="display: none;"></button>
        </form>
    </div>

    <!-- POSTS LIST -->
    <div class="posts-list">
        <?php
        $hasPost = false;
        foreach ($posts as $post):
            $hasPost  = true;
            $initiales = strtoupper(substr($post['prenom'], 0, 1) . substr($post['nom'], 0, 1));
        ?>
        <a href="/view/frontoffice/forum/detail.php?id=<?= $post['id'] ?>"
           class="post-card <?= $post['is_pinned'] ? 'pinned' : '' ?>"
           data-searchable>

            <?php if ($post['is_pinned']): ?>
                <span class="pin-badge"><i class="fas fa-thumbtack"></i> Épinglé</span>
            <?php endif; ?>

            <div class="post-title"><?= htmlspecialchars($post['titre']) ?></div>
            <div class="post-excerpt"><?= htmlspecialchars($post['contenu']) ?></div>

            <div class="post-meta">
                <span class="author">
                    <span class="avatar"><?= $initiales ?></span>
                    <?= htmlspecialchars($post['prenom'] . ' ' . $post['nom']) ?>
                </span>
                <span class="meta-chip">
                    <i class="fas fa-calendar-alt"></i>
                    <?= date('d/m/Y', strtotime($post['created_at'])) ?>
                </span>
                <span class="meta-chip">
                    <i class="fas fa-comment"></i>
                    <?= $post['nb_replies'] ?> réponse<?= $post['nb_replies'] != 1 ? 's' : '' ?>
                </span>
                <span class="meta-chip like"><i class="fas fa-thumbs-up"></i> <?= $post['nb_likes'] ?></span>
                <span class="meta-chip dislike"><i class="fas fa-thumbs-down"></i> <?= $post['nb_dislikes'] ?></span>
            </div>
        </a>
        <?php endforeach; ?>

        <?php if (!$hasPost): ?>
        <div class="empty-state">
            <i class="fas fa-comments"></i>
            <h3>Aucun post pour l'instant</h3>
            <p>Soyez le premier à lancer une discussion !</p>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="/view/frontoffice/forum/ajout.php" class="btn btn-primary" style="margin-top:16px">
                    <i class="fas fa-plus"></i> Créer le premier post
                </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>

</div>

<script src="/view/assets/forum.js"></script>
</body>
</html>
