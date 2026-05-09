<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GS Assurance | Historique des déclarations</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: #F2F2F2;
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
            color: #2E2E2E;
        }
        .animated-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -2;
            background: radial-gradient(circle at 20% 30%, #6FAF4C, #A67C52, #F2F2F2, #6FAF4C);
            background-size: 300% 300%;
            animation: radialShift 14s ease infinite alternate;
        }
        @keyframes radialShift {
            0% { background-position: 0% 0%; background-size: 300% 300%; }
            50% { background-position: 100% 100%; background-size: 400% 400%; }
            100% { background-position: 50% 50%; background-size: 300% 300%; }
        }
        .floating-shape {
            position: fixed;
            background: rgba(111, 175, 76, 0.15);
            backdrop-filter: blur(12px);
            border-radius: 50%;
            pointer-events: none;
            z-index: -1;
            animation: floatComplex 20s infinite alternate ease-in-out;
        }
        .shape1 { width: 350px; height: 350px; top: -120px; left: -100px; background: radial-gradient(circle, rgba(111,175,76,0.3), rgba(166,124,82,0.2)); animation-duration: 24s; }
        .shape2 { width: 480px; height: 480px; bottom: -180px; right: -140px; background: radial-gradient(circle, rgba(166,124,82,0.3), rgba(111,175,76,0.2)); animation-duration: 28s; animation-direction: alternate-reverse; }
        .shape3 { width: 220px; height: 220px; top: 35%; right: 2%; background: rgba(242,242,242,0.25); border-radius: 40% 60% 60% 40% / 40% 50% 50% 60%; animation: morphing 12s infinite alternate, floatComplex 18s infinite; }
        .shape4 { width: 170px; height: 170px; bottom: 12%; left: 2%; background: rgba(111,175,76,0.2); border-radius: 30% 70% 70% 30% / 30% 40% 60% 70%; animation: morphing 15s infinite alternate-reverse, floatComplex 22s infinite; }
        @keyframes floatComplex {
            0% { transform: translateY(0) rotate(0deg) scale(1); }
            50% { transform: translateY(-50px) rotate(12deg) scale(1.08); }
            100% { transform: translateY(30px) rotate(-8deg) scale(0.95); }
        }
        @keyframes morphing {
            0% { border-radius: 40% 60% 60% 40% / 40% 50% 50% 60%; }
            100% { border-radius: 60% 40% 40% 60% / 50% 40% 60% 50%; }
        }
        .container { max-width: 1280px; margin: 0 auto; padding: 0 24px; position: relative; z-index: 2; }
        .navbar {
            background: rgba(242, 242, 242, 0.85);
            backdrop-filter: blur(12px);
            border-radius: 0 0 36px 36px;
            box-shadow: 0 6px 18px rgba(0,0,0,0.05);
            position: sticky;
            top: 0;
            z-index: 10;
            margin-bottom: 2rem;
        }
        .nav-flex {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.8rem 0;
            flex-wrap: wrap;
        }
        .logo-area { display: flex; align-items: center; gap: 12px; }
        .logo-circle { background: #6FAF4C; width: 50px; height: 50px; border-radius: 30px; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 18px rgba(111,175,76,0.3); }
        .logo-circle span { font-size: 26px; font-weight: 800; color: white; }
        .logo-text h1 { font-size: 1.5rem; font-weight: 800; color: #A67C52; }
        .logo-text p { font-size: 0.7rem; color: #6FAF4C; font-weight: 700; letter-spacing: 1px; }
        .nav-links { display: flex; gap: 1.8rem; list-style: none; align-items: center; }
        .nav-links a { text-decoration: none; font-weight: 700; color: #4A3B2C; transition: 0.2s; }
        .nav-links a:hover { color: #6FAF4C; }
        .btn-nav { background: #6FAF4C; color: white !important; padding: 0.45rem 1.2rem; border-radius: 40px; font-weight: 700; }
        .btn-nav-outline { border: 1.5px solid #6FAF4C; color: #6FAF4C !important; padding: 0.45rem 1.2rem; border-radius: 40px; font-weight: 700; }
        .card {
            background: rgba(242, 242, 242, 0.85);
            backdrop-filter: blur(8px);
            border-radius: 48px;
            padding: 2rem;
            margin: 1.5rem 0 2rem;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            border: 1px solid rgba(166,124,82,0.2);
        }
        .card h2 {
            color: #A67C52;
            font-size: 1.8rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        th {
            background: #A67C52;
            color: white;
            padding: 1rem;
            text-align: left;
            font-weight: 700;
        }
        td {
            padding: 1rem;
            border-bottom: 1px solid #eee;
        }
        .badge {
            background: #E8F5E9;
            color: #2E7D32;
            padding: 0.2rem 0.8rem;
            border-radius: 40px;
            font-weight: 600;
            display: inline-block;
        }
        .qr-img {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }
        .btn-reponse, .btn-traiter {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 0.4rem 1rem;
            border-radius: 40px;
            text-decoration: none;
            font-size: 0.8rem;
            font-weight: 600;
            transition: 0.2s;
        }
        .btn-reponse {
            background: #6FAF4C;
            color: white;
        }
        .btn-reponse:hover {
            background: #5A9A3A;
            transform: translateY(-2px);
        }
        .btn-traiter {
            background: #A67C52;
            color: white;
        }
        .btn-traiter:hover {
            background: #8B5E3C;
            transform: translateY(-2px);
        }
        .btn-home {
            background: #A67C52;
            color: white;
            padding: 0.6rem 1.2rem;
            border-radius: 40px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-top: 1rem;
            transition: 0.2s;
        }
        .btn-home:hover {
            background: #8B5E3C;
            transform: translateY(-2px);
        }
        footer {
            background: #A67C52;
            color: white;
            padding: 2rem 0 1rem;
            border-radius: 32px 32px 0 0;
            margin-top: 2rem;
        }
        .footer-grid {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 1.5rem;
        }
        .footer-col h4 { margin-bottom: 0.8rem; font-weight: 800; }
        .footer-col a, .footer-col p { color: #F2F2F2; text-decoration: none; font-size: 0.9rem; font-weight: 600; }
        .social i { font-size: 1.3rem; margin-right: 0.8rem; transition: 0.2s; }
        .copyright { text-align: center; padding-top: 1.5rem; margin-top: 1.5rem; border-top: 1px solid rgba(255,255,255,0.2); font-size: 0.75rem; font-weight: 600; }
        @media (max-width: 800px) {
            .nav-flex { flex-direction: column; gap: 1rem; }
            table, thead, tbody, th, td, tr { display: block; }
            th { display: none; }
            td { display: flex; justify-content: space-between; align-items: center; padding: 0.75rem; border-bottom: 1px solid #eee; }
            td::before { content: attr(data-label); font-weight: 700; width: 40%; color: #A67C52; }
            .qr-img { width: 50px; height: 50px; }
        }
    </style>
</head>
<body>

<div class="animated-bg"></div>
<div class="floating-shape shape1"></div>
<div class="floating-shape shape2"></div>
<div class="floating-shape shape3"></div>
<div class="floating-shape shape4"></div>

<div class="navbar">
    <div class="container nav-flex">
        <div class="logo-area">
            <div class="logo-circle"><span>GS</span></div>
            <div class="logo-text"><h1>GS Assurance</h1><p>CONFIANCE & STABILITÉ</p></div>
        </div>
        <ul class="nav-links">
            <li><a href="index.php?action=home">Accueil</a></li>
            <li><a href="index.php?action=demande">Déclaration</a></li>
            <li><a href="index.php?action=historique">Historique</a></li>
            <li><a href="#" class="btn-nav-outline"><i class="fas fa-user"></i> Espace client</a></li>
        </ul>
    </div>
</div>

<div class="container">
    <div class="card">
        <h2><i class="fas fa-history"></i> Historique des déclarations</h2>
        <?php if (empty($constats)): ?>
            <p style="text-align:center; padding:2rem;">Aucune déclaration pour le moment.</p>
        <?php else: ?>
            <div style="overflow-x: auto;">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Client</th>
                            <th>Lieu</th>
                            <th>Statut</th>
                            <th>QR Code</th>
                            <th>Réponse</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($constats as $c): ?>
                        <tr>
                            <td data-label="ID">#<?= $c['id'] ?></td>
                            <td data-label="Client"><?= htmlspecialchars($c['prenom'] . ' ' . $c['nom']) ?></td>
                            <td data-label="Lieu"><?= htmlspecialchars($c['lieu_accident']) ?></td>
                            <td data-label="Statut"><span class="badge"><?= htmlspecialchars($c['statut']) ?></span></td>
                            <td data-label="QR Code">
                                <?php 
                                $url = "http://" . $_SERVER['HTTP_HOST'] . "/gs_assurance/index.php?action=voir_reponse&id=" . $c['id'];
                                $qr = "https://chart.googleapis.com/chart?chs=80x80&cht=qr&chl=" . urlencode($url);
                                ?>
                                <img src="<?= $qr ?>" class="qr-img" alt="QR Code">
                            </td>
                            <td data-label="Réponse">
                                <a href="index.php?action=voir_reponse&id=<?= $c['id'] ?>" class="btn-reponse">
                                    <i class="fas fa-eye"></i> Voir la réponse
                                </a>
                            </td>
                            <td data-label="Action">
                                <a href="index.php?action=reponse_form&id=<?= $c['id'] ?>" class="btn-traiter">
                                    <i class="fas fa-edit"></i> Traiter
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
        <div style="text-align: center;">
            <a href="index.php?action=home" class="btn-home"><i class="fas fa-home"></i> Retour à l'accueil</a>
        </div>
    </div>
</div>

<footer>
    <div class="container">
        <div class="footer-grid">
            <div class="footer-col">
                <div class="logo-area" style="margin-bottom: 0.8rem;">
                    <div class="logo-circle" style="background: white;"><span style="color:#6FAF4C;">GS</span></div>
                    <div class="logo-text"><h1 style="color:white;">GS Assurance</h1></div>
                </div>
                <p>Assurance auto nouvelle génération, alliée à des partenaires de confiance.</p>
            </div>
            <div class="footer-col">
                <h4>Liens utiles</h4>
                <p><a href="index.php?action=demande">Déclaration sinistre</a></p>
                <p><a href="index.php?action=historique">Historique & QR code</a></p>
            </div>
            <div class="footer-col">
                <h4>Contact</h4>
                <p><i class="fas fa-phone-alt"></i> +216 70 123 456</p>
                <p><i class="fas fa-envelope"></i> contact@gsassurance.tn</p>
                <div class="social">
                    <i class="fab fa-facebook"></i>
                    <i class="fab fa-instagram"></i>
                    <i class="fab fa-linkedin"></i>
                </div>
            </div>
        </div>
        <div class="copyright">
            © 2025 GS Assurance – Transformation numérique & services durables
        </div>
    </div>
</footer>
</body>
</html>