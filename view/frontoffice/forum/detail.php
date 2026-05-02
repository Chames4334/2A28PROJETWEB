<?php
// view/frontoffice/forum/detail.php
include_once __DIR__ . '/../../../controller/ControlPost.php';
include_once __DIR__ . '/../../../controller/ControlReply.php';
include_once __DIR__ . '/../../../controller/ControlReaction.php';
include_once __DIR__ . '/../../assets/forum_nav.php';
if (session_status() === PHP_SESSION_NONE) session_start();

$postCtrl     = new ControlPost();
$replyCtrl    = new ControlReply();
$reactionCtrl = new ControlReaction();

$id   = intval($_GET['id'] ?? 0);
$post = $postCtrl->getPostById($id);
if (!$post || $post['statut'] === 'supprime') { header('Location: /view/frontoffice/forum/liste.php'); exit; }

// ── Handle reply submit ──────────────────────────────────────
$replyError = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'reply') {
    if (!isset($_SESSION['user_id'])) { header('Location: /view/auth/login.php'); exit; }
    $contenu         = trim($_POST['contenu'] ?? '');
    $parent_reply_id = intval($_POST['parent_reply_id'] ?? 0) ?: null;
    // SERVER-SIDE validation
    if (strlen($contenu) < 3) {
        $replyError = "La réponse doit contenir au moins 3 caractères.";
    } else {
        $reply = new Reply($id, $_SESSION['user_id'], $contenu, $parent_reply_id);
        $replyCtrl->addReply($reply);
        $_SESSION['success'] = "Réponse ajoutée !";
        header("Location: detail.php?id=$id#replies"); exit;
    }
}

// ── Handle reaction ──────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'react') {
    if (!isset($_SESSION['user_id'])) { header('Location: ../../auth/login.php'); exit; }
    $type     = in_array($_POST['type'] ?? '', ['like','dislike']) ? $_POST['type'] : 'like';
    $post_id  = intval($_POST['post_id']  ?? 0) ?: null;
    $reply_id = intval($_POST['reply_id'] ?? 0) ?: null;
    $reactionCtrl->toggleReaction($_SESSION['user_id'], $type, $post_id, $reply_id);
    header("Location: detail.php?id=$id#" . ($reply_id ? "reply-$reply_id" : "post-top")); exit;
}

$replies = $replyCtrl->getRepliesByPost($id);
$userPostReaction = isset($_SESSION['user_id'])
    ? $reactionCtrl->getUserReaction($_SESSION['user_id'], $id)
    : null;

