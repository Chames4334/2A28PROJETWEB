<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier réponse</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="container">
    <h1>✏️ Modifier Réponse #<?= $this->reponse->id ?></h1>
    <form method="POST" class="form-container">
        <div class="form-group"><label>Demande :</label>
            <select name="demande_id" required>
                <?php foreach ($demandes as $d): ?>
                <option value="<?= $d['id'] ?>" <?= $d['id'] == $this->reponse->demande_id ? 'selected' : '' ?>>
                    #<?= $d['id'] ?> - <?= htmlspecialchars($d['nom'].' '.$d['prenom']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group"><label>Type :</label>
            <select name="type_reponse_id" required>
                <?php foreach ($types as $t): ?>
                <option value="<?= $t['id'] ?>" <?= $t['id'] == $this->reponse->type_reponse_id ? 'selected' : '' ?>><?= htmlspecialchars($t['nom']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group"><label>Montant :</label><input type="number" step="0.01" name="montant" value="<?= $this->reponse->montant ?>"></div>
        <div class="form-group"><label>Atelier :</label>
            <select name="id_atelier">
                <option value="">-- Aucun --</option>
                <?php foreach ($ateliers as $a): ?>
                <option value="<?= $a['id'] ?>" <?= $a['id'] == $this->reponse->id_atelier ? 'selected' : '' ?>><?= htmlspecialchars($a['nom']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group"><label>Message :</label><textarea name="message_admin" rows="4"><?= htmlspecialchars($this->reponse->message_admin) ?></textarea></div>
        <button type="submit" class="btn btn-success">💾 Mettre à jour</button>
        <a href="index.php?action=reponses" class="btn btn-secondary">Annuler</a>
    </form>
</div>
</body>
</html>