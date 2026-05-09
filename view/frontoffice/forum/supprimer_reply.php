<?php
// view/frontoffice/forum/supprimer_reply.php
include_once __DIR__ . '/../../../controller/ControlReply.php';
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id'])) { header('Location: ../../auth/login.php'); exit; }

$ctrl    = new ControlReply();
$id      = intval($_GET['id'] ?? 0);
$post_id = intval($_GET['post_id'] ?? 0);
$reply   = $ctrl->getReplyById($id);

if ($reply && $reply['user_id'] == $_SESSION['user_id']) {
    $ctrl->deleteReply($id);
    $_SESSION['success'] = "Réponse supprimée.";
}

header("Location: detail.php?id=$post_id"); exit;
