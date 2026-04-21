<?php

declare(strict_types=1);

require_once __DIR__ . '/../core/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('admin_posts.php');
}

$id = (int) ($_POST['id'] ?? 0);
if ($id <= 0) {
    redirect('admin_posts.php');
}

$app = forum_app($pdo);
$app['admin']->deletePost($id);
