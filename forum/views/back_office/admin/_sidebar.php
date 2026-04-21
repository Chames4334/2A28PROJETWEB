<?php
declare(strict_types=1);
/** @var string $adminNavActive */
$adminNavActive = $adminNavActive ?? '';
?>
<aside class="admin-sidebar" aria-label="Administration">
    <nav class="admin-sidebar__nav">
        <a class="admin-sidebar__link<?= $adminNavActive === 'dashboard' ? ' is-active' : '' ?>" href="admin_dashboard.php">Dashboard</a>
        <a class="admin-sidebar__link<?= $adminNavActive === 'posts' ? ' is-active' : '' ?>" href="admin_posts.php">Posts</a>
        <a class="admin-sidebar__link<?= $adminNavActive === 'replies' ? ' is-active' : '' ?>" href="admin_replies.php">Replies</a>
    </nav>
    <div class="admin-sidebar__footer">
        <a class="admin-sidebar__link admin-sidebar__link--exit" href="index.php">← Retour au forum</a>
    </div>
</aside>
