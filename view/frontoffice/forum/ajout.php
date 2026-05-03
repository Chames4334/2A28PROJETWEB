<?php
// view/frontoffice/forum/ajout.php
include_once __DIR__ . '/../../../controller/ControlPost.php';
include_once __DIR__ . '/../../assets/forum_nav.php';
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id'])) { header('Location: ../../auth/login.php'); exit; }

$errors = [];
$old    = [];
$ctrl   = new ControlPost();
$tagsEnabled = $ctrl->tagSystemReady();
$tags = $tagsEnabled ? $ctrl->getAllTags() : [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre   = trim($_POST['titre']   ?? '');
    $contenu = trim($_POST['contenu'] ?? '');
    $tag_id  = $tagsEnabled && ($_POST['tag_id'] ?? '') !== '' ? intval($_POST['tag_id']) : null;

    // SERVER-SIDE validation (côté serveur PHP)
    if (strlen($titre) < 5)    $errors['titre']   = "Le titre doit contenir au moins 5 caractères.";
    elseif (strlen($titre) > 200) $errors['titre'] = "Le titre ne peut pas dépasser 200 caractères.";
    if (strlen($contenu) < 10) $errors['contenu']  = "Le contenu doit contenir au moins 10 caractères.";
    if ($tagsEnabled && !$ctrl->tagExists($tag_id)) $errors['tag_id'] = "Le tag sélectionné est invalide.";

    if (empty($errors)) {
        $post = new Post($_SESSION['user_id'], $titre, $contenu, 0, 'actif', $tag_id);
        $ctrl->addPost($post);
        $_SESSION['success'] = "Votre post a été publié avec succès !";
        header('Location: liste.php'); exit;
    } else {
        $old = ['titre' => $titre, 'contenu' => $contenu, 'tag_id' => $tag_id];
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Nouveau Post - Forum Green Assurance</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/forum.css">
</head>
<body>

<?php forumNav('front', 'ajout'); ?>

<div class="back-nav">
    <a href="liste.php"><i class="fas fa-arrow-left"></i> Retour au forum</a>
</div>

<div class="forum-wrapper">
    <div class="form-card">
        <h2><i class="fas fa-plus-circle"></i> Nouveau Post</h2>

        <form method="POST" action="" id="postForm" novalidate>

            <div class="form-group <?= isset($errors['titre']) ? 'has-error' : '' ?>">
                <label><i class="fas fa-heading"></i> Titre du post *</label>
                <input type="text" name="titre" id="titre"
                       value="<?= htmlspecialchars($old['titre'] ?? '') ?>"
                       placeholder="Quel est votre sujet ?"
                       data-maxlength="200">
                <div class="char-count" id="titre_count"></div>
                <?php if (isset($errors['titre'])): ?>
                    <span class="error-msg"><?= $errors['titre'] ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group <?= isset($errors['contenu']) ? 'has-error' : '' ?>">
                <label><i class="fas fa-align-left"></i> Contenu *</label>
                <textarea name="contenu" id="contenu" rows="8"
                          placeholder="Décrivez votre sujet en détail..."
                          class="auto-resize"
                          data-maxlength="5000"><?= htmlspecialchars($old['contenu'] ?? '') ?></textarea>
                <div class="char-count" id="contenu_count"></div>
                <?php if (isset($errors['contenu'])): ?>
                    <span class="error-msg"><?= $errors['contenu'] ?></span>
                <?php endif; ?>
            </div>

            <?php if ($tagsEnabled): ?>
            <div class="form-group <?= isset($errors['tag_id']) ? 'has-error' : '' ?>">
                <label><i class="fas fa-tag"></i> Tag</label>
                <select name="tag_id">
                    <option value="">Sans tag</option>
                    <?php foreach ($tags as $tag): ?>
                        <option value="<?= $tag['id'] ?>"
                            <?= (string)($old['tag_id'] ?? '') === (string)$tag['id'] ? 'selected' : '' ?>>
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
                <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Publier</button>
            </div>
        </form>
    </div>
</div>

<script src="../../assets/forum.js"></script>
</body>
</html>
