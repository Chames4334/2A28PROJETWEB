<?php
// view/backoffice/forum/supprimer.php
include_once __DIR__ . '/../../../controller/ControlPost.php';
if (session_status() === PHP_SESSION_NONE) session_start();

$ctrl = new ControlPost();
$id   = intval($_GET['id'] ?? 0);

if ($id) {
    $ctrl->hardDeletePost($id);
    $_SESSION['success'] = "Post supprimé définitivement.";
}

header('Location: liste.php'); exit;
