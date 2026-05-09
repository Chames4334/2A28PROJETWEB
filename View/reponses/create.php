<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Nouvelle réponse</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: linear-gradient(135deg, #6FAF4C 0%, #A67C52 100%); padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; background: white; border-radius: 15px; padding: 30px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); }
        h1 { color: #A67C52; margin-bottom: 20px; border-left: 5px solid #6FAF4C; padding-left: 15px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; color: #333; }
        select, textarea, input { width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 8px; font-size: 14px; }
        button { background: #6FAF4C; color: white; padding: 12px 25px; border: none; border-radius: 8px; cursor: pointer; margin-right: 10px; }
        .btn-secondary { background: #6c757d; text-decoration: none; padding: 12px 25px; border-radius: 8px; color: white; display: inline-block; }
        .alert { background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 20px; }
    </style>
</head>
<body>
<div class="container">
    <h1>➕ Nouvelle réponse</h1>
    
    <?php if(isset($error)): ?>
        <div class="alert"><?= $error ?></div>
    <?php endif; ?>
    
    <?php $prefillDemande = $_GET['demande_id'] ?? null; ?>
    <form method="POST" action="index.php?action=store_reponse">
        <div class="form-group">
            <label>Demande :</label>
            <select name="demande_id" required>
                <option value="">-- Sélectionnez --</option>
                <?php if(!empty($demandes)): ?>
                    <?php foreach($demandes as $d): ?>
                        <option value="<?= $d['id'] ?>" <?= ($prefillDemande && $prefillDemande == $d['id']) ? 'selected' : '' ?>>#<?= $d['id'] ?> - <?= htmlspecialchars($d['nom'] . ' ' . $d['prenom']) ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label>Type de réponse :</label>
            <select name="type_reponse_id" required>
                <option value="1">💰 Remboursement</option>
                <option value="2">🔧 Atelier</option>
            </select>
        </div>
        
        <div class="form-group">
            <label>Montant (TND) :</label>
            <input type="number" step="0.01" name="montant" placeholder="Ex: 1500.00">
        </div>
        
        <div class="form-group">
            <label>Atelier :</label>
            <select name="id_atelier">
                <option value="">-- Aucun --</option>
                <?php if(!empty($ateliers)): ?>
                    <?php foreach($ateliers as $a): ?>
                        <option value="<?= $a['id'] ?>"><?= htmlspecialchars($a['nom']) ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label>Message :</label>
            <textarea name="message_admin" rows="4" placeholder="Message de l'assurance..."></textarea>
        </div>
        
        <div>
            <button type="submit">💾 Enregistrer</button>
            <a href="index.php?action=reponses" class="btn-secondary">Annuler</a>
        </div>
    </form>
</div>
</body>
</html>