<?php
// view/backoffice/forum/modifier.php
include_once __DIR__ . '/../../../controller/ControlPost.php';
include_once __DIR__ . '/../../assets/forum_nav.php';
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    header('Location: ../../auth/login.php'); exit;
}

$ctrl = new ControlPost();
$id   = intval($_GET['id'] ?? 0);
$post = $ctrl->getPostById($id);
$tagsEnabled = $ctrl->tagSystemReady();
$tags = $tagsEnabled ? $ctrl->getAllTags() : [];
if (!$post) { header('Location: liste.php'); exit; }

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre     = trim($_POST['titre']   ?? '');
    $contenu   = trim($_POST['contenu'] ?? '');
    $is_pinned = isset($_POST['is_pinned']) ? 1 : 0;
    $statut    = in_array($_POST['statut'] ?? '', ['actif','masque','supprime']) ? $_POST['statut'] : 'actif';
    $tag_id    = $tagsEnabled && ($_POST['tag_id'] ?? '') !== '' ? intval($_POST['tag_id']) : null;

    // SERVER-SIDE validation
    if (strlen($titre) < 5)       $errors['titre']   = "Minimum 5 caractères.";
    elseif (strlen($titre) > 200) $errors['titre']   = "Maximum 200 caractères.";
    if (strlen($contenu) < 10)    $errors['contenu'] = "Minimum 10 caractères.";
    if ($tagsEnabled && !$ctrl->tagExists($tag_id)) $errors['tag_id'] = "Tag invalide.";

    if (empty($errors)) {
        $updated = new Post($post['user_id'], $titre, $contenu, $is_pinned, $statut, $tag_id);
        $ctrl->updatePost($updated, $id);
        $_SESSION['success'] = "Post modifié avec succès.";
        header('Location: liste.php'); exit;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier Post - Back Office Green Assurance</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/forum.css">
</head>
<body>
<div class="bo-layout">
    <?php forumNav('back', 'liste'); ?>
    <div class="bo-main">
        <div class="back-nav" style="margin-bottom:20px;background:transparent;padding:0">
            <a href="liste.php"><i class="fas fa-arrow-left"></i> Retour à la liste</a>
            <a href="../../frontoffice/forum/detail.php?id=<?= $id ?>"
               style="margin-left:auto" class="btn btn-secondary btn-sm">
                <i class="fas fa-eye"></i> Voir dans le Front Office
            </a>
        </div>
        <div class="form-card" style="max-width:820px">
            <h2><i class="fas fa-edit"></i> Modifier Post #<?= $id ?></h2>
            <form method="POST" action="" id="postForm" novalidate>
                <div class="form-group <?= isset($errors['titre']) ? 'has-error' : '' ?>">
                    <label><i class="fas fa-heading"></i> Titre *</label>
                    <input type="text" name="titre" id="titre"
                           value="<?= htmlspecialchars($_POST['titre'] ?? $post['titre']) ?>"
                           data-maxlength="200">
                    <div class="char-count" id="titre_count"></div>
                    <?php if (isset($errors['titre'])): ?>
                        <span class="error-msg"><?= $errors['titre'] ?></span>
                    <?php endif; ?>
                </div>
                <div class="form-group <?= isset($errors['contenu']) ? 'has-error' : '' ?>">
                    <label><i class="fas fa-align-left"></i> Contenu *</label>
                    <textarea name="contenu" id="contenu" rows="10"
                              class="auto-resize" data-maxlength="5000"><?= htmlspecialchars($_POST['contenu'] ?? $post['contenu']) ?></textarea>
                    <div class="char-count" id="contenu_count"></div>
                    <?php if (isset($errors['contenu'])): ?>
                        <span class="error-msg"><?= $errors['contenu'] ?></span>
                    <?php endif; ?>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label><i class="fas fa-toggle-on"></i> Statut</label>
                        <select name="statut">
                            <?php foreach (['actif'=>'Actif','masque'=>'Masqué','supprime'=>'Supprimé'] as $val=>$label): ?>
                            <option value="<?= $val ?>"
                                <?= (($_POST['statut'] ?? $post['statut'])===$val) ? 'selected':'' ?>>
                                <?= $label ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-thumbtack"></i> Options</label>
                        <label style="display:flex;align-items:center;gap:8px;margin-top:10px;cursor:pointer;font-weight:500">
                            <input type="checkbox" name="is_pinned" value="1" style="width:auto;"
                                   <?= (isset($_POST['is_pinned']) ? (bool)$_POST['is_pinned'] : (bool)$post['is_pinned']) ? 'checked' : '' ?>>
                            Épingler ce post en haut du forum
                        </label>
                    </div>
                </div>
                <?php if ($tagsEnabled): ?>
                <div class="form-group <?= isset($errors['tag_id']) ? 'has-error' : '' ?>">
                    <label><i class="fas fa-tag"></i> Tag</label>
                    <select name="tag_id">
                        <option value="">Sans tag</option>
                        <?php foreach ($tags as $tag): ?>
                            <option value="<?= $tag['id'] ?>"
                                <?= (string)($_POST['tag_id'] ?? $post['tag_id'] ?? '') === (string)$tag['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($tag['name'] . (!empty($tag['color']) ? ' (' . $tag['color'] . ')' : '')) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($errors['tag_id'])): ?>
                        <span class="error-msg"><?= $errors['tag_id'] ?></span>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                <div class="form-actions">
                    <a href="liste.php" class="btn btn-secondary"><i class="fas fa-times"></i> Annuler</a>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="../../assets/forum.js"></script>
</body>
</html>
