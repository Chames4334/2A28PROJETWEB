<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Nouvelle demande</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="assets/js/validation.js"></script>
</head>
<body>
    <div class="page-shell">
        <header class="page-header">
            <div>
                <p class="breadcrumb">Espace</p>
                <h1>Nouvelle demande de congé</h1>
            </div>
            <div class="header-actions">
                <a class="button button-secondary" href="?action=index">Retour à la liste</a>
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

            <form id="form-conge" method="post" action="?action=create" onsubmit="return validateCongeForm();">
                <label for="date_debut">Date de début</label>
                <input type="date" id="date_debut" name="date_debut" value="<?php echo htmlspecialchars($_POST['date_debut'] ?? ''); ?>">

                <label for="date_fin">Date de fin</label>
                <input type="date" id="date_fin" name="date_fin" value="<?php echo htmlspecialchars($_POST['date_fin'] ?? ''); ?>">

                <label for="type_conge">Type de congé</label>
                <select id="type_conge" name="type_conge">
                    <option value="">-- Sélectionner --</option>
                    <option value="Congé payé">Congé payé</option>
                    <option value="Congé maladie">Congé maladie</option>
                    <option value="Congé sans solde">Congé sans solde</option>
                </select>

                <label for="motif">Motif</label>
                <textarea id="motif" name="motif"><?php echo htmlspecialchars($_POST['motif'] ?? ''); ?></textarea>

                <input type="hidden" name="statut" value="en_attente">

                <button class="button button-primary" type="submit">Envoyer</button>
            </form>
        </section>
    </div>
</body>
</html>