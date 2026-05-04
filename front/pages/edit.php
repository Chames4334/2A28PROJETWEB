<?php
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Récupérer les données de la demande
$query = "SELECT * FROM demande_constat WHERE id = ?";
$stmt = $db->prepare($query);
$stmt->execute([$id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row) {
    header("Location: index.php?action=historique");
    exit();
}

// Créer l'objet Demande et remplir avec les données
$demande = new Demande($db);
$demande->setNom($row['nom'])
        ->setPrenom($row['prenom'])
        ->setEmail($row['email'])
        ->setTelephone($row['telephone'])
        ->setLieuAccident($row['lieu_accident'])
        ->setDateAccident($row['date_accident'])
        ->setDescription($row['description'])
        ->setStatut($row['statut']);

// Requête SQL directe pour les types de réponse
$queryTypes = "SELECT * FROM type_reponse ORDER BY id";
$stmtTypes = $db->prepare($queryTypes);
$stmtTypes->execute();
$types = $stmtTypes->fetchAll(PDO::FETCH_ASSOC);

// Requête SQL directe pour les ateliers
$queryAteliers = "SELECT * FROM ateliers ORDER BY nom";
$stmtAteliers = $db->prepare($queryAteliers);
$stmtAteliers->execute();
$ateliers = $stmtAteliers->fetchAll(PDO::FETCH_ASSOC);

// Récupérer la réponse existante
$queryReponse = "SELECT * FROM reponse_constat WHERE demande_id = ?";
$stmtReponse = $db->prepare($queryReponse);
$stmtReponse->execute([$id]);
$reponseData = $stmtReponse->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>AS Assurance - Modifier déclaration #<?= $id ?></title>
    <link rel="stylesheet" href="../../View/assets/css/style.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            min-height: 100vh;
            animation: bgChange 12s infinite ease-in-out;
        }
        
        @keyframes bgChange {
            0% { background-color: #6FAF4C; }
            33% { background-color: #A67C52; }
            66% { background-color: #F2F2F2; }
            100% { background-color: #6FAF4C; }
        }
        
        .gs-header {
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 20px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        .gs-header-inner {
            max-width: 1400px;
            margin: 0 auto;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .gs-logo {
            font-size: 1.8rem;
            font-weight: bold;
            background: linear-gradient(135deg, #6FAF4C 0%, #A67C52 100%);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            text-decoration: none;
        }
        .gs-logo span { font-size: 0.8rem; display: block; color: #666; }
        .gs-nav { display: flex; gap: 2rem; }
        .gs-nav a { text-decoration: none; color: #333; font-weight: 500; transition: color 0.3s; }
        .gs-nav a:hover, .gs-nav a.is-active { color: #6FAF4C; }
        .gs-btn-client {
            background: #6FAF4C;
            color: white;
            padding: 0.5rem 1.5rem;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
        }
        .gs-btn-client:hover { background: #A67C52; transform: translateY(-2px); }
        
        .container {
            max-width: 900px;
            margin: 2rem auto;
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            border-top: 4px solid #6FAF4C;
        }
        h1 { text-align: center; margin-bottom: 10px; color: #A67C52; }
        .subtitle { text-align: center; color: #666; margin-bottom: 30px; font-size: 14px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; color: #333; }
        input, select, textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s;
            background: white;
        }
        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: #6FAF4C;
            box-shadow: 0 0 0 3px rgba(111,175,76,0.1);
        }
        .row-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .section-title {
            font-size: 1.1rem;
            color: #6FAF4C;
            margin: 20px 0 15px 0;
            padding-bottom: 8px;
            border-bottom: 2px solid #6FAF4C;
        }
        button {
            background: #6FAF4C;
            color: white;
            padding: 14px 28px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            transition: all 0.3s;
            margin-right: 10px;
        }
        button:hover {
            background: #5d9a3f;
            transform: translateY(-2px);
        }
        .btn-cancel {
            background: #6c757d;
        }
        .btn-cancel:hover {
            background: #5a6268;
        }
        .btn-back {
            display: inline-block;
            margin-top: 20px;
            color: #6FAF4C;
            text-decoration: none;
            transition: color 0.3s;
        }
        .btn-back:hover { color: #A67C52; }
        
        .gs-footer {
            background: rgba(0,0,0,0.7);
            backdrop-filter: blur(10px);
            color: white;
            margin-top: 2rem;
            padding: 3rem 2rem 1rem;
        }
        .gs-footer-inner {
            max-width: 1400px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
        }
        .gs-footer a { color: rgba(255,255,255,0.8); text-decoration: none; }
        .gs-footer a:hover { color: #6FAF4C; }
        .gs-copy { text-align: center; padding-top: 2rem; margin-top: 2rem; border-top: 1px solid rgba(255,255,255,0.2); }
        
        @media (max-width: 768px) {
            .gs-header-inner { flex-direction: column; text-align: center; }
            .gs-nav { flex-direction: column; gap: 1rem; text-align: center; }
            .container { margin: 1rem; padding: 20px; }
            .row-2 { grid-template-columns: 1fr; gap: 0; }
        }
    </style>
    <script>
        function validateLetters(input, fieldName) {
            const regex = /^[a-zA-ZÀ-ÿ\u0600-\u06FF\s\-']+$/;
            const value = input.value.trim();
            const errorSpan = document.getElementById(fieldName + '_error');
            
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
            
            if (nom) isValid = validateLetters(nom, 'nom') && isValid;
            if (prenom) isValid = validateLetters(prenom, 'prenom') && isValid;
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
                const isValid = isFormValid();
                submitBtn.disabled = !isValid;
                return isValid;
            }
            return false;
        }
        
        function toggleReponseFields() {
            const typeReponse = document.getElementById('type_reponse_id');
            if (!typeReponse) return;
            
            const selectedOption = typeReponse.options[typeReponse.selectedIndex];
            const typeName = selectedOption ? selectedOption.text.toLowerCase() : '';
            
            const atelierField = document.getElementById('atelier_field');
            const montantField = document.getElementById('montant_field');
            
            if (atelierField && montantField) {
                if (typeName.includes('atelier') || typeName.includes('garage') || typeName.includes('réparation')) {
                    atelierField.style.display = 'block';
                    montantField.style.display = 'none';
                } else if (typeName.includes('remboursement') || typeName.includes('indemnisation')) {
                    montantField.style.display = 'block';
                    atelierField.style.display = 'none';
                } else {
                    atelierField.style.display = 'none';
                    montantField.style.display = 'none';
                }
            }
        }
        
        function validateFormBeforeSubmit() {
            const isValid = isFormValid();
            if (!isValid) {
                alert('Veuillez corriger les erreurs dans le formulaire avant de soumettre.');
                return false;
            }
            return true;
        }
        
        function initValidation() {
            const fields = [
                { id: 'nom', handler: validateLetters, param: 'nom' },
                { id: 'prenom', handler: validateLetters, param: 'prenom' },
                { id: 'email', handler: validateEmail, param: null },
                { id: 'telephone', handler: validatePhone, param: null },
                { id: 'lieu_accident', handler: validateLieu, param: null },
                { id: 'date_accident', handler: validateDate, param: null },
                { id: 'description', handler: validateDescription, param: null }
            ];
            
            fields.forEach(field => {
                const element = document.getElementById(field.id);
                if (element) {
                    const handler = function() {
                        if (field.handler) {
                            if (field.param) {
                                field.handler(element, field.param);
                            } else {
                                field.handler(element);
                            }
                        }
                        updateSubmitButton();
                    };
                    element.addEventListener('input', handler);
                    element.addEventListener('blur', handler);
                    if (field.id === 'date_accident') {
                        element.addEventListener('change', handler);
                    }
                }
            });
            
            setTimeout(updateSubmitButton, 100);
        }
        
        window.onload = function() {
            initValidation();
            
            const typeSelect = document.getElementById('type_reponse_id');
            if (typeSelect) {
                typeSelect.addEventListener('change', toggleReponseFields);
                toggleReponseFields();
            }
            
            const form = document.querySelector('form');
            if (form) {
                form.onsubmit = validateFormBeforeSubmit;
            }
        };
    </script>
</head>
<body>
    <header class="gs-header">
        <div class="gs-header-inner">
            <a href="index.php?action=accueil" class="gs-logo">
                AS ASSURANCE
                <span>Confiance & Stabilité</span>
            </a>
            <nav class="gs-nav">
                <a href="index.php?action=accueil">Accueil</a>
                <a href="index.php?action=declaration">Déclaration</a>
                <a href="index.php?action=historique" class="is-active">Historique</a>
            </nav>
            <a href="index.php?action=historique" class="gs-btn-client">Espace client</a>
        </div>
    </header>

    <div class="container">
        <h1>✏️ Modifier déclaration #<?= $id ?></h1>
        <p class="subtitle">Modifiez votre déclaration et la réponse de l'assurance</p>
        
        <form method="POST" action="index.php?action=update_declaration">
            <input type="hidden" name="id" value="<?= $id ?>">
            
            <div class="section-title">📋 Informations du sinistre</div>
            <div class="row-2">
                <div class="form-group">
                    <label>Nom</label>
                    <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($demande->getNom()) ?>" required>
                    <div id="nom_error" class="error-message"></div>
                </div>
                <div class="form-group">
                    <label>Prénom</label>
                    <input type="text" id="prenom" name="prenom" value="<?= htmlspecialchars($demande->getPrenom()) ?>" required>
                    <div id="prenom_error" class="error-message"></div>
                </div>
            </div>
            <div class="row-2">
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($demande->getEmail()) ?>" required>
                    <div id="email_error" class="error-message"></div>
                </div>
                <div class="form-group">
                    <label>Téléphone</label>
                    <input type="tel" id="telephone" name="telephone" value="<?= htmlspecialchars($demande->getTelephone()) ?>" required>
                    <div id="telephone_error" class="error-message"></div>
                </div>
            </div>
            <div class="row-2">
                <div class="form-group">
                    <label>Lieu de l'accident</label>
                    <input type="text" id="lieu_accident" name="lieu_accident" value="<?= htmlspecialchars($demande->getLieuAccident()) ?>" required>
                    <div id="lieu_error" class="error-message"></div>
                </div>
                <div class="form-group">
                    <label>Date de l'accident</label>
                    <input type="date" id="date_accident" name="date_accident" value="<?= $demande->getDateAccident() ?>" required>
                    <div id="date_error" class="error-message"></div>
                </div>
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea id="description" name="description" rows="4" required><?= htmlspecialchars($demande->getDescription()) ?></textarea>
                <div id="description_error" class="error-message"></div>
            </div>
            <div class="form-group">
                <label>Statut</label>
                <select name="statut">
                    <option value="soumis" <?= $demande->getStatut() == 'soumis' ? 'selected' : '' ?>>Soumis</option>
                    <option value="en_cours" <?= $demande->getStatut() == 'en_cours' ? 'selected' : '' ?>>En cours</option>
                    <option value="accepte" <?= $demande->getStatut() == 'accepte' ? 'selected' : '' ?>>Accepté</option>
                    <option value="refuse" <?= $demande->getStatut() == 'refuse' ? 'selected' : '' ?>>Refusé</option>
                    <option value="clos" <?= $demande->getStatut() == 'clos' ? 'selected' : '' ?>>Clos</option>
                </select>
            </div>
            
            <div class="section-title">🎯 Réponse de l'assurance</div>
            
            <div class="form-group">
                <label>Type de réponse</label>
                <select name="type_reponse_id" id="type_reponse_id">
                    <option value="">-- Sélectionnez un type --</option>
                    <?php foreach ($types as $t): ?>
                    <option value="<?= $t['id'] ?>" <?= ($reponseData && $reponseData['type_reponse_id'] == $t['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($t['nom']) ?> - <?= htmlspecialchars($t['description']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div id="atelier_field" style="display: none;">
                <div class="form-group">
                    <label>Atelier partenaire</label>
                    <select name="id_atelier">
                        <option value="">-- Sélectionnez un atelier --</option>
                        <?php foreach ($ateliers as $a): ?>
                        <option value="<?= $a['id'] ?>" <?= ($reponseData && $reponseData['id_atelier'] == $a['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($a['nom']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div id="montant_field" style="display: none;">
                <div class="form-group">
                    <label>Montant (TND)</label>
                    <input type="number" step="0.01" name="montant" value="<?= $reponseData ? $reponseData['montant'] : '' ?>" placeholder="Ex: 1500.00">
                </div>
            </div>
            
            <div class="form-group">
                <label>Message / Informations</label>
                <textarea name="message_reponse" rows="3" placeholder="Message de l'assurance..."><?= $reponseData ? htmlspecialchars($reponseData['message_admin']) : '' ?></textarea>
            </div>
            
            <div>
                <button type="submit" id="submitBtn">💾 Enregistrer les modifications</button>
                <button type="button" class="btn-cancel" onclick="window.location.href='index.php?action=historique'">Annuler</button>
            </div>
        </form>
        <div style="text-align: center;">
            <a href="index.php?action=historique" class="btn-back">← Retour à l'historique</a>
        </div>
    </div>

    <footer class="gs-footer">
        <div class="gs-footer-inner">
            <div><strong>AS ASSURANCE</strong><p>Assurance auto nouvelle génération.</p></div>
            <div><strong>Liens utiles</strong><p><a href="index.php?action=declaration">Déclaration sinistre</a></p><p><a href="index.php?action=historique">Historique</a></p></div>
            <div><strong>Contact</strong><p>📧 <a href="mailto:contact@asassurance.tn">contact@asassurance.tn</a></p></div>
        </div>
        <p class="gs-copy">© <?php echo date('Y'); ?> AS ASSURANCE</p>
    </footer>
</body>
</html>