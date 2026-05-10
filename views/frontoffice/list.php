<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes Congés</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="assets/js/validation.js"></script>
</head>
<body>
    <div class="page-shell">
        <header class="page-header">
            <div>
                <p class="breadcrumb">Espace</p>
                <h1>Mes demandes de congé</h1>
            </div>
            <div class="header-actions">
                <a class="button button-primary" href="?action=create">Nouvelle demande</a>
                <a class="button button-secondary" href="?page=home">Accueil</a>
            </div>
        </header>

        <section class="content-card">
            <form method="GET" style="margin-bottom: 1rem; display: flex; gap: 0.5rem; flex-wrap: wrap;">
                <input type="hidden" name="action" value="index">
                <input type="text" name="q" placeholder="Rechercher..." value="<?php echo htmlspecialchars($_GET['q'] ?? ''); ?>">
                <select name="sort">
                    <?php $sort = $_GET['sort'] ?? 'date_demande'; ?>
                    <option value="date_debut" <?php echo $sort === 'date_debut' ? 'selected' : ''; ?>>Date début</option>
                    <option value="date_fin" <?php echo $sort === 'date_fin' ? 'selected' : ''; ?>>Date fin</option>
                    <option value="type_conge" <?php echo $sort === 'type_conge' ? 'selected' : ''; ?>>Type</option>
                    <option value="statut" <?php echo $sort === 'statut' ? 'selected' : ''; ?>>Statut</option>
                </select>
                <select name="dir">
                    <?php $dir = strtoupper($_GET['dir'] ?? 'DESC'); ?>
                    <option value="ASC" <?php echo $dir === 'ASC' ? 'selected' : ''; ?>>Asc</option>
                    <option value="DESC" <?php echo $dir === 'DESC' ? 'selected' : ''; ?>>Desc</option>
                </select>
                <button class="button button-small" type="submit">Filtrer</button>
                <a class="button button-small button-secondary" href="?action=congePdf&q=<?php echo urlencode($_GET['q'] ?? ''); ?>&sort=<?php echo urlencode($_GET['sort'] ?? 'date_demande'); ?>&dir=<?php echo urlencode($_GET['dir'] ?? 'DESC'); ?>">Exporter PDF</a>
            </form>

            <table class="table-list">
                <thead>
                    <tr>
                        <th>Date début</th>
                        <th>Date fin</th>
                        <th>Type</th>
                        <th>Motif</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($conges)): ?>
                        <tr><td colspan="5">Aucun congé trouvé.</td></tr>
                    <?php else: ?>
                        <?php foreach ($conges as $conge): ?>
                            <tr>
                                <td><?php echo $conge['date_debut']; ?></td>
                                <td><?php echo $conge['date_fin']; ?></td>
                                <td><?php echo htmlspecialchars($conge['type_conge']); ?></td>
                                <td><?php echo htmlspecialchars(substr($conge['motif'], 0, 50)); ?></td>
                                <td><?php echo htmlspecialchars($conge['statut']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>

        <footer class="page-footer">
            <a class="footer-link" href="?page=backoffice">Voir l'autre espace</a>
        </footer>
    </div>
</body>
</html>
