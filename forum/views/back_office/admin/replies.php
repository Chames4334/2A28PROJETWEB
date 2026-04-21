<?php
declare(strict_types=1);
/** @var string $pageTitle */
/** @var list<array<string, mixed>> $replies */
require dirname(__DIR__, 2) . '/front_office/layout/header.php';
require __DIR__ . '/_shell_start.php';
?>
<div class="page-heading">
    <h1>Toutes les réponses</h1>
    <p class="text-muted">Tous statuts ; lien vers le sujet d’origine.</p>
</div>

<?php if ($replies === []) : ?>
    <p class="card card--empty">Aucune réponse.</p>
<?php else : ?>
    <div class="table-wrap card">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Sujet</th>
                    <th>Auteur</th>
                    <th>Extrait</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($replies as $row) : ?>
                    <tr>
                        <td><?= (int) $row['id'] ?></td>
                        <td><a href="post.php?id=<?= (int) $row['post_ref_id'] ?>"><?= h(mb_substr((string) $row['post_titre'], 0, 40)) ?></a></td>
                        <td><?= h(trim((string) $row['auteur_nom'] . ' ' . (string) $row['auteur_prenom'])) ?></td>
                        <td><?= h(excerpt((string) $row['contenu'], 80)) ?></td>
                        <td><span class="admin-badge"><?= h((string) $row['statut']) ?></span></td>
                        <td class="admin-table__actions">
                            <a class="btn btn--small btn--secondary" href="admin_reply_edit.php?id=<?= (int) $row['id'] ?>">Modifier</a>
                            <?php if ((string) $row['statut'] === 'actif') : ?>
                                <form class="inline-form" method="post" action="admin_reply_delete.php" data-confirm="Masquer cette réponse ?">
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
