<?php

declare(strict_types=1);

require_once __DIR__ . '/../core/bootstrap.php';

$id = (int) ($_GET['id'] ?? 0);
if ($id <= 0) {
    redirect('admin_replies.php');
}

$app = forum_app($pdo);
$app['admin']->editReply($id);
