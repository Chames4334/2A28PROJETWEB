<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GS Assurance | Assurance Auto Nouvelle Génération</title>
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
        .container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 24px;
        }
        .nav-flex {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.8rem 0;
            flex-wrap: wrap;
        }
        .logo-area {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .logo-circle {
            background: #6FAF4C;
            width: 50px;
            height: 50px;
            border-radius: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 18px rgba(111,175,76,0.3);
        }
        .logo-circle span {
            font-size: 26px;
            font-weight: 800;
            color: white;
        }
        .logo-text h1 {
            font-size: 1.5rem;
            font-weight: 800;
            color: #A67C52;
        }
        .logo-text p {
            font-size: 0.7rem;
            color: #6FAF4C;
            font-weight: 700;
            letter-spacing: 1px;
        }
        .nav-links {
            display: flex;
            gap: 1.8rem;
            list-style: none;
            align-items: center;
        }
        .nav-links a {
            text-decoration: none;
            font-weight: 700;
            color: #4A3B2C;
            transition: 0.2s;
        }
        .nav-links a:hover {
            color: #6FAF4C;
        }
        .btn-nav {
            background: #6FAF4C;
            color: white !important;
            padding: 0.45rem 1.2rem;
            border-radius: 40px;
            font-weight: 700;
        }
        .btn-nav-outline {
            border: 1.5px solid #6FAF4C;
            color: #6FAF4C !important;
            padding: 0.45rem 1.2rem;
            border-radius: 40px;
            font-weight: 700;
        }
        .hero {
            display: flex;
            align-items: center;
            gap: 2.5rem;
            flex-wrap: wrap;
            background: rgba(242, 242, 242, 0.75);
            backdrop-filter: blur(8px);
            border-radius: 48px;
            padding: 2rem;
            margin: 1.5rem 0 2rem;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        }
        .hero-content { flex: 1; }
        .hero-badge {
            background: rgba(111,175,76,0.2);
            color: #5A3E2A;
            padding: 0.25rem 0.9rem;
            border-radius: 40px;
            font-size: 0.75rem;
            font-weight: 700;
            display: inline-block;
            margin-bottom: 1rem;
        }
        .slogan {
            font-size: 3rem;
            font-weight: 900;
            line-height: 1.2;
            color: #A67C52;
            margin-bottom: 0.5rem;
        }
        .slogan span {
            background: linear-gradient(120deg, #E8F5E9, #C8E6C9);
            background-clip: text;
            -webkit-background-clip: text;
            color: transparent;
        }
        .btn-group {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            margin-top: 1rem;
        }
        .btn-primary {
            background: #6FAF4C;
            color: white;
            padding: 0.7rem 1.6rem;
            border-radius: 40px;
            text-decoration: none;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: 0.2s;
        }
        .btn-primary:hover {
            background: #5A9A3A;
            transform: translateY(-2px);
        }
        .btn-secondary {
            border: 2px solid #6FAF4C;
            background: rgba(242,242,242,0.6);
            color: #6FAF4C;
            padding: 0.65rem 1.5rem;
            border-radius: 40px;
            text-decoration: none;
            font-weight: 800;
        }
        .btn-secondary:hover {
            background: #6FAF4C;
            color: white;
        }
        .hero-image {
            flex: 1;
            text-align: center;
            position: relative;
        }
        .hero-image img {
            max-width: 100%;
            border-radius: 32px;
            box-shadow: 0 20px 25px -12px rgba(0,0,0,0.2);
        }
        .alert {
            padding: 1rem;
            border-radius: 16px;
            margin-bottom: 1rem;
            background: white;
            border-left: 6px solid #6FAF4C;
        }
        .alert-success { border-left-color: #6FAF4C; background: #E8F5E9; }
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
            .slogan { font-size: 2rem; }
            .nav-flex { flex-direction: column; gap: 1rem; }
        }
    </style>
</head>
<body>

<div class="navbar">
    <div class="container nav-flex">
        <div class="logo-area">
            <div class="logo-circle"><span>GS</span></div>
            <div class="logo-text"><h1>GS Assurance</h1><p>CONFIANCE & STABILITÉ</p></div>
        </div>
        <ul class="nav-links">
            <li><a href="index.php?action=home">Accueil</a></li>
            <li><a href="index.php?action=demande">Déclaration</a></li>
            <li><a href="#">Assurance Auto</a></li>
            <li><a href="#" class="btn-nav-outline"><i class="fas fa-user"></i> Espace client</a></li>
            <li><a href="index.php?action=historique" class="btn-nav">Suivre un dossier</a></li>
        </ul>
    </div>
</div>

<div class="container">
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_SESSION['success']) ?></div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <div class="hero">
        <div class="hero-content">
            <div class="hero-badge"><i class="fas fa-shield-alt"></i> 100% digital & sans papier</div>
            <div class="slogan">
                GS Assurance : <span>roulez l'esprit tranquille</span>
            </div>
            <p>Devis instantané, déclaration par photo, et une IA vous guide vers l’option la plus avantageuse : indemnisation rapide ou réparation dans un garage de confiance.</p>
            <div class="btn-group">
                <a href="index.php?action=demande" class="btn-primary"><i class="fas fa-file-signature"></i> Déclarer un sinistre</a>
                <a href="index.php?action=historique" class="btn-secondary"><i class="fas fa-history"></i> Suivre mon dossier</a>
            </div>
        </div>
        <div class="hero-image">
            <img src="https://images.pexels.com/photos/116675/pexels-photo-116675.jpeg?auto=compress&cs=tinysrgb&w=600&h=400&fit=crop" alt="Voiture assurance">
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