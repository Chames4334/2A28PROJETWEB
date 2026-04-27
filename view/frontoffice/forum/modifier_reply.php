<?php
// view/frontoffice/forum/modifier_reply.php
include_once __DIR__ . '/../../../controller/ControlReply.php';
include_once __DIR__ . '/../../assets/forum_nav.php';
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id'])) { header('Location: ../../auth/login.php'); exit; }

$ctrl    = new ControlReply();
$id      = intval($_GET['id']      ?? 0);
$post_id = intval($_GET['post_id'] ?? 0);
$reply   = $ctrl->getReplyById($id);

if (!$reply || $reply['user_id'] != $_SESSION['user_id']) {
    header("Location: detail.php?id=$post_id"); exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $contenu = trim($_POST['contenu'] ?? '');
    // SERVER-SIDE validation
    if (strlen($contenu) < 3) $errors['contenu'] = "La réponse doit contenir au moins 3 caractères.";

    if (empty($errors)) {
        $updated = new Reply($reply['post_id'], $reply['user_id'], $contenu, $reply['parent_reply_id']);
        $ctrl->updateReply($updated, $id);
        $_SESSION['success'] = "Réponse modifiée.";
        header("Location: detail.php?id=$post_id#reply-$id"); exit;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier Réponse - Forum Green Assurance</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/forum.css">
</head>
<body>
<?php forumNav('front', ''); ?>
<div class="back-nav">
    <a href="detail.php?id=<?= $post_id ?>"><i class="fas fa-arrow-left"></i> Retour au post</a>
</div>
<div class="forum-wrapper">
    <div class="form-card">
        <h2><i class="fas fa-comment-edit"></i> Modifier ma Réponse</h2>
        <form method="POST" class="reply-form" novalidate>
            <div class="form-group <?= isset($errors['contenu']) ? 'has-error' : '' ?>">
                <label><i class="fas fa-comment"></i> Contenu *</label>
                <textarea name="contenu" id="contenu" rows="5"
                          class="auto-resize" data-maxlength="1000"><?= htmlspecialchars($_POST['contenu'] ?? $reply['contenu']) ?></textarea>
                <div class="char-count" id="contenu_count"></div>
                <?php if (isset($errors['contenu'])): ?>
                    <span class="error-msg"><?= $errors['contenu'] ?></span>
                <?php endif; ?>
            </div>
            <div class="form-actions">
                <a href="detail.php?id=<?= $post_id ?>" class="btn btn-secondary"><i class="fas fa-times"></i> Annuler</a>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Enregistrer</button>
            </div>
        </form>
    </div>
</div>
<script src="../../assets/forum.js"></script>
</body>
</html>
