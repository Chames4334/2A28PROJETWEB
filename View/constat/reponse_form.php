<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GS Assurance | Traiter la déclaration</title>
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
        .info {
            background: white;
            border-radius: 24px;
            padding: 1.2rem;
            margin-bottom: 1.5rem;
        }
        .info p { margin: 0.5rem 0; }
        .form-group { margin-bottom: 1rem; }
        label { font-weight: 700; display: block; margin-bottom: 0.3rem; color: #4A3B2C; }
        input, select, textarea {
            width: 100%;
            padding: 12px;
            border-radius: 16px;
            border: 1px solid #ddd;
            font-family: 'Inter', sans-serif;
            transition: 0.2s;
        }
        input:focus, select:focus, textarea:focus {
            border-color: #6FAF4C;
            outline: none;
            box-shadow: 0 0 0 3px rgba(111,175,76,0.2);
        }
        .radio-group {
            background: white;
            padding: 1rem;
            border-radius: 24px;
            margin-bottom: 1rem;
        }
        .radio-group label {
            display: inline-block;
            margin-right: 1.5rem;
            font-weight: 600;
        }
        .radio-group input {
            width: auto;
            margin-right: 0.3rem;
        }
        .conditional-box {
            background: white;
            border-radius: 24px;
            padding: 1rem;
            margin: 1rem 0;
            display: none;
        }
        .btn-submit {
            background: #6FAF4C;
            color: white;
            border: none;
            padding: 14px 24px;
            border-radius: 40px;
            font-weight: 800;
            font-size: 1rem;
            cursor: pointer;
            width: 100%;
            transition: 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .btn-submit:hover {
            background: #5A9A3A;
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
            .card h2 { font-size: 1.4rem; }
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
            <div class="logo-text"><h1>GS Assurance</h1><p>ADMINISTRATION</p></div>
        </div>
        <ul class="nav-links">
            <li><a href="index.php?action=home">Accueil</a></li>
            <li><a href="index.php?action=historique">Historique</a></li>
            <li><a href="index.php?action=demande">Nouvelle déclaration</a></li>
        </ul>
    </div>
</div>

<div class="container">
    <div class="card">
        <h2><i class="fas fa-gavel"></i> Traiter la déclaration #<?= htmlspecialchars($constat['id']) ?></h2>
        <div class="info">
            <p><strong>Client :</strong> <?= htmlspecialchars($constat['prenom'] . ' ' . $constat['nom']) ?></p>
            <p><strong>Email :</strong> <?= htmlspecialchars($constat['email']) ?></p>
            <p><strong>Téléphone :</strong> <?= htmlspecialchars($constat['telephone']) ?></p>
            <p><strong>Lieu de l'accident :</strong> <?= htmlspecialchars($constat['lieu_accident']) ?></p>
            <p><strong>Date :</strong> <?= htmlspecialchars($constat['date_accident']) ?></p>
            <p><strong>Description :</strong> <?= nl2br(htmlspecialchars($constat['description'])) ?></p>
        </div>

        <form action="index.php?action=reponse_submit" method="POST">
            <input type="hidden" name="demande_id" value="<?= $constat['id'] ?>">

            <div class="radio-group">
                <label>Type de réponse :</label><br>
                <label><input type="radio" name="type_reponse" value="remboursement" required> 💰 Remboursement</label>
                <label><input type="radio" name="type_reponse" value="atelier" required> 🔧 Atelier partenaire</label>
            </div>

            <div id="remboursement_box" class="conditional-box">
                <div class="form-group">
                    <label>Montant (€) :</label>
                    <input type="number" name="montant" step="0.01" placeholder="Ex: 1250.00">
                </div>
            </div>

            <div id="atelier_box" class="conditional-box">
                <div class="form-group">
                    <label>Atelier partenaire :</label>
                    <select name="id_atelier">
                        <?php foreach ($ateliers as $a): ?>
                            <option value="<?= $a['id'] ?>"><?= htmlspecialchars($a['nom']) ?> - <?= htmlspecialchars($a['adresse']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label>Message complémentaire (optionnel) :</label>
                <textarea name="message_admin" rows="3" placeholder="Informations supplémentaires pour le client..."></textarea>
            </div>

            <button type="submit" class="btn-submit"><i class="fas fa-save"></i> Enregistrer la décision</button>
        </form>
    </div>
</div>

<script>
    const radios = document.querySelectorAll('input[name="type_reponse"]');
    const rembBox = document.getElementById('remboursement_box');
    const atelierBox = document.getElementById('atelier_box');

    function toggleBoxes() {
        const selected = document.querySelector('input[name="type_reponse"]:checked');
        if (selected) {
            rembBox.style.display = selected.value === 'remboursement' ? 'block' : 'none';
            atelierBox.style.display = selected.value === 'atelier' ? 'block' : 'none';
        } else {
            rembBox.style.display = 'none';
            atelierBox.style.display = 'none';
        }
    }

    radios.forEach(radio => radio.addEventListener('change', toggleBoxes));
    toggleBoxes();
</script>

<footer>
    <div class="container">
        <div class="footer-grid">
            <div class="footer-col">
                <div class="logo-area" style="margin-bottom: 0.8rem;">
                    <div class="logo-circle" style="background: white;"><span style="color:#6FAF4C;">GS</span></div>
                    <div class="logo-text"><h1 style="color:white;">GS Assurance</h1></div>
                </div>
                <p>Back-office – Gestion des sinistres</p>
            </div>
            <div class="footer-col">
                <h4>Liens rapides</h4>
                <p><a href="index.php?action=historique">Toutes les déclarations</a></p>
                <p><a href="index.php?action=demande">Nouvelle déclaration</a></p>
            </div>
        </div>
        <div class="copyright">© 2025 GS Assurance – Administration</div>
    </div>
</footer>
</body>
</html>