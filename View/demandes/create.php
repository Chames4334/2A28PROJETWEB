<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Nouvelle demande</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #6FAF4C 0%, #A67C52 100%); min-height: 100vh; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; background: white; border-radius: 15px; padding: 30px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); }
        h1 { color: #A67C52; margin-bottom: 20px; border-left: 5px solid #6FAF4C; padding-left: 15px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; color: #333; }
        label .required { color: #dc3545; }
        input, select, textarea { width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 8px; font-size: 14px; transition: all 0.3s; }
        input:focus, select:focus, textarea:focus { outline: none; border-color: #6FAF4C; }
        .row-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        button { background: #6FAF4C; color: white; padding: 14px 28px; border: none; border-radius: 8px; cursor: pointer; font-size: 16px; font-weight: bold; width: 100%; }
        button:hover { background: #5d9a3f; transform: translateY(-2px); }
        button:disabled { background: #ccc; cursor: not-allowed; }
        .btn-secondary { background: #6c757d; text-decoration: none; padding: 12px 25px; border-radius: 8px; color: white; display: inline-block; text-align: center; margin-top: 10px; }
        .error-message { color: #dc3545; font-size: 12px; margin-top: 5px; display: none; }
        .error-message.show { display: block; }
        input.error, select.error, textarea.error { border-color: #dc3545; background-color: #fff8f8; }
        .success { background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 20px; }
        #accident-map { height: 200px; width: 100%; margin-top: 10px; border-radius: 8px; border: 2px solid #ddd; z-index: 1; }
        #map-coords { margin-top: 6px; font-size: 0.85rem; color: #666; }
    </style>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
</head>
<body>
<div class="container">
    <h1>➕ Nouvelle Demande</h1>
    <?php
    // Charger types et gouvernorats pour le formulaire Types
    require_once __DIR__ . '/../../Model/Config/Database.php';
    $database = new Database();
    $db = $database->getConnection();

    $typesStmt = $db->prepare("SELECT * FROM type_reponse ORDER BY id");
    $typesStmt->execute();
    $allTypes = $typesStmt->fetchAll(PDO::FETCH_ASSOC);

    $govStmt = $db->prepare("SELECT DISTINCT gouvernorat FROM ateliers ORDER BY gouvernorat");
    $govStmt->execute();
    $gouvernorats = $govStmt->fetchAll(PDO::FETCH_COLUMN);
    ?>

    <form method="POST" action="index.php?action=create_demande" id="demandeForm">
        <div class="row-2">
            <div class="form-group">
                <label>Nom <span class="required">*</span></label>
                <input type="text" name="nom" id="nom" class="form-control" required placeholder="Votre nom">
                <div id="nom_error" class="error-message">Le nom ne doit contenir que des lettres</div>
            </div>
            <div class="form-group">
                <label>Prénom <span class="required">*</span></label>
                <input type="text" name="prenom" id="prenom" class="form-control" required placeholder="Votre prénom">
                <div id="prenom_error" class="error-message">Le prénom ne doit contenir que des lettres</div>
            </div>
        </div>
        
        <div class="row-2">
            <div class="form-group">
                <label>Email <span class="required">*</span></label>
                <input type="email" name="email" id="email" class="form-control" required placeholder="exemple@email.com">
                <div id="email_error" class="error-message">Veuillez entrer un email valide</div>
            </div>
            <div class="form-group">
                <label>Téléphone <span class="required">*</span></label>
                <input type="tel" name="telephone" id="telephone" class="form-control" required placeholder="+216 XX XXX XXX">
                <div id="telephone_error" class="error-message">Le numéro doit commencer par +216 suivi de 8 chiffres</div>
            </div>
        </div>
        
        <div class="row-2">
            <div class="form-group">
                <label>Lieu de l'accident <span class="required">*</span></label>
                <input type="text" name="lieu_accident" id="lieu_accident" class="form-control" required placeholder="Adresse ou lieu précis">
                <div id="accident-map"></div>
                <div id="map-coords"></div>
                <input type="hidden" id="latitude" name="latitude">
                <input type="hidden" id="longitude" name="longitude">
                <div id="lieu_error" class="error-message">Le lieu est requis</div>
            </div>
            <div class="form-group">
                <label>Date de l'accident <span class="required">*</span></label>
                <input type="date" name="date_accident" id="date_accident" class="form-control" required>
                <div id="date_error" class="error-message">La date est requise</div>
            </div>
        </div>
        
        <div class="form-group">
            <label>Description <span class="required">*</span></label>
            <textarea name="description" id="description" rows="4" required placeholder="Décrivez les circonstances de l'accident... (minimum 10 caractères)"></textarea>
            <div id="description_error" class="error-message">La description doit contenir au moins 10 caractères</div>
        </div>

        <!-- Types section: atelier vs remboursement -->
        <div class="form-group">
            <label>Type (atelier / remboursement)</label>
            <div style="display:flex;gap:12px;margin-top:8px;">
                <label style="display:flex;align-items:center;gap:6px;"><input type="radio" name="type_mode" value="atelier" id="type_atelier"> Atelier</label>
                <label style="display:flex;align-items:center;gap:6px;"><input type="radio" name="type_mode" value="remboursement" id="type_remboursement"> Remboursement</label>
            </div>
        </div>

        <div id="atelier_block" style="display:none;">
            <div class="form-group">
                <label>Gouvernorat</label>
                <select id="gouvernorat_select" name="gouvernorat">
                    <option value="">-- Choisir --</option>
                    <?php foreach($gouvernorats as $g): ?>
                        <option value="<?= htmlspecialchars($g) ?>"><?= htmlspecialchars($g) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Atelier</label>
                <select id="atelier_select" name="id_atelier">
                    <option value="">-- Choisir gouvernorat d'abord --</option>
                </select>
            </div>
        </div>

        <div id="remboursement_block" style="display:none;">
            <div class="form-group">
                <label>Montant (TND)</label>
                <input type="number" step="0.01" name="montant_client" id="montant_client" placeholder="Ex: 1500.00">
            </div>
        </div>
        
        <button type="submit" id="submitBtn">💾 Enregistrer</button>
        <a href="index.php?action=demandes" class="btn-secondary">Annuler</a>
    </form>
</div>

<script>
    // Validation des lettres (pas de chiffres)
    function validateLetters(input, errorId) {
        const regex = /^[a-zA-ZÀ-ÿ\s\-']+$/;
        const value = input.value.trim();
        const errorSpan = document.getElementById(errorId);
        
        if (value === '') {
            errorSpan.textContent = 'Ce champ est requis';
            errorSpan.classList.add('show');
            input.classList.add('error');
            return false;
        } else if (!regex.test(value)) {
            errorSpan.textContent = 'Ce champ ne doit contenir que des lettres';
            errorSpan.classList.add('show');
            input.classList.add('error');
            return false;
        } else {
            errorSpan.classList.remove('show');
            input.classList.remove('error');
            return true;
        }
    }

    function validateEmail(emailInput) {
        const regex = /^[^\s@]+@([^\s@]+\.)+[^\s@]+$/;
        const value = emailInput.value.trim();
        const errorSpan = document.getElementById('email_error');
        
        if (value === '') {
            errorSpan.textContent = 'L\'email est requis';
            errorSpan.classList.add('show');
            emailInput.classList.add('error');
            return false;
        } else if (!regex.test(value)) {
            errorSpan.textContent = 'Veuillez entrer un email valide';
            errorSpan.classList.add('show');
            emailInput.classList.add('error');
            return false;
        } else {
            errorSpan.classList.remove('show');
            emailInput.classList.remove('error');
            return true;
        }
    }

    function validatePhone(phoneInput) {
        let value = phoneInput.value.trim();
        const errorSpan = document.getElementById('telephone_error');
        
        if (value === '') {
            errorSpan.textContent = 'Le téléphone est requis';
            errorSpan.classList.add('show');
            phoneInput.classList.add('error');
            return false;
        }
        
        let cleanPhone = value.replace(/\s/g, '');
        if (!cleanPhone.startsWith('+216')) {
            errorSpan.textContent = 'Le numéro doit commencer par +216';
            errorSpan.classList.add('show');
            phoneInput.classList.add('error');
            return false;
        }
        let reste = cleanPhone.substring(4);
        if (!/^[0-9]{8}$/.test(reste)) {
            errorSpan.textContent = 'Après +216, veuillez entrer exactement 8 chiffres';
            errorSpan.classList.add('show');
            phoneInput.classList.add('error');
            return false;
        }
        
        errorSpan.classList.remove('show');
        phoneInput.classList.remove('error');
        return true;
    }

    function formatPhone(input) {
        let value = input.value.replace(/\s/g, '');
        
        if (value.length > 0 && !value.startsWith('+')) {
            if (value.startsWith('216')) {
                value = '+' + value;
            }
        }
        
        if (value.startsWith('+216')) {
            let reste = value.substring(4);
            if (reste.length > 0) {
                let formatted = '+216';
                if (reste.length >= 2) formatted += ' ' + reste.substring(0, 2);
                if (reste.length >= 5) formatted += ' ' + reste.substring(2, 5);
                if (reste.length >= 8) formatted += ' ' + reste.substring(5, 8);
                input.value = formatted.trim();
            } else {
                input.value = '+216';
            }
        }
    }

    function validateLieu(lieuInput) {
        const value = lieuInput.value.trim();
        const errorSpan = document.getElementById('lieu_error');
        
        if (value === '') {
            errorSpan.textContent = 'Le lieu est requis';
            errorSpan.classList.add('show');
            lieuInput.classList.add('error');
            return false;
        } else {
            errorSpan.classList.remove('show');
            lieuInput.classList.remove('error');
            return true;
        }
    }

    function validateDate(dateInput) {
        const value = dateInput.value;
        const errorSpan = document.getElementById('date_error');
        
        if (value === '') {
            errorSpan.textContent = 'La date est requise';
            errorSpan.classList.add('show');
            dateInput.classList.add('error');
            return false;
        } else {
            errorSpan.classList.remove('show');
            dateInput.classList.remove('error');
            return true;
        }
    }

    function validateDescription(descInput) {
        const value = descInput.value.trim();
        const errorSpan = document.getElementById('description_error');
        
        if (value === '') {
            errorSpan.textContent = 'La description est requise';
            errorSpan.classList.add('show');
            descInput.classList.add('error');
            return false;
        } else if (value.length < 10) {
            errorSpan.textContent = 'La description doit contenir au moins 10 caractères';
            errorSpan.classList.add('show');
            descInput.classList.add('error');
            return false;
        } else {
            errorSpan.classList.remove('show');
            descInput.classList.remove('error');
            return true;
        }
    }

    function isFormValid() {
        const nom = document.getElementById('nom');
        const prenom = document.getElementById('prenom');
        const email = document.getElementById('email');
        const telephone = document.getElementById('telephone');
        const lieu = document.getElementById('lieu_accident');
        const date = document.getElementById('date_accident');
        const description = document.getElementById('description');
        
        let isValid = true;
        
        if (nom) isValid = validateLetters(nom, 'nom_error') && isValid;
        if (prenom) isValid = validateLetters(prenom, 'prenom_error') && isValid;
        if (email) isValid = validateEmail(email) && isValid;
        if (telephone) isValid = validatePhone(telephone) && isValid;
        if (lieu) isValid = validateLieu(lieu) && isValid;
        if (date) isValid = validateDate(date) && isValid;
        if (description) isValid = validateDescription(description) && isValid;
        
        return isValid;
    }

    function updateSubmitButton() {
        const submitBtn = document.getElementById('submitBtn');
        if (submitBtn) {
            submitBtn.disabled = !isFormValid();
        }
    }

    function validateFormBeforeSubmit(event) {
        if (!isFormValid()) {
            event.preventDefault();
            alert('Veuillez corriger les erreurs dans le formulaire avant de soumettre.');
            return false;
        }
        // Additional checks for Types: require atelier selection or montant depending on choice
        var atelierRadio = document.getElementById('type_atelier');
        var rembRadio = document.getElementById('type_remboursement');
        if (atelierRadio && atelierRadio.checked) {
            var atelierSelect = document.getElementById('atelier_select');
            if (!atelierSelect || !atelierSelect.value) {
                event.preventDefault();
                alert('Veuillez choisir un atelier (sélectionnez un gouvernorat puis un atelier).');
                return false;
            }
        } else if (rembRadio && rembRadio.checked) {
            var montant = document.getElementById('montant_client');
            if (!montant || montant.value === '' || parseFloat(montant.value) <= 0) {
                event.preventDefault();
                alert('Veuillez entrer un montant valide pour le remboursement.');
                return false;
            }
        }
        return true;
    }

    window.onload = function() {
        const fields = [
            { id: 'nom', handler: validateLetters, errorId: 'nom_error' },
            { id: 'prenom', handler: validateLetters, errorId: 'prenom_error' },
            { id: 'email', handler: validateEmail, errorId: 'email_error' },
            { id: 'telephone', handler: validatePhone, errorId: 'telephone_error' },
            { id: 'lieu_accident', handler: validateLieu, errorId: 'lieu_error' },
            { id: 'date_accident', handler: validateDate, errorId: 'date_error' },
            { id: 'description', handler: validateDescription, errorId: 'description_error' }
        ];
        
        fields.forEach(field => {
            const element = document.getElementById(field.id);
            if (element) {
                element.addEventListener('input', function() {
                    field.handler(element, field.errorId);
                    updateSubmitButton();
                });
                element.addEventListener('blur', function() {
                    field.handler(element, field.errorId);
                    updateSubmitButton();
                });
            }
        });
        
        const phoneInput = document.getElementById('telephone');
        if (phoneInput) {
            phoneInput.addEventListener('input', function() {
                formatPhone(this);
                validatePhone(this);
                updateSubmitButton();
            });
        }
        
        const form = document.getElementById('demandeForm');
        if (form) {
            form.addEventListener('submit', validateFormBeforeSubmit);
        }
        
        updateSubmitButton();

        // === Leaflet Map ===
        var map = L.map('accident-map').setView([36.8065, 10.1815], 12);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">Contributeurs OpenStreetMap</a>'
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
            fetch('https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=' + e.latlng.lat + '&lon=' + e.latlng.lng)
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    document.getElementById('lieu_accident').value = data.display_name || '';
                    setMarker(e.latlng.lat, e.latlng.lng, data.display_name);
                }).catch(function() { setMarker(e.latlng.lat, e.latlng.lng, null); });
        });

        var lieuInput = document.getElementById('lieu_accident');
        if (lieuInput) {
            lieuInput.addEventListener('blur', function() {
                if (this.value.trim() !== '') {
                    fetch('https://nominatim.openstreetmap.org/search?format=jsonv2&q=' + encodeURIComponent(this.value))
                        .then(function(r) { return r.json(); })
                        .then(function(results) {
                            if (results && results.length) {
                                var r0 = results[0];
                                setMarker(parseFloat(r0.lat), parseFloat(r0.lon), r0.display_name);
                                map.setView([parseFloat(r0.lat), parseFloat(r0.lon)], 14);
                            }
                        }).catch(function() {});
                }
            });
        }
    };

    // Types UI
    document.addEventListener('DOMContentLoaded', function() {
        var atelierRadio = document.getElementById('type_atelier');
        var rembRadio = document.getElementById('type_remboursement');
        var atelierBlock = document.getElementById('atelier_block');
        var rembBlock = document.getElementById('remboursement_block');
        var govSelect = document.getElementById('gouvernorat_select');
        var atelierSelect = document.getElementById('atelier_select');

        function toggleTypeBlocks() {
            if (atelierRadio && atelierRadio.checked) {
                atelierBlock.style.display = 'block';
                rembBlock.style.display = 'none';
            } else if (rembRadio && rembRadio.checked) {
                atelierBlock.style.display = 'none';
                rembBlock.style.display = 'block';
            } else {
                atelierBlock.style.display = 'none';
                rembBlock.style.display = 'none';
            }
        }

        if (atelierRadio) atelierRadio.addEventListener('change', toggleTypeBlocks);
        if (rembRadio) rembRadio.addEventListener('change', toggleTypeBlocks);

        // Ensure correct initial visibility based on pre-selected radio
        toggleTypeBlocks();

        // Load gouvernorats if empty via AJAX
        function ensureGouvernorats() {
            if (!govSelect) return;
            if (govSelect.options.length <= 1) {
                govSelect.innerHTML = '<option value="">Chargement...</option>';
                fetch('ajax_get_gouvernorats.php')
                    .then(r => r.json())
                    .then(list => {
                        govSelect.innerHTML = '';
                        var defaultOpt = document.createElement('option');
                        defaultOpt.value = '';
                        defaultOpt.text = '-- Choisir --';
                        govSelect.appendChild(defaultOpt);
                        if (!list || !list.length) {
                            var emptyOpt = document.createElement('option');
                            emptyOpt.value = '';
                            emptyOpt.text = 'Aucun gouvernorat disponible';
                            govSelect.appendChild(emptyOpt);
                            return;
                        }
                        list.forEach(function(g) {
                            var opt = document.createElement('option');
                            opt.value = g;
                            opt.text = g;
                            govSelect.appendChild(opt);
                        });
                    })
                    .catch(() => {
                        govSelect.innerHTML = '';
                        var errOpt = document.createElement('option');
                        errOpt.value = '';
                        errOpt.text = 'Erreur chargement';
                        govSelect.appendChild(errOpt);
                    });
            }
        }

        ensureGouvernorats();

        if (atelierRadio) {
            atelierRadio.addEventListener('click', function() {
                ensureGouvernorats();
                toggleTypeBlocks();
            });
        }

        if (govSelect) {
            govSelect.addEventListener('change', function() {
                var g = this.value;
                if (!g) {
                    atelierSelect.innerHTML = '<option value="">-- Choisir gouvernorat d\'abord --</option>';
                    return;
                }
                atelierSelect.innerHTML = '<option value="">Chargement...</option>';
                fetch('ajax_get_ateliers.php?gouvernorat=' + encodeURIComponent(g))
                    .then(r => r.json())
                    .then(list => {
                        atelierSelect.innerHTML = '<option value="">-- Choisir un atelier --</option>';
                        list.forEach(function(a) {
                            var opt = document.createElement('option');
                            opt.value = a.id;
                            opt.text = a.nom + ' - ' + (a.adresse || '');
                            atelierSelect.appendChild(opt);
                        });
                    })
                    .catch(() => {
                        atelierSelect.innerHTML = '<option value="">-- Erreur chargement --</option>';
                    });
            });
        }
    });
</script>
</body>
</html>