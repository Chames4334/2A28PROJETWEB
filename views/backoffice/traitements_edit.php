<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion du traitement</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="page-shell">
        <header class="page-header">
            <div>
                <p class="breadcrumb">Espace</p>
                <h1>Gestion du traitement du congé</h1>
            </div>
            <div class="header-actions">
                <a class="button button-secondary" href="?action=adminIndex">Retour à la liste</a>
            </div>
        </header>

        <section class="form-card">
            <div class="info-card" style="background: #f0f7e8; padding: 16px; border-radius: 12px; margin-bottom: 24px;">
                <h3>Informations du congé</h3>
                <p><strong>Dates :</strong> <?php echo $conge['date_debut']; ?> → <?php echo $conge['date_fin']; ?></p>
                <p><strong>Type :</strong> <?php echo htmlspecialchars($conge['type_conge']); ?></p>
                <p><strong>Motif :</strong> <?php echo htmlspecialchars($conge['motif']); ?></p>
                <p><strong>Statut actuel :</strong> 
                    <span class="badge-soft" style="background: <?php 
                        echo $conge['statut'] === 'approuvé' ? '#4caf50' : ($conge['statut'] === 'refusé' ? '#f44336' : '#ff9800'); 
                    ?>20; color: <?php 
                        echo $conge['statut'] === 'approuvé' ? '#2e7d32' : ($conge['statut'] === 'refusé' ? '#c62828' : '#e65100'); 
                    ?>">
                        <?php echo htmlspecialchars($conge['statut']); ?>
                    </span>
                </p>
            </div>

            <?php if (!empty($errors)): ?>
                <div class="error-box">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="post" action="?action=editTraitement&id=<?php echo $conge['id_conge']; ?>">
                <label for="date_traitement">Date de traitement</label>
                <input type="date" id="date_traitement" name="date_traitement" 
                       value="<?php echo htmlspecialchars($_POST['date_traitement'] ?? ($conge['date_traitement'] ?? date('Y-m-d'))); ?>">

                <label for="decision">Décision</label>
                <select id="decision" name="decision">
                    <option value="en_attente" <?php echo (($_POST['decision'] ?? $conge['decision'] ?? '') === 'en_attente') ? 'selected' : ''; ?>>En attente</option>
                    <option value="approuvé" <?php echo (($_POST['decision'] ?? $conge['decision'] ?? '') === 'approuvé') ? 'selected' : ''; ?>>Approuvé</option>
                    <option value="refusé" <?php echo (($_POST['decision'] ?? $conge['decision'] ?? '') === 'refusé') ? 'selected' : ''; ?>>Refusé</option>
                </select>

                <label for="commentaire_traitement">Commentaire du traitement</label>
                <textarea id="commentaire_traitement" name="commentaire_traitement" rows="4"><?php echo htmlspecialchars($_POST['commentaire_traitement'] ?? $conge['commentaire_traitement'] ?? ''); ?></textarea>

                <div class="form-actions" style="display: flex; gap: 12px; margin-top: 20px;">
                    <button class="button button-primary" type="submit">Enregistrer le traitement</button>
                    <a class="button button-secondary" href="?action=adminIndex">Annuler</a>
                </div>
            </form>
        </section>
    </div>
</body>
</html>