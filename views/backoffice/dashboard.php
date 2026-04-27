<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Congés et traitements</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="page-shell modern-shell">
        <header class="page-header modern-header admin-tone">
            <div>
                <p class="breadcrumb">Espace</p>
                <h1>Congés et traitements</h1>
            </div>
            <div class="header-actions">
                <a class="button button-primary" href="?action=traitementCreate">Ajouter traitement</a>
                <a class="button button-secondary" href="?page=home">Accueil</a>
            </div>
        </header>

        <section class="content-card dual-section">
            <div class="section-title-row">
                <h2>Gestion des congés</h2>
                <span class="badge-soft"><?php echo count($conges); ?> élément(s)</span>
            </div>
            <form method="GET" class="toolbar-form">
                <input type="hidden" name="action" value="adminIndex">
                <input type="text" name="q_conge" placeholder="Recherche congé..." value="<?php echo htmlspecialchars($_GET['q_conge'] ?? ''); ?>">
                <select name="sort_conge">
                    <?php $sortConge = $_GET['sort_conge'] ?? 'date_demande'; ?>
                    <option value="date_debut" <?php echo $sortConge === 'date_debut' ? 'selected' : ''; ?>>Date début</option>
                    <option value="date_fin" <?php echo $sortConge === 'date_fin' ? 'selected' : ''; ?>>Date fin</option>
                    <option value="type_conge" <?php echo $sortConge === 'type_conge' ? 'selected' : ''; ?>>Type</option>
                    <option value="statut" <?php echo $sortConge === 'statut' ? 'selected' : ''; ?>>Statut</option>
                </select>
                <select name="dir_conge">
                    <?php $dirConge = strtoupper($_GET['dir_conge'] ?? 'DESC'); ?>
                    <option value="ASC" <?php echo $dirConge === 'ASC' ? 'selected' : ''; ?>>Asc</option>
                    <option value="DESC" <?php echo $dirConge === 'DESC' ? 'selected' : ''; ?>>Desc</option>
                </select>
                <button class="button button-small" type="submit">Filtrer</button>
                <a class="button button-small button-secondary" href="?action=congePdf&q=<?php echo urlencode($_GET['q_conge'] ?? ''); ?>&sort=<?php echo urlencode($_GET['sort_conge'] ?? 'date_demande'); ?>&dir=<?php echo urlencode($_GET['dir_conge'] ?? 'DESC'); ?>">PDF Congés</a>
            </form>

            <table class="table-list modern-table">
                <thead>
                    <tr>
                        <th>Date début</th>
                        <th>Date fin</th>
                        <th>Type</th>
                        <th>Statut</th>
                        <th>Actions</th>
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
                                <td><?php echo htmlspecialchars($conge['statut']); ?></td>
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

        <section class="content-card dual-section">
            <div class="section-title-row">
                <h2>Gestion des traitements</h2>
                <span class="badge-soft"><?php echo count($traitements); ?> élément(s)</span>
            </div>
            <form method="GET" class="toolbar-form">
                <input type="hidden" name="action" value="adminIndex">
                <input type="text" name="q_traitement" placeholder="Recherche traitement..." value="<?php echo htmlspecialchars($_GET['q_traitement'] ?? ''); ?>">
                <select name="sort_traitement">
                    <?php $sortTraitement = $_GET['sort_traitement'] ?? 'date_traitement'; ?>
                    <option value="type_conge" <?php echo $sortTraitement === 'type_conge' ? 'selected' : ''; ?>>Type</option>
                    <option value="date_traitement" <?php echo $sortTraitement === 'date_traitement' ? 'selected' : ''; ?>>Date</option>
                    <option value="decision" <?php echo $sortTraitement === 'decision' ? 'selected' : ''; ?>>Décision</option>
                </select>
                <select name="dir_traitement">
                    <?php $dirTraitement = strtoupper($_GET['dir_traitement'] ?? 'DESC'); ?>
                    <option value="ASC" <?php echo $dirTraitement === 'ASC' ? 'selected' : ''; ?>>Asc</option>
                    <option value="DESC" <?php echo $dirTraitement === 'DESC' ? 'selected' : ''; ?>>Desc</option>
                </select>
                <button class="button button-small" type="submit">Filtrer</button>
                <a class="button button-small button-secondary" href="?action=traitementPdf&q=<?php echo urlencode($_GET['q_traitement'] ?? ''); ?>&sort=<?php echo urlencode($_GET['sort_traitement'] ?? 'date_traitement'); ?>&dir=<?php echo urlencode($_GET['dir_traitement'] ?? 'DESC'); ?>">PDF Traitements</a>
            </form>

            <table class="table-list modern-table">
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Date traitement</th>
                        <th>Décision</th>
                        <th>Commentaire</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($traitements)): ?>
                        <tr><td colspan="5">Aucun traitement trouvé.</td></tr>
                    <?php else: ?>
                        <?php foreach ($traitements as $t): ?>
                            <tr>
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
            <a class="footer-link" href="?action=index">Voir l'autre espace</a>
        </footer>
    </div>
</body>
</html>
