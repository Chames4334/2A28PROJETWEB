<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Traitement du congé</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="assets/js/validation.js"></script>
</head>
<body>
    <div class="page-shell">
        <header class="page-header">
            <div>
                <p class="breadcrumb">Espace</p>
                <h1>Traitement du congé</h1>
            </div>
            <div class="header-actions">
                <a class="button button-secondary" href="javascript:history.back()">Retour à la liste</a>
            </div>
        </header>

        <section class="form-card">
            <?php if (!empty($errors)): ?>
                <div class="error-box">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form id="form-traitement" method="post" action="?action=editTraitement&id=<?php echo $conge['id_conge']; ?>">
                <label for="date_traitement">Date de traitement</label>
                <input type="date" id="date_traitement" name="date_traitement" value="<?php echo htmlspecialchars($_POST['date_traitement'] ?? $conge['date_traitement'] ?? date('Y-m-d')); ?>">

                <label for="decision">Décision</label>
                <?php if (isset($soldeInsuffisant) && $soldeInsuffisant): ?>
                    <div class="error-box" style="margin-bottom: 10px; padding: 10px; border-left: 4px solid #c33; background: #fee;">
                        <strong>Action requise :</strong> Le solde est insuffisant pour ce congé (<?php echo $joursDemandes; ?> jours demandés, solde actuel : <?php echo max(0, $employeInfo['solde_restant']); ?>). Le système refuse automatiquement cette demande.
                    </div>
                    <select id="decision" name="decision" disabled style="background-color: #eee; color: #888; cursor: not-allowed;">
                        <option value="refusé" selected>Refusé (Solde insuffisant)</option>
                    </select>
                    <input type="hidden" name="decision" value="refusé">
                <?php else: ?>
                    <select id="decision" name="decision">
                        <option value="en_attente" <?php echo (($conge['decision'] ?? '') == 'en_attente') ? 'selected' : ''; ?>>En attente</option>
                        <option value="approuvé" <?php echo (($conge['decision'] ?? '') == 'approuvé') ? 'selected' : ''; ?>>Approuvé</option>
                        <option value="refusé" <?php echo (($conge['decision'] ?? '') == 'refusé') ? 'selected' : ''; ?>>Refusé</option>
                    </select>
                <?php endif; ?>

                <label for="commentaire_traitement">Commentaire</label>
                <textarea id="commentaire_traitement" name="commentaire_traitement"><?php echo htmlspecialchars($_POST['commentaire_traitement'] ?? $conge['commentaire_traitement'] ?? ''); ?></textarea>

                <button class="button button-primary" type="submit">Enregistrer le traitement</button>
            </form>
        </section>
    </div>
</body>
</html>
