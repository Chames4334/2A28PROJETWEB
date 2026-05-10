<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Gestion des Congés</title>
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
            <p>Panel Administrateur</p>
        </div>
        <div class="links_area">
            <a href="?page=backoffice">Tableau de bord</a>
            <a href="?action=adminIndex">Congés</a>
            <a href="?action=calendarAdmin">Calendrier</a>
        </div>
        <a href="?page=home" class="header-btn">Déconnexion</a>
    </div>

    <div class="app-shell">
        <header class="app-header admin-header">
            <div>
                <p class="breadcrumb">Panel Administrateur</p>
                <h2>Espace</h2>
            </div>
            <div class="admin-stats-overview">
                <div>
                    <span>Employés Actifs</span>
                    <strong>42</strong>
                </div>
                <div>
                    <span>Demandes d&apos;absence</span>
                    <strong>18</strong>
                </div>
                <div>
                    <span>Congés en Attente</span>
                    <strong>3</strong>
                </div>
                <div>
                    <span>Taux de Présence</span>
                    <strong>94%</strong>
                </div>
            </div>
        </header>

        <section class="panel-grid admin-grid">
            <div class="panel panel-light" style="border: 2px solid #6FAF4C; background: linear-gradient(145deg, #ffffff, #f0f7f0);">
                <h3 style="color: #2d462f; display: flex; align-items: center; gap: 10px;">
                    <span>✨</span> Assistant de Planification IA
                </h3>
                <p>Générez instantanément un rapport intelligent sur l'état du planning mensuel. L'algorithme détecte automatiquement les risques de sous-effectif et les périodes critiques.</p>
                <div style="margin-top: 20px;">
                    <a href="?action=ai_report_view" target="_blank" class="button button-primary" style="display: inline-block; text-decoration: none;">Générer le rapport mensuel</a>
                </div>
            </div>

            <div class="panel panel-light">
                <h3>Demandes de congés</h3>
                <p>Suivez et gérez les congés du personnel en attente d'approbation.</p>
                <div style="margin-top: 20px; display: flex; gap: 10px;">
                    <a class="button button-secondary" style="text-decoration: none;" href="?action=adminIndex">Ouvrir la liste complète</a>
                </div>
            </div>

            <div class="panel panel-light">
                <h3>Gestion du Calendrier</h3>
                <p>Visualisez la disponibilité globale de l'équipe sur le mois.</p>
                <div style="margin-top: 20px; display: flex; gap: 10px;">
                    <a class="button button-secondary" style="text-decoration: none;" href="?action=calendarAdmin">Voir le calendrier</a>
                </div>
            </div>
        </section>

        <section class="panel-grid admin-grid">
            <div class="panel panel-dark">
                <h3>Dernières Demandes en Attente</h3>
                <div class="request-card">
                    <div>
                        <strong>Paul Martin</strong>
                        <p>Congé payé du 05/08/2025 au 10/08/2025</p>
                    </div>
                    <div class="request-actions">
                        <button style="background: #6FAF4C; border: none; color: white; padding: 5px 10px; border-radius: 4px; cursor: pointer;">Approuver</button>
                        <button style="background: transparent; border: 1px solid white; color: white; padding: 5px 10px; border-radius: 4px; cursor: pointer;">Refuser</button>
                    </div>
                </div>
                <div class="request-card" style="margin-top: 10px;">
                    <div>
                        <strong>Marie Dupont</strong>
                        <p>Congé sans solde du 12/08/2025 au 14/08/2025</p>
                    </div>
                    <div class="request-actions">
                        <button style="background: #6FAF4C; border: none; color: white; padding: 5px 10px; border-radius: 4px; cursor: pointer;">Approuver</button>
                        <button style="background: transparent; border: 1px solid white; color: white; padding: 5px 10px; border-radius: 4px; cursor: pointer;">Refuser</button>
                    </div>
                </div>
            </div>

            <div class="panel panel-dark">
                <h3>Alertes Système</h3>
                <div class="request-card" style="border-left: 4px solid #f2994a;">
                    <div>
                        <strong>Attention : Période estivale</strong>
                        <p>Plusieurs employés ont posé des congés en août. Pensez à vérifier le rapport IA pour éviter le sous-effectif.</p>
                    </div>
                </div>
            </div>
        </section>

        <footer class="app-footer">
            <a class="footer-link" href="?page=frontoffice">Retour à l'autre espace</a>
        </footer>
    </div>
</body>
</html>
