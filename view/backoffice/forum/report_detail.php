<?php
// view/backoffice/forum/report_detail.php
include_once __DIR__ . '/../../../controller/ControlReport.php';
include_once __DIR__ . '/../../assets/forum_nav.php';
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    header('Location: ../../auth/login.php'); exit;
}

$type = $_GET['type'] ?? 'post';
if (!in_array($type, ['post', 'reply'], true)) $type = 'post';
$id = intval($_GET['id'] ?? 0);

$ctrl = new ControlReport();
$reports = $id ? $ctrl->getReportsForTarget($type, $id) : [];
if (empty($reports)) { header('Location: reports.php'); exit; }

$target = $reports[0];
$frontLink = '../../frontoffice/forum/detail.php?id=' . $target['target_post_id'] . ($type === 'reply' ? '#reply-' . $id : '#post-top');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Details des signalements - Back Office Green Assurance</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/forum.css">
</head>
<body>

<div class="bo-layout">
    <?php forumNav('back', 'reports'); ?>

    <div class="bo-main">
        <div class="back-nav" style="margin:-32px -32px 24px">
            <a href="reports.php"><i class="fas fa-arrow-left"></i> Retour aux signalements</a>
            <a href="<?= $frontLink ?>" style="margin-left:auto"><i class="fas fa-eye"></i> Voir dans le forum</a>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?= htmlspecialchars($_SESSION['success']) ?>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <div class="post-detail">
            <span class="badge <?= $type === 'post' ? 'badge-actif' : 'badge-masque' ?>">
                <?= $type === 'post' ? 'Post signale' : 'Reponse signalee' ?>
            </span>
            <h1 style="margin-top:12px"><?= htmlspecialchars($target['title']) ?></h1>
            <div class="post-body"><?= htmlspecialchars($target['content']) ?></div>
            <div class="post-meta">
                <span class="meta-chip"><i class="fas fa-flag"></i> <?= count($reports) ?> signalement<?= count($reports) > 1 ? 's' : '' ?></span>
            </div>
        </div>

        <div class="card-head">
            <h2 class="section-title"><i class="fas fa-list"></i> Raisons</h2>
        </div>

        <div class="posts-list">
            <?php foreach ($reports as $report): ?>
                <div class="post-card">
                    <div class="post-title"><?= htmlspecialchars($report['raison']) ?></div>
                    <div class="post-meta">
                        <span class="author">
                            <span class="avatar"><?= strtoupper(substr($report['prenom'], 0, 1) . substr($report['nom'], 0, 1)) ?></span>
                            <?= htmlspecialchars($report['prenom'] . ' ' . $report['nom']) ?>
                        </span>
                        <span class="meta-chip"><i class="fas fa-envelope"></i> <?= htmlspecialchars($report['email']) ?></span>
                        <span class="meta-chip"><i class="fas fa-calendar-alt"></i> <?= date('d/m/Y H:i', strtotime($report['created_at'])) ?></span>
                        <a href="supprimer_report.php?id=<?= $report['id'] ?>&type=<?= $type ?>&target_id=<?= $id ?>"
                           class="btn btn-danger btn-sm"
                           style="margin-left:auto"
                           data-confirm="Supprimer ce signalement ?">
                            <i class="fas fa-trash"></i> Supprimer
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<script src="../../assets/forum.js"></script>
</body>
</html>
