<?php
// view/frontoffice/forum/search.php
include_once __DIR__ . '/../../../controller/ControlPost.php';
include_once __DIR__ . '/../../assets/forum_nav.php';
if (session_status() === PHP_SESSION_NONE) session_start();

$ctrl = new ControlPost();
$q = trim($_GET['q'] ?? '');
$posts = $q ? $ctrl->searchPosts($q) : [];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Résultats de recherche - Forum Communautaire</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="/view/assets/forum.css">
</head>
<body>

<?php forumNav('front', 'liste'); ?>

<div class="forum-wrapper">

    <!-- HERO -->
    <div class="forum-hero">
        <h1><i class="fas fa-search"></i> Résultats de recherche</h1>
        <p>Résultats pour "<?= htmlspecialchars($q) ?>"</p>
        <a href="/view/frontoffice/forum/liste.php" class="btn-new" style="background: #6c757d;"><i class="fas fa-arrow-left"></i> Retour au forum</a>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($_SESSION['success']) ?></div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <!-- SEARCH -->
    <div class="search-wrap">
        <form action="/view/frontoffice/forum/search.php" method="GET" style="display: flex; align-items: center;">
            <i class="fas fa-search"></i>
            <input type="text" name="q" id="forumSearch" placeholder="Rechercher un sujet, un mot-clé..." value="<?= htmlspecialchars($q) ?>">
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
            <i class="fas fa-search"></i>
            <h3>Aucun résultat trouvé</h3>
            <p>Essayez avec d'autres mots-clés.</p>
            <a href="/view/frontoffice/forum/liste.php" class="btn btn-primary" style="margin-top:16px">
                <i class="fas fa-list"></i> Voir tous les posts
            </a>
        </div>
        <?php endif; ?>
    </div>

</div>

<script src="/view/assets/forum.js"></script>
</body>
</html>