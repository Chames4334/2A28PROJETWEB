<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Front Office - Gestion des Congés</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <!-- Animated Background -->
    <div class="animated-bg"></div>
    <div class="floating-shape shape1"></div>
    <div class="floating-shape shape2"></div>
    <div class="floating-shape shape3"></div>
    <div class="floating-shape shape4"></div>
    <div class="floating-shape shape5"></div>

    <!-- Header with Logo -->
    <div class="bord">
        <div class="logo_area">
            <img src="assets/images/logo.png" alt="GreenSecure Logo" height="100" width="150">
        </div>
        <div class="slogon">
            <h1>GreenSecure</h1>
            <p>Gestion des Congés</p>
        </div>
        <div class="links_area">
            <a href="?page=frontoffice">Accueil</a>
            <a href="?action=index">Mes Congés</a>
            <a href="?action=traitementIndex">Traitements</a>
        </div>
        <a href="?page=home" class="header-btn">Déconnexion</a>
    </div>

    <div class="app-shell">
        <header class="app-header">
            <div>
                <p class="breadcrumb">Transformation Numérique</p>
                <h2>Front Office</h2>
            </div>
            <div class="user-panel">
                <span>Bonjour Sophie !</span>
                <div class="avatar"></div>
            </div>
        </header>

        <section class="summary-row">
            <article class="summary-card primary-card">
                <h3>Mes Contrats</h3>
                <p>Consultez tous vos contrats en cours.</p>
            </article>
            <article class="summary-card">
                <h3>Demander un Devis</h3>
                <p>Obtenez une proposition d’assurance personnalisée.</p>
            </article>
            <article class="summary-card">
                <h3>Mes Congés</h3>
                <p>Gérez vos demandes de congés et suivez leur statut.</p>
                <a class="mini-link" href="?action=index">Voir mes congés</a>
                <a class="mini-link" href="?action=create">Nouvelle demande</a>
            </article>
            <article class="summary-card">                <h3>Suivi des Demandes</h3>
                <p>Consultez le traitement de vos demandes.</p>
                <a class="mini-link" href="?action=traitementIndex">Voir le traitement</a>
                <a class="mini-link" href="?action=traitementCreate">Ajouter un traitement</a>
            </article>
            <article class="summary-card">                <h3>Mes Réclamations</h3>
                <p>Suivez l’avancement de vos demandes de réclamation.</p>
            </article>
        </section>

        <section class="panel-grid">
            <div class="panel panel-light">
                <h3>Offres d&apos;Assurance</h3>
                <div class="offer-row">
                    <div class="offer-card">
                        <h4>Assurance Santé</h4>
                        <p>Des garanties complètes pour votre santé.</p>
                        <a href="#">Demander un Devis</a>
                    </div>
                    <div class="offer-card">
                        <h4>Assurance Auto</h4>
                        <p>Protection 24/7 pour votre véhicule.</p>
                        <a href="#">Demander un Devis</a>
                    </div>
                    <div class="offer-card">
                        <h4>Assurance Habitation</h4>
                        <p>Couverture complète pour votre domicile.</p>
                        <a href="#">Demander un Devis</a>
                    </div>
                </div>
            </div>

            <div class="panel panel-dark">
                <h3>Mes Congés</h3>
                <div class="stat-grid">
                    <div class="stat-block">
                        <span>Solde de Congés</span>
                        <strong>8 jours</strong>
                    </div>
                    <div class="stat-block">
                        <span>Congés en attente</span>
                        <strong>2</strong>
                    </div>
                    <div class="stat-block">
                        <span>Congé validé</span>
                        <strong>1</strong>
                    </div>
                </div>
                <div class="timeline-card">
                    <p><strong>Congé du 15/07/2021 au 20/07/2021</strong></p>
                    <p>Statut : En attente</p>
                </div>
            </div>
        </section>

        <section class="panel panel-light">
            <h3>Mes Réclamations</h3>
            <div class="claim-card">
                <div>
                    <strong>Service rapide et efficace !</strong>
                    <p>Votre retour a été traité avec soin.</p>
                </div>
                <div class="rating">★★★★★</div>
            </div>
        </section>

        <footer class="app-footer">
            <a class="footer-link" href="?page=backoffice">Passer au Back Office</a>
        </footer>
    </div>
</body>
</html>
