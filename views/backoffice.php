<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Back Office - Gestion des Congés</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="app-shell">
        <header class="app-header admin-header">
            <div>
                <p class="breadcrumb">Panel Administrateur</p>
                <h2>Back Office</h2>
            </div>
            <div class="admin-stats-overview">
                <div>
                    <span>Utilisateurs</span>
                    <strong>2 543</strong>
                </div>
                <div>
                    <span>Demandes d&apos;absence</span>
                    <strong>187</strong>
                </div>
                <div>
                    <span>Congés en Attente</span>
                    <strong>15</strong>
                </div>
                <div>
                    <span>Avis 4.2 ⭐</span>
                </div>
            </div>
        </header>

        <section class="panel-grid admin-grid">
            <div class="panel panel-light">
                <h3>Gestion des Utilisateurs</h3>
                <div class="admin-actions">
                    <button>Ajouter Utilisateur</button>
                    <button>Modifier</button>
                    <button>Supprimer</button>
                </div>
                <div class="action-row">
                    <span>Paul Martin</span>
                    <button>Approuver</button>
                    <button>Refuser</button>
                </div>
            </div>
            <div class="panel panel-light">
                <h3>Demandes de congés</h3>
                <p>Suivez et gérez les congés du personnel.</p>
                <a class="mini-link" href="?action=adminIndex">Ouvrir la liste</a>
                <a class="mini-link" href="?action=adminIndex">Gérer les congés</a>
            </div>

            <div class="panel panel-light">
                <h3>Gestion des Offres</h3>
                <div class="offer-list admin-list">
                    <div>
                        <span>Assurance Santé</span>
                        <button>Éditer</button>
                    </div>
                    <div>
                        <span>Assurance Auto</span>
                        <button>Éditer</button>
                    </div>
                    <div>
                        <span>Assurance Habitation</span>
                        <button>Éditer</button>
                    </div>
                </div>
            </div>
        </section>

        <section class="panel-grid admin-grid">
            <div class="panel panel-dark">
                <h3>Demandes de Congés</h3>
                <div class="request-card">
                    <div>
                        <strong>Paul Martin</strong>
                        <p>Congé du 05/08/2021 au 10/08/2021</p>
                    </div>
                    <div class="request-actions">
                        <button>Approuver</button>
                        <button>Refuser</button>
                    </div>
                </div>
            </div>

            <div class="panel panel-dark">
                <h3>Demandes d&apos;Offres</h3>
                <div class="request-card">
                    <div>
                        <strong>Un pondé de plus</strong>
                        <p>Très satisfait du service !</p>
                    </div>
                    <div class="request-actions">
                        <button>Répondre</button>
                    </div>
                </div>
            </div>
        </section>

        <section class="panel panel-light">
            <h3>Commentaires Clients</h3>
            <div class="comment-list">
                <div>
                    <strong>Très satisfait du service !</strong>
                    <span>Note : 5/5</span>
                </div>
                <div class="comment-pagination">
                    <button>1</button>
                    <button>2</button>
                    <button>3</button>
                </div>
            </div>
        </section>

        <footer class="app-footer">
            <a class="footer-link" href="?page=frontoffice">Retour au Front Office</a>
        </footer>
    </div>
</body>
</html>
