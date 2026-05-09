<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Congés et traitements</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="page-shell modern-shell">
        <header class="page-header modern-header">
            <div>
                <p class="breadcrumb">Espace</p>
                <h1>Mes congés et traitements</h1>
            </div>
            <div class="header-actions">
                <a class="button button-primary" href="?action=calendarAdmin" style="background-color: #6b7d62; border-color: #6b7d62;">📅 Voir le Calendrier</a>
                <a class="button button-primary" href="?action=create">Nouveau congé</a>
                <a class="button button-secondary" href="?page=home">Accueil</a>
            </div>
        </header>

        <section class="content-card dual-section stats-section">
            <div class="section-title-row">
                <h2>Statistiques & Solde</h2>
                <span class="badge-soft"><?php echo $stats['total'] ?? 0; ?> demande(s) au total</span>
            </div>

            <div class="stats-grid">
                <!-- Carte Solde -->
                <div class="stats-card">
                    <div class="stats-card-head">
                        <h3>Mon Solde de Congés</h3>
                        <span class="muted"><?php echo htmlspecialchars($employeInfo['prenom'] . ' ' . $employeInfo['nom']); ?></span>
                    </div>
                    <div class="stat-row">
                        <div class="stat-label">
                            <span class="pill tone-neutral">Solde Total</span>
                            <span class="muted"><?php echo $employeInfo['solde_total']; ?> jours</span>
                        </div>
                    </div>
                    <div class="stat-row">
                        <div class="stat-label">
                            <span class="pill tone-bad">Jours pris</span>
                            <span class="muted"><?php echo $employeInfo['jours_pris']; ?> jours</span>
                        </div>
                    </div>
                    <div class="stat-row">
                        <div class="stat-label">
                            <span class="pill tone-ok">Solde restant</span>
                            <span class="muted"><strong><?php echo $employeInfo['solde_restant']; ?> jours</strong></span>
                        </div>
                    </div>
                    <div style="width: 100%; height: 200px; margin-top: 15px;">
                        <canvas id="soldeChart"></canvas>
                    </div>
                </div>

                <!-- Carte Demandes -->
                <div class="stats-card">
                    <div class="stats-card-head">
                        <h3>Congés</h3>
                        <span class="muted"><?php echo $stats['total'] ?? 0; ?> demande(s)</span>
                    </div>
                    <?php
                        $total = max((int)($stats['total'] ?? 0), 1); // eviter la division par zéro
                        $pct_attente = round(((int)($stats['en_attente'] ?? 0) / $total) * 100, 1);
                        $pct_approuve = round(((int)($stats['approuve'] ?? 0) / $total) * 100, 1);
                        $pct_refuse = round(((int)($stats['refuse'] ?? 0) / $total) * 100, 1);
                        $pct_traites = round(((int)($stats['traites'] ?? 0) / $total) * 100, 1);
                    ?>
                    <div class="stat-row">
                        <div class="stat-label">
                            <span class="pill tone-warn">En attente</span>
                            <span class="muted"><?php echo $stats['en_attente'] ?? 0; ?></span>
                        </div>
                        <div class="stat-circle-wrap">
                            <div class="stat-circle tone-warn" style="--pct: <?php echo $pct_attente; ?>;">
                                <span><?php echo $pct_attente; ?>%</span>
                            </div>
                        </div>
                    </div>
                    <div class="stat-row">
                        <div class="stat-label">
                            <span class="pill tone-ok">Approuvés</span>
                            <span class="muted"><?php echo $stats['approuve'] ?? 0; ?></span>
                        </div>
                        <div class="stat-circle-wrap">
                            <div class="stat-circle tone-ok" style="--pct: <?php echo $pct_approuve; ?>;">
                                <span><?php echo $pct_approuve; ?>%</span>
                            </div>
                        </div>
                    </div>
                    <div class="stat-row">
                        <div class="stat-label">
                            <span class="pill tone-bad">Refusés</span>
                            <span class="muted"><?php echo $stats['refuse'] ?? 0; ?></span>
                        </div>
                        <div class="stat-circle-wrap">
                            <div class="stat-circle tone-bad" style="--pct: <?php echo $pct_refuse; ?>;">
                                <span><?php echo $pct_refuse; ?>%</span>
                            </div>
                        </div>
                    </div>
                    <div class="stat-row">
                        <div class="stat-label">
                            <span class="pill tone-neutral">Traités</span>
                            <span class="muted"><?php echo $stats['traites'] ?? 0; ?></span>
                        </div>
                        <div class="stat-circle-wrap">
                            <div class="stat-circle tone-neutral" style="--pct: <?php echo $pct_traites; ?>;">
                                <span><?php echo $pct_traites; ?>%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="conges" class="content-card dual-section">
            <div class="section-title-row">
                <h2>Demandes de congé</h2>
                <span class="badge-soft"><?php echo count($conges); ?> élément(s)</span>
            </div>
            <form method="GET" class="toolbar-form">
                <input type="hidden" name="action" value="index">
                <input type="text" name="q" placeholder="Recherche congé..." value="<?php echo htmlspecialchars($_GET['q'] ?? ''); ?>">
                <button class="button button-small" type="submit">Rechercher</button>
                <a class="button button-small button-secondary" href="?action=congePdf&q=<?php echo urlencode($_GET['q'] ?? ''); ?>&sort=<?php echo urlencode($_GET['sort'] ?? 'date_demande'); ?>&dir=<?php echo urlencode($_GET['dir'] ?? 'DESC'); ?>">PDF Congés</a>
            </form>

            <?php
                $sort = $_GET['sort'] ?? 'date_demande';
                $dir = strtoupper($_GET['dir'] ?? 'DESC');
                $nextDir = function ($field) use ($sort, $dir) {
                    return ($sort === $field && $dir === 'ASC') ? 'DESC' : 'ASC';
                };
                $sortLinkConge = function ($field) use ($nextDir) {
                    $params = $_GET;
                    $params['action'] = 'index';
                    $params['sort'] = $field;
                    $params['dir'] = $nextDir($field);
                    return '?' . http_build_query($params) . '#conges';
                };
            ?>

            <table class="table-list modern-table">
                <thead>
                    <tr>
                        <th><a class="sort-link" href="<?php echo htmlspecialchars($sortLinkConge('date_debut')); ?>">Date début</a></th>
                        <th><a class="sort-link" href="<?php echo htmlspecialchars($sortLinkConge('date_fin')); ?>">Date fin</a></th>
                        <th><a class="sort-link" href="<?php echo htmlspecialchars($sortLinkConge('type_conge')); ?>">Type</a></th>
                        <th><a class="sort-link" href="<?php echo htmlspecialchars($sortLinkConge('motif')); ?>">Motif</a></th>
                        <th><a class="sort-link" href="<?php echo htmlspecialchars($sortLinkConge('statut')); ?>">Statut</a></th>
                        <th>Traitement</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($conges)): ?>
                        <tr><td colspan="6">Aucun congé trouvé.</td></tr>
                    <?php else: ?>
                        <?php foreach ($conges as $conge): ?>
                            <tr>
                                <td><?php echo $conge['date_debut']; ?></td>
                                <td><?php echo $conge['date_fin']; ?></td>
                                <td><?php echo htmlspecialchars($conge['type_conge']); ?></td>
                                <td><?php echo htmlspecialchars(substr($conge['motif'], 0, 50)); ?></td>
                                <td>
                                    <span class="pill tone-<?php 
                                        echo $conge['statut'] === 'approuvé' ? 'ok' : ($conge['statut'] === 'refusé' ? 'bad' : 'warn'); 
                                    ?>">
                                        <?php echo htmlspecialchars($conge['statut']); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if (!empty($conge['date_traitement'])): ?>
                                        <div><strong><?php echo $conge['date_traitement']; ?></strong></div>
                                        <div>
                                            <span class="pill tone-<?php 
                                                echo ($conge['decision'] ?? '') === 'approuvé' ? 'ok' : (($conge['decision'] ?? '') === 'refusé' ? 'bad' : 'warn'); 
                                            ?>">
                                                <?php echo htmlspecialchars($conge['decision'] ?? 'N/A'); ?>
                                            </span>
                                        </div>
                                    <?php else: ?>
                                        <span class="muted">Non traité</span>
                                    <?php endif; ?>
                                </td>
                                <td class="action-buttons">
                                    <a class="btn btn-edit" href="?action=edit&id=<?php echo $conge['id_conge']; ?>">Modifier</a>
                                    <a class="btn btn-edit" href="?action=editTraitement&id=<?php echo $conge['id_conge']; ?>">Traitement</a>
                                    <a class="btn btn-delete" href="?action=delete&id=<?php echo $conge['id_conge']; ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce congé ?');">Supprimer</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>



        <footer class="page-footer">
            <a class="footer-link" href="?page=home">Retour accueil</a>
        </footer>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var ctxSolde = document.getElementById('soldeChart').getContext('2d');
            var soldeChart = new Chart(ctxSolde, {
                type: 'doughnut',
                data: {
                    labels: ['Jours pris', 'Solde restant'],
                    datasets: [{
                        data: [<?php echo $employeInfo['jours_pris']; ?>, <?php echo $employeInfo['solde_restant']; ?>],
                        backgroundColor: [
                            '#e74c3c', // rouge pour les jours pris
                            '#2ecc71'  // vert pour le solde restant
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom' }
                    }
                }
            });
        });
    </script>

</body>
</html>