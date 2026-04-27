<?php
// view/frontoffice/forum/supprimer.php
include_once __DIR__ . '/../../../controller/ControlPost.php';
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id'])) { header('Location: ../../auth/login.php'); exit; }

$ctrl = new ControlPost();
$id   = intval($_GET['id'] ?? 0);
$post = $ctrl->getPostById($id);

if ($post && $post['user_id'] == $_SESSION['user_id']) {
    $ctrl->deletePost($id);
    $_SESSION['success'] = "Post supprimé avec succès.";
}

header('Location: liste.php'); exit;
