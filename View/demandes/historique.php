<?php
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$script = $_SERVER['SCRIPT_NAME'] ?? '/index.php';
$basePath = rtrim(str_replace('\\', '/', dirname($script)), '/');
$publicRoot = $scheme . '://' . $host . ($basePath === '' ? '' : $basePath);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>GS Assurance — Historique des déclarations</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="gs-page">
    <header class="gs-header">
        <div class="gs-header-inner">
            <a href="index.php?action=accueil" class="gs-logo">GS Assurance</a>
            <nav class="gs-nav">
                <a href="index.php?action=accueil">Accueil</a>
                <a href="index.php?action=declaration">Déclaration</a>
                <a href="index.php?action=historique" class="is-active">Historique</a>
            </nav>
            <a href="index.php?action=historique" class="gs-btn-client">Espace client</a>
        </div>
    </header>

    <main class="gs-main">
        <div class="container gs-container">
            <h1 class="gs-title">Historique des déclarations</h1>

            <table class="data-table gs-table">
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
                    <?php foreach ($demandes as $demande): ?>
                        <?php
                        $did = (int) $demande['id'];
                        $ficheUrl = $publicRoot . '/index.php?action=show_demande&id=' . $did . '&return=historique';
                        $qrSrc = 'https://api.qrserver.com/v1/create-qr-code/?size=72x72&data=' . rawurlencode($ficheUrl);
                        ?>
                        <tr>
                            <td>#<?php echo $did; ?></td>
                            <td><?php echo htmlspecialchars(trim(($demande['nom'] ?? '') . ' ' . ($demande['prenom'] ?? ''))); ?></td>
                            <td><?php echo htmlspecialchars($demande['lieu_accident'] ?? ''); ?></td>
                            <td><span class="status status-<?php echo htmlspecialchars($demande['statut'] ?? ''); ?>"><?php echo htmlspecialchars($demande['statut'] ?? ''); ?></span></td>
                            <td class="gs-qr">
                                <img src="<?php echo htmlspecialchars($qrSrc); ?>" width="72" height="72" alt="QR Code déclaration #<?php echo $did; ?>">
                            </td>
                            <td>
                                <a href="index.php?action=traiter_demande&id=<?php echo $did; ?>" class="btn-reponse">Voir la réponse</a>
                            </td>
                            <td class="action-cell">
                                <a href="index.php?action=edit_demande&id=<?php echo $did; ?>&return=historique" class="btn-edit">Modifier</a>
                                <a href="index.php?action=delete_demande&id=<?php echo $did; ?>&return=historique" class="btn-delete" onclick="return confirm('Supprimer cette déclaration ?');">Supprimer</a>
                                <a href="index.php?action=traiter_demande&id=<?php echo $did; ?>" class="btn-traiter">Traiter</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <?php if (empty($demandes)): ?>
                <p class="gs-empty">Aucune déclaration enregistrée.</p>
            <?php endif; ?>

            <p class="gs-back-wrap">
                <a href="index.php?action=accueil" class="btn btn-traiter gs-btn-home">Retour à l'accueil</a>
            </p>
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
                <p>Tél. +216 XX XXX XXX</p>
                <p><a href="mailto:contact@gsassurance.tn">contact@gsassurance.tn</a></p>
            </div>
        </div>
        <p class="gs-copy">© <?php echo date('Y'); ?> GS Assurance</p>
    </footer>
</body>
</html>
