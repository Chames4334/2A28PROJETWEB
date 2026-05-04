<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Types de réponse</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="container">
    <h1>🏷️ Types de réponse</h1>
    <div class="action-buttons">
        <a href="index.php?action=create_type_reponse" class="btn btn-success">➕ Nouveau type</a>
        <a href="index.php?action=demandes" class="btn btn-info">← Demandes</a>
    </div>
    <table class="data-table">
        <thead>
            <tr><th>ID</th><th>Nom</th><th>Description</th><th>Créé le</th><th>Actions</th></tr>
        </thead>
        <tbody>
            <?php foreach ($typeReponses as $t): ?>
            <tr>
                <td><?= $t['id'] ?></td>
                <td><strong><?= htmlspecialchars($t['nom']) ?></strong></td>
                <td><?= htmlspecialchars($t['description']) ?></td>
                <td><?= $t['created_at'] ?></td>
                <td class="actions">
                    <a href="index.php?action=edit_type_reponse&id=<?= $t['id'] ?>" class="btn-edit">✏️</a>
                    <a href="index.php?action=delete_type_reponse&id=<?= $t['id'] ?>" onclick="return confirm('Supprimer ?')" class="btn-delete">🗑️</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>