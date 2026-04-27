<?php
// view/backoffice/forum/supprimer_reply.php
include_once __DIR__ . '/../../../controller/ControlReply.php';
if (session_status() === PHP_SESSION_NONE) session_start();

$ctrl = new ControlReply();
$id   = intval($_GET['id'] ?? 0);

if ($id) {
    $ctrl->hardDeleteReply($id);
    $_SESSION['success'] = "Réponse supprimée définitivement.";
}

header('Location: liste.php?tab=replies'); exit;
