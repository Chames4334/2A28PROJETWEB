<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des congés</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .traitement-cell {
            max-width: 200px;
            font-size: 0.9rem;
        }
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .badge-approuve { background: #4caf50; color: white; }
        .badge-refuse { background: #f44336; color: white; }
        .badge-attente { background: #ff9800; color: white; }
        .action-group {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
        .btn-sm {
            padding: 6px 12px;
            font-size: 0.85rem;
        }
        .stats-summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }
        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 16px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: #6FAF4C;
        }
    </style>
</head>
<body>
    <div class="page-shell modern-shell">
        <header class="page-header modern-header admin-tone">
            <div>
                <p class="breadcrumb">Administration</p>
                <h1>Gestion des congés</h1>
            </div>
            <div class="header-actions">
                <a class="button button-primary" href="?action=calendarAdmin" style="background-color: #6b7d62; border-color: #6b7d62;">📅 Voir le Calendrier</a>
                <a class="button button-primary" href="?action=create">Nouveau congé</a>
                <a class="button button-secondary" href="?page=home">Accueil</a>
            </div>
        </header>

        <!-- Statistiques -->
        <div class="stats-summary">
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['total'] ?? 0; ?></div>
                <div>Total congés</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['en_attente'] ?? 0; ?></div>
                <div>En attente</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['approuve'] ?? 0; ?></div>
                <div>Approuvés</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" style="color: #e74c3c;"><?php echo $stats['refuse'] ?? 0; ?></div>
                <div>Refusés</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['traites'] ?? 0; ?></div>
                <div>Traitements effectués</div>
            </div>
        </div>

        <!-- Section Soldes & Graphique -->
        <section class="content-card dual-section" style="margin-bottom: 24px;">
            <div class="section-title-row">
                <h2>Solde des employés</h2>
            </div>
            <div style="display: flex; gap: 20px; flex-wrap: wrap;">
                <div style="flex: 1; min-width: 300px;">
                    <table class="table-list modern-table">
                        <thead>
                            <tr>
                                <th>Employé</th>
                                <th>Solde Total</th>
                                <th>Jours Pris</th>
                                <th>Solde Restant</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($employes as $emp): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($emp['prenom'] . ' ' . $emp['nom']); ?></td>
                                <td><?php echo $emp['solde_total']; ?></td>
                                <td><span class="pill tone-bad"><?php echo $emp['jours_pris']; ?></span></td>
                                <td>
                                    <?php if ($emp['solde_restant'] < 0): ?>
                                        <span class="pill tone-bad"><strong><?php echo $emp['solde_restant']; ?></strong></span>
                                    <?php else: ?>
                                        <span class="pill tone-ok"><strong><?php echo $emp['solde_restant']; ?></strong></span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div style="flex: 1; min-width: 300px; height: 300px;">
                    <canvas id="employesChart"></canvas>
                </div>
            </div>
        </section>

        <!-- Formulaire de recherche -->
        <section class="content-card dual-section">
            <div class="section-title-row">
                <h2>Liste des congés</h2>
                <span class="badge-soft"><?php echo count($conges); ?> élément(s)</span>
            </div>
            
            <form method="GET" class="toolbar-form">
                <input type="hidden" name="action" value="adminIndex">
                <input type="text" name="q" placeholder="Rechercher congé..." value="<?php echo htmlspecialchars($_GET['q'] ?? ''); ?>">
                <select name="sort">
                    <?php $sort = $_GET['sort'] ?? 'date_demande'; ?>
                    <option value="date_debut" <?php echo $sort === 'date_debut' ? 'selected' : ''; ?>>Date début</option>
                    <option value="date_fin" <?php echo $sort === 'date_fin' ? 'selected' : ''; ?>>Date fin</option>
                    <option value="type_conge" <?php echo $sort === 'type_conge' ? 'selected' : ''; ?>>Type</option>
                    <option value="statut" <?php echo $sort === 'statut' ? 'selected' : ''; ?>>Statut</option>
                    <option value="decision" <?php echo $sort === 'decision' ? 'selected' : ''; ?>>Décision</option>
                </select>
                <select name="dir">
                    <?php $dir = strtoupper($_GET['dir'] ?? 'DESC'); ?>
                    <option value="ASC" <?php echo $dir === 'ASC' ? 'selected' : ''; ?>>Asc</option>
                    <option value="DESC" <?php echo $dir === 'DESC' ? 'selected' : ''; ?>>Desc</option>
                </select>
                <button class="button button-small" type="submit">Filtrer</button>
                <a class="button button-small button-secondary" href="?action=congePdf&q=<?php echo urlencode($_GET['q'] ?? ''); ?>&sort=<?php echo urlencode($_GET['sort'] ?? 'date_demande'); ?>&dir=<?php echo urlencode($_GET['dir'] ?? 'DESC'); ?>">Exporter PDF</a>
            </form>

            <table class="table-list modern-table">
                <thead>
                    <tr>
                        <th>Dates</th>
                        <th>Type</th>
                        <th>Motif</th>
                        <th>Statut</th>
                        <th>Traitement</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($conges)): ?>
                        <tr><td colspan="6">Aucun congé trouvé.</td></tr>
                    <?php else: ?>
                        <?php foreach ($conges as $conge): ?>
                            <tr id="row-conge-<?php echo $conge['id_conge']; ?>">
                                <td style="white-space: nowrap;">
                                    <?php echo $conge['date_debut']; ?><br>
                                    <span style="font-size: 0.8rem; color: #888;">au</span><br>
                                    <?php echo $conge['date_fin']; ?>
                                </td>
                                <td><?php echo htmlspecialchars($conge['type_conge']); ?></td>
                                <td><?php echo htmlspecialchars(substr($conge['motif'], 0, 50)); ?></td>
                                <td id="status-container-<?php echo $conge['id_conge']; ?>">
                                    <span class="pill <?php 
                                        echo $conge['statut'] === 'approuvé' ? 'tone-ok' : ($conge['statut'] === 'refusé' ? 'tone-bad' : 'tone-warn'); 
                                    ?>">
                                        <?php echo htmlspecialchars($conge['statut']); ?>
                                    </span>
                                </td>
                                <td class="traitement-cell">
                                    <?php if (!empty($conge['date_traitement'])): ?>
                                        <div><strong>Date:</strong> <?php echo $conge['date_traitement']; ?></div>
                                        <div><strong>Décision:</strong> 
                                            <span class="badge badge-<?php 
                                                echo $conge['decision'] === 'approuvé' ? 'approuve' : ($conge['decision'] === 'refusé' ? 'refuse' : 'attente'); 
                                            ?>">
                                                <?php echo htmlspecialchars($conge['decision'] ?? 'N/A'); ?>
                                            </span>
                                        </div>
                                        <?php if (!empty($conge['commentaire_traitement'])): ?>
                                            <div><small><?php echo htmlspecialchars(substr($conge['commentaire_traitement'], 0, 40)); ?></small></div>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="muted">Non traité</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div style="display: flex; flex-direction: column; gap: 6px;">
                                        <?php if ($conge['statut'] === 'en_attente'): ?>
                                            <div style="display: flex; gap: 4px;">
                                                <button class="button button-small" onclick="quickUpdate(<?php echo $conge['id_conge']; ?>, 'approuvé')" title="Approuver rapidement" style="padding: 4px 8px; flex: 1;">✅</button>
                                                <button class="button button-small button-danger" onclick="quickUpdate(<?php echo $conge['id_conge']; ?>, 'refusé')" title="Refuser rapidement" style="padding: 4px 8px; flex: 1;">❌</button>
                                            </div>
                                        <?php endif; ?>
                                        <a class="button button-small" href="?action=edit&id=<?php echo $conge['id_conge']; ?>" style="text-align: center; font-size: 0.75rem; padding: 4px;">Modifier</a>
                                        <a class="button button-small button-secondary" href="?action=editTraitement&id=<?php echo $conge['id_conge']; ?>" style="text-align: center; font-size: 0.75rem; padding: 4px;">Traitement</a>
                                        <a class="button button-small button-danger" href="?action=delete&id=<?php echo $conge['id_conge']; ?>" onclick="return confirm('Supprimer ce congé ?');" style="text-align: center; font-size: 0.75rem; padding: 4px;">Supprimer</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>

        <footer class="page-footer">
            <a class="footer-link" href="?page=frontoffice">Voir l'espace utilisateur</a>
        </footer>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var employesData = <?php 
                $chartData = array_map(function($e) {
                    return [
                        'nom' => $e['prenom'] . ' ' . $e['nom'],
                        'pris' => $e['jours_pris'],
                        'restant' => $e['solde_restant']
                    ];
                }, $employes);
                echo json_encode($chartData);
            ?>;

            var labels = employesData.map(e => e.nom);
            var dataPris = employesData.map(e => e.pris);
            var dataRestant = employesData.map(e => e.restant);

            var ctx = document.getElementById('employesChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Jours pris',
                            data: dataPris,
                            backgroundColor: '#e74c3c'
                        },
                        {
                            label: 'Solde restant',
                            data: dataRestant,
                            backgroundColor: '#2ecc71'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: { stacked: true },
                        y: { stacked: true, beginAtZero: true }
                    }
                }
            });
        });
    </script>
    <script>
        function quickUpdate(id, decision) {
            if (!confirm("Confirmer la décision : " + decision + " ?")) return;

            const formData = new FormData();
            formData.append('decision', decision);
            formData.append('date_traitement', new Date().toISOString().split('T')[0]);
            formData.append('commentaire_traitement', 'Traité via action rapide.');

            fetch('?action=editTraitement&id=' + id, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const container = document.getElementById('status-container-' + id);
                    const pill = container.querySelector('.pill');
                    
                    // Mise à jour de la couleur et du texte
                    pill.textContent = data.statut;
                    pill.className = 'pill ' + (data.statut === 'approuvé' ? 'tone-ok status-updated-ok' : 'tone-bad status-updated-bad');
                    
                    // Retirer les boutons d'action rapide
                    const row = document.getElementById('row-conge-' + id);
                    const quickActions = row.querySelector('div[style*="display: flex; gap: 4px;"]');
                    if (quickActions) quickActions.remove();
                    
                    console.log('Update success for ID ' + id);
                } else {
                    alert('Erreur : ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Une erreur est survenue lors du traitement.');
            });
        }
    </script>
</body>
</html>