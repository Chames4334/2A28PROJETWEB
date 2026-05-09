<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier la demande</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #6FAF4C 0%, #A67C52 100%); min-height: 100vh; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; background: white; border-radius: 15px; padding: 30px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); }
        h1 { color: #A67C52; margin-bottom: 20px; border-left: 5px solid #6FAF4C; padding-left: 15px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; color: #333; }
        input, select, textarea { width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 8px; font-size: 14px; }
        input:focus, select:focus, textarea:focus { outline: none; border-color: #6FAF4C; }
        .row-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        button { background: #6FAF4C; color: white; padding: 12px 25px; border: none; border-radius: 8px; cursor: pointer; font-size: 16px; margin-right: 10px; }
        button:hover { background: #5d9a3f; }
        .btn-secondary { background: #6c757d; text-decoration: none; padding: 12px 25px; border-radius: 8px; color: white; display: inline-block; }
        .btn-secondary:hover { background: #5a6268; }
        .error-message { color: #dc3545; font-size: 12px; margin-top: 5px; }
    </style>
</head>
<body>
<div class="container">
    <h1>✏️ Modifier la demande #<?= $this->demande->getId() ?></h1>
    
    <form method="POST">
        <div class="row-2">
            <div class="form-group">
                <label>Nom :</label>
                <input type="text" name="nom" value="<?= htmlspecialchars($this->demande->getNom()) ?>" required>
            </div>
            <div class="form-group">
                <label>Prénom :</label>
                <input type="text" name="prenom" value="<?= htmlspecialchars($this->demande->getPrenom()) ?>" required>
            </div>
        </div>
        <div class="row-2">
            <div class="form-group">
                <label>Email :</label>
                <input type="email" name="email" value="<?= htmlspecialchars($this->demande->getEmail()) ?>" required>
            </div>
            <div class="form-group">
                <label>Téléphone :</label>
                <input type="text" name="telephone" value="<?= htmlspecialchars($this->demande->getTelephone()) ?>" required>
            </div>
        </div>
        <div class="row-2">
            <div class="form-group">
                <label>Lieu de l'accident :</label>
                <input type="text" name="lieu_accident" value="<?= htmlspecialchars($this->demande->getLieuAccident()) ?>" required>
            </div>
            <div class="form-group">
                <label>Date de l'accident :</label>
                <input type="date" name="date_accident" value="<?= $this->demande->getDateAccident() ?>" required>
            </div>
        </div>
        <div class="form-group">
            <label>Description :</label>
            <textarea name="description" rows="4" required><?= htmlspecialchars($this->demande->getDescription()) ?></textarea>
        </div>
        <div class="form-group">
            <label>Statut :</label>
            <select name="statut">
                <option value="soumis" <?= $this->demande->getStatut() == 'soumis' ? 'selected' : '' ?>>Soumis</option>
                <option value="en_cours" <?= $this->demande->getStatut() == 'en_cours' ? 'selected' : '' ?>>En cours</option>
                <option value="accepte" <?= $this->demande->getStatut() == 'accepte' ? 'selected' : '' ?>>Accepté</option>
                <option value="refuse" <?= $this->demande->getStatut() == 'refuse' ? 'selected' : '' ?>>Refusé</option>
                <option value="clos" <?= $this->demande->getStatut() == 'clos' ? 'selected' : '' ?>>Clos</option>
            </select>
        </div>
        
        <div style="margin-top: 20px;">
            <button type="submit">💾 Enregistrer</button>
            <a href="index.php?action=demandes" class="btn-secondary">Annuler</a>
        </div>
    </form>
</div>
</body>
</html>