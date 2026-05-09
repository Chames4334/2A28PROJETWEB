<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Détails du type de réponse</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #6FAF4C 0%, #A67C52 100%); min-height: 100vh; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 15px; padding: 30px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); }
        h1 { color: #A67C52; margin-bottom: 20px; border-left: 5px solid #6FAF4C; padding-left: 15px; }
        .detail-card { background: #f8f9fa; border-radius: 10px; padding: 20px; margin: 20px 0; }
        .detail-card p { padding: 12px; border-bottom: 1px solid #ddd; font-size: 16px; }
        .detail-card p:last-child { border-bottom: none; }
        .detail-card strong { color: #A67C52; width: 140px; display: inline-block; }
        .btn-group { display: flex; gap: 10px; margin-top: 20px; flex-wrap: wrap; }
        .btn { padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; font-size: 14px; transition: transform 0.2s; display: inline-block; }
        .btn:hover { transform: translateY(-2px); }
        .btn-success { background: #28a745; color: white; }
        .btn-danger { background: #dc3545; color: white; }
        .btn-secondary { background: #6c757d; color: white; }
        
        /* Modal HTML */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            justify-content: center;
            align-items: center;
        }
        .modal-content {
            background: white;
            padding: 25px;
            border-radius: 10px;
            text-align: center;
            max-width: 400px;
            margin: auto;
            box-shadow: 0 5px 20px rgba(0,0,0,0.3);
            border-top: 4px solid #dc3545;
        }
        .modal-content p {
            margin-bottom: 20px;
            font-size: 16px;
            color: #333;
        }
        .modal-buttons {
            display: flex;
            gap: 10px;
            justify-content: center;
        }
        .modal-buttons button {
            padding: 8px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }
        .btn-confirm {
            background: #dc3545;
            color: white;
        }
        .btn-cancel-modal {
            background: #6c757d;
            color: white;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>📄 Détails du type de réponse</h1>
    
    <div class="detail-card">
        <p><strong>Nom :</strong> <?= htmlspecialchars($this->typeReponse->getNom()) ?></p>
        <p><strong>Description :</strong> <?= nl2br(htmlspecialchars($this->typeReponse->getDescription())) ?></p>
        <p><strong>Catégorie :</strong> <?= $this->typeReponse->getCategorie() == 'atelier' ? '🔧 Atelier / Réparation' : '💰 Remboursement' ?></p>
        <p><strong>📍 Gouvernorat :</strong> <?= htmlspecialchars($this->typeReponse->getGouvernorat() ?: 'Tunis') ?></p>
        <p><strong>📅 Date de création :</strong> <?= $this->typeReponse->getCreatedAt() ? date('d/m/Y à H:i', strtotime($this->typeReponse->getCreatedAt())) : 'Non définie' ?></p>
    </div>
    
    <div class="btn-group">
        <a href="index.php?action=edit_type_reponse&id=<?= $this->typeReponse->getId() ?>" class="btn btn-success">✏️ Modifier</a>
        <button type="button" class="btn btn-danger" onclick="openModal()">🗑️ Supprimer</button>
        <a href="index.php?action=type_reponses" class="btn btn-secondary">← Retour</a>
    </div>
</div>

<!-- Modal HTML de confirmation -->
<div id="deleteModal" class="modal">
    <div class="modal-content">
        <p>⚠️ Êtes-vous sûr de vouloir supprimer ce type de réponse ?</p>
        <p id="modalTypeName" style="font-weight: bold; color: #dc3545;"><?= htmlspecialchars($this->typeReponse->getNom()) ?></p>
        <div class="modal-buttons">
            <button class="btn-confirm" id="confirmDelete">Oui, supprimer</button>
            <button class="btn-cancel-modal" id="cancelDelete">Annuler</button>
        </div>
    </div>
</div>

<script>
    var deleteId = <?= $this->typeReponse->getId() ?>;
    
    function openModal() {
        document.getElementById('deleteModal').style.display = 'flex';
    }
    
    function closeModal() {
        document.getElementById('deleteModal').style.display = 'none';
    }
    
    document.getElementById('confirmDelete').onclick = function() {
        window.location.href = 'index.php?action=delete_type_reponse&id=' + deleteId;
    };
    
    document.getElementById('cancelDelete').onclick = function() {
        closeModal();
    };
    
    window.onclick = function(event) {
        var modal = document.getElementById('deleteModal');
        if (event.target == modal) {
            closeModal();
        }
    };
</script>
</body>
</html>