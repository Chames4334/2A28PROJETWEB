<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Traitements de congés</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="page-shell">
        <header class="page-header">
            <div>
                <p class="breadcrumb">Espace</p>
                <h1>Suivi des traitements de congé</h1>
            </div>
            <div class="header-actions">
                <a class="button button-primary" href="?action=traitementCreate">Nouveau traitement</a>
                <a class="button button-secondary" href="?page=home">Accueil</a>
            </div>
        </header>

        <section class="content-card">
            <form method="GET" style="margin-bottom: 1rem; display: flex; gap: 0.5rem; flex-wrap: wrap;">
                <input type="hidden" name="action" value="traitementIndex">
                <input type="text" name="q" placeholder="Rechercher..." value="<?php echo htmlspecialchars($_GET['q'] ?? ''); ?>">
                <select name="sort">
                    <?php $sort = $_GET['sort'] ?? 'date_traitement'; ?>
                    <option value="type_conge" <?php echo $sort === 'type_conge' ? 'selected' : ''; ?>>Type</option>
                    <option value="date_traitement" <?php echo $sort === 'date_traitement' ? 'selected' : ''; ?>>Date</option>
                    <option value="decision" <?php echo $sort === 'decision' ? 'selected' : ''; ?>>Décision</option>
                </select>
                <select name="dir">
                    <?php $dir = strtoupper($_GET['dir'] ?? 'DESC'); ?>
                    <option value="ASC" <?php echo $dir === 'ASC' ? 'selected' : ''; ?>>Asc</option>
                    <option value="DESC" <?php echo $dir === 'DESC' ? 'selected' : ''; ?>>Desc</option>
                </select>
                <button class="button button-small" type="submit">Filtrer</button>
                <a class="button button-small button-secondary" href="?action=traitementPdf&q=<?php echo urlencode($_GET['q'] ?? ''); ?>&sort=<?php echo urlencode($_GET['sort'] ?? 'date_traitement'); ?>&dir=<?php echo urlencode($_GET['dir'] ?? 'DESC'); ?>">Exporter PDF</a>
            </form>

            <table class="table-list">
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Date</th>
                        <th>Décision</th>
                        <th>Commentaire</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($traitements)): ?>
                        <tr><td colspan="4">Aucun traitement trouvé.</td></tr>
                    <?php else: ?>
                        <?php foreach ($traitements as $t): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($t['type_conge'] ?? ''); ?></td>
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
