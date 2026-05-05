<?php
// view/backoffice/forum/liste.php
include_once __DIR__ . '/../../../controller/ControlPost.php';
include_once __DIR__ . '/../../../controller/ControlReply.php';
include_once __DIR__ . '/../../../controller/ControlReaction.php';
include_once __DIR__ . '/../../assets/forum_nav.php';
if (session_status() === PHP_SESSION_NONE) session_start();

// Access control – admin only
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    header('Location: ../../auth/login.php'); exit;
}

$postCtrl     = new ControlPost();
$replyCtrl    = new ControlReply();
$reactionCtrl = new ControlReaction();

$tab = $_GET['tab'] ?? 'posts';

$stats = [
    'posts'    => $postCtrl->countPosts(),
    'replies'  => $replyCtrl->countReplies(),
    'likes'    => $reactionCtrl->countByType('like'),
    'dislikes' => $reactionCtrl->countByType('dislike'),
    'masked'   => $postCtrl->countPostsByStatut('masque'),
    'pinned'   => $postCtrl->countPostsByStatut('actif'),
];

function renderAiScoreBar($score) {
    if ($score === null || $score === '') {
        return '<span class="score-empty">—</span>';
    }
    $score = max(0, min(100, (int)$score));
    $color = 'hsl(' . round($score * 1.2) . ', 65%, 42%)';
    return '<div class="score-wrap"><span class="score-label">' . $score . '/100</span><div class="score-bar"><div class="score-fill" style="width:100%;background:' . $color . '"></div></div></div>';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion Forum - Back Office Green Assurance</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/forum.css">
</head>
<body>

<div class="bo-layout">

    <!-- SIDEBAR WITH BACK/FRONT SWITCH -->
    <?php forumNav('back', 'liste'); ?>

    <div class="bo-main">

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?= htmlspecialchars($_SESSION['success']) ?>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <!-- STATS -->
        <div class="stats-row">
            <div class="stat-card">
                <i class="fas fa-file-alt"></i>
                <div><span class="stat-num"><?= $stats['posts'] ?></span><span class="stat-label">Posts actifs</span></div>
            </div>
            <div class="stat-card">
                <i class="fas fa-comment-dots"></i>
                <div><span class="stat-num"><?= $stats['replies'] ?></span><span class="stat-label">Réponses</span></div>
            </div>
            <div class="stat-card s-likes">
                <i class="fas fa-thumbs-up"></i>
                <div><span class="stat-num"><?= $stats['likes'] ?></span><span class="stat-label">Likes</span></div>
            </div>
            <div class="stat-card s-pinned">
                <i class="fas fa-thumbs-down"></i>
                <div><span class="stat-num"><?= $stats['dislikes'] ?></span><span class="stat-label">Dislikes</span></div>
            </div>
            <div class="stat-card s-masked">
                <i class="fas fa-eye-slash"></i>
                <div><span class="stat-num"><?= $stats['masked'] ?></span><span class="stat-label">Masqués</span></div>
            </div>
        </div>

        <!-- TABS -->
        <div class="tabs">
            <a href="liste.php?tab=posts"     class="tab <?= $tab==='posts'     ? 'active':'' ?>">
                <i class="fas fa-file-alt"></i> Posts
            </a>
            <a href="liste.php?tab=replies"   class="tab <?= $tab==='replies'   ? 'active':'' ?>">
                <i class="fas fa-comments"></i> Réponses
            </a>
            <a href="liste.php?tab=reactions" class="tab <?= $tab==='reactions' ? 'active':'' ?>">
                <i class="fas fa-heart"></i> Réactions
            </a>
        </div>

        <!-- ── TAB: POSTS ──────────────────────────────────── -->
        <?php if ($tab === 'posts'):
            $posts = $postCtrl->listePostsAdmin(); ?>
        <div class="card-head">
            <h2 class="section-title"><i class="fas fa-file-alt"></i> Gestion des Posts</h2>
            <a href="ajout.php" class="btn btn-primary"><i class="fas fa-plus"></i> Nouveau Post</a>
        </div>
        <div class="search-wrap">
            <i class="fas fa-search"></i>
            <input type="text" id="forumSearch" placeholder="Rechercher un post...">
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>#</th><th>Titre</th><th>Tag</th><th>Auteur</th>
                        <th>Réponses</th><th>Réactions</th><th>AI Fraud Score</th><th>Signalements</th>
                        <th>Statut</th><th>Date</th><th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($posts as $p): ?>
                <tr data-searchable>
                    <td><?= $p['id'] ?></td>
                    <td>
                        <?php if ($p['is_pinned']): ?>
                            <i class="fas fa-thumbtack" style="color:var(--accent);margin-right:5px" title="Épinglé"></i>
                        <?php endif; ?>
                        <a href="../../frontoffice/forum/detail.php?id=<?= $p['id'] ?>"
                           style="color:var(--green-dark);font-weight:600;text-decoration:none"
                           title="Voir dans le front office">
                            <?= htmlspecialchars(mb_substr($p['titre'],0,45)) ?><?= mb_strlen($p['titre'])>45 ? '…' : '' ?>
                        </a>
                    </td>
                    <td>
                        <?php if (!empty($p['tag_name'])):
                            $tagColor = preg_match('/^(#[0-9a-fA-F]{3,8}|[a-zA-Z]+)$/', $p['tag_color'] ?? '') ? $p['tag_color'] : '#6b8f3a';
                        ?>
                            <span class="forum-tag forum-tag-sm" style="background-color:<?= htmlspecialchars($tagColor) ?>">
                                <?= htmlspecialchars($p['tag_name']) ?>
                            </span>
                        <?php else: ?>
                            <span style="color:var(--text-light)">—</span>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($p['prenom'] . ' ' . $p['nom']) ?></td>
                    <td><span class="meta-chip"><i class="fas fa-comment"></i> <?= $p['nb_replies'] ?></span></td>
                    <td>
                        <span class="meta-chip like"><i class="fas fa-thumbs-up"></i> <?= $p['nb_likes'] ?></span>
                        <span class="meta-chip dislike"><i class="fas fa-thumbs-down"></i> <?= $p['nb_dislikes'] ?></span>
                    </td>
                    <td><?= renderAiScoreBar($p['ai_score'] ?? null) ?></td>
                    <td>
                        <?php if ($p['nb_reports'] > 0): ?>
                            <a href="report_detail.php?type=post&id=<?= $p['id'] ?>" class="meta-chip" style="text-decoration:none">
                                <i class="fas fa-flag"></i> <?= $p['nb_reports'] ?>
                            </a>
                        <?php else: ?>
                            <span class="meta-chip"><i class="fas fa-flag"></i> 0</span>
                        <?php endif; ?>
                    </td>
                    <td><span class="badge badge-<?= $p['statut'] ?>"><?= ucfirst($p['statut']) ?></span></td>
                    <td><?= date('d/m/Y', strtotime($p['created_at'])) ?></td>
                    <td class="action-cell">
                        <a href="../../frontoffice/forum/detail.php?id=<?= $p['id'] ?>"
                           class="btn-icon view" title="Voir (Front Office)">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="modifier.php?id=<?= $p['id'] ?>"
                           class="btn-icon edit" title="Modifier">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="toggle_pin.php?id=<?= $p['id'] ?>"
                           class="btn-icon pin"
                           title="<?= $p['is_pinned'] ? 'Désépingler' : 'Épingler' ?>">
                            <i class="fas fa-thumbtack"></i>
                        </a>
                        <a href="supprimer.php?id=<?= $p['id'] ?>"
                           class="btn-icon del" title="Supprimer définitivement"
                           data-confirm="Supprimer ce post et toutes ses données ?">
                            <i class="fas fa-trash"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- ── TAB: REPLIES ───────────────────────────────── -->
        <?php elseif ($tab === 'replies'):
            $replies = $replyCtrl->getAllRepliesAdmin(); ?>
        <div class="card-head">
            <h2 class="section-title"><i class="fas fa-comments"></i> Gestion des Réponses</h2>
        </div>
        <div class="search-wrap">
            <i class="fas fa-search"></i>
            <input type="text" id="forumSearch" placeholder="Rechercher une réponse...">
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>#</th><th>Contenu</th><th>Auteur</th>
                        <th>Post</th><th>Imbriqué</th><th>AI Fraud Score</th><th>Signalements</th>
                        <th>Statut</th><th>Date</th><th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($replies as $r): ?>
                <tr data-searchable>
                    <td><?= $r['id'] ?></td>
                    <td><?= htmlspecialchars(mb_substr($r['contenu'],0,55)) ?><?= mb_strlen($r['contenu'])>55?'…':'' ?></td>
                    <td><?= htmlspecialchars($r['prenom'] . ' ' . $r['nom']) ?></td>
                    <td>
                        <a href="../../frontoffice/forum/detail.php?id=<?= $r['post_id'] ?>#replies"
                           style="color:var(--green-main);font-size:.85rem">
                            <?= htmlspecialchars(mb_substr($r['post_titre'],0,28)) ?>…
                        </a>
                    </td>
                    <td>
                        <?php if ($r['parent_reply_id']): ?>
                            <span class="badge badge-masque">↳ #<?= $r['parent_reply_id'] ?></span>
                        <?php else: ?>
                            <span style="color:var(--text-light)">—</span>
                        <?php endif; ?>
                    </td>
                    <td><?= renderAiScoreBar($r['ai_score'] ?? null) ?></td>
                    <td>
                        <?php if ($r['nb_reports'] > 0): ?>
                            <a href="report_detail.php?type=reply&id=<?= $r['id'] ?>" class="meta-chip" style="text-decoration:none">
                                <i class="fas fa-flag"></i> <?= $r['nb_reports'] ?>
                            </a>
                        <?php else: ?>
                            <span class="meta-chip"><i class="fas fa-flag"></i> 0</span>
                        <?php endif; ?>
                    </td>
                    <td><span class="badge badge-<?= $r['statut'] ?>"><?= ucfirst($r['statut']) ?></span></td>
                    <td><?= date('d/m/Y', strtotime($r['created_at'])) ?></td>
                    <td class="action-cell">
                        <a href="../../frontoffice/forum/detail.php?id=<?= $r['post_id'] ?>#reply-<?= $r['id'] ?>"
                           class="btn-icon view" title="Voir dans le front office">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="modifier_reply.php?id=<?= $r['id'] ?>&post_id=<?= $r['post_id'] ?>"
                           class="btn-icon edit"><i class="fas fa-edit"></i></a>
                        <a href="supprimer_reply.php?id=<?= $r['id'] ?>"
                           class="btn-icon del"
                           data-confirm="Supprimer cette réponse définitivement ?">
                            <i class="fas fa-trash"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- ── TAB: REACTIONS ─────────────────────────────── -->
        <?php elseif ($tab === 'reactions'):
            $reactions = $reactionCtrl->getAllReactionsAdmin(); ?>
        <div class="card-head">
            <h2 class="section-title"><i class="fas fa-heart"></i> Gestion des Réactions</h2>
        </div>
        <div class="search-wrap">
            <i class="fas fa-search"></i>
            <input type="text" id="forumSearch" placeholder="Rechercher...">
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr><th>#</th><th>Utilisateur</th><th>Type</th><th>Cible</th><th>Date</th><th>Actions</th></tr>
                </thead>
                <tbody>
                <?php foreach ($reactions as $rc): ?>
                <tr data-searchable>
                    <td><?= $rc['id'] ?></td>
                    <td><?= htmlspecialchars($rc['prenom'] . ' ' . $rc['nom']) ?></td>
                    <td>
                        <?php if ($rc['type_reaction']==='like'): ?>
                            <span class="badge badge-like"><i class="fas fa-thumbs-up"></i> Like</span>
                        <?php else: ?>
                            <span class="badge badge-dislike"><i class="fas fa-thumbs-down"></i> Dislike</span>
                        <?php endif; ?>
                    </td>
                    <td style="font-size:.83rem">
                        <?php if ($rc['post_titre']): ?>
                            <i class="fas fa-file-alt" style="color:var(--green-main)"></i>
                            Post : <?= htmlspecialchars(mb_substr($rc['post_titre'],0,30)) ?>
                        <?php elseif ($rc['reply_contenu']): ?>
                            <i class="fas fa-comment" style="color:var(--green-light)"></i>
                            Réponse : <?= htmlspecialchars(mb_substr($rc['reply_contenu'],0,30)) ?>
                        <?php endif; ?>
                    </td>
                    <td><?= date('d/m/Y H:i', strtotime($rc['created_at'])) ?></td>
                    <td class="action-cell">
                        <a href="supprimer_reaction.php?id=<?= $rc['id'] ?>"
                           class="btn-icon del"
                           data-confirm="Supprimer cette réaction ?">
                            <i class="fas fa-trash"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>

    </div><!-- /bo-main -->
</div><!-- /bo-layout -->

<script src="../../assets/forum.js"></script>
</body>
</html>
