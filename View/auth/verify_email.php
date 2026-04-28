<?php
include_once '../../controller/ControlUser.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$token = $_GET['token'] ?? '';

if (empty($token)) {
    header('Location: login.php');
    exit;
}

$ctrl = new ControlUser();
if ($ctrl->verifyEmail($token)) {
    header('Location: login.php?verified=1');
} else {
    header('Location: login.php?error=invalid_token');
}
exit;