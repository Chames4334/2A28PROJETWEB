<?php
declare(strict_types=1);
/** @var string $pageTitle */
/** @var list<array<string, mixed>> $posts */
require dirname(__DIR__, 2) . '/front_office/layout/header.php';
require __DIR__ . '/_shell_start.php';
?>
<div class="page-heading">
    <h1>Tous les sujets</h1>
    <p class="text-muted">Y compris masqués ou supprimés (statut affiché).</p>
</div>

<?php if ($posts === []) : ?>
    <p class="card card--empty">Aucun sujet.</p>
<?php else : ?>
    <div class="table-wrap card">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Titre</th>
                    <th>Auteur</th>
                    <th>Statut</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($posts as $row) : ?>
                    <tr>
                        <td><?= (int) $row['id'] ?></td>
                        <td><?= h(mb_substr((string) $row['titre'], 0, 60)) ?><?= mb_strlen((string) $row['titre']) > 60 ? '…' : '' ?></td>
                        <td><?= h(trim((string) $row['auteur_nom'] . ' ' . (string) $row['auteur_prenom'])) ?></td>
                        <td><span class="admin-badge"><?= h((string) $row['statut']) ?></span></td>
                        <td><?= h(date('d/m/Y H:i', strtotime((string) $row['created_at']))) ?></td>
                        <td class="admin-table__actions">
                            <a class="btn btn--small btn--secondary" href="admin_post_edit.php?id=<?= (int) $row['id'] ?>">Modifier</a>
                            <?php if ((string) $row['statut'] === 'actif') : ?>
                                <form class="inline-form" method="post" action="admin_post_delete.php" data-confirm="Masquer ce sujet ?">
                                    <input type="hidden" name="_csrf" value="<?= h(csrf_token()) ?>">
                                    <input type="hidden" name="id" value="<?= (int) $row['id'] ?>">
                                    <button type="submit" class="btn btn--small btn--danger">Masquer</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php
require __DIR__ . '/_shell_end.php';
require dirname(__DIR__, 2) . '/front_office/layout/footer.php';
