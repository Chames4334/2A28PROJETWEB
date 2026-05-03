<?php
// view/frontoffice/forum/report.php
include_once __DIR__ . '/../../../controller/ControlReport.php';
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user_id'])) { header('Location: /view/auth/login.php'); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: liste.php'); exit; }

$post_id = intval($_POST['post_id'] ?? 0);
$target_type = $_POST['target_type'] ?? '';
$target_id = intval($_POST['target_id'] ?? 0);
$reason = trim($_POST['reason'] ?? '');
$other_reason = trim($_POST['other_reason'] ?? '');

$allowedReasons = ['Spam', 'Harcelement', 'Contenu inapproprie', 'Fausse information', 'Informations personnelles', 'Autre'];
if (!in_array($reason, $allowedReasons, true)) $reason = '';
if ($reason === 'Autre') $reason = 'Autre: ' . $other_reason;

if ($post_id && $target_id && in_array($target_type, ['post', 'reply'], true) && strlen($reason) >= 3) {
    $report = new Report(
        $_SESSION['user_id'],
        $reason,
        $target_type === 'post' ? $target_id : null,
        $target_type === 'reply' ? $target_id : null
    );
    $ctrl = new ControlReport();
    if ($ctrl->addReport($report)) {
        $_SESSION['success'] = "Signalement envoye.";
    } else {
        $_SESSION['success'] = "Vous avez deja signale ce contenu.";
    }
}

$anchor = $target_type === 'reply' ? '#reply-' . $target_id : '#post-top';
header('Location: detail.php?id=' . $post_id . $anchor); exit;
