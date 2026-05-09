<?php
    include "C:/xampp/htdocs/GreenSecure/Controller/ControlOffre.php";
    include "C:/xampp/htdocs/GreenSecure/Controller/ControlTypes.php";
    include "C:/xampp/htdocs/GreenSecure/Controller/Controlnscription.php";

    $CntrlInscri = new Controlnscription();
    $Cntrlt      = new ControlTypes();
    $of          = new controlOffre();

    $offresRaw        = $of->listeOffre('');
    $typesRaw         = $Cntrlt->listeType('');
    $subscriptionsRaw = $CntrlInscri->listeInscription('');

    $offres=($offresRaw instanceof PDOStatement) ? $offresRaw->fetchAll(PDO::FETCH_ASSOC): (array)$offresRaw;
    $types=($typesRaw instanceof PDOStatement) ? $typesRaw->fetchAll(PDO::FETCH_ASSOC): (array)$typesRaw;
    $subscriptions=($subscriptionsRaw instanceof PDOStatement) ? $subscriptionsRaw->fetchAll(PDO::FETCH_ASSOC): (array)$subscriptionsRaw;

    $totalOffres= count($offres);
    $totalSubs=count($subscriptions);

    $statutOffre=['active'=>0,'inactive'=>0,'archived'=>0];
    foreach ($offres as $o) {
        $s=strtolower($o['Status']);
        if(isset($statutOffre[$s])) $statutOffre[$s]++;
    }

    $methods      = ['Carte'=>0,'Virement'=>0,'Cheque'=>0,'Especes'=>0];
    $payStatuses  = ['Paid'=>0,'Pending'=>0,'Failed'=>0,'Refunded'=>0];
    $revenusTotal = 0;
    $revenusOffre = [];
    $parMois      = array_fill(1, 12, 0);

    foreach ($subscriptions as $s) {
        $m = $s['Payment_method'] ?? '';
        if (isset($methods[$m])) $methods[$m]++;

        $ps = $s['Payment_status'] ?? '';
        if (isset($payStatuses[$ps])) $payStatuses[$ps]++;

        if ($ps==='Paid') {
            $montant=(int)($s['Montant_paye'] ?? 0);
            $revenusTotal+=$montant;
            $choix = $s['Choix'] ?? 'Autre';
            $revenusOffre[$choix] = ($revenusOffre[$choix] ?? 0) + $montant;
        }

        $dateStr = $s['date_souscription'] ?? $s['Created_AT'] ?? '';
        if ($dateStr) {
            $moisNum = (int)date('n', strtotime($dateStr));
            if ($moisNum >= 1 && $moisNum <= 12) $parMois[$moisNum]++;
        }
    }

    $tauxPaiement=$totalSubs > 0 ? round(($payStatuses['Paid'] / $totalSubs) * 100): 0;

    $jsStatutOffre=json_encode(array_values($statutOffre));
    $jsMethods=json_encode(array_values($methods));
    $jsMethodLabels=json_encode(array_keys($methods));
    $jsPayStatuses=json_encode(array_values($payStatuses));
    $jsParMois=json_encode(array_values($parMois));

    arsort($revenusOffre);
    $jsRevenusLabels=json_encode(array_keys($revenusOffre));
    $jsRevenusData=json_encode(array_values($revenusOffre));
