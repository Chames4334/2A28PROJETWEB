<?php
include_once '../../controller/ControlUser.php';

$ctrl = new ControlUser();
$id = $_GET['id'] ?? 0;

if ($id) {
    $ctrl->deleteUser($id);
    $_SESSION['success'] = "Utilisateur supprimé avec succès";
}

header('Location: liste.php');
exit;