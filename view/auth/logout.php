<?php
// view/auth/logout.php
if (session_status() === PHP_SESSION_NONE) session_start();

// Destroy session
session_destroy();

// Redirect to login
header('Location: login.php');
exit;
