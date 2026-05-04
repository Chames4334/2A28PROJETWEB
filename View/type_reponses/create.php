<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Nouveau type de réponse</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #6FAF4C 0%, #A67C52 100%); min-height: 100vh; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 15px; padding: 30px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); }
        h1 { color: #A67C52; margin-bottom: 20px; border-left: 5px solid #6FAF4C; padding-left: 15px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; color: #333; }
        select, input { width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 8px; font-size: 14px; background: white; }
        select:focus, input:focus { outline: none; border-color: #6FAF4C; }
        .info { background: #e8f5e8; padding: 10px; border-radius: 5px; margin-bottom: 20px; color: #2e7d32; font-size: 14px; }
        .nom-auto { background: #fff3cd; padding: 12px; border-radius: 5px; margin-top: 20px; font-weight: bold; color: #856404; text-align: center; }
        .btn-group { display: flex; gap: 10px; margin-top: 20px; }
        button { background: #6FAF4C; color: white; padding: 12px 25px; border: none; border-radius: 8px; cursor: pointer; font-size: 16px; font-weight: bold; transition: all 0.3s; }
        button:hover { background: #5d9a3f; transform: translateY(-2px); }
        .btn-secondary { background: #6c757d; text-decoration: none; padding: 12px 25px; border-radius: 8px; color: white; display: inline-block; font-size: 16px; font-weight: bold; transition: all 0.3s; }
        .btn-secondary:hover { background: #5a6268; transform: translateY(-2px); }
        @media (max-width: 768px) { .btn-group { flex-direction: column; } }
    </style>
    <script>
        function genererNom() {
            var categorie = document.getElementById('categorie').value;
            var nomGenere = '';
            
            if (categorie === 'atelier') {
                var gouvernorat = document.getElementById('gouvernorat').value;
                var atelierSelect = document.getElementById('id_atelier');
                if (gouvernorat && atelierSelect.value) {
                    var textAtelier = atelierSelect.options[atelierSelect.selectedIndex]?.text || '';
                    nomGenere = 'Atelier - ' + gouvernorat + ': ' + textAtelier;
                } else {
                    nomGenere = 'Atelier - Nouveau';
                }
            } else if (categorie === 'remboursement') {
                var montant = document.getElementById('montant').value;
                if (montant && montant > 0) {
                    nomGenere = 'Remboursement - ' + montant + ' TND';
                } else {
                    nomGenere = 'Remboursement - Montant à définir';
                }
            }
            
            document.getElementById('nom_genere').innerHTML = nomGenere;
            document.getElementById('nom_type').value = nomGenere;
        }
        
        function chargerAteliers() {
            var gouvernorat = document.getElementById('gouvernorat').value;
            var atelierSelect = document.getElementById('id_atelier');
            
            if (!gouvernorat) {
                atelierSelect.innerHTML = '<option value="">-- Choisir gouvernorat d\'abord --</option>';
                genererNom();
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
                    genererNom();
                });
        }
        
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
            genererNom();
        }
        
        window.onload = function() {
            var select = document.getElementById('categorie');
            if (select) {
                select.addEventListener('change', toggleFields);
                toggleFields();
            }
            
            var gouvernorat = document.getElementById('gouvernorat');
            if (gouvernorat) {
                gouvernorat.addEventListener('change', function() {
                    chargerAteliers();
                    genererNom();
                });
            }
            
            var atelierSelect = document.getElementById('id_atelier');
            if (atelierSelect) {
                atelierSelect.addEventListener('change', genererNom);
            }
            
            var montant = document.getElementById('montant');
            if (montant) {
                montant.addEventListener('keyup', genererNom);
            }
        };
    </script>
</head>
<body>
<div class="container">
    <h1>➕ Nouveau type de réponse</h1>
    
    <div class="info">
        ℹ️ Le nom sera généré automatiquement en fonction de votre choix
    </div>
    
    <form method="POST" action="index.php?action=store_type_reponse">
        <input type="hidden" name="nom" id="nom_type">
        
        <div class="form-group">
            <label>Catégorie :</label>
            <select name="categorie" id="categorie" required>
                <option value="">-- Sélectionnez --</option>
                <option value="atelier">🔧 Atelier / Réparation</option>
                <option value="remboursement">💰 Remboursement</option>
            </select>
        </div>
        
        <!-- SECTION ATELIER -->
        <div id="atelier_section" style="display: none;">
            <div class="form-group">
                <label>📍 Gouvernorat</label>
                <select name="gouvernorat" id="gouvernorat">
                    <option value="">-- Sélectionnez --</option>
                    <option value="Tunis">Tunis</option>
                    <option value="Ariana">Ariana</option>
                    <option value="Ben Arous">Ben Arous</option>
                    <option value="Manouba">Manouba</option>
                    <option value="Nabeul">Nabeul</option>
                    <option value="Sousse">Sousse</option>
                    <option value="Sfax">Sfax</option>
                </select>
            </div>
            <div class="form-group">
                <label>🔧 Atelier</label>
                <select name="id_atelier" id="id_atelier">
                    <option value="">-- Choisir gouvernorat d'abord --</option>
                </select>
            </div>
        </div>
        
        <!-- SECTION REMBOURSEMENT -->
        <div id="remboursement_section" style="display: none;">
            <div class="form-group">
                <label>💰 Montant (TND)</label>
                <input type="number" step="0.01" name="montant" id="montant" placeholder="Ex: 1500.00">
                <small style="color: #666;">Laissez vide pour un montant à définir plus tard</small>
            </div>
        </div>
        
        <div class="nom-auto" id="nom_genere">
            En attente de sélection
        </div>
        
        <div class="btn-group">
            <button type="submit">💾 Créer</button>
            <a href="index.php?action=type_reponses" class="btn-secondary">Annuler</a>
        </div>
    </form>
</div>
</body>
</html>