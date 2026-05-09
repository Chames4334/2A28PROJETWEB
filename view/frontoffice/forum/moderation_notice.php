<?php
// view/frontoffice/forum/moderation_notice.php
include_once __DIR__ . '/../../assets/forum_nav.php';
if (session_status() === PHP_SESSION_NONE) session_start();

$type = ($_GET['type'] ?? '') === 'reply' ? 'reply' : 'post';
$postId = intval($_GET['post_id'] ?? 0);
$label = $type === 'reply' ? 'réponse' : 'post';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Contenu en modération - Forum Green Assurance</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/forum.css">
</head>
<body>
<?php forumNav('front', ''); ?>

<div class="forum-wrapper">
    <div class="post-detail moderation-notice">
        <span class="badge badge-masque"><i class="fas fa-triangle-exclamation"></i> En modération</span>
        <h1>Votre <?= htmlspecialchars($label) ?> est en attente de validation</h1>
        <p>
            Notre système de modération automatique a détecté que votre contenu doit être vérifié par un administrateur avant publication.
        </p>
        <p>
            Merci de respecter les règles du forum : pas de spam, d'insultes, de contenu toxique, de promotion abusive ou de messages hors sujet.
        </p>
        <div class="form-actions" style="justify-content:flex-start">
            <?php if ($type === 'reply' && $postId): ?>
                <a href="detail.php?id=<?= $postId ?>" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Retour au post</a>
            <?php endif; ?>
            <a href="liste.php" class="btn btn-primary"><i class="fas fa-comments"></i> Retour au forum</a>
        </div>
    </div>
</div>

<script src="../../assets/forum.js"></script>
</body>
</html>
