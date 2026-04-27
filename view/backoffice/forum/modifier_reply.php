<?php
// view/backoffice/forum/modifier_reply.php
include_once __DIR__ . '/../../../controller/ControlReply.php';
include_once __DIR__ . '/../../assets/forum_nav.php';
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    header('Location: ../../auth/login.php'); exit;
}

$ctrl    = new ControlReply();
$id      = intval($_GET['id']      ?? 0);
$post_id = intval($_GET['post_id'] ?? 0);
$reply   = $ctrl->getReplyById($id);
if (!$reply) { header('Location: liste.php?tab=replies'); exit; }

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $contenu = trim($_POST['contenu'] ?? '');
    $statut  = in_array($_POST['statut'] ?? '', ['actif','masque','supprime']) ? $_POST['statut'] : 'actif';
    // SERVER-SIDE validation
    if (strlen($contenu) < 3) $errors['contenu'] = "La réponse doit contenir au moins 3 caractères.";

    if (empty($errors)) {
        $updated = new Reply($reply['post_id'], $reply['user_id'], $contenu, $reply['parent_reply_id'], $statut);
        $ctrl->updateReply($updated, $id);
        $_SESSION['success'] = "Réponse modifiée avec succès.";
        header('Location: liste.php?tab=replies'); exit;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier Réponse - Back Office Green Assurance</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/forum.css">
</head>
<body>
<div class="bo-layout">
    <?php forumNav('back', 'liste'); ?>
    <div class="bo-main">
        <div class="back-nav" style="margin-bottom:20px;background:transparent;padding:0">
            <a href="liste.php?tab=replies"><i class="fas fa-arrow-left"></i> Retour aux réponses</a>
            <a href="../../frontoffice/forum/detail.php?id=<?= $reply['post_id'] ?>#reply-<?= $id ?>"
               style="margin-left:auto" class="btn btn-secondary btn-sm">
                <i class="fas fa-eye"></i> Voir dans le Front Office
            </a>
        </div>
        <div class="form-card">
            <h2><i class="fas fa-comment-edit"></i> Modifier Réponse #<?= $id ?></h2>
            <div class="alert alert-warning">
                <i class="fas fa-user"></i>
                <strong>Auteur :</strong> <?= htmlspecialchars($reply['prenom'] . ' ' . $reply['nom']) ?>
                &nbsp;|&nbsp;
                <strong>Post ID :</strong> <?= $reply['post_id'] ?>
            </div>
            <form method="POST" action="" novalidate>
                <div class="form-group <?= isset($errors['contenu']) ? 'has-error' : '' ?>">
                    <label><i class="fas fa-comment"></i> Contenu *</label>
                    <textarea name="contenu" id="contenu" rows="5"
                              class="auto-resize" data-maxlength="1000"><?= htmlspecialchars($_POST['contenu'] ?? $reply['contenu']) ?></textarea>
                    <div class="char-count" id="contenu_count"></div>
                    <?php if (isset($errors['contenu'])): ?>
                        <span class="error-msg"><?= $errors['contenu'] ?></span>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-toggle-on"></i> Statut</label>
                    <select name="statut">
                        <?php foreach (['actif'=>'Actif','masque'=>'Masqué','supprime'=>'Supprimé'] as $val=>$label): ?>
                        <option value="<?= $val ?>"
                            <?= (($_POST['statut'] ?? $reply['statut'])===$val) ? 'selected':'' ?>>
                            <?= $label ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-actions">
                    <a href="liste.php?tab=replies" class="btn btn-secondary"><i class="fas fa-times"></i> Annuler</a>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="../../assets/forum.js"></script>
</body>
</html>
