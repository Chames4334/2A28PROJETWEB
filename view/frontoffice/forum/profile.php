<?php
// view/frontoffice/forum/profile.php
include_once __DIR__ . '/../../../controller/ControlPost.php';
include_once __DIR__ . '/../../../controller/ControlReply.php';
include_once __DIR__ . '/../../assets/forum_nav.php';
if (session_status() === PHP_SESSION_NONE) session_start();

$postCtrl = new ControlPost();
$replyCtrl = new ControlReply();

$user_id = intval($_GET['user_id'] ?? 0);
$user = $postCtrl->getForumUserById($user_id);
if (!$user) { header('Location: liste.php'); exit; }

$tab = $_GET['tab'] ?? 'posts';
if (!in_array($tab, ['posts', 'replies'], true)) $tab = 'posts';
$sort = $_GET['sort'] ?? 'recent';
if (!in_array($sort, ['recent', 'likes'], true)) $sort = 'recent';

$posts = $tab === 'posts' ? $postCtrl->getPostsByUser($user_id, $sort) : [];
$replies = $tab === 'replies' ? $replyCtrl->getRepliesByUser($user_id, $sort) : [];
$fullName = trim($user['prenom'] . ' ' . $user['nom']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($fullName) ?> - Profil Forum</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="/view/assets/forum.css">
</head>
<body>

<?php forumNav('front', ''); ?>

<div class="back-nav">
    <a href="/view/frontoffice/forum/liste.php"><i class="fas fa-arrow-left"></i> Retour au forum</a>
</div>

<div class="forum-wrapper">
    <div class="post-detail">
        <div class="post-meta">
            <span class="avatar avatar-lg"><?= strtoupper(substr($user['prenom'], 0, 1) . substr($user['nom'], 0, 1)) ?></span>
            <div>
                <h1 style="margin-bottom:4px"><?= htmlspecialchars($fullName) ?></h1>
                <span class="meta-chip"><i class="fas fa-calendar-alt"></i> Membre depuis <?= date('d/m/Y', strtotime($user['created_at'])) ?></span>
            </div>
        </div>
    </div>

    <div class="tabs">
        <a href="profile.php?user_id=<?= $user_id ?>&tab=posts&sort=<?= $sort ?>" class="tab <?= $tab === 'posts' ? 'active' : '' ?>">
            <i class="fas fa-file-alt"></i> Posts
        </a>
        <a href="profile.php?user_id=<?= $user_id ?>&tab=replies&sort=<?= $sort ?>" class="tab <?= $tab === 'replies' ? 'active' : '' ?>">
            <i class="fas fa-comments"></i> Réponses
        </a>
    </div>

    <div class="search-wrap">
        <form method="GET" class="sort-form">
            <input type="hidden" name="user_id" value="<?= $user_id ?>">
            <input type="hidden" name="tab" value="<?= htmlspecialchars($tab) ?>">
            <select name="sort" class="sort-select" aria-label="Trier">
                <option value="recent" <?= $sort === 'recent' ? 'selected' : '' ?>>Plus récents</option>
                <option value="likes" <?= $sort === 'likes' ? 'selected' : '' ?>>Plus aimés</option>
            </select>
        </form>
    </div>

    <div class="posts-list">
        <?php if ($tab === 'posts'): ?>
            <?php foreach ($posts as $p): ?>
                <a href="/view/frontoffice/forum/detail.php?id=<?= $p['id'] ?>" class="post-card" data-searchable>
                    <div class="post-title"><?= htmlspecialchars($p['titre']) ?></div>
                    <div class="post-excerpt"><?= htmlspecialchars($p['contenu']) ?></div>
                    <div class="post-meta">
                        <span class="meta-chip"><i class="fas fa-calendar-alt"></i> <?= date('d/m/Y', strtotime($p['created_at'])) ?></span>
                        <span class="meta-chip"><i class="fas fa-comment"></i> <?= $p['nb_replies'] ?></span>
                        <span class="meta-chip like"><i class="fas fa-thumbs-up"></i> <?= $p['nb_likes'] ?></span>
                        <span class="meta-chip dislike"><i class="fas fa-thumbs-down"></i> <?= $p['nb_dislikes'] ?></span>
                    </div>
                </a>
            <?php endforeach; ?>
            <?php if (empty($posts)): ?>
                <div class="empty-state"><i class="fas fa-file-alt"></i><h3>Aucun post</h3></div>
            <?php endif; ?>
        <?php else: ?>
            <?php foreach ($replies as $r): ?>
                <a href="/view/frontoffice/forum/detail.php?id=<?= $r['post_id'] ?>#reply-<?= $r['id'] ?>" class="post-card" data-searchable>
                    <div class="post-title"><?= htmlspecialchars($r['post_titre']) ?></div>
                    <div class="post-excerpt"><?= htmlspecialchars($r['contenu']) ?></div>
                    <div class="post-meta">
                        <span class="meta-chip"><i class="fas fa-calendar-alt"></i> <?= date('d/m/Y', strtotime($r['created_at'])) ?></span>
                        <span class="meta-chip like"><i class="fas fa-thumbs-up"></i> <?= $r['nb_likes'] ?></span>
                        <span class="meta-chip dislike"><i class="fas fa-thumbs-down"></i> <?= $r['nb_dislikes'] ?></span>
                    </div>
                </a>
            <?php endforeach; ?>
            <?php if (empty($replies)): ?>
                <div class="empty-state"><i class="fas fa-comments"></i><h3>Aucune réponse</h3></div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<script src="/view/assets/forum.js"></script>
</body>
</html>
