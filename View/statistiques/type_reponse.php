<?php
require_once __DIR__ . '/../../Model/Config/Database.php';
$database = new Database();
$db = $database->getConnection();

// Count ALL reponse_constat linked to ANY type of category 'atelier'
$queryAtelier = "SELECT COUNT(*) as total 
                 FROM reponse_constat r
                 INNER JOIN type_reponse t ON r.type_reponse_id = t.id
                 WHERE t.categorie = 'atelier'";
$stmtAtelier = $db->prepare($queryAtelier);
$stmtAtelier->execute();
$atelier = (int)($stmtAtelier->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);

// Count ALL reponse_constat linked to ANY type of category 'remboursement'
$queryRemboursement = "SELECT COUNT(*) as total 
                       FROM reponse_constat r
                       INNER JOIN type_reponse t ON r.type_reponse_id = t.id
                       WHERE t.categorie = 'remboursement'";
$stmtRemboursement = $db->prepare($queryRemboursement);
$stmtRemboursement->execute();
$remboursement = (int)($stmtRemboursement->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);

// Also count type entries per category (for display)
$queryCountTypes = "SELECT categorie, COUNT(*) as nb FROM type_reponse GROUP BY categorie";
$stmtCountTypes = $db->prepare($queryCountTypes);
$stmtCountTypes->execute();
$typeCounts = ['atelier' => 0, 'remboursement' => 0];
foreach ($stmtCountTypes->fetchAll(PDO::FETCH_ASSOC) as $row) {
    $typeCounts[$row['categorie']] = (int)$row['nb'];
}

$total = $atelier + $remboursement;
$hauteurAtelier       = $total > 0 ? ($atelier       / max($atelier, $remboursement, 1)) * 250 : 0;
$hauteurRemboursement = $total > 0 ? ($remboursement / max($atelier, $remboursement, 1)) * 250 : 0;
$pourcentageAtelier       = $total > 0 ? round(($atelier       / $total) * 100) : 0;
$pourcentageRemboursement = $total > 0 ? round(($remboursement / $total) * 100) : 0;

