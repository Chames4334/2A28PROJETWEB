<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Nouvelle réponse</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="container">
    <h1>➕ Nouvelle Réponse</h1>
    <?php if (isset($error)): ?>
        <div class="alert error" style="background:#f8d7da;color:#721c24;padding:10px;border-radius:5px;margin-bottom:20px;"><?= $error ?></div>
    <?php endif; ?>
    <form method="POST" class="form-container">
        <div class="form-group"><label>Demande :</label>
            <select name="demande_id" required>
                <option value="">-- Sélectionnez --</option>
                <?php foreach ($demandes as $d): ?>
                <option value="<?= $d['id'] ?>">#<?= $d['id'] ?> - <?= htmlspecialchars($d['nom'].' '.$d['prenom']) ?> (<?= $d['date_accident'] ?>)</option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group"><label>Type de réponse :</label>
            <select name="type_reponse_id" required>
                <option value="">-- Choisir --</option>
                <?php foreach ($types as $t): ?>
                <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['nom']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group"><label>Montant (€) :</label><input type="number" step="0.01" name="montant"></div>
        <div class="form-group"><label>Atelier :</label>
            <select name="id_atelier">
                <option value="">-- Aucun --</option>
                <?php foreach ($ateliers as $a): ?>
                <option value="<?= $a['id'] ?>"><?= htmlspecialchars($a['nom']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group"><label>Message admin :</label><textarea name="message_admin" rows="4"></textarea></div>
        <button type="submit" class="btn btn-success">💾 Enregistrer</button>
        <a href="index.php?action=reponses" class="btn btn-secondary">Annuler</a>
    </form>
</div>
</body>
</html>