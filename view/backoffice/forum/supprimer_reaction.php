<?php
// view/backoffice/forum/supprimer_reaction.php
include_once __DIR__ . '/../../../controller/ControlReaction.php';
if (session_status() === PHP_SESSION_NONE) session_start();

$ctrl = new ControlReaction();
$id   = intval($_GET['id'] ?? 0);

if ($id) {
    $ctrl->deleteReaction($id);
    $_SESSION['success'] = "Réaction supprimée.";
}

header('Location: liste.php?tab=reactions'); exit;