// Montant total par catégorie (sommes des montants dans reponse_constat)
$qMontant = "SELECT t.categorie, COALESCE(SUM(r.montant),0) as somme FROM reponse_constat r INNER JOIN type_reponse t ON r.type_reponse_id = t.id GROUP BY t.categorie";
$sMont = $db->prepare($qMontant);
$sMont->execute();
$montants = ['atelier' => 0.0, 'remboursement' => 0.0];
foreach($sMont->fetchAll(PDO::FETCH_ASSOC) as $rw) {
    $montants[$rw['categorie']] = (float)$rw['somme'];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Statistiques - Types de réponse</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #6FAF4C 0%, #A67C52 100%); min-height: 100vh; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; background: white; border-radius: 15px; padding: 30px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); }
        h1 { color: #A67C52; margin-bottom: 20px; border-left: 5px solid #6FAF4C; padding-left: 15px; }
        .chart-container { text-align: center; margin: 40px 0; }
        .chart-title { font-size: 1.2rem; color: #333; margin-bottom: 30px; font-weight: bold; }
        .bar-chart { display: flex; justify-content: center; align-items: flex-end; gap: 50px; min-height: 350px; padding: 20px; background: #f8f9fa; border-radius: 10px; }
        .bar-wrapper { text-align: center; width: 150px; }
        .bar { background: linear-gradient(135deg, #6FAF4C, #A67C52); width: 80px; margin: 0 auto; border-radius: 8px 8px 0 0; position: relative; }
        .bar-value { position: absolute; top: -30px; left: 50%; transform: translateX(-50%); font-weight: bold; color: #333; background: white; padding: 4px 8px; border-radius: 5px; font-size: 14px; }
        .bar-label { margin-top: 15px; font-weight: bold; color: #555; font-size: 16px; }
        .bar-count { font-size: 13px; color: #888; margin-top: 5px; }
        .stats-summary { display: flex; justify-content: space-around; flex-wrap: wrap; gap: 20px; margin-top: 40px; padding: 20px; background: #f0f4f0; border-radius: 10px; }
        .stat-card { text-align: center; padding: 15px; background: white; border-radius: 10px; min-width: 150px; }
        .stat-number { font-size: 2rem; font-weight: bold; color: #6FAF4C; }
        .stat-label { color: #666; margin-top: 5px; }
        .btn-back { background: #6c757d; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; margin-top: 20px; }
    </style>
</head>
<body>
<div class="container">
    <h1>📊 Statistiques des types de réponse</h1>
    
    <div class="chart-container">
        <div class="chart-title">🎯 Répartition Atelier vs Remboursement</div>

        <div style="background:#f8f9fa;border-radius:10px;padding:18px;">
            <canvas id="typeChart" height="180"></canvas>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            (function(){
                const labels = ['Atelier','Remboursement'];
                const counts = [<?= (int)$atelier ?>, <?= (int)$remboursement ?>];
                const amounts = [<?= json_encode($montants['atelier']) ?>, <?= json_encode($montants['remboursement']) ?>];
                const ctx = document.getElementById('typeChart').getContext('2d');

                const grad1 = ctx.createLinearGradient(0,0,0,220);
                grad1.addColorStop(0,'rgba(111,175,76,0.45)');
                grad1.addColorStop(1,'rgba(111,175,76,0.06)');

                const grad2 = ctx.createLinearGradient(0,0,0,220);
                grad2.addColorStop(0,'rgba(166,124,82,0.36)');
                grad2.addColorStop(1,'rgba(166,124,82,0.03)');

                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                label: 'Nombre de réponses',
                                data: counts,
                                yAxisID: 'y',
                                fill: true,
                                backgroundColor: grad1,
                                borderColor: '#6FAF4C',
                                tension: 0.5,
                                pointRadius: 6,
                                pointBackgroundColor: '#fff',
                                pointBorderColor: '#6FAF4C',
                                borderWidth: 2
                            },
                            {
                                label: 'Montant total (TND)',
                                data: amounts,
                                yAxisID: 'y1',
                                fill: true,
                                backgroundColor: grad2,
                                borderColor: '#A67C52',
                                tension: 0.5,
                                pointRadius: 6,
                                pointBackgroundColor: '#fff',
                                pointBorderColor: '#A67C52',
                                borderWidth: 2
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        plugins: { legend: { position: 'top' } },
                        scales: {
                            x: { grid: { display: false } },
                            y: { type: 'linear', position: 'left', beginAtZero: true, ticks: { color: '#444' } },
                            y1: { type: 'linear', position: 'right', beginAtZero: true, ticks: { color: '#444' }, grid: { drawOnChartArea: false } }
                        },
                        interaction: { mode: 'index', intersect: false }
                    }
                });
            })();
        </script>
    </div>
    
    <div class="stats-summary">
        <div class="stat-card">
            <div class="stat-number"><?= $total ?></div>
            <div class="stat-label">Total réponses</div>
        </div>
        <div class="stat-card">
            <div class="stat-number" style="color:#A67C52;"><?= $atelier ?></div>
            <div class="stat-label">Réponses Atelier</div>
            <div style="font-size:12px;color:#888;margin-top:4px;"><?= $typeCounts['atelier'] ?> type(s) enregistré(s)</div>
        </div>
        <div class="stat-card">
            <div class="stat-number" style="color:#6FAF4C;"><?= $remboursement ?></div>
            <div class="stat-label">Réponses Remboursement</div>
            <div style="font-size:12px;color:#888;margin-top:4px;"><?= $typeCounts['remboursement'] ?> type(s) enregistré(s)</div>
        </div>
    </div>
    
    <div style="text-align: center;">
        <a href="index.php?action=type_reponses" class="btn-back">← Retour aux types de réponse</a>
    </div>
</div>
</body>
</html>