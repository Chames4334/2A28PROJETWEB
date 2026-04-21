<?php
declare(strict_types=1);
/** @var string $pageTitle */
require dirname(__DIR__) . '/layout/header.php';
?>
<div class="empty-state card card--elevated">
    <div class="empty-state__icon" aria-hidden="true">?</div>
    <h1 class="empty-state__title">Sujet introuvable</h1>
    <p class="empty-state__text">Ce sujet n’existe pas ou n’est plus visible.</p>
    <p class="empty-state__action"><a class="btn" href="index.php">Retour à la liste</a></p>
</div>
<?php require dirname(__DIR__) . '/layout/footer.php'; ?>
