<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Front Office - Traitements de congés</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="page-shell">
        <header class="page-header">
            <div>
                <p class="breadcrumb">Front Office</p>
                <h1>Suivi des traitements de congé</h1>
            </div>
            <div class="header-actions">
                <a class="button button-primary" href="?action=traitementCreate">Nouveau traitement</a>
                <a class="button button-secondary" href="?page=home">Accueil</a>
            </div>
        </header>

        <section class="content-card">
            <table class="table-list">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Congé ID</th>
                        <th>Date</th>
                        <th>Décision</th>
                        <th>Commentaire</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($traitements)): ?>
                        <tr><td colspan="5">Aucun traitement trouvé.</td></tr>
                    <?php else: ?>
                        <?php foreach ($traitements as $t): ?>
                            <tr>
                                <td><?php echo $t['id_traitement']; ?></td>
                                <td><?php echo $t['id_conge']; ?></td>
                                <td><?php echo $t['date_traitement']; ?></td>
                                <td><?php echo htmlspecialchars($t['decision']); ?></td>
                                <td><?php echo htmlspecialchars(substr($t['commentaire'] ?? '', 0, 50)); ?></td>
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
