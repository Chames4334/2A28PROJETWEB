<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Nouveau type</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="container">
    <h1>➕ Nouveau type de réponse</h1>
    <form method="POST" class="form-container">
        <div class="form-group"><label>Nom :</label><input type="text" name="nom" required placeholder="Ex: Remboursement total"></div>
        <div class="form-group"><label>Description :</label><textarea name="description" rows="3"></textarea></div>
        <button type="submit" class="btn btn-success">💾 Créer</button>
        <a href="index.php?action=type_reponses" class="btn btn-secondary">Annuler</a>
    </form>
</div>
</body>
</html>