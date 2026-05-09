<?php 
    include "C:/xampp/htdocs/GreenSecure/Controller/ControlTypes.php";

    $cntrlF=new ControlTypes();
    $Finance=$cntrlF->listeType('');
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Page Financiére</title>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
        <link rel="stylesheet" href="./assets/css/style.css">
        <style>
            /* Styles spécifiques pour la page d'accueil */
            .hero {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 50px;
                padding: 60px 0;
                flex-wrap: wrap;
            }
            .hero-content {
                flex: 1;
            }
            .hero-content h1 {
                font-size: 3rem;
                color: olivedrab;
                margin-bottom: 20px;
            }
            .hero-content h2 {
                color: #A67C52;
                margin-bottom: 20px;
                font-size: 1.5rem;
            }
            .hero-content p {
                font-size: 1.2rem;
                color: #555;
                line-height: 1.6;
                margin-bottom: 30px;
            }
            .hero-image {
                flex: 1;
                text-align: center;
            }
            .hero-image img {
                max-width: 100%;
                border-radius: 30px;
                box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            }
            .btn-hero {
                background: olivedrab;
                color: white;
                padding: 15px 35px;
                border-radius: 50px;
                text-decoration: none;
                font-weight: 600;
                display: inline-block;
                transition: all 0.3s;
            }
            .btn-hero:hover {
                background: #5a7a26;
                transform: translateY(-3px);
            }
            .services {
                display: grid;
                grid-template-columns: repeat(4, 1fr);
                gap: 30px;
                margin: 80px 0;
            }
            .service-card {
                background: rgba(255,255,255,0.9);
                backdrop-filter: blur(10px);
                padding: 30px;
                border-radius: 20px;
                text-align: center;
                transition: transform 0.3s;
            }
            .service-card:hover {
                transform: translateY(-10px);
            }
            .service-card i {
                font-size: 3rem;
                color: olivedrab;
                margin-bottom: 20px;
            }
            .service-card h3 {
                font-size: 1.5rem;
                margin-bottom: 15px;
            }
            .service-card p {
                color: #666;
            }
            .about-section {
                background: rgba(107, 142, 35, 0.1);
                border-radius: 30px;
                padding: 50px;
                margin: 60px 0;
                text-align: center;
            }
            .about-section h2 {
                font-size: 2rem;
                color: olivedrab;
                margin-bottom: 20px;
            }
            .about-section p {
                font-size: 1.1rem;
                line-height: 1.8;
                max-width: 800px;
                margin: 0 auto;
            }
            .stats-section {
                display: grid;
                grid-template-columns: repeat(4, 1fr);
                gap: 30px;
                margin: 60px 0;
                text-align: center;
            }
            .stat-item {
                background: rgba(255,255,255,0.9);
                padding: 30px;
                border-radius: 20px;
            }
            .stat-number {
                font-size: 2.5rem;
                font-weight: 800;
                color: olivedrab;
            }
            .stat-label {
                color: #666;
                margin-top: 10px;
            }
            .cta-section {
                background: linear-gradient(135deg, olivedrab, #5a7a26);
                border-radius: 30px;
                padding: 60px;
                text-align: center;
                color: white;
                margin: 60px 0;
            }
            .cta-section h2 {
                font-size: 2rem;
                margin-bottom: 20px;
            }
            .cta-section .btn-cta {
                background: white;
                color: olivedrab;
                padding: 15px 40px;
                border-radius: 50px;
                text-decoration: none;
                font-weight: 700;
                display: inline-block;
                margin-top: 20px;
                transition: all 0.3s;
            }
            .cta-section .btn-cta:hover {
                transform: scale(1.05);
            }
            /* Style pour le bouton GreenBot */
            .btn-greenbot {
                background: #6c757d;
                color: white;
                padding: 8px 20px;
                border-radius: 25px;
                text-decoration: none;
                transition: all 0.3s;
                display: inline-flex;
                align-items: center;
                gap: 8px;
            }
            .btn-greenbot:hover {
                background: #5a6268;
                transform: translateY(-2px);
            }
            @media (max-width: 1024px) {
                .services {
                    grid-template-columns: repeat(2, 1fr);
                }
            }
            @media (max-width: 768px) {
                .hero {
                    flex-direction: column;
                    text-align: center;
                }
                .services {
                    grid-template-columns: 1fr;
                }
                .stats-section {
                    grid-template-columns: repeat(2, 1fr);
                }
            }
        </style>
    </head>
    <body>
        <div class="animated-bg"></div>

        <div class="floating-shape shape1"></div>
        <div class="floating-shape shape2"></div>
        <div class="floating-shape shape3"></div>
        <div class="floating-shape shape4"></div>
        <div class="floating-shape shape5"></div>
        <div class="bord">
            <div class="bord-left">
                <div class="logo_area">
                    <img src="../images/logo.png" alt="logo" height="50" width="65">
                </div>
                <div class="slogon">
                    <h1>GreenSecure</h1>
                    <small>Assurance verte, avenir serein</small>
                </div>
            </div>
            <div class="bord-right">
                <a href="accueil.php"><i class="fas fa-home"></i> Accueil</a>
                <a href="#assurances"><i class="fas fa-shield-alt"></i> Nos assurances</a>
                <!-- NOUVEAU BOUTON GREENBOT - CHATBOT -->
                <a href="chatbot.php" class="btn-greenbot"><i class="fas fa-robot"></i> 💬 GreenBot</a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="../backoffice/liste.php"><i class="fas fa-users"></i> Administration</a>
                    <a href="profil.php?id=<?= $_SESSION['user_id'] ?>"><i class="fas fa-user"></i> Mon Profil</a>
                    <a href="../auth/logout.php" class="btn-logout"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
                <?php else: ?>
                    <a href="../auth/login.php" class="btn-nav-primary"><i class="fas fa-sign-in-alt"></i> Connexion</a>
                    <a href="../auth/register.php"><i class="fas fa-user-plus"></i> Inscription</a>
                <?php endif; ?>
            </div>
        </div>
        <div class="page-wrapper">

            <!-- Section Héro -->
            <div class="hero">
                <div class="hero-content">
                    <h1>🌿 Green Assurance</h1>
                    <h2>Protégez ce qui compte le plus pour vous</h2>
                    <p>Santé, Accident, Auto, Habitation — Green Assurance vous offre une protection complète et responsable. 
                    Des garanties adaptées à vos besoins, avec un engagement fort pour l'environnement.</p>
                    <a href="../auth/register.php" class="btn-hero"><i class="fas fa-leaf"></i> Obtenir un devis gratuit</a>
                </div>
                <div class="hero-image">
                    <img src="https://images.unsplash.com/photo-1556745753-b2904692b3cd?w=500&h=400&fit=crop" alt="Assurance santé">
                </div>
            </div>
            <!-- Section Nos assurances -->
            <div class="choix" id="assurances">
                <h1>Nos assurances</h1>
                <?php if (!empty($Finance)) { ?>
                    <?php foreach($Finance as $f){  ?>
                        <a class="card" href="http://localhost/GreenSecure/View/FrontOffice/InscriptionPage.php?TypeID=<?= urlencode($f['Titre']) ?>">
                            <img src="../Backoffice/images/<?= $f['Image'] ?>" alt="image">
                            <h2><?= htmlspecialchars($f['Titre']) ?></h2>
                            <p><?= htmlspecialchars($f['Description']) ?></p>
                        </a>
                    <?php } ?>
                <?php } else {?>
                    <p>Aucun type d'assurance disponible.</p>
                <?php } ?>
            </div>
            <!-- Section À propos -->
            <div class="about-section">
                <h2><i class="fas fa-leaf"></i> Pourquoi choisir Green Assurance ?</h2>
                <p>Fondée en 2020, Green Assurance est la première compagnie d'assurance 100% engagée pour l'environnement. 
                Nous reversons 5% de nos bénéfices à des associations de protection de la nature. 
                Nos contrats sont sans papier, nos investissements sont verts, et nos équipes sont formées aux enjeux écologiques.</p>
                <br>
                <p><strong>🌍 15 000+ clients satisfaits</strong> | <strong>🤝 98% de recommandation</strong> | <strong>⭐ 4.8/5 sur Google</strong></p>
            </div>

            <!-- Section Chiffres -->
            <div class="stats-section">
                <div class="stat-item">
                    <div class="stat-number">15 000+</div>
                    <div class="stat-label">Clients satisfaits</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">98%</div>
                    <div class="stat-label">Taux de recommandation</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">24/7</div>
                    <div class="stat-label">Assistance disponible</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">5%</div>
                    <div class="stat-label">Reversés à la planète</div>
                </div>
            </div>
            <!-- Section Appel à l'action -->
            <div class="cta-section">
                <h2>Prêt à rejoindre l'aventure Green Assurance ?</h2>
                <p>Obtenez un devis gratuit en 5 minutes et profitez de -20% sur votre première année.</p>
                <a href="../auth/register.php" class="btn-cta"><i class="fas fa-leaf"></i> Je rejoins Green Assurance</a>
            </div>
        </div>
        <footer class="footer">
            <div style="display: flex; justify-content: space-between; flex-wrap: wrap; gap: 30px; max-width: 1200px; margin: 0 auto; padding: 20px;">
                <div>
                    <h4 style="color: olivedrab;">🌿 Green Assurance</h4>
                    <p>Assurance responsable<br>pour votre avenir</p>
                </div>
                <div>
                    <h4>Contact</h4>
                    <p><i class="fas fa-phone"></i> +216 70 123 456</p>
                    <p><i class="fas fa-envelope"></i> contact@greenassurance.com</p>
                </div>
                <div>
                    <h4>Suivez-nous</h4>
                    <p><i class="fab fa-facebook"></i> Facebook | <i class="fab fa-linkedin"></i> LinkedIn</p>
                </div>
            </div>
            <p style="text-align: center; margin-top: 20px;">&copy; 2024 Green Assurance - Tous droits réservés</p>
        </footer>
        <script src="./assets/js/validation.js"></script>
    </body>
</html>