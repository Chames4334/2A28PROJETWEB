<?php

declare(strict_types=1);

require_once __DIR__ . '/../core/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('create.php');
}

$app = forum_app($pdo);
$app['posts']->store();
