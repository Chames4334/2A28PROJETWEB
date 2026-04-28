<?php
include_once '../../controller/ControlUser.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

$ctrl = new ControlUser();
$userId = $_SESSION['user_id'];
$result = $ctrl->deleteMyAccount($userId);

if ($result['success']) {
    header('Location: ../auth/login.php?deleted=1');
} else {
    header('Location: profil.php?id=' . $userId . '&error=delete');
}
exit;
?>