// Organise replies: parents + nested
$parents = [];
$nested  = [];
foreach ($replies as $r) {
    if ($r['parent_reply_id']) $nested[$r['parent_reply_id']][] = $r;
    else $parents[] = $r;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($post['titre']) ?> - Forum Green Assurance</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="/view/assets/forum.css">
</head>
<body>

<?php forumNav('front', ''); ?>

<div class="back-nav">
    <a href="/view/frontoffice/forum/liste.php"><i class="fas fa-arrow-left"></i> Retour au forum</a>
    <?php
    // Admin quick-link to backoffice edit
    $isAdmin = isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
    if ($isAdmin): ?>
        <a href="/view/backoffice/forum/modifier.php?id=<?= $id ?>" style="margin-left:auto" class="btn btn-secondary btn-sm">
            <i class="fas fa-cogs"></i> Gérer ce post (Back Office)
        </a>
    <?php endif; ?>
</div>

<div class="forum-wrapper" id="post-top">

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($_SESSION['success']) ?></div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <!-- ── POST DETAIL ─────────────────────────────────── -->
    <div class="post-detail">
        <?php if ($post['is_pinned']): ?>
            <span class="badge" style="background:#fff8e1;color:#f57f17;margin-bottom:12px;display:inline-block">
                <i class="fas fa-thumbtack"></i> Post épinglé
            </span>
        <?php endif; ?>

        <h1><?= htmlspecialchars($post['titre']) ?></h1>

        <div class="post-meta" style="margin-bottom:14px">
            <span class="author">
                <span class="avatar avatar-lg">
                    <?= strtoupper(substr($post['prenom'],0,1).substr($post['nom'],0,1)) ?>
                </span>
                <strong><?= htmlspecialchars($post['prenom'] . ' ' . $post['nom']) ?></strong>
            </span>
            <span class="meta-chip">
                <i class="fas fa-calendar-alt"></i>
                <?= date('d/m/Y à H:i', strtotime($post['created_at'])) ?>
            </span>
        </div>

        <div class="post-body"><?= htmlspecialchars($post['contenu']) ?></div>

        <div class="post-actions-bar">
            <!-- Like -->
            <form method="POST" style="display:inline">
                <input type="hidden" name="action"  value="react">
                <input type="hidden" name="type"    value="like">
                <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                <button type="submit" class="reaction-btn <?= ($userPostReaction && $userPostReaction['type_reaction']==='like') ? 'liked' : '' ?>">
                    <i class="fas fa-thumbs-up"></i> <span><?= $post['nb_likes'] ?></span>
                </button>
            </form>
            <!-- Dislike -->
            <form method="POST" style="display:inline">
                <input type="hidden" name="action"  value="react">
                <input type="hidden" name="type"    value="dislike">
                <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                <button type="submit" class="reaction-btn <?= ($userPostReaction && $userPostReaction['type_reaction']==='dislike') ? 'disliked' : '' ?>">
                    <i class="fas fa-thumbs-down"></i> <span><?= $post['nb_dislikes'] ?></span>
                </button>
            </form>

            <?php if (isset($_SESSION['user_id'])): ?>
                <button type="button" class="reply-mini-btn btn-report"
                        data-report-type="post"
                        data-report-id="<?= $post['id'] ?>"
                        data-report-post="<?= $post['id'] ?>">
                    <i class="fas fa-flag"></i> Signaler
                </button>
            <?php endif; ?>

            <!-- Owner actions -->
            <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $post['user_id']): ?>
                <a href="/view/frontoffice/forum/modifier.php?id=<?= $post['id'] ?>" class="btn btn-secondary btn-sm" style="margin-left:auto">
                    <i class="fas fa-edit"></i> Modifier
                </a>
                <a href="/view/frontoffice/forum/supprimer.php?id=<?= $post['id'] ?>" class="btn btn-danger btn-sm"
                   data-confirm="Supprimer votre post définitivement ?">
                    <i class="fas fa-trash"></i> Supprimer
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- ── REPLIES ─────────────────────────────────────── -->
    <div class="replies-section" id="replies">
        <div class="card-head">
            <h2 class="section-title">
                <i class="fas fa-comment-dots"></i> Réponses
                <span><?= count($replies) ?></span>
            </h2>
        </div>

        <?php if ($replyError): ?>
            <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?= $replyError ?></div>
        <?php endif; ?>

        <?php foreach ($parents as $r):
            $userReplyReaction = isset($_SESSION['user_id'])
                ? $reactionCtrl->getUserReaction($_SESSION['user_id'], null, $r['id'])
                : null;
            $initR = strtoupper(substr($r['prenom'],0,1).substr($r['nom'],0,1));
        ?>
        <div class="reply-item" id="reply-<?= $r['id'] ?>">
            <div class="reply-header">
                <span class="avatar"><?= $initR ?></span>
                <span class="reply-author"><?= htmlspecialchars($r['prenom'] . ' ' . $r['nom']) ?></span>
                <span class="reply-date">
                    <i class="fas fa-clock"></i>
                    <?= date('d/m/Y H:i', strtotime($r['created_at'])) ?>
                </span>
            </div>

            <div class="reply-text"><?= htmlspecialchars($r['contenu']) ?></div>

            <div class="reply-actions">
                <!-- Like reply -->
                <form method="POST" style="display:inline">
                    <input type="hidden" name="action"   value="react">
                    <input type="hidden" name="type"     value="like">
                    <input type="hidden" name="reply_id" value="<?= $r['id'] ?>">
                    <button type="submit" class="reply-mini-btn <?= ($userReplyReaction && $userReplyReaction['type_reaction']==='like') ? 'liked' : '' ?>">
                        <i class="fas fa-thumbs-up"></i> <?= $r['nb_likes'] ?>
                    </button>
                </form>
                <!-- Dislike reply -->
                <form method="POST" style="display:inline">
                    <input type="hidden" name="action"   value="react">
                    <input type="hidden" name="type"     value="dislike">
                    <input type="hidden" name="reply_id" value="<?= $r['id'] ?>">
                    <button type="submit" class="reply-mini-btn">
                        <i class="fas fa-thumbs-down"></i> <?= $r['nb_dislikes'] ?>
                    </button>
                </form>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <button class="reply-mini-btn btn-reply-toggle"
                            data-target="reply-form-<?= $r['id'] ?>">
                        <i class="fas fa-reply"></i> Répondre
                    </button>
                    <button type="button" class="reply-mini-btn btn-report"
                            data-report-type="reply"
                            data-report-id="<?= $r['id'] ?>"
                            data-report-post="<?= $id ?>">
                        <i class="fas fa-flag"></i> Signaler
                    </button>
                <?php endif; ?>

                <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $r['user_id']): ?>
                    <a href="/view/frontoffice/forum/modifier_reply.php?id=<?= $r['id'] ?>&post_id=<?= $id ?>"
                       class="reply-mini-btn"><i class="fas fa-edit"></i> Modifier</a>
                    <a href="/view/frontoffice/forum/supprimer_reply.php?id=<?= $r['id'] ?>&post_id=<?= $id ?>"
                       class="reply-mini-btn"
                       data-confirm="Supprimer cette réponse ?">
                        <i class="fas fa-trash"></i>
                    </a>
                <?php endif; ?>
            </div>

            <!-- Inline nested reply form -->
            <?php if (isset($_SESSION['user_id'])): ?>
            <div id="reply-form-<?= $r['id'] ?>" style="display:none;margin-top:12px">
                <form method="POST" class="reply-form">
                    <input type="hidden" name="action"          value="reply">
                    <input type="hidden" name="parent_reply_id" value="<?= $r['id'] ?>">
                    <div class="reply-form-wrap">
                        <h3><i class="fas fa-reply"></i> Répondre à <?= htmlspecialchars($r['prenom']) ?></h3>
                        <textarea name="contenu" placeholder="Votre réponse..."
                                  class="auto-resize" data-maxlength="1000"></textarea>
                        <button type="submit" class="btn btn-primary btn-sm" style="margin-top:10px">
                            <i class="fas fa-paper-plane"></i> Envoyer
                        </button>
                    </div>
                </form>
            </div>
            <?php endif; ?>

            <!-- Nested replies -->
            <?php if (!empty($nested[$r['id']])): ?>
                <?php foreach ($nested[$r['id']] as $nr):
                    $initN = strtoupper(substr($nr['prenom'],0,1).substr($nr['nom'],0,1));
                ?>
                <div class="reply-item nested" id="reply-<?= $nr['id'] ?>">
                    <div class="reply-header">
                        <span class="avatar"><?= $initN ?></span>
                        <span class="reply-author"><?= htmlspecialchars($nr['prenom'] . ' ' . $nr['nom']) ?></span>
                        <span class="reply-date"><?= date('d/m/Y H:i', strtotime($nr['created_at'])) ?></span>
                    </div>
                    <div class="reply-text"><?= htmlspecialchars($nr['contenu']) ?></div>
                    <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="reply-actions">
                        <button type="button" class="reply-mini-btn btn-report"
                                data-report-type="reply"
                                data-report-id="<?= $nr['id'] ?>"
                                data-report-post="<?= $id ?>">
                            <i class="fas fa-flag"></i> Signaler
                        </button>
                        <?php if ($_SESSION['user_id'] == $nr['user_id']): ?>
                        <a href="/view/frontoffice/forum/modifier_reply.php?id=<?= $nr['id'] ?>&post_id=<?= $id ?>"
                           class="reply-mini-btn"><i class="fas fa-edit"></i></a>
                        <a href="/view/frontoffice/forum/supprimer_reply.php?id=<?= $nr['id'] ?>&post_id=<?= $id ?>"
                           class="reply-mini-btn"
                           data-confirm="Supprimer cette réponse ?">
                            <i class="fas fa-trash"></i>
                        </a>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>

        <?php if (empty($parents)): ?>
        <div class="empty-state" style="padding:40px 0">
            <i class="fas fa-comment-slash"></i>
            <h3>Aucune réponse pour l'instant</h3>
            <p>Soyez le premier à répondre !</p>
        </div>
        <?php endif; ?>

        <!-- Main reply form -->
        <?php if (isset($_SESSION['user_id'])): ?>
        <div style="margin-top:28px">
            <form method="POST" class="reply-form">
                <input type="hidden" name="action" value="reply">
                <div class="reply-form-wrap">
                    <h3><i class="fas fa-comment-dots"></i> Ajouter une réponse</h3>
                    <textarea name="contenu" id="contenu"
                              placeholder="Partagez votre réponse ou expérience..."
                              class="auto-resize" data-maxlength="1000"></textarea>
                    <div class="char-count" id="contenu_count"></div>
                    <button type="submit" class="btn btn-primary" style="margin-top:12px">
                        <i class="fas fa-paper-plane"></i> Publier ma réponse
                    </button>
                </div>
            </form>
        </div>
        <?php else: ?>
        <div class="alert alert-warning" style="margin-top:24px">
            <i class="fas fa-info-circle"></i>
            <a href="/view/auth/login.php" style="color:inherit;font-weight:700">Connectez-vous</a>
            pour participer à la discussion.
        </div>
        <?php endif; ?>
    </div>

