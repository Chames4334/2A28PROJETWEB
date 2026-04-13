<?php

declare(strict_types=1);

require_once __DIR__ . '/../core/bootstrap.php';

$app = forum_app($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $app['auth']->processRegister();
} else {
    $app['auth']->showRegister();
}
