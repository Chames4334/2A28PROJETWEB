<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Back Office - Modifier congé</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="assets/js/validation.js"></script>
</head>
<body>
    <div class="page-shell">
        <header class="page-header">
            <div>
                <p class="breadcrumb">Back Office</p>
                <h1>Modifier la demande de congé</h1>
            </div>
            <div class="header-actions">
                <a class="button button-secondary" href="?action=adminIndex">Retour à la liste</a>
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

            <form id="form-conge" method="post" action="?action=edit&id=<?php echo $conge['id_conge']; ?>" onsubmit="return validateCongeForm();">
                <label for="date_debut">Date de début (AAAA-MM-JJ)</label>
                <input type="text" id="date_debut" name="date_debut" value="<?php echo htmlspecialchars($_POST['date_debut'] ?? $conge['date_debut']); ?>">

                <label for="date_fin">Date de fin (AAAA-MM-JJ)</label>
                <input type="text" id="date_fin" name="date_fin" value="<?php echo htmlspecialchars($_POST['date_fin'] ?? $conge['date_fin']); ?>">

                <label for="type_conge">Type de congé</label>
                <input type="text" id="type_conge" name="type_conge" value="<?php echo htmlspecialchars($_POST['type_conge'] ?? $conge['type_conge']); ?>">

                <label for="motif">Motif</label>
                <textarea id="motif" name="motif"><?php echo htmlspecialchars($_POST['motif'] ?? $conge['motif']); ?></textarea>

                <label for="statut">Statut</label>
                <select id="statut" name="statut">
                    <option value="en_attente" <?php echo ($conge['statut'] === 'en_attente') ? 'selected' : ''; ?>>En attente</option>
                    <option value="approuvé" <?php echo ($conge['statut'] === 'approuvé') ? 'selected' : ''; ?>>Approuvé</option>
                    <option value="refusé" <?php echo ($conge['statut'] === 'refusé') ? 'selected' : ''; ?>>Refusé</option>
                </select>

                <button class="button button-primary" type="submit">Enregistrer</button>
            </form>
        </section>
    </div>
</body>
</html>
