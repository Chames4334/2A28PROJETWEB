<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Back Office - Gestion des traitements</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="page-shell">
        <header class="page-header">
            <div>
                <p class="breadcrumb">Back Office</p>
                <h1>Gestion des traitements de congé</h1>
            </div>
            <div class="header-actions">
                <a class="button button-secondary" href="?page=home">Accueil</a>
            </div>
        </header>

        <section class="content-card">
            <table class="table-list">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Congé ID</th>
                        <th>Type</th>
                        <th>Date traitement</th>
                        <th>Décision</th>
                        <th>Commentaire</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($traitements)): ?>
                        <tr><td colspan="7">Aucun traitement trouvé.</td></tr>
                    <?php else: ?>
                        <?php foreach ($traitements as $t): ?>
                            <tr>
                                <td><?php echo $t['id_traitement']; ?></td>
                                <td><?php echo $t['id_conge']; ?></td>
                                <td><?php echo htmlspecialchars($t['type_conge']); ?></td>
                                <td><?php echo $t['date_traitement']; ?></td>
                                <td><?php echo htmlspecialchars($t['decision']); ?></td>
                                <td><?php echo htmlspecialchars(substr($t['commentaire'] ?? '', 0, 35)); ?></td>
                                <td>
                                    <a class="button button-small" href="?action=traitementEdit&id=<?php echo $t['id_traitement']; ?>">Modifier</a>
                                    <a class="button button-small button-danger" href="?action=traitementDelete&id=<?php echo $t['id_traitement']; ?>" onclick="return confirm('Supprimer ce traitement ?');">Supprimer</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>

        <footer class="page-footer">
            <a class="footer-link" href="?page=home">Accueil</a>
        </footer>
    </div>
</body>
</html>
