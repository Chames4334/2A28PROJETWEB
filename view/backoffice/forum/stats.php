<?php
// view/backoffice/forum/stats.php
include_once __DIR__ . '/../../../controller/ControlPost.php';
include_once __DIR__ . '/../../../controller/ControlReply.php';
include_once __DIR__ . '/../../../controller/ControlReaction.php';
include_once __DIR__ . '/../../assets/forum_nav.php';
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    header('Location: ../../auth/login.php'); exit;
}

$postCtrl     = new ControlPost();
$replyCtrl    = new ControlReply();
$reactionCtrl = new ControlReaction();
$days = 14;

$labels = [];
$labelText = [];
for ($i = $days - 1; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $labels[] = $date;
    $labelText[] = date('d/m', strtotime($date));
}

function emptySeries($labels) {
    return array_fill_keys($labels, 0);
}

$posts = emptySeries($labels);
foreach ($postCtrl->getDailyPostCounts($days) as $row) {
    if (isset($posts[$row['jour']])) $posts[$row['jour']] = (int)$row['total'];
}

$replies = emptySeries($labels);
foreach ($replyCtrl->getDailyReplyCounts($days) as $row) {
    if (isset($replies[$row['jour']])) $replies[$row['jour']] = (int)$row['total'];
}

$likes = emptySeries($labels);
$dislikes = emptySeries($labels);
foreach ($reactionCtrl->getDailyReactionCounts($days) as $row) {
    if ($row['type_reaction'] === 'like' && isset($likes[$row['jour']])) $likes[$row['jour']] = (int)$row['total'];
    if ($row['type_reaction'] === 'dislike' && isset($dislikes[$row['jour']])) $dislikes[$row['jour']] = (int)$row['total'];
}

