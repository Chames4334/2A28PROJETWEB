<?php
require_once __DIR__ . '/../../Model/Config/Database.php';
$database = new Database();
$db = $database->getConnection();

// Prepare stats: count demandes per month for last 6 months
$months = [];
$counts = [];
$now = new DateTime();
for ($i = 5; $i >= 0; $i--) {
    $m = (clone $now)->modify("-{$i} months");
    $months[] = $m->format('Y-m');
}
$placeholders = implode(',', array_fill(0, count($months), '?'));
$q = "SELECT DATE_FORMAT(created_at, '%Y-%m') as ym, COUNT(*) as cnt FROM demande_constat WHERE DATE_FORMAT(created_at, '%Y-%m') IN ($placeholders) GROUP BY ym";
$stmt = $db->prepare($q);
$stmt->execute($months);
$rows = $stmt->fetchAll(PDO::FETCH_KEY_PAIR); // ym => cnt
foreach ($months as $m) { $counts[] = isset($rows[$m]) ? (int)$rows[$m] : 0; }

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AS Assurance - Accueil</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, rgba(111,175,76,0.10) 0%, rgba(255,255,255,0.95) 48%, rgba(166,124,82,0.06) 100%);
            color: #2b2b2b;
        }
        
        @keyframes bgChange {
            0% { background-color: #6FAF4C; }
            33% { background-color: #A67C52; }
            66% { background-color: #F2F2F2; }
            100% { background-color: #6FAF4C; }
        }
        
        .header {
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 20px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
            padding: 1rem 2rem;
        }
        
        .header-inner {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        .logo {
            font-size: 1.8rem;
            font-weight: bold;
            background: linear-gradient(135deg, #6FAF4C 0%, #A67C52 100%);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            text-decoration: none;
        }
        
        .logo span {
            font-size: 0.8rem;
            display: block;
            color: #666;
        }
        
        .nav {
            display: flex;
            gap: 2rem;
        }
        
        .nav a {
            text-decoration: none;
            color: #333;
            font-weight: 500;
            transition: color 0.3s;
        }
        
        .nav a:hover, .nav a.active {
            color: #6FAF4C;
        }
        
        .btn-client {
            background: #6FAF4C;
            color: white;
            padding: 0.5rem 1.5rem;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .btn-client:hover {
            background: #A67C52;
            transform: translateY(-2px);
        }
        
        .hero {
            background: rgba(255,255,255,0.98);
            backdrop-filter: blur(4px);
            text-align: center;
            padding: 3.5rem 2rem;
            color: #2b2b2b;
            margin: 1.8rem;
            border-radius: 14px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.06);
            border-top: 4px solid #6FAF4C;
        }
        
        .hero h1 {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        
        .hero p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
        }
        
        .features {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }
        
        .feature-card {
            background: rgba(255,255,255,1);
            border-radius: 12px;
            padding: 1.6rem;
            text-align: center;
            transition: all 0.18s;
            border-top: 4px solid #6FAF4C;
            box-shadow: 0 8px 24px rgba(0,0,0,0.05);
        }
        
        .feature-card:hover {
            transform: translateY(-10px);
            background: white;
        }
        
        .feature-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        
        .feature-card h3 {
            margin-bottom: 1rem;
            color: #A67C52;
        }
        
        .stats {
            background: rgba(255,255,255,0.15);
            padding: 3rem 2rem;
            text-align: center;
            margin: 2rem;
            border-radius: 20px;
        }
        
        .stats-inner {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2rem;
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: white;
        }
        
        .footer {
            background: rgba(0,0,0,0.7);
            color: white;
            margin-top: 2rem;
            padding: 3rem 2rem 1rem;
        }
        
        .footer-inner {
            max-width: 1400px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
        }
        
        .footer a {
            color: rgba(255,255,255,0.8);
            text-decoration: none;
        }
        
        .footer a:hover {
            color: #6FAF4C;
        }
        
        .copyright {
            text-align: center;
            padding-top: 2rem;
            margin-top: 2rem;
            border-top: 1px solid rgba(255,255,255,0.2);
        }
        
        .btn {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 50px;
            text-decoration: none;
            font-size: 1rem;
            transition: all 0.3s;
        }
        
        .btn-primary {
            background: #6FAF4C;
            color: white;
        }
        
        .btn-primary:hover {
            background: #5d9a3f;
            transform: translateY(-2px);
        }
        
        .btn-outline {
            background: transparent;
            border: 2px solid white;
            color: white;
        }
        
        .btn-outline:hover {
            background: white;
            color: #6FAF4C;
        }
        
        @media (max-width: 768px) {
            .header-inner { flex-direction: column; text-align: center; }
            .nav { flex-direction: column; gap: 1rem; text-align: center; }
            .hero h1 { font-size: 2rem; }
            .hero { margin: 1rem; padding: 2rem; }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-inner">
            <a href="index.php?action=accueil" class="logo">
                AS ASSURANCE
                <span>Confiance & Stabilité</span>
            </a>
            <nav class="nav">
                <a href="index.php?action=accueil" class="active">Accueil</a>
                <a href="index.php?action=declaration">Déclaration</a>
                <a href="index.php?action=historique">Historique</a>
            </nav>
            <a href="index.php?action=historique" class="btn-client">Espace client</a>
        </div>
    </div>

    <main>
        <div class="hero" style="display:grid;grid-template-columns:1fr 420px;align-items:center;gap:24px;">
            <div>
                <h1>AS ASSURANCE : L'esprit tranquille</h1>
                <p>Devis instantané, déclaration par photo, et une IA vous guide vers l'option la plus avantageuse.</p>
                <div style="display:flex;gap:12px;margin-top:18px;flex-wrap:wrap">
                    <a href="index.php?action=declaration" class="btn btn-primary">📝 Déclarer un sinistre</a>
                    <a href="index.php?action=historique" class="btn btn-outline">📊 Suivre mon dossier</a>
                </div>
            </div>
            <div style="text-align:center">
                <img src="https://images.unsplash.com/photo-1542362567-b07e54358753?auto=format&fit=crop&w=900&q=60" alt="Voiture" style="width:100%;max-width:420px;border-radius:12px;box-shadow:0 12px 30px rgba(0,0,0,0.12);">
            </div>
        </div>

        <div class="features">
            <div class="feature-card">
                <div class="feature-icon">📱</div>
                <h3>100% Digital</h3>
                <p>Déclaration en ligne, suivi en temps réel, zéro papier.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🤖</div>
                <h3>IA Intelligente</h3>
                <p>Notre IA vous guide vers l'option la plus avantageuse.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🔧</div>
                <h3>Garage de confiance</h3>
                <p>Un réseau de garages partenaires pour une réparation de qualité.</p>
            </div>
        </div>

        <div class="stats">
            <div style="max-width:900px;margin:0 auto;">
                <h3 style="color:white;margin-bottom:12px">📈 Nombre de déclarations (6 derniers mois)</h3>
                <canvas id="demandesChart" height="140"></canvas>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            (function(){
                const labels = <?= json_encode(array_map(function($m){ return date('M Y', strtotime($m.'-01')); }, $months)); ?>;
                const data = <?= json_encode($counts); ?>;
                const ctx = document.getElementById('demandesChart').getContext('2d');
                const gradient = ctx.createLinearGradient(0,0,0,200);
                gradient.addColorStop(0,'rgba(111,175,76,0.5)');
                gradient.addColorStop(1,'rgba(111,175,76,0.05)');

                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Déclarations',
                            data: data,
                            fill: true,
                            backgroundColor: gradient,
                            borderColor: '#6FAF4C',
                            tension: 0.35,
                            pointRadius: 4,
                            pointBackgroundColor: '#fff',
                            pointBorderColor: '#6FAF4C',
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: { legend: { display: false } },
                        scales: {
                            x: { grid: { display: false }, ticks: { color: '#fff' } },
                            y: { beginAtZero: true, ticks: { color: '#fff' }, grid: { color: 'rgba(255,255,255,0.08)' } }
                        }
                    }
                });
            })();
        </script>
    </main>

    <footer class="footer">
        <div class="footer-inner">
            <div>
                <strong style="font-size: 1.2rem;">AS ASSURANCE</strong>
                <p style="margin-top: 0.5rem;">Assurance auto nouvelle génération.</p>
            </div>
            <div>
                <strong>Liens utiles</strong>
                <p><a href="declaration.php">📝 Déclaration sinistre</a></p>
                <p><a href="historique.php">📊 Historique</a></p>
            </div>
            <div>
                <strong>Contact</strong>
                <p>📞 +216 70 123 456</p>
                <p>📧 <a href="mailto:contact@asassurance.tn">contact@asassurance.tn</a></p>
                <p>📍 Tunis, Tunisie</p>
            </div>
        </div>
        <p class="copyright">© <?php echo date('Y'); ?> AS ASSURANCE</p>
    </footer>


</body>
</html>