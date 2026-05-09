<?php
// view/backoffice/forum/reports.php
include_once __DIR__ . '/../../../controller/ControlReport.php';
include_once __DIR__ . '/../../assets/forum_nav.php';
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    header('Location: ../../auth/login.php'); exit;
}

$ctrl = new ControlReport();
$q = trim($_GET['q'] ?? '');
$types = $_GET['types'] ?? ['post', 'reply'];
if (!is_array($types)) $types = ['post', 'reply'];
$types = array_values(array_intersect($types, ['post', 'reply']));
if (empty($types)) $types = ['post', 'reply'];
$sort = $_GET['sort'] ?? 'recent';
if (!in_array($sort, ['recent', 'most_reported'], true)) $sort = 'recent';

$items = $ctrl->getReportedTargets($q, $types, $sort);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Signalements - Back Office Green Assurance</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/forum.css">
</head>
<body>

<div class="bo-layout">
    <?php forumNav('back', 'reports'); ?>

    <div class="bo-main">
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?= htmlspecialchars($_SESSION['success']) ?>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <div class="card-head">
            <h2 class="section-title"><i class="fas fa-flag"></i> Signalements</h2>
        </div>

        <form method="GET" class="search-wrap report-toolbar">
            <div class="search-form">
                <i class="fas fa-search"></i>
                <input type="text" name="q" id="forumSearch" placeholder="Rechercher un post ou une reponse..." value="<?= htmlspecialchars($q) ?>">
            </div>
            <div class="report-filters">
                <label><input type="checkbox" name="types[]" value="post" <?= in_array('post', $types, true) ? 'checked' : '' ?>> Posts</label>
                <label><input type="checkbox" name="types[]" value="reply" <?= in_array('reply', $types, true) ? 'checked' : '' ?>> Reponses</label>
                <select name="sort" class="sort-select" aria-label="Trier">
                    <option value="recent" <?= $sort === 'recent' ? 'selected' : '' ?>>Plus recents</option>
                    <option value="most_reported" <?= $sort === 'most_reported' ? 'selected' : '' ?>>Plus signales</option>
                </select>
                <button type="submit" class="btn btn-primary btn-sm">Filtrer</button>
            </div>
        </form>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Contenu</th>
                        <th>Signalements</th>
                        <th>Dernier signalement</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($items as $item): ?>
                    <tr data-searchable>
                        <td>
                            <span class="badge <?= $item['target_type'] === 'post' ? 'badge-actif' : 'badge-masque' ?>">
                                <?= $item['target_type'] === 'post' ? 'Post' : 'Reponse' ?>
                            </span>
                        </td>
                        <td>
                            <strong><?= htmlspecialchars(mb_substr($item['title'], 0, 55)) ?><?= mb_strlen($item['title']) > 55 ? '...' : '' ?></strong>
                            <div style="color:var(--text-light);font-size:.84rem;margin-top:4px">
                                <?= htmlspecialchars(mb_substr($item['content'], 0, 90)) ?><?= mb_strlen($item['content']) > 90 ? '...' : '' ?>
                            </div>
                        </td>
                        <td><span class="meta-chip"><i class="fas fa-flag"></i> <?= $item['report_count'] ?></span></td>
                        <td><?= date('d/m/Y H:i', strtotime($item['latest_report_at'])) ?></td>
                        <td class="action-cell">
                            <a class="btn-icon view"
                               href="report_detail.php?type=<?= $item['target_type'] ?>&id=<?= $item['target_id'] ?>"
                               title="Voir les signalements">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($items)): ?>
                    <tr>
                        <td colspan="5" style="text-align:center;color:var(--text-light);padding:28px">Aucun signalement trouve.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="../../assets/forum.js"></script>
</body>
</html>
