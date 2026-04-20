<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Front Office - Nouveau traitement</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="assets/js/validation.js"></script>
</head>
<body>
    <div class="page-shell">
        <header class="page-header">
            <div class="logo_area">
                <img src="assets/images/logo.png" alt="GreenSecure Logo" height="50" width="75">
            </div>
            <div>
                <p class="breadcrumb">Front Office</p>
                <h1>Nouveau traitement de congé</h1>
            </div>
            <div class="header-actions">
                <a class="button button-secondary" href="?action=traitementIndex">Retour à la liste</a>
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

            <form id="form-traitement" method="post" action="?action=traitementCreate" onsubmit="return validateTraitementForm();">
                <label for="id_conge">Congé associé</label>
                <select id="id_conge" name="id_conge">
                    <option value="">-- Sélectionner un congé --</option>
                    <?php foreach ($conges as $conge): ?>
                        <option value="<?php echo $conge['id_conge']; ?>" <?php echo (isset($_POST['id_conge']) && $_POST['id_conge'] == $conge['id_conge']) ? 'selected' : ''; ?>>
                            Congé #<?php echo $conge['id_conge']; ?> - <?php echo $conge['date_debut']; ?> à <?php echo $conge['date_fin']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="date_traitement">Date de traitement (AAAA-MM-JJ)</label>
                <input type="text" id="date_traitement" name="date_traitement" value="<?php echo htmlspecialchars($_POST['date_traitement'] ?? date('Y-m-d')); ?>">

                <label for="decision">Décision</label>
                <select id="decision" name="decision">
                    <option value="">-- Sélectionner --</option>
                    <option value="en_attente" <?php echo (isset($_POST['decision']) && $_POST['decision'] === 'en_attente') ? 'selected' : ''; ?>>En attente</option>
                    <option value="approuvé" <?php echo (isset($_POST['decision']) && $_POST['decision'] === 'approuvé') ? 'selected' : ''; ?>>Approuvé</option>
                    <option value="refusé" <?php echo (isset($_POST['decision']) && $_POST['decision'] === 'refusé') ? 'selected' : ''; ?>>Refusé</option>
                </select>

                <label for="commentaire">Commentaire</label>
                <textarea id="commentaire" name="commentaire"><?php echo htmlspecialchars($_POST['commentaire'] ?? ''); ?></textarea>

                <button class="button button-primary" type="submit">Envoyer</button>
            </form>
        </section>
    </div>
</body>
</html>
