<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Back Office - Gestion des congés</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="page-shell">
        <header class="page-header">
            <div>
                <p class="breadcrumb">Back Office</p>
                <h1>Gestion des demandes de congé</h1>
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
                        <th>Date début</th>
                        <th>Date fin</th>
                        <th>Type</th>
                        <th>Statut</th>
                        <th>Employé</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($conges)): ?>
                        <tr><td colspan="7">Aucun congé trouvé.</td></tr>
                    <?php else: ?>
                        <?php foreach ($conges as $conge): ?>
                            <tr>
                                <td><?php echo $conge['id_conge']; ?></td>
                                <td><?php echo $conge['date_debut']; ?></td>
                                <td><?php echo $conge['date_fin']; ?></td>
                                <td><?php echo htmlspecialchars($conge['type_conge']); ?></td>
                                <td><?php echo htmlspecialchars($conge['statut']); ?></td>
                                <td><?php echo $conge['id_employe']; ?></td>
                                <td>
                                    <a class="button button-small" href="?action=edit&id=<?php echo $conge['id_conge']; ?>">Modifier</a>
                                    <a class="button button-small button-danger" href="?action=delete&id=<?php echo $conge['id_conge']; ?>" onclick="return confirm('Supprimer ce congé ?');">Supprimer</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>

        <footer class="page-footer">
            <a class="footer-link" href="?page=frontoffice">Voir le Front Office</a>
        </footer>
    </div>
</body>
</html>