</div>

<?php if (isset($_SESSION['user_id'])): ?>
<div class="modal-overlay" id="reportModal" style="display:none">
    <div class="modal-box">
        <button type="button" class="modal-close" id="reportModalClose" aria-label="Fermer">
            <i class="fas fa-times"></i>
        </button>
        <h2><i class="fas fa-flag"></i> Signaler ce contenu</h2>
        <form action="/view/frontoffice/forum/report.php" method="POST" id="reportForm">
            <input type="hidden" name="target_type" id="reportTargetType">
            <input type="hidden" name="target_id" id="reportTargetId">
            <input type="hidden" name="post_id" id="reportPostId">

            <div class="report-reasons">
                <label><input type="radio" name="reason" value="Spam"> Spam</label>
                <label><input type="radio" name="reason" value="Harcelement"> Harcelement</label>
                <label><input type="radio" name="reason" value="Contenu inapproprie"> Contenu inapproprie</label>
                <label><input type="radio" name="reason" value="Fausse information"> Fausse information</label>
                <label><input type="radio" name="reason" value="Informations personnelles"> Informations personnelles</label>
                <label><input type="radio" name="reason" value="Autre"> Autre</label>
            </div>

            <div class="form-group other-reason-wrap" id="otherReasonWrap" style="display:none">
                <label for="otherReason">Precision</label>
                <textarea name="other_reason" id="otherReason" placeholder="Decrivez le probleme..."></textarea>
            </div>
            <span class="error-msg js-report-error" id="reportError" style="display:none"></span>

            <div class="form-actions">
                <button type="button" class="btn btn-secondary" id="reportCancel">Annuler</button>
                <button type="submit" class="btn btn-danger"><i class="fas fa-flag"></i> Envoyer</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<script src="/view/assets/forum.js"></script>
</body>
</html>