?>
<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <title>Statistiques – GreenSecure</title>
        <link rel="stylesheet" href="./assets/css/font.css">
    </head>
    <body>
    <div class="layout">
        <div class="sidebar">
            <img src="../images/logo.png" alt="logo" height="65" width="90">
            <h2>GreenSecure</h2>
            <a href="#">Dashboard</a>
            <a href="./addOffre.php">Offres</a>
            <a href="./addType.php">Assurance Types</a>
            <a href="./Statistique.php">Statistique</a>
        </div>
        <div class="main">
            <div class="topbar">
                <h1 style="text-align:left;">Statistiques</h1>
                <div style="text-align:right;">
                    <a href="addOffre.php" class="btn-primary">← Retour</a>
                    <a id="themeToggle" class="btn-primary">Dark Mode</a>
                </div>
            </div>

            <div class="content">

                <div class="metrics-grid">
                    <div class="metric-card">
                        <div class="metric-label">Total offres</div>
                        <div class="metric-value"><?= $totalOffres ?></div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-label">Offres actives</div>
                        <div class="metric-value" style="color:#1D9E75"><?= $statutOffre['active'] ?></div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-label">Total souscriptions</div>
                        <div class="metric-value"><?= $totalSubs ?></div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-label">Revenus perçus</div>
                        <div class="metric-value"><?= number_format($revenusTotal, 0, ',', ' ') ?></div>
                        <div class="metric-sub">DT (paiements confirmés)</div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-label">Taux de paiement</div>
                        <div class="metric-value"><?= $tauxPaiement ?>%</div>
                    </div>
                </div>

                <div class="charts-grid">
                    <div class="chart-card">
                        <div class="chart-title">Offres par statut</div>
                        <div class="legend">
                            <span><span class="legend-dot" style="background:#1D9E75"></span>Active (<?= $statutOffre['active'] ?>)</span>
                            <span><span class="legend-dot" style="background:#E24B4A"></span>Inactive (<?= $statutOffre['inactive'] ?>)</span>
                            <span><span class="legend-dot" style="background:#888780"></span>Archivée (<?= $statutOffre['archived'] ?>)</span>
                        </div>
                        <div style="position:relative; height:220px;">
                            <canvas id="c1" role="img" aria-label="Offres par statut">
                                Active: <?= $statutOffre['active'] ?>, Inactive: <?= $statutOffre['inactive'] ?>, Archivée: <?= $statutOffre['archived'] ?>
                            </canvas>
                        </div>
                    </div>

                    <div class="chart-card">
                        <div class="chart-title">Méthodes de paiement</div>
                        <div class="legend">
                            <?php
                            $methodColors = ['Carte'=>'#378ADD','Virement'=>'#EF9F27','Cheque'=>'#7F77DD','Especes'=>'#D85A30'];
                            foreach ($methods as $label => $val): ?>
                                <span><span class="legend-dot" style="background:<?= $methodColors[$label] ?>"></span><?= $label ?> (<?= $val ?>)</span>
                            <?php endforeach; ?>
                        </div>
                        <div style="position:relative; height:220px;">
                            <canvas id="c2" role="img" aria-label="Méthodes de paiement">
                                <?php foreach ($methods as $k => $v) echo "$k: $v. "; ?>
                            </canvas>
                        </div>
                    </div>

                    <div class="chart-card">
                        <div class="chart-title">Statut des paiements</div>
                        <div class="legend">
                            <?php
                            $psColors = ['Paid'=>'#1D9E75','Pending'=>'#EF9F27','Failed'=>'#E24B4A','Refunded'=>'#888780'];
                            foreach ($payStatuses as $label => $val): ?>
                                <span><span class="legend-dot" style="background:<?= $psColors[$label] ?>"></span><?= $label ?> (<?= $val ?>)</span>
                            <?php endforeach; ?>
                        </div>
                        <div style="position:relative; height:220px;">
                            <canvas id="c3" role="img" aria-label="Statut des paiements">
                                <?php foreach ($payStatuses as $k => $v) echo "$k: $v. "; ?>
                            </canvas>
                        </div>
                    </div>

                    <div class="chart-card">
                        <div class="chart-title">Revenus par offre (DT)</div>
                        <div style="position:relative; height:220px;">
                            <canvas id="c4" role="img" aria-label="Revenus par offre">
                                <?php foreach ($revenusOffre as $k => $v) echo "$k: $v DT. "; ?>
                            </canvas>
                        </div>
                    </div>
                </div>

                <div class="chart-card-full">
                    <div class="chart-title">Évolution des souscriptions par mois</div>
                    <div style="position:relative; height:220px;">
                        <canvas id="c5" role="img" aria-label="Souscriptions par mois">Évolution mensuelle</canvas>
                    </div>
                </div>
            </div>
            <button type="submit">Exporter en PDF</button>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.js"></script>
    <script src="./assets/js/script.js" defer></script>
    <script>
    const gridColor = 'rgba(0,0,0,0.06)';
    const textColor = '#888';

    new Chart(document.getElementById('c1'), {
        type: 'doughnut',
        data: {
            labels: ['Active','Inactive','Archivée'],
            datasets: [{ data: <?= $jsStatutOffre ?>, backgroundColor: ['#1D9E75','#E24B4A','#888780'], borderWidth: 0 }]
        },
        options: { responsive:true, maintainAspectRatio:false, plugins:{ legend:{ display:false } } }
    });

    new Chart(document.getElementById('c2'), {
        type: 'doughnut',
        data: {
            labels: <?= $jsMethodLabels ?>,
            datasets: [{ data: <?= $jsMethods ?>, backgroundColor: ['#378ADD','#EF9F27','#7F77DD','#D85A30'], borderWidth: 0 }]
        },
        options: { responsive:true, maintainAspectRatio:false, plugins:{ legend:{ display:false } } }
    });

    new Chart(document.getElementById('c3'), {
        type: 'bar',
        data: {
            labels: ['Paid','Pending','Failed','Refunded'],
            datasets: [{ data: <?= $jsPayStatuses ?>, backgroundColor: ['#1D9E75','#EF9F27','#E24B4A','#888780'], borderRadius: 4, borderWidth: 0 }]
        },
        options: {
            responsive:true, maintainAspectRatio:false,
            plugins:{ legend:{ display:false } },
            scales:{
                x:{ ticks:{ color:textColor }, grid:{ display:false } },
                y:{ ticks:{ color:textColor, stepSize:1 }, grid:{ color:gridColor }, beginAtZero:true }
            }
        }
    });

    new Chart(document.getElementById('c4'), {
        type: 'bar',
        data: {
            labels: <?= $jsRevenusLabels ?>,
            datasets: [{ data: <?= $jsRevenusData ?>, backgroundColor: '#378ADD', borderRadius: 4, borderWidth: 0 }]
        },
        options: {
            indexAxis: 'y',
            responsive:true, maintainAspectRatio:false,
            plugins:{ legend:{ display:false } },
            scales:{
                x:{ ticks:{ color:textColor }, grid:{ color:gridColor } },
                y:{ ticks:{ color:textColor }, grid:{ display:false } }
            }
        }
    });

    new Chart(document.getElementById('c5'), {
        type: 'line',
        data: {
            labels: ['Jan','Fév','Mar','Avr','Mai','Jun','Jul','Aoû','Sep','Oct','Nov','Déc'],
            datasets: [{
                label: 'Souscriptions',
                data: <?= $jsParMois ?>,
                fill: true,
                borderColor: '#378ADD',
                backgroundColor: 'rgba(55,138,221,0.10)',
                pointBackgroundColor: '#378ADD',
                tension: 0.3,
                borderWidth: 2
            }]
        },
        options: {
            responsive:true, maintainAspectRatio:false,
            plugins:{ legend:{ display:false } },
            scales:{
                x:{ ticks:{ color:textColor }, grid:{ color:gridColor } },
                y:{ ticks:{ color:textColor, stepSize:1 }, grid:{ color:gridColor }, beginAtZero:true }
            }
        }
    });
    </script>
    </body>
</html>