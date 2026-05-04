<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier la réponse</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #6FAF4C 0%, #A67C52 100%); min-height: 100vh; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; background: white; border-radius: 15px; padding: 30px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); }
        h1 { color: #A67C52; margin-bottom: 20px; border-left: 5px solid #6FAF4C; padding-left: 15px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; color: #333; }
        select, input, textarea { width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 8px; font-size: 14px; }
        button { background: #6FAF4C; color: white; padding: 12px 25px; border: none; border-radius: 8px; cursor: pointer; margin-right: 10px; font-size: 16px; }
        button:hover { background: #5d9a3f; transform: translateY(-2px); }
        .btn-secondary { background: #6c757d; text-decoration: none; padding: 12px 25px; border-radius: 8px; color: white; display: inline-block; }
        .btn-secondary:hover { background: #5a6268; transform: translateY(-2px); }
        .alert-error { background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 20px; }
        .btn-info { background: #A67C52; }
        .btn-success { background: #28a745; }
        .btn-warning { background: #ffc107; color: #333; }
        .btn { display: inline-block; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-right: 10px; }
        .btn:hover { transform: translateY(-2px); }
    </style>
</head>
<body>
<div class="container">
    <h1>✏️ Modifier la réponse #<?= $this->reponse->getId() ?></h1>
    
    <?php if(isset($error)): ?>
        <div class="alert-error"><?= $error ?></div>
    <?php endif; ?>
    
    <form method="POST" action="index.php?action=update_reponse&id=<?= $this->reponse->getId() ?>">
        <div class="form-group">
            <label>Demande :</label>
            <select name="demande_id" readonly style="background:#e9ecef;">
                <option value="<?= $this->reponse->getDemandeId() ?>">#<?= $this->reponse->getDemandeId() ?></option>
            </select>
        </div>
        
        <div class="form-group">
            <label>Type de réponse :</label>
            <select name="type_reponse_id" required>
                <?php foreach($types as $t): ?>
                <option value="<?= $t['id'] ?>" <?= $t['id'] == $this->reponse->getTypeReponseId() ? 'selected' : '' ?>>
                    <?= htmlspecialchars($t['nom']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label>Montant (TND) :</label>
            <input type="number" step="0.01" name="montant" value="<?= $this->reponse->getMontant() ?>" placeholder="Ex: 1500.00">
        </div>
        
        <div class="form-group">
            <label>Atelier :</label>
            <select name="id_atelier">
                <option value="">-- Aucun --</option>
                <?php foreach($ateliers as $a): ?>
                <option value="<?= $a['id'] ?>" <?= $a['id'] == $this->reponse->getIdAtelier() ? 'selected' : '' ?>>
                    <?= htmlspecialchars($a['nom']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label>Statut de la voiture :</label>
            <select name="statut_voiture">
                <option value="en_attente" <?= $this->reponse->getStatutVoiture() == 'en_attente' ? 'selected' : '' ?>>⏳ En attente</option>
                <option value="en_cours" <?= $this->reponse->getStatutVoiture() == 'en_cours' ? 'selected' : '' ?>>🔧 En cours de réparation</option>
                <option value="pret" <?= $this->reponse->getStatutVoiture() == 'pret' ? 'selected' : '' ?>>✅ Voiture prête</option>
                <option value="termine" <?= $this->reponse->getStatutVoiture() == 'termine' ? 'selected' : '' ?>>🎉 Réparation terminée</option>
            </select>
        </div>
        
        <div class="form-group">
            <label>Temps restant (jours) :</label>
            <input type="number" name="temps_restant" value="<?= $this->reponse->getTempsRestant() ?>" placeholder="Ex: 3">
        </div>
        
        <div class="form-group">
            <label>Message admin :</label>
            <textarea name="message_admin" rows="4"><?= htmlspecialchars($this->reponse->getMessageAdmin()) ?></textarea>
        </div>
        
        <div style="margin-top: 20px;">
            <button type="submit">💾 Enregistrer</button>
            <a href="index.php?action=reponses" class="btn-secondary">Annuler</a>
        </div>
    </form>
    
    <!-- ===== SECTION NOTIFICATIONS ===== -->
    <div style="margin-top: 30px; padding: 15px; background: #e8f5e9; border-radius: 8px; border-left: 4px solid #6FAF4C;">
        <h3 style="color: #6FAF4C; margin-bottom: 15px;">📱 Envoyer une notification au client</h3>
        
        <div style="display: flex; gap: 15px; flex-wrap: wrap;">
            <a href="index.php?action=send_notification&type=prise_en_charge&id=<?= $this->reponse->getDemandeId() ?>" 
               class="btn btn-info" style="background:#A67C52;color:white;padding:10px20px;text-decoration:none;border-radius:5px;"
               onclick="return confirm('Envoyer une notification de prise en charge ?')">
               📧 Dossier pris en charge
            </a>
            
            <a href="index.php?action=send_notification&type=voiture_prete&id=<?= $this->reponse->getDemandeId() ?>" 
               class="btn btn-success" style="background:#28a745;color:white;padding:10px20px;text-decoration:none;border-radius:5px;"
               onclick="return confirm('Envoyer une notification que la voiture est prête ?')">
               ✅ Voiture prête
            </a>
            
            <a href="index.php?action=send_notification&type=rappel&id=<?= $this->reponse->getDemandeId() ?>" 
               class="btn btn-warning" style="background:#ffc107;color:#333;padding:10px20px;text-decoration:none;border-radius:5px;"
               onclick="return confirm('Envoyer un rappel au client ?')">
               ⏰ Rappel
            </a>
        </div>
    </div>
    
    <?php if(isset($_SESSION['notif_msg'])): ?>
        <div style="background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-top: 15px;">
            ✅ <?= $_SESSION['notif_msg'] ?>
            <?php unset($_SESSION['notif_msg']); ?>
        </div>
    <?php endif; ?>
    <!-- ===== FIN NOTIFICATIONS ===== -->
    <!-- ===== SECTION NOTIFICATIONS ===== -->
<div style="margin-top: 30px; padding: 15px; background: #e8f5e9; border-radius: 8px; border-left: 4px solid #6FAF4C;">
    <h3 style="color: #6FAF4C; margin-bottom: 15px;">📱 Envoyer une notification au client</h3>
    
    <div style="display: flex; gap: 15px; flex-wrap: wrap;">
        <a href="index.php?action=send_notification&type=prise_en_charge&id=<?= $this->reponse->getDemandeId() ?>" 
           style="background: #A67C52; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;"
           onclick="return confirm('Envoyer une notification de prise en charge ?')">
           📧 Dossier pris en charge
        </a>
        
        <a href="index.php?action=send_notification&type=voiture_prete&id=<?= $this->reponse->getDemandeId() ?>" 
           style="background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;"
           onclick="return confirm('Envoyer une notification que la voiture est prête ?')">
           ✅ Voiture prête
        </a>
        
        <a href="index.php?action=send_notification&type=rappel&id=<?= $this->reponse->getDemandeId() ?>" 
           style="background: #ffc107; color: #333; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;"
           onclick="return confirm('Envoyer un rappel au client ?')">
           ⏰ Rappel
        </a>
    </div>
</div>

<?php if(isset($_SESSION['notif_msg'])): ?>
    <div style="background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-top: 15px;">
        ✅ <?= $_SESSION['notif_msg'] ?>
        <?php unset($_SESSION['notif_msg']); ?>
    </div>
<?php endif; ?>
<!-- ===== FIN NOTIFICATIONS ===== -->
</div>
</body>
</html>