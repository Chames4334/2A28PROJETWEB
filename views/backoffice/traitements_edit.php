<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Back Office - Modifier traitement</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="assets/js/validation.js"></script>
</head>
<body>
    <div class="page-shell">
        <header class="page-header">
            <div>
                <p class="breadcrumb">Back Office</p>
                <h1>Modifier le traitement de congé</h1>
            </div>
            <div class="header-actions">
                <a class="button button-secondary" href="?action=traitementAdminIndex">Retour à la liste</a>
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

            <form id="form-traitement" method="post" action="?action=traitementEdit&id=<?php echo $traitement['id_traitement']; ?>" onsubmit="return validateTraitementForm();">
                <label for="id_conge">Congé associé</label>
                <select id="id_conge" name="id_conge">
                    <option value="">-- Sélectionner un congé --</option>
                    <?php foreach ($conges as $conge): ?>
                        <option value="<?php echo $conge['id_conge']; ?>" <?php echo (($_POST['id_conge'] ?? $traitement['id_conge']) == $conge['id_conge']) ? 'selected' : ''; ?>>
                            Congé #<?php echo $conge['id_conge']; ?> - <?php echo $conge['date_debut']; ?> à <?php echo $conge['date_fin']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="date_traitement">Date de traitement (AAAA-MM-JJ)</label>
                <input type="text" id="date_traitement" name="date_traitement" value="<?php echo htmlspecialchars($_POST['date_traitement'] ?? $traitement['date_traitement']); ?>">

                <label for="decision">Décision</label>
                <select id="decision" name="decision">
                    <option value="">-- Sélectionner --</option>
                    <option value="en_attente" <?php echo (($_POST['decision'] ?? $traitement['decision']) === 'en_attente' ? 'selected' : ''); ?>>En attente</option>
                    <option value="approuvé" <?php echo (($_POST['decision'] ?? $traitement['decision']) === 'approuvé' ? 'selected' : ''); ?>>Approuvé</option>
                    <option value="refusé" <?php echo (($_POST['decision'] ?? $traitement['decision']) === 'refusé' ? 'selected' : ''); ?>>Refusé</option>
                </select>

                <label for="commentaire">Commentaire</label>
                <textarea id="commentaire" name="commentaire"><?php echo htmlspecialchars($_POST['commentaire'] ?? $traitement['commentaire']); ?></textarea>

                <button class="button button-primary" type="submit">Enregistrer</button>
            </form>
        </section>
    </div>
</body>
</html>
