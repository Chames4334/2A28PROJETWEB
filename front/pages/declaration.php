<?php
require_once __DIR__ . '/../../Model/Config/Database.php';

$database = new Database();
$db = $database->getConnection();

$queryTypes = "SELECT * FROM type_reponse ORDER BY id";
$stmtTypes = $db->prepare($queryTypes);
$stmtTypes->execute();
$types = $stmtTypes->fetchAll(PDO::FETCH_ASSOC);

$queryAteliers = "SELECT * FROM ateliers ORDER BY nom";
$stmtAteliers = $db->prepare($queryAteliers);
$stmtAteliers->execute();
$ateliers = $stmtAteliers->fetchAll(PDO::FETCH_ASSOC);

// Dynamic Gouvernorats
$queryGouv = "SELECT DISTINCT gouvernorat FROM ateliers WHERE gouvernorat IS NOT NULL AND gouvernorat != '' ORDER BY gouvernorat";
$stmtGouv = $db->prepare($queryGouv);
$stmtGouv->execute();
$gouvernorats = $stmtGouv->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>AS Assurance - Déclaration</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: linear-gradient(135deg, #6FAF4C 0%, #A67C52 100%); min-height: 100vh; }
        .header { background: rgba(255,255,255,0.95); padding: 1rem 2rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; }
        .logo { font-size: 1.8rem; font-weight: bold; background: linear-gradient(135deg, #6FAF4C, #A67C52); -webkit-background-clip: text; background-clip: text; color: transparent; text-decoration: none; }
        .nav a { text-decoration: none; color: #333; margin: 0 1rem; }
        .nav a:hover, .nav a.active { color: #6FAF4C; }
        .container { max-width: 900px; margin: 2rem auto; background: white; border-radius: 20px; padding: 30px; box-shadow: 0 20px 40px rgba(0,0,0,0.1); border-top: 4px solid #6FAF4C; }
        h1 { color: #A67C52; text-align: center; margin-bottom: 20px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; font-weight: bold; margin-bottom: 5px; color: #333; }
        input, select, textarea { width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 8px; font-size: 14px; }
        input:focus, select:focus { border-color: #6FAF4C; outline: none; }
        .row-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .choice-field { display: none; padding: 15px; background: #f8f9fa; border-radius: 8px; margin-top: 10px; border-left: 4px solid #6FAF4C; }
        .choice-field.active { display: block; }
        #accident-map { height: 200px; width: 100%; margin-top: 10px; border-radius: 8px; border: 2px solid #ddd; z-index: 1; }
        #map-coords { margin-top: 6px; font-size: 0.85rem; color: #666; }
        button { background: #6FAF4C; color: white; padding: 14px; border: none; border-radius: 8px; cursor: pointer; width: 100%; font-size: 16px; font-weight: bold; }
        button:hover { background: #5d9a3f; transform: translateY(-2px); transition: all 0.3s; }
        .footer { background: rgba(0,0,0,0.7); color: white; text-align: center; padding: 2rem; margin-top: 2rem; }
        .footer a { color: #6FAF4C; text-decoration: none; }
        @media (max-width: 768px) { .row-2 { grid-template-columns: 1fr; } .header { flex-direction: column; text-align: center; } .nav { margin-top: 1rem; } }
    </style>
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
    <script>
        function toggleChoice() {
            var choix = document.querySelector('input[name="choix_reponse"]:checked');
            var atelierDiv = document.getElementById('atelier_section');
            var remboursementDiv = document.getElementById('remboursement_section');
            
            if (choix && choix.value === 'atelier') {
                atelierDiv.classList.add('active');
                remboursementDiv.classList.remove('active');
                var hidden = document.getElementById('type_reponse_id'); if(hidden) hidden.value = '2';
            } else if (choix && choix.value === 'remboursement') {
                remboursementDiv.classList.add('active');
                atelierDiv.classList.remove('active');
                var hidden = document.getElementById('type_reponse_id'); if(hidden) hidden.value = '1';
            } else {
                atelierDiv.classList.remove('active');
                remboursementDiv.classList.remove('active');
                var hidden = document.getElementById('type_reponse_id'); if(hidden) hidden.value = '';
            }
        }
        
        function chargerAteliers() {
            var gouvernorat = document.getElementById('gouvernorat').value;
            var atelierSelect = document.getElementById('id_atelier');
            var base = '/gs_assurance';
            fetch(base + '/ajax_get_ateliers.php?gouvernorat=' + encodeURIComponent(gouvernorat))
                .then(response => { if(!response.ok) throw new Error('network'); return response.json(); })
                .then(data => {
                    atelierSelect.innerHTML = '<option value="">-- Choisir un atelier --</option>';
                    data.forEach(function(atelier) {
                        var option = document.createElement('option');
                        option.value = atelier.id;
                        option.text = atelier.nom + (atelier.adresse ? ' - ' + atelier.adresse : '');
                        atelierSelect.appendChild(option);
                    });
                }).catch(function(){ atelierSelect.innerHTML = '<option value="">Aucun atelier</option>'; });
        }
        
        window.onload = function() {
            var radios = document.querySelectorAll('input[name="choix_reponse"]');
            radios.forEach(function(radio) {
                radio.addEventListener('change', toggleChoice);
            });
            toggleChoice();
            
            var gouvernorat = document.getElementById('gouvernorat');
            if (gouvernorat) {
                gouvernorat.addEventListener('change', chargerAteliers);
            }

            // Initialize Leaflet map
            var defaultLat = 36.8065, defaultLng = 10.1815; // Tunis
            var map = L.map('accident-map').setView([defaultLat, defaultLng], 12);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">Contributeurs OpenStreetMap</a> ❤️ <a href="https://donate.openstreetmap.org">Faire un don</a>. Conditions d\'utilisation du site web et de l\'API'
            }).addTo(map);

            var marker = null;

            function setMarker(lat, lng, address) {
                if (marker) map.removeLayer(marker);
                marker = L.marker([lat, lng]).addTo(map);
                marker.bindPopup(address || 'Position sélectionnée').openPopup();
                document.getElementById('latitude').value = lat;
                document.getElementById('longitude').value = lng;
                document.getElementById('map-coords').textContent = 'Lat: ' + lat.toFixed(6) + '  Lng: ' + lng.toFixed(6);
            }

            map.on('click', function(e) {
                var lat = e.latlng.lat, lng = e.latlng.lng;
                fetch('https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=' + lat + '&lon=' + lng)
                    .then(function(r) { return r.json(); })
                    .then(function(data) {
                        var display = data.display_name || '';
                        document.getElementById('lieu_accident').value = display;
                        setMarker(lat, lng, display);
                    }).catch(function() { setMarker(lat, lng, null); });
            });

            var lieuInput = document.getElementById('lieu_accident');
            if (lieuInput) {
                lieuInput.addEventListener('blur', function() {
                    var lieu = this.value;
                    if (lieu.trim() !== '') {
                        fetch('https://nominatim.openstreetmap.org/search?format=jsonv2&q=' + encodeURIComponent(lieu))
                            .then(function(r) { return r.json(); })
                            .then(function(results) {
                                if (results && results.length) {
                                    var r0 = results[0];
                                    var lat = parseFloat(r0.lat), lng = parseFloat(r0.lon);
                                    setMarker(lat, lng, r0.display_name);
                                    map.setView([lat, lng], 14);
                                }
                            }).catch(function() {});
                    }
                });
            }
        };
    </script>
</head>
<body>
    <div class="header">
        <a href="index.php?action=accueil" class="logo">AS ASSURANCE</a>
        <div class="nav">
            <a href="index.php?action=accueil">Accueil</a>
            <a href="index.php?action=declaration" class="active">Déclaration</a>
            <a href="index.php?action=historique">Historique</a>
        </div>
    </div>

    <div class="container">
        <h1>📝 Nouvelle déclaration de sinistre</h1>
        
        <form method="POST" action="index.php?action=save_declaration">
            <div class="row-2">
                <div class="form-group"><label>Nom *</label><input type="text" name="nom" required></div>
                <div class="form-group"><label>Prénom *</label><input type="text" name="prenom" required></div>
            </div>
            <div class="row-2">
                <div class="form-group"><label>Email *</label><input type="email" name="email" required></div>
                <div class="form-group"><label>Téléphone</label><input type="tel" name="telephone" placeholder="+216 XX XXX XXX"></div>
            </div>
            <div class="row-2">
                <div class="form-group">
                    <label>Lieu de l'accident *</label>
                    <input type="text" id="lieu_accident" name="lieu_accident" required placeholder="Ex: Tunis Centre">
                    <div id="accident-map"></div>
                    <div id="map-coords"></div>
                    <input type="hidden" id="latitude" name="latitude">
                    <input type="hidden" id="longitude" name="longitude">
                </div>
                <div class="form-group"><label>Date de l'accident *</label><input type="date" name="date_accident" required></div>
            </div>
            <div class="form-group"><label>Description *</label><textarea name="description" rows="4" required placeholder="Décrivez les circonstances de l'accident..."></textarea></div>
            
            <hr style="margin: 20px 0;">
            <h3 style="color: #A67C52;">🎯 Choisissez votre type de réponse</h3>
            
            <div class="form-group">
                <label style="margin-right: 20px;"><input type="radio" name="choix_reponse" value="atelier" required> 🔧 Atelier / Réparation</label>
                <label><input type="radio" name="choix_reponse" value="remboursement"> 💰 Remboursement</label>
                <div id="choix-error" style="color:red; font-size:0.85rem; margin-top:5px; display:none;">Veuillez choisir un type de réponse.</div>
            </div>
            
            <div id="atelier_section" class="choice-field">
                <div class="form-group">
                    <label>📍 Gouvernorat</label>
                    <select name="gouvernorat" id="gouvernorat">
                        <option value="">-- Sélectionnez --</option>
                        <?php foreach($gouvernorats as $gouv): ?>
                            <option value="<?= htmlspecialchars($gouv) ?>"><?= htmlspecialchars($gouv) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>🔧 Choisir un atelier</label>
                    <select name="id_atelier" id="id_atelier">
                        <option value="">-- Sélectionnez d'abord un gouvernorat --</option>
                    </select>
                </div>
                
            </div>
            
            <div id="remboursement_section" class="choice-field">
                <div class="form-group">
                    <label>💰 Montant estimé (TND)</label>
                    <input type="number" step="0.01" name="montant" placeholder="Ex: 1500.00">
                </div>
                
            </div>
            
            <input type="hidden" name="type_reponse_id" id="type_reponse_id" value="">
            
            <div class="form-group"><label>💬 Message complémentaire</label><textarea name="message_reponse" rows="3"></textarea></div>
            
            <button type="submit">📤 Envoyer ma déclaration</button>
        </form>
    </div>
    
    <div class="footer">
        <p>© 2025 AS ASSURANCE - Assurance auto nouvelle génération</p>
        <p><a href="mailto:contact@asassurance.tn">contact@asassurance.tn</a></p>
    </div>


</body>
</html>