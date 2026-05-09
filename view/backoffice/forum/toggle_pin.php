<?php
// view/backoffice/forum/toggle_pin.php
include_once __DIR__ . '/../../../controller/ControlPost.php';
if (session_status() === PHP_SESSION_NONE) session_start();

$ctrl = new ControlPost();
$id   = intval($_GET['id'] ?? 0);

if ($id) {
    $ctrl->togglePin($id);
    $_SESSION['success'] = "Statut d'épinglage modifié.";
}

header('Location: liste.php?tab=posts'); exit;
