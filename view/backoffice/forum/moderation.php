<?php
// view/backoffice/forum/moderation.php
include_once __DIR__ . '/../../../controller/ControlAI.php';
include_once __DIR__ . '/../../assets/forum_nav.php';
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    header('Location: ../../auth/login.php'); exit;
}

$aiCtrl = new ControlAI();
$hasFilter = isset($_GET['filter']);
$includePosts = !$hasFilter || isset($_GET['posts']);
$includeReplies = !$hasFilter || isset($_GET['replies']);
$sort = strtolower($_GET['sort'] ?? 'asc') === 'desc' ? 'desc' : 'asc';
$storageReady = $aiCtrl->scoreStorageReady();
$items = $storageReady ? $aiCtrl->getScoreItems($includePosts, $includeReplies, $sort, true) : [];

function renderAiScoreBar($score) {
    if ($score === null || $score === '') return '<span class="score-empty">—</span>';
    $score = max(0, min(100, (int)$score));
    $color = 'hsl(' . round($score * 1.2) . ', 65%, 42%)';
    return '<div class="score-wrap"><span class="score-label">' . $score . '/100</span><div class="score-bar"><div class="score-fill" style="width:100%;background:' . $color . '"></div></div></div>';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modération IA - Back Office Green Assurance</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/forum.css">
</head>
<body>
<div class="bo-layout">
    <?php forumNav('back', 'moderation'); ?>
    <div class="bo-main">
        <div class="card-head">
            <h2 class="section-title"><i class="fas fa-triangle-exclamation"></i> Modération IA</h2>
            <a href="scores.php" class="btn btn-secondary"><i class="fas fa-list-check"></i> Tous les scores</a>
        </div>

        <?php if (!$storageReady): ?>
            <div class="alert alert-warning">
                <i class="fas fa-database"></i> Les colonnes ai_score sont absentes. Exécutez ai_score_migration.sql.
            </div>
        <?php endif; ?>

        <form method="GET" class="search-wrap report-toolbar">
            <input type="hidden" name="filter" value="1">
            <div class="report-filters">
                <label><input type="checkbox" name="posts" value="1" <?= $includePosts ? 'checked' : '' ?>> Posts</label>
                <label><input type="checkbox" name="replies" value="1" <?= $includeReplies ? 'checked' : '' ?>> Réponses</label>
                <select name="sort" class="sort-select" aria-label="Tri score">
                    <option value="asc" <?= $sort === 'asc' ? 'selected' : '' ?>>Score ascendant</option>
                    <option value="desc" <?= $sort === 'desc' ? 'selected' : '' ?>>Score descendant</option>
                </select>
                <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Filtrer</button>
            </div>
        </form>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr><th>Type</th><th>Contenu suspendu</th><th>Auteur</th><th>Score</th><th>Date</th><th>Actions</th></tr>
                </thead>
                <tbody>
                <?php foreach ($items as $item): ?>
                    <tr data-searchable>
                        <td>
                            <span class="badge <?= $item['target_type'] === 'post' ? 'badge-actif' : 'badge-masque' ?>">
                                <?= $item['target_type'] === 'post' ? 'Post' : 'Réponse' ?>
                            </span>
                        </td>
                        <td>
                            <strong><?= htmlspecialchars(mb_substr($item['title'] ?? '', 0, 42)) ?></strong><br>
                            <?= htmlspecialchars(mb_substr($item['content'] ?? '', 0, 100)) ?><?= mb_strlen($item['content'] ?? '') > 100 ? '…' : '' ?>
                        </td>
                        <td><?= htmlspecialchars(trim(($item['prenom'] ?? '') . ' ' . ($item['nom'] ?? ''))) ?></td>
                        <td><?= renderAiScoreBar($item['ai_score']) ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($item['created_at'])) ?></td>
                        <td class="action-cell">
                            <?php if ($item['target_type'] === 'post'): ?>
                                <a href="../../frontoffice/forum/detail.php?id=<?= $item['id'] ?>" class="btn-icon view" title="Voir"><i class="fas fa-eye"></i></a>
                                <a href="modifier.php?id=<?= $item['id'] ?>" class="btn-icon edit" title="Modifier / activer"><i class="fas fa-edit"></i></a>
                            <?php else: ?>
                                <a href="../../frontoffice/forum/detail.php?id=<?= $item['post_id'] ?>#reply-<?= $item['id'] ?>" class="btn-icon view" title="Voir"><i class="fas fa-eye"></i></a>
                                <a href="modifier_reply.php?id=<?= $item['id'] ?>&post_id=<?= $item['post_id'] ?>" class="btn-icon edit" title="Modifier / activer"><i class="fas fa-edit"></i></a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($items)): ?>
                    <tr><td colspan="6" style="text-align:center;color:var(--text-light);padding:28px">Aucun contenu suspendu par l'IA.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script src="../../assets/forum.js"></script>
</body>
</html>
