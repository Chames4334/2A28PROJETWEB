<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Types de réponse</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: radial-gradient(circle at 12% 18%, rgba(111,175,76,0.34), transparent 24%), radial-gradient(circle at 82% 16%, rgba(255,255,255,0.22), transparent 18%), radial-gradient(circle at 70% 78%, rgba(166,124,82,0.30), transparent 24%), radial-gradient(circle at 25% 82%, rgba(111,175,76,0.18), transparent 20%), linear-gradient(135deg, #6faf4c 0%, #ffffff 48%, #a67c52 100%); background-size: 140% 140%; animation: adminAurora 18s ease-in-out infinite alternate; padding: 20px; color: #e8ecff; }
        @keyframes adminAurora { 0% { background-position: 0% 0%, 100% 0%, 100% 100%, 0% 100%, 50% 50%; } 50% { background-position: 12% 8%, 88% 18%, 78% 90%, 8% 84%, 50% 50%; } 100% { background-position: 20% 14%, 74% 8%, 92% 76%, 18% 96%, 50% 50%; } }
        .container { max-width: 1200px; margin: 0 auto; background: rgba(86,69,52,0.58); border-radius: 24px; padding: 30px; box-shadow: 0 24px 70px rgba(166,124,82,0.24); backdrop-filter: blur(16px); border: 1px solid rgba(166,124,82,0.22); }
        h1 { color: #f4f7ff; margin-bottom: 20px; border-left: 5px solid #6faf4c; padding-left: 15px; text-shadow: 0 10px 30px rgba(0,0,0,0.25); }
        .btn,
        .btn-view,
        .btn-edit,
        .btn-delete,
        .modal-buttons button {
            padding: 11px 22px;
            border: 1px solid rgba(255,255,255,0.14);
            border-radius: 999px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-size: 14px;
            font-weight: 600;
            letter-spacing: 0.2px;
            color: #fff;
            box-shadow: 0 12px 30px rgba(166, 124, 82, 0.20);
            backdrop-filter: blur(12px);
            transition: transform 0.25s ease, box-shadow 0.25s ease, filter 0.25s ease, background 0.25s ease;
        }
        .btn:hover,
        .btn-view:hover,
        .btn-edit:hover,
        .btn-delete:hover,
        .modal-buttons button:hover {
            transform: translateY(-3px) scale(1.01);
            box-shadow: 0 18px 34px rgba(166, 124, 82, 0.28);
            filter: saturate(1.06);
        }
        .btn-success {
            background: linear-gradient(135deg, #6FAF4C 0%, #89C56B 100%);
            color: white;
        }
        .btn-info,
        .btn-view {
            background: linear-gradient(135deg, #a67c52 0%, #6faf4c 100%);
            color: white;
        }
        .btn-edit {
            background: linear-gradient(135deg, #6FAF4C 0%, #A67C52 100%);
            color: white;
        }
        .btn-delete,
        .btn-confirm {
            background: linear-gradient(135deg, #A67C52 0%, #8E5F37 100%);
            color: white;
        }
        .btn-cancel-modal {
            background: linear-gradient(135deg, #A67C52 0%, #6FAF4C 100%);
            color: white;
        }
        .data-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-top: 20px;
            background: transparent;
            border: 1px solid rgba(166,124,82,0.28);
            border-radius: 22px;
            overflow: hidden;
            box-shadow: 0 18px 38px rgba(166,124,82,0.18);
            backdrop-filter: blur(4px);
        }
        .data-table thead {
            background: linear-gradient(135deg, rgba(111,175,76,0.62) 0%, rgba(166,124,82,0.78) 100%);
            color: #ffffff;
        }
        .data-table th,
        .data-table td {
            padding: 14px 16px;
            text-align: left;
            border-bottom: 1px solid rgba(166,124,82,0.20);
            color: #fffdf8;
        }
        .data-table th {
            font-weight: 700;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 0.08em;
        }
        .data-table tbody tr {
            background: rgba(111,175,76,0.22);
            transition: background 0.2s ease, transform 0.2s ease;
        }
        .data-table tbody tr:nth-child(even) {
            background: rgba(255,255,255,0.28);
        }
        .data-table tbody tr:hover {
            background: rgba(166,124,82,0.18);
        }
        .data-table tbody tr:last-child td {
            border-bottom: none;
        }
        .actions { display: flex; gap: 8px; flex-wrap: wrap; }
        .success { background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 20px; border-left: 4px solid #28a745; }
        .error { background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 20px; }
        
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
            flex-wrap: wrap;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>🏷️ Types de réponse</h1>
    
    <?php if(isset($_GET['success'])): ?>
        <div class="success">✅ Type créé avec succès !</div>
    <?php endif; ?>
    <?php if(isset($_GET['update'])): ?>
        <div class="success">✏️ Type modifié avec succès !</div>
    <?php endif; ?>
    <?php if(isset($_GET['delete'])): ?>
        <div class="success">🗑️ Type supprimé avec succès !</div>
    <?php endif; ?>
    
    <div style="margin-bottom:20px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
        <div>
            <a href="index.php?action=create_type_reponse" class="btn btn-success">➕ Nouveau type</a>
            <a href="index.php?action=demandes" class="btn btn-info">← Demandes</a>
        </div>
        
        <form method="GET" action="index.php" style="display: flex; gap: 10px; align-items: center;">
            <input type="hidden" name="action" value="type_reponses">
            <input type="text" name="search" placeholder="Rechercher par nom..." value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>" style="padding: 8px; border: 1px solid #ccc; border-radius: 4px; outline: none;">
            <select name="sort" style="padding: 8px; border: 1px solid #ccc; border-radius: 4px; outline: none;">
                <option value="id_asc" <?= (isset($_GET['sort']) && $_GET['sort'] == 'id_asc') ? 'selected' : '' ?>>Trier par défaut</option>
                <option value="nom_asc" <?= (isset($_GET['sort']) && $_GET['sort'] == 'nom_asc') ? 'selected' : '' ?>>Nom (A-Z)</option>
                <option value="nom_desc" <?= (isset($_GET['sort']) && $_GET['sort'] == 'nom_desc') ? 'selected' : '' ?>>Nom (Z-A)</option>
                <option value="date_desc" <?= (isset($_GET['sort']) && $_GET['sort'] == 'date_desc') ? 'selected' : '' ?>>Plus récents</option>
                <option value="date_asc" <?= (isset($_GET['sort']) && $_GET['sort'] == 'date_asc') ? 'selected' : '' ?>>Plus anciens</option>
            </select>
            <button type="submit" class="btn btn-info" style="padding: 8px 15px;">🔍 Filtrer</button>
        </form>
    </div>
    
    <table class="data-table">
        <thead>
            <tr><th>Nom</th><th>Description</th><th>Catégorie</th><th>Créé le</th><th>Actions</th></tr>
        </thead>
        <tbody>
            <?php foreach ($typeReponses as $t): ?>
            <tr>
                <td><strong><?= htmlspecialchars($t['nom']) ?></strong></td>
                <td><?= htmlspecialchars(substr($t['description'], 0, 50)) ?>...</td>
                <td><?= isset($t['categorie']) && $t['categorie'] == 'atelier' ? '🔧 Atelier' : '💰 Remboursement' ?></td>
                <td><?= $t['created_at'] ?></td>
                <td class="actions">
                    <a href="index.php?action=show_type_reponse&id=<?= $t['id'] ?>" class="btn-view">👁️ Voir</a>
                    <a href="index.php?action=edit_type_reponse&id=<?= $t['id'] ?>" class="btn-edit">✏️ Modifier</a>
                    <button type="button" class="btn-delete" onclick="openModal(<?= $t['id'] ?>, '<?= htmlspecialchars($t['nom']) ?>')">🗑️ Supprimer</button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Modal HTML de confirmation -->
<div id="deleteModal" class="modal">
    <div class="modal-content">
        <p>⚠️ Êtes-vous sûr de vouloir supprimer ce type ?</p>
        <p id="modalTypeName" style="font-weight: bold; color: #dc3545;"></p>
        <div class="modal-buttons">
            <button class="btn-confirm" id="confirmDelete">Oui, supprimer</button>
            <button class="btn-cancel-modal" id="cancelDelete">Annuler</button>
        </div>
    </div>
</div>

<script>
    var deleteId = null;
    
    function openModal(id, nom) {
        deleteId = id;
        document.getElementById('modalTypeName').innerHTML = nom;
        document.getElementById('deleteModal').style.display = 'flex';
    }
    
    function closeModal() {
        document.getElementById('deleteModal').style.display = 'none';
        deleteId = null;
    }
    
    document.getElementById('confirmDelete').onclick = function() {
        if (deleteId) {
            window.location.href = 'index.php?action=delete_type_reponse&id=' + deleteId;
        }
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