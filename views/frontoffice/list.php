<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Front Office - Mes Congés</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="assets/js/validation.js"></script>
</head>
<body>
    <div class="page-shell">
        <header class="page-header">
            <div>
                <p class="breadcrumb">Front Office</p>
                <h1>Mes demandes de congé</h1>
            </div>
            <div class="header-actions">
                <a class="button button-primary" href="?action=create">Nouvelle demande</a>
                <a class="button button-secondary" href="?page=home">Accueil</a>
            </div>
        </header>

        <section class="content-card">
            <table class="table-list">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Date début</th>
                        <th>Date fin</th>
                        <th>Type</th>
                        <th>Statut</th>
                        <th>Employé</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($conges)): ?>
                        <tr><td colspan="6">Aucun congé trouvé.</td></tr>
                    <?php else: ?>
                        <?php foreach ($conges as $conge): ?>
                            <tr>
                                <td><?php echo $conge['id_conge']; ?></td>
                                <td><?php echo $conge['date_debut']; ?></td>
                                <td><?php echo $conge['date_fin']; ?></td>
                                <td><?php echo htmlspecialchars($conge['type_conge']); ?></td>
                                <td><?php echo htmlspecialchars($conge['statut']); ?></td>
                                <td><?php echo $conge['id_employe']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>

        <footer class="page-footer">
            <a class="footer-link" href="?page=backoffice">Voir le Back Office</a>
        </footer>
    </div>
</body>
</html>
