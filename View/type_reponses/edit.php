<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier le type de réponse</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: linear-gradient(135deg, #6FAF4C 0%, #A67C52 100%); padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 15px; padding: 30px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); }
        h1 { color: #A67C52; margin-bottom: 20px; border-left: 5px solid #6FAF4C; padding-left: 15px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; color: #333; }
        select, input { width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 8px; font-size: 14px; }
        .btn-group { display: flex; gap: 10px; margin-top: 20px; }
        button { background: #6FAF4C; color: white; padding: 12px 25px; border: none; border-radius: 8px; cursor: pointer; }
        .btn-secondary { background: #6c757d; text-decoration: none; padding: 12px 25px; border-radius: 8px; color: white; display: inline-block; }
        .info { background: #e8f5e8; padding: 10px; border-radius: 5px; margin-bottom: 20px; color: #2e7d32; }
        .error { background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 20px; }
    </style>
    <script>
        function toggleFields() {
            var categorie = document.getElementById('categorie').value;
            var atelierDiv = document.getElementById('atelier_section');
            var remboursementDiv = document.getElementById('remboursement_section');
            
            if (categorie === 'atelier') {
                atelierDiv.style.display = 'block';
                remboursementDiv.style.display = 'none';
            } else if (categorie === 'remboursement') {
                atelierDiv.style.display = 'none';
                remboursementDiv.style.display = 'block';
            } else {
                atelierDiv.style.display = 'none';
                remboursementDiv.style.display = 'none';
            }
        }
        
        function chargerAteliers() {
            var gouvernorat = document.getElementById('gouvernorat').value;
            var atelierSelect = document.getElementById('id_atelier');
            
            if (!gouvernorat) {
                atelierSelect.innerHTML = '<option value="">-- Choisir gouvernorat d\'abord --</option>';
                return;
            }
            
            fetch('ajax_get_ateliers.php?gouvernorat=' + encodeURIComponent(gouvernorat))
                .then(response => response.json())
                .then(data => {
                    atelierSelect.innerHTML = '<option value="">-- Choisir un atelier --</option>';
                    data.forEach(function(atelier) {
                        var option = document.createElement('option');
                        option.value = atelier.id;
                        option.text = atelier.nom + ' - ' + atelier.adresse;
                        atelierSelect.appendChild(option);
                    });
                });
        }
        
        window.onload = function() {
            var select = document.getElementById('categorie');
            if (select) {
                select.addEventListener('change', toggleFields);
                toggleFields();
            }
            
            var gouvernorat = document.getElementById('gouvernorat');
            if (gouvernorat) {
                gouvernorat.addEventListener('change', chargerAteliers);
            }
        };
    </script>
</head>
<body>
<div class="container">
    <h1>✏️ Modifier le type de réponse</h1>
    
    <?php if(isset($error)): ?>
        <div class="error"><?= $error ?></div>
    <?php endif; ?>
    
    <div class="info">
        ℹ️ Modifiez la catégorie de réponse
    </div>
    
    <form method="POST" action="index.php?action=update_type_reponse&id=<?= $this->typeReponse->getId() ?>">
        <div class="form-group">
            <label>Catégorie :</label>
            <select name="categorie" id="categorie" required>
                <option value="remboursement" <?= $this->typeReponse->getCategorie() == 'remboursement' ? 'selected' : '' ?>>💰 Remboursement</option>
                <option value="atelier" <?= $this->typeReponse->getCategorie() == 'atelier' ? 'selected' : '' ?>>🔧 Atelier / Réparation</option>
            </select>
        </div>
        
        <div id="atelier_section" style="display: <?= $this->typeReponse->getCategorie() == 'atelier' ? 'block' : 'none' ?>;">
            <div class="form-group">
                <label>📍 Gouvernorat</label>
                <select name="gouvernorat" id="gouvernorat">
                    <option value="">-- Sélectionnez --</option>
                    <option value="Tunis" <?= $this->typeReponse->getGouvernorat() == 'Tunis' ? 'selected' : '' ?>>Tunis</option>
                    <option value="Ariana" <?= $this->typeReponse->getGouvernorat() == 'Ariana' ? 'selected' : '' ?>>Ariana</option>
                    <option value="Sousse" <?= $this->typeReponse->getGouvernorat() == 'Sousse' ? 'selected' : '' ?>>Sousse</option>
                    <option value="Sfax" <?= $this->typeReponse->getGouvernorat() == 'Sfax' ? 'selected' : '' ?>>Sfax</option>
                </select>
            </div>
            <div class="form-group">
                <label>🔧 Atelier</label>
                <select name="id_atelier" id="id_atelier">
                    <option value="">-- Choisir gouvernorat d'abord --</option>
                </select>
            </div>
        </div>
        
        <div id="remboursement_section" style="display: <?= $this->typeReponse->getCategorie() == 'remboursement' ? 'block' : 'none' ?>;">
            <div class="form-group">
                <label>💰 Montant (TND)</label>
                <input type="number" step="0.01" name="montant" value="<?= $this->typeReponse->getMontant() ?>" placeholder="Ex: 1500.00">
            </div>
        </div>
        
        <div class="btn-group">
            <button type="submit">💾 Mettre à jour</button>
            <a href="index.php?action=type_reponses" class="btn-secondary">Annuler</a>
        </div>
    </form>
</div>
</body>
</html>