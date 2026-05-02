<?php
// view/backoffice/forum/supprimer_report.php
include_once __DIR__ . '/../../../controller/ControlReport.php';
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    header('Location: ../../auth/login.php'); exit;
}

$ctrl = new ControlReport();
$id = intval($_GET['id'] ?? 0);
$type = $_GET['type'] ?? 'post';
if (!in_array($type, ['post', 'reply'], true)) $type = 'post';
$target_id = intval($_GET['target_id'] ?? 0);

if ($id) {
    $ctrl->deleteReport($id);
    $_SESSION['success'] = "Signalement supprime.";
}

if ($target_id) {
    header('Location: report_detail.php?type=' . $type . '&id=' . $target_id); exit;
}
header('Location: reports.php'); exit;
