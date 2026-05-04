<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>GS Assurance — Accueil</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="gs-page">
    <header class="gs-header">
        <div class="gs-header-inner">
            <a href="index.php?action=accueil" class="gs-logo">GS Assurance</a>
            <nav class="gs-nav">
                <a href="index.php?action=accueil" class="is-active">Accueil</a>
                <a href="index.php?action=declaration">Déclaration</a>
                <a href="index.php?action=historique">Historique</a>
            </nav>
            <a href="index.php?action=historique" class="gs-btn-client">Espace client</a>
        </div>
    </header>

    <main class="gs-main">
        <div class="container gs-container gs-hero">
            <h1 class="gs-title">Bienvenue chez GS Assurance</h1>
            <p class="gs-lead">Déclarez un sinistre ou consultez l’historique de vos dossiers.</p>
            <div class="action-buttons" style="justify-content: center;">
                <a href="index.php?action=declaration" class="btn btn-success">Nouvelle déclaration</a>
                <a href="index.php?action=historique" class="btn btn-info">Historique &amp; QR code</a>
            </div>
        </div>
    </main>

    <footer class="gs-footer">
        <div class="gs-footer-inner">
            <div>
                <strong>GS Assurance</strong>
                <p class="gs-foot-note">Assurance auto nouvelle génération.</p>
            </div>
            <div>
                <strong>Liens utiles</strong>
                <p><a href="index.php?action=declaration">Déclaration sinistre</a></p>
                <p><a href="index.php?action=historique">Historique &amp; QR code</a></p>
            </div>
            <div>
                <strong>Contact</strong>
                <p><a href="mailto:contact@gsassurance.tn">contact@gsassurance.tn</a></p>
            </div>
        </div>
        <p class="gs-copy">© <?php echo date('Y'); ?> GS Assurance</p>
    </footer>
</body>
</html>
