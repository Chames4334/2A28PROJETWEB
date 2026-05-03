<?php
// view/backoffice/forum/tags.php
include_once __DIR__ . '/../../../controller/ControlPost.php';
include_once __DIR__ . '/../../assets/forum_nav.php';
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    header('Location: ../../auth/login.php'); exit;
}

$ctrl = new ControlPost();
$errors = [];
$old = ['name' => '', 'color' => '#6b8f3a'];
$tagsTableExists = $ctrl->tagsTableExists();

function validTagColor($color) {
    return (bool)preg_match('/^#[0-9a-fA-F]{6}$/', $color);
}

function tagColorForStyle($color) {
    return validTagColor($color) ? $color : '#6b8f3a';
}

if ($tagsTableExists && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add' || $action === 'update') {
        $name = trim($_POST['name'] ?? '');
        $color = trim($_POST['color'] ?? '');
        $id = intval($_POST['id'] ?? 0);

        if ($name === '') $errors['name'] = "Le nom du tag est obligatoire.";
        elseif (mb_strlen($name) > 50) $errors['name'] = "Le nom ne peut pas dépasser 50 caractères.";
        if (!validTagColor($color)) $errors['color'] = "La couleur doit être au format #RRGGBB.";

        if (empty($errors)) {
            if ($action === 'add') {
                $ctrl->addTag($name, $color);
                $_SESSION['success'] = "Tag ajouté avec succès.";
            } else {
                $tag = $ctrl->getTagById($id);
                if ($tag) {
                    $ctrl->updateTag($id, $name, $color);
                    $_SESSION['success'] = "Tag modifié avec succès.";
                }
            }
            header('Location: tags.php'); exit;
        }

        $old = ['name' => $name, 'color' => $color, 'id' => $id, 'action' => $action];
    }

    if ($action === 'delete') {
        $id = intval($_POST['id'] ?? 0);
        if ($ctrl->getTagById($id)) {
            $ctrl->deleteTag($id);
            $_SESSION['success'] = "Tag supprimé avec succès.";
        }
        header('Location: tags.php'); exit;
    }
}

$tags = $tagsTableExists ? $ctrl->getAllTags() : [];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tags Forum - Back Office Green Assurance</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/forum.css">
</head>
<body>
<div class="bo-layout">
    <?php forumNav('back', 'tags'); ?>
    <div class="bo-main">

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?= htmlspecialchars($_SESSION['success']) ?>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (!$tagsTableExists): ?>
            <div class="alert alert-warning">
                <i class="fas fa-triangle-exclamation"></i> La table tags est absente de la base actuelle.
            </div>
        <?php endif; ?>

        <div class="card-head">
            <h2 class="section-title"><i class="fas fa-tags"></i> Gestion des Tags</h2>
        </div>

        <?php if ($tagsTableExists): ?>
        <div class="form-card" style="max-width:820px;margin:0 0 28px">
            <h2><i class="fas fa-plus-circle"></i> Ajouter un Tag</h2>
            <form method="POST" action="" novalidate>
                <input type="hidden" name="action" value="add">
                <div class="form-row">
                    <div class="form-group <?= isset($errors['name']) && ($old['action'] ?? 'add') === 'add' ? 'has-error' : '' ?>">
                        <label><i class="fas fa-tag"></i> Nom *</label>
                        <input type="text" name="name" value="<?= htmlspecialchars(($old['action'] ?? 'add') === 'add' ? $old['name'] : '') ?>" maxlength="50">
                        <?php if (isset($errors['name']) && ($old['action'] ?? 'add') === 'add'): ?>
                            <span class="error-msg"><?= $errors['name'] ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="form-group <?= isset($errors['color']) && ($old['action'] ?? 'add') === 'add' ? 'has-error' : '' ?>">
                        <label><i class="fas fa-palette"></i> Couleur *</label>
                        <input type="color" name="color" value="<?= htmlspecialchars(($old['action'] ?? 'add') === 'add' ? $old['color'] : '#6b8f3a') ?>">
                        <?php if (isset($errors['color']) && ($old['action'] ?? 'add') === 'add'): ?>
                            <span class="error-msg"><?= $errors['color'] ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Ajouter</button>
                </div>
            </form>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr><th>#</th><th>Aperçu</th><th>Modifier</th><th>Actions</th></tr>
                </thead>
                <tbody>
                <?php foreach ($tags as $tag): ?>
                    <?php $tagColor = tagColorForStyle($tag['color'] ?? ''); ?>
                    <tr>
                        <td><?= $tag['id'] ?></td>
                        <td>
                            <span class="forum-tag forum-tag-sm" style="background-color:<?= htmlspecialchars($tagColor) ?>">
                                <?= htmlspecialchars($tag['name']) ?>
                            </span>
                        </td>
                        <td>
                            <form method="POST" action="" class="inline-tag-form" novalidate>
                                <input type="hidden" name="action" value="update">
                                <input type="hidden" name="id" value="<?= $tag['id'] ?>">
                                <input type="text" name="name" value="<?= htmlspecialchars(($old['action'] ?? '') === 'update' && (int)($old['id'] ?? 0) === (int)$tag['id'] ? $old['name'] : $tag['name']) ?>" maxlength="50">
                                <input type="color" name="color" value="<?= htmlspecialchars(($old['action'] ?? '') === 'update' && (int)($old['id'] ?? 0) === (int)$tag['id'] ? tagColorForStyle($old['color']) : $tagColor) ?>">
                                <button type="submit" class="btn-icon edit" title="Modifier">
                                    <i class="fas fa-save"></i>
                                </button>
                                <?php if (($old['action'] ?? '') === 'update' && (int)($old['id'] ?? 0) === (int)$tag['id']): ?>
                                    <?php foreach ($errors as $error): ?>
                                        <span class="error-msg"><?= htmlspecialchars($error) ?></span>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </form>
                        </td>
                        <td class="action-cell">
                            <form method="POST" action="" style="display:inline">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= $tag['id'] ?>">
                                <button type="submit" class="btn-icon del" title="Supprimer" data-confirm="Supprimer ce tag ? Les posts associés passeront en Sans tag.">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($tags)): ?>
                    <tr><td colspan="4" style="text-align:center;color:var(--text-light);padding:28px">Aucun tag.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>
<script src="../../assets/forum.js"></script>
</body>
</html>
