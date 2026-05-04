<?php
// view/backoffice/forum/autoresponder.php
include_once __DIR__ . '/../../../controller/ControlAI.php';
include_once __DIR__ . '/../../../controller/ControlReply.php';
include_once __DIR__ . '/../../../model/Reply.php';
include_once __DIR__ . '/../../assets/forum_nav.php';
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    header('Location: ../../auth/login.php'); exit;
}

$aiCtrl = new ControlAI();
$replyCtrl = new ControlReply();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'save_prompt') {
        $prompt = trim($_POST['prompt'] ?? '');
        if ($aiCtrl->updateAutoResponderPrompt($prompt)) {
            $_SESSION['success'] = "Prompt de l'auto-répondeur enregistré.";
        } else {
            $_SESSION['error'] = "Impossible d'enregistrer le prompt.";
        }
        header('Location: autoresponder.php'); exit;
    }

    if ($action === 'update_reply') {
        $id = intval($_POST['id'] ?? 0);
        $contenu = trim($_POST['contenu'] ?? '');
        $statut = $_POST['statut'] ?? 'actif';
        $reply = $replyCtrl->getReplyById($id);

        if (!$reply || (int)$reply['user_id'] !== ControlAI::AUTO_RESPONDER_USER_ID) {
            $_SESSION['error'] = "Réponse introuvable.";
        } elseif ($contenu === '') {
            $_SESSION['error'] = "Le contenu de la réponse est obligatoire.";
        } elseif (!in_array($statut, ['actif', 'masque', 'supprime'], true)) {
            $_SESSION['error'] = "Statut invalide.";
        } else {
            $updated = new Reply($reply['post_id'], ControlAI::AUTO_RESPONDER_USER_ID, $contenu, $reply['parent_reply_id'], $statut);
            $replyCtrl->updateReply($updated, $id);
            $_SESSION['success'] = "Réponse de l'auto-répondeur modifiée.";
        }
        header('Location: autoresponder.php'); exit;
    }

    if ($action === 'delete_reply') {
        $id = intval($_POST['id'] ?? 0);
        $reply = $replyCtrl->getReplyById($id);
        if ($reply && (int)$reply['user_id'] === ControlAI::AUTO_RESPONDER_USER_ID) {
            $replyCtrl->hardDeleteReply($id);
            $_SESSION['success'] = "Réponse de l'auto-répondeur supprimée.";
        }
        header('Location: autoresponder.php'); exit;
    }
}

$prompt = $aiCtrl->getAutoResponderPrompt();
$replies = $replyCtrl->getAutoResponderReplies();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Auto-répondeur IA - Back Office Green Assurance</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/forum.css">
</head>
<body>
<div class="bo-layout">
    <?php forumNav('back', 'autoresponder'); ?>
    <div class="bo-main">

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?= htmlspecialchars($_SESSION['success']) ?>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-warning">
                <i class="fas fa-triangle-exclamation"></i> <?= htmlspecialchars($_SESSION['error']) ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <div class="card-head">
            <h2 class="section-title"><i class="fas fa-robot"></i> Auto-répondeur IA</h2>
            <a href="scores.php" class="btn btn-secondary"><i class="fas fa-shield-halved"></i> Scores IA</a>
        </div>

        <div class="form-card" style="max-width:920px;margin:0 0 28px">
            <h2><i class="fas fa-message"></i> Prompt</h2>
            <form method="POST" action="">
                <input type="hidden" name="action" value="save_prompt">
                <div class="form-group">
                    <label>Prompt envoyé au modèle</label>
                    <textarea name="prompt" rows="7"><?= htmlspecialchars($prompt) ?></textarea>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Enregistrer</button>
                </div>
            </form>
        </div>

        <div class="card-head">
            <h2 class="section-title"><i class="fas fa-comments"></i> Réponses générées</h2>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr><th>#</th><th>Post</th><th>Réponse</th><th>Statut</th><th>Date</th><th>Actions</th></tr>
                </thead>
                <tbody>
                <?php foreach ($replies as $reply): ?>
                    <tr>
                        <td><?= (int)$reply['id'] ?></td>
                        <td>
                            <strong><?= htmlspecialchars(mb_substr($reply['post_titre'] ?? 'Post supprimé', 0, 48)) ?></strong><br>
                            <?php if (!empty($reply['post_id'])): ?>
                                <a href="../../frontoffice/forum/detail.php?id=<?= (int)$reply['post_id'] ?>#reply-<?= (int)$reply['id'] ?>">Voir le post</a>
                            <?php endif; ?>
                        </td>
                        <td>
                            <form method="POST" action="" class="ai-reply-form" id="reply-update-<?= (int)$reply['id'] ?>">
                                <input type="hidden" name="action" value="update_reply">
                                <input type="hidden" name="id" value="<?= (int)$reply['id'] ?>">
                                <textarea name="contenu" rows="3"><?= htmlspecialchars($reply['contenu'] ?? '') ?></textarea>
                            </form>
                        </td>
                        <td>
                            <select name="statut" form="reply-update-<?= (int)$reply['id'] ?>" class="sort-select">
                                <option value="actif" <?= ($reply['statut'] ?? '') === 'actif' ? 'selected' : '' ?>>Actif</option>
                                <option value="masque" <?= ($reply['statut'] ?? '') === 'masque' ? 'selected' : '' ?>>Masqué</option>
                                <option value="supprime" <?= ($reply['statut'] ?? '') === 'supprime' ? 'selected' : '' ?>>Supprimé</option>
                            </select>
                        </td>
                        <td><?= !empty($reply['created_at']) ? date('d/m/Y H:i', strtotime($reply['created_at'])) : '-' ?></td>
                        <td class="action-cell">
                            <button type="submit" form="reply-update-<?= (int)$reply['id'] ?>" class="btn-icon edit" title="Modifier">
                                <i class="fas fa-save"></i>
                            </button>
                            <form method="POST" action="" style="display:inline">
                                <input type="hidden" name="action" value="delete_reply">
                                <input type="hidden" name="id" value="<?= (int)$reply['id'] ?>">
                                <button type="submit" class="btn-icon del" title="Supprimer" data-confirm="Supprimer cette réponse générée ?">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($replies)): ?>
                    <tr><td colspan="6" style="text-align:center;color:var(--text-light);padding:28px">Aucune réponse générée.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script src="../../assets/forum.js"></script>
</body>
</html>