$chartData = [
    'labels' => $labelText,
    'activity' => [
        ['key' => 'posts', 'label' => 'Posts', 'color' => '#6b8f3a', 'values' => array_values($posts)],
        ['key' => 'replies', 'label' => 'Réponses', 'color' => '#1565c0', 'values' => array_values($replies)],
    ],
    'reactions' => [
        ['key' => 'likes', 'label' => 'Likes', 'color' => '#2e7d32', 'values' => array_values($likes)],
        ['key' => 'dislikes', 'label' => 'Dislikes', 'color' => '#c62828', 'values' => array_values($dislikes)],
    ],
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Graphiques Forum - Back Office Green Assurance</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/forum.css">
</head>
<body>
<div class="bo-layout">
    <?php forumNav('back', 'stats'); ?>
    <div class="bo-main">
        <div class="card-head">
            <h2 class="section-title"><i class="fas fa-chart-line"></i> Graphiques du Forum</h2>
        </div>

        <div class="stats-row">
            <div class="stat-card">
                <i class="fas fa-file-alt"></i>
                <div><span class="stat-num"><?= array_sum($posts) ?></span><span class="stat-label">Posts sur <?= $days ?> jours</span></div>
            </div>
            <div class="stat-card">
                <i class="fas fa-comment-dots"></i>
                <div><span class="stat-num"><?= array_sum($replies) ?></span><span class="stat-label">Réponses sur <?= $days ?> jours</span></div>
            </div>
            <div class="stat-card s-likes">
                <i class="fas fa-heart"></i>
                <div><span class="stat-num"><?= array_sum($likes) + array_sum($dislikes) ?></span><span class="stat-label">Réactions sur <?= $days ?> jours</span></div>
            </div>
        </div>

        <div class="chart-grid">
            <div class="chart-panel">
                <div class="chart-head">
                    <h3><i class="fas fa-layer-group"></i> Posts et Réponses</h3>
                    <div class="chart-toggles" data-chart="activity"></div>
                </div>
                <canvas id="activityChart" height="280"></canvas>
            </div>

            <div class="chart-panel">
                <div class="chart-head">
                    <h3><i class="fas fa-heart"></i> Likes et Dislikes</h3>
                    <div class="chart-toggles" data-chart="reactions"></div>
                </div>
                <canvas id="reactionChart" height="280"></canvas>
            </div>
        </div>
    </div>
</div>

<script>
const chartData = <?= json_encode($chartData, JSON_UNESCAPED_UNICODE) ?>;

function buildToggles(chartKey, render) {
    const wrap = document.querySelector('[data-chart="' + chartKey + '"]');
    if (!wrap) return {};

    const visible = {};
    chartData[chartKey].forEach(function (serie) {
        visible[serie.key] = true;
        const label = document.createElement('label');
        label.className = 'chart-toggle';
        label.innerHTML = '<input type="checkbox" checked data-serie="' + serie.key + '"> <span style="background:' + serie.color + '"></span>' + serie.label;
        wrap.appendChild(label);
    });

    wrap.addEventListener('change', function (e) {
        const key = e.target.getAttribute('data-serie');
        if (!key) return;
        visible[key] = e.target.checked;
        render();
    });

    return visible;
}

function drawLineChart(canvasId, series, visible) {
    const canvas = document.getElementById(canvasId);
    const ctx = canvas.getContext('2d');
    const rect = canvas.getBoundingClientRect();
    const dpr = window.devicePixelRatio || 1;
    canvas.width = rect.width * dpr;
    canvas.height = 280 * dpr;
    canvas.style.height = '280px';
    ctx.setTransform(dpr, 0, 0, dpr, 0, 0);

    const width = rect.width;
    const height = 280;
    const pad = {top: 20, right: 18, bottom: 42, left: 42};
    ctx.clearRect(0, 0, width, height);

    const active = series.filter(function (serie) { return visible[serie.key]; });
    const max = Math.max(1, ...active.flatMap(function (serie) { return serie.values; }));
    const plotW = width - pad.left - pad.right;
    const plotH = height - pad.top - pad.bottom;
    const labels = chartData.labels;

    ctx.strokeStyle = '#dde8c8';
    ctx.lineWidth = 1;
    ctx.fillStyle = '#8a9e6a';
    ctx.font = '12px DM Sans, sans-serif';
    ctx.textAlign = 'right';
    ctx.textBaseline = 'middle';

    for (let i = 0; i <= 4; i++) {
        const y = pad.top + plotH - (plotH * i / 4);
        const value = Math.round(max * i / 4);
        ctx.beginPath();
        ctx.moveTo(pad.left, y);
        ctx.lineTo(width - pad.right, y);
        ctx.stroke();
        ctx.fillText(value, pad.left - 8, y);
    }

    ctx.textAlign = 'center';
    ctx.textBaseline = 'top';
    labels.forEach(function (label, i) {
        if (i % 2 !== 0 && labels.length > 10) return;
        const x = pad.left + (labels.length === 1 ? 0 : plotW * i / (labels.length - 1));
        ctx.fillText(label, x, height - pad.bottom + 16);
    });

    active.forEach(function (serie) {
        ctx.strokeStyle = serie.color;
        ctx.fillStyle = serie.color;
        ctx.lineWidth = 3;
        ctx.beginPath();
        serie.values.forEach(function (value, i) {
            const x = pad.left + (serie.values.length === 1 ? 0 : plotW * i / (serie.values.length - 1));
            const y = pad.top + plotH - (plotH * value / max);
            if (i === 0) ctx.moveTo(x, y);
            else ctx.lineTo(x, y);
        });
        ctx.stroke();

        serie.values.forEach(function (value, i) {
            const x = pad.left + (serie.values.length === 1 ? 0 : plotW * i / (serie.values.length - 1));
            const y = pad.top + plotH - (plotH * value / max);
            ctx.beginPath();
            ctx.arc(x, y, 4, 0, Math.PI * 2);
            ctx.fill();
        });
    });
}

let activityVisible;
let reactionVisible;
function renderActivity() { drawLineChart('activityChart', chartData.activity, activityVisible); }
function renderReactions() { drawLineChart('reactionChart', chartData.reactions, reactionVisible); }

document.addEventListener('DOMContentLoaded', function () {
    activityVisible = buildToggles('activity', renderActivity);
    reactionVisible = buildToggles('reactions', renderReactions);
    renderActivity();
    renderReactions();
    window.addEventListener('resize', function () {
        renderActivity();
        renderReactions();
    });
});
</script>
<script src="../../assets/forum.js"></script>
</body>
</html>
