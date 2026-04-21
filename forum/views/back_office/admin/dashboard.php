<?php
declare(strict_types=1);
/** @var string $pageTitle */
/** @var array{users_total: int, posts_active: int, posts_masque: int, replies_active: int, replies_masque: int} $adminKpis */
require dirname(__DIR__, 2) . '/front_office/layout/header.php';
require __DIR__ . '/_shell_start.php';
?>
<div class="page-heading">
    <h1>Administration</h1>
    <p class="text-muted">Utilisez la barre latérale pour gérer les sujets et les réponses.</p>
</div>
<section class="admin-kpis" aria-label="Indicateurs">
    <article class="admin-kpi">
        <span class="admin-kpi__label">Membres</span>
        <span class="admin-kpi__value"><?= (int) $adminKpis['users_total'] ?></span>
    </article>
    <article class="admin-kpi">
        <span class="admin-kpi__label">Sujets actifs</span>
        <span class="admin-kpi__value"><?= (int) $adminKpis['posts_active'] ?></span>
    </article>
    <article class="admin-kpi admin-kpi--muted">
        <span class="admin-kpi__label">Sujets masqués</span>
        <span class="admin-kpi__value"><?= (int) $adminKpis['posts_masque'] ?></span>
    </article>
    <article class="admin-kpi">
        <span class="admin-kpi__label">Réponses actives</span>
        <span class="admin-kpi__value"><?= (int) $adminKpis['replies_active'] ?></span>
    </article>
    <article class="admin-kpi admin-kpi--muted">
        <span class="admin-kpi__label">Réponses masquées</span>
        <span class="admin-kpi__value"><?= (int) $adminKpis['replies_masque'] ?></span>
    </article>
</section>
<?php
require __DIR__ . '/_shell_end.php';
require dirname(__DIR__, 2) . '/front_office/layout/footer.php';
