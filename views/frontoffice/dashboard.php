<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Congés et traitements</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="page-shell modern-shell">
        <header class="page-header modern-header">
            <div>
                <p class="breadcrumb">Espace</p>
                <h1>Mes congés et traitements</h1>
            </div>
            <div class="header-actions">
                <a class="button button-primary" href="?action=create">Nouveau congé</a>
                <a class="button button-primary" href="?action=traitementCreate">Nouveau traitement</a>
                <a class="button button-secondary" href="?page=home">Accueil</a>
            </div>
        </header>

        <section class="content-card dual-section stats-section">
            <div class="section-title-row">
                <h2>Statistiques</h2>
                <span class="badge-soft"><?php echo (int)($congeStats['total'] ?? 0) + (int)($traitementStats['total'] ?? 0); ?> total</span>
            </div>

            <div class="stats-grid">
                <div class="stats-card">
                    <div class="stats-card-head">
                        <h3>Congés par statut</h3>
                        <span class="muted"><?php echo (int)($congeStats['total'] ?? 0); ?> élément(s)</span>
                    </div>
                    <?php if (empty($congeStats['items'])): ?>
                        <p class="muted">Aucune donnée.</p>
                    <?php else: ?>
                        <?php foreach ($congeStats['items'] as $s): ?>
                            <?php
                                $key = strtolower((string)$s['key']);
                                $tone = 'tone-neutral';
                                if ($key === 'approuvé' || $key === 'approuve' || $key === 'approuvee' || $key === 'approuvée') $tone = 'tone-ok';
                                if ($key === 'refusé' || $key === 'refuse' || $key === 'refusee' || $key === 'refusée') $tone = 'tone-bad';
                                if ($key === 'en_attente' || $key === 'en attente') $tone = 'tone-warn';
                            ?>
                            <div class="stat-row">
                                <div class="stat-label">
                                    <span class="pill <?php echo $tone; ?>"><?php echo htmlspecialchars((string)$s['key']); ?></span>
                                    <span class="muted"><?php echo (int)$s['count']; ?></span>
                                </div>
                                <div class="stat-circle-wrap">
                                    <div class="stat-circle <?php echo $tone; ?>" style="--pct: <?php echo (float)$s['pct']; ?>;">
                                        <span><?php echo (float)$s['pct']; ?>%</span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <div class="stats-card">
                    <div class="stats-card-head">
                        <h3>Traitements par décision</h3>
                        <span class="muted"><?php echo (int)($traitementStats['total'] ?? 0); ?> élément(s)</span>
                    </div>
                    <?php if (empty($traitementStats['items'])): ?>
                        <p class="muted">Aucune donnée.</p>
                    <?php else: ?>
                        <?php foreach ($traitementStats['items'] as $s): ?>
                            <?php
                                $key = strtolower((string)$s['key']);
                                $tone = 'tone-neutral';
                                if ($key === 'approuvé' || $key === 'approuve') $tone = 'tone-ok';
                                if ($key === 'refusé' || $key === 'refuse') $tone = 'tone-bad';
                                if ($key === 'en_attente' || $key === 'en attente') $tone = 'tone-warn';
                            ?>
                            <div class="stat-row">
                                <div class="stat-label">
                                    <span class="pill <?php echo $tone; ?>"><?php echo htmlspecialchars((string)$s['key']); ?></span>
                                    <span class="muted"><?php echo (int)$s['count']; ?></span>
                                </div>
                                <div class="stat-circle-wrap">
                                    <div class="stat-circle <?php echo $tone; ?>" style="--pct: <?php echo (float)$s['pct']; ?>;">
                                        <span><?php echo (float)$s['pct']; ?>%</span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
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
                <input type="text" name="q_conge" placeholder="Recherche congé..." value="<?php echo htmlspecialchars($_GET['q_conge'] ?? ''); ?>">
                <button class="button button-small" type="submit">Rechercher</button>
                <a class="button button-small button-secondary" href="?action=congePdf&q=<?php echo urlencode($_GET['q_conge'] ?? ''); ?>&sort=<?php echo urlencode($_GET['sort_conge'] ?? 'date_demande'); ?>&dir=<?php echo urlencode($_GET['dir_conge'] ?? 'DESC'); ?>">PDF Congés</a>
            </form>

            <?php
                $sortConge = $_GET['sort_conge'] ?? 'date_demande';
                $dirConge = strtoupper($_GET['dir_conge'] ?? 'DESC');
                $nextDirConge = function ($field) use ($sortConge, $dirConge) {
                    return ($sortConge === $field && $dirConge === 'ASC') ? 'DESC' : 'ASC';
                };
                $sortLinkConge = function ($field) use ($nextDirConge) {
                    $params = $_GET;
                    $params['action'] = 'index';
                    $params['sort_conge'] = $field;
                    $params['dir_conge'] = $nextDirConge($field);
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

        <section id="traitements" class="content-card dual-section">
            <div class="section-title-row">
                <h2>Traitements de congé</h2>
                <span class="badge-soft"><?php echo count($traitements); ?> élément(s)</span>
            </div>
            <form method="GET" class="toolbar-form">
                <input type="hidden" name="action" value="index">
                <input type="text" name="q_traitement" placeholder="Recherche traitement..." value="<?php echo htmlspecialchars($_GET['q_traitement'] ?? ''); ?>">
                <button class="button button-small" type="submit">Rechercher</button>
                <a class="button button-small button-secondary" href="?action=traitementPdf&q=<?php echo urlencode($_GET['q_traitement'] ?? ''); ?>&sort=<?php echo urlencode($_GET['sort_traitement'] ?? 'date_traitement'); ?>&dir=<?php echo urlencode($_GET['dir_traitement'] ?? 'DESC'); ?>">PDF Traitements</a>
            </form>

            <?php
                $sortTraitement = $_GET['sort_traitement'] ?? 'date_traitement';
                $dirTraitement = strtoupper($_GET['dir_traitement'] ?? 'DESC');
                $nextDirTraitement = function ($field) use ($sortTraitement, $dirTraitement) {
                    return ($sortTraitement === $field && $dirTraitement === 'ASC') ? 'DESC' : 'ASC';
                };
                $sortLinkTraitement = function ($field) use ($nextDirTraitement) {
                    $params = $_GET;
                    $params['action'] = 'index';
                    $params['sort_traitement'] = $field;
                    $params['dir_traitement'] = $nextDirTraitement($field);
                    return '?' . http_build_query($params) . '#traitements';
                };
            ?>

            <table class="table-list modern-table">
                <thead>
                    <tr>
                        <th><a class="sort-link" href="<?php echo htmlspecialchars($sortLinkTraitement('type_conge')); ?>">Type</a></th>
                        <th><a class="sort-link" href="<?php echo htmlspecialchars($sortLinkTraitement('date_traitement')); ?>">Date</a></th>
                        <th><a class="sort-link" href="<?php echo htmlspecialchars($sortLinkTraitement('decision')); ?>">Décision</a></th>
                        <th><a class="sort-link" href="<?php echo htmlspecialchars($sortLinkTraitement('commentaire')); ?>">Commentaire</a></th>
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
            <a class="footer-link" href="?page=home">Retour accueil</a>
        </footer>
    </div>
</body>
</html>
