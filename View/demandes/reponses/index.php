<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Réponses</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="container">
    <h1>💬 Réponses aux demandes</h1>
    <div class="action-buttons">
        <a href="index.php?action=create_reponse" class="btn btn-success">➕ Nouvelle réponse</a>
        <a href="index.php?action=demandes" class="btn btn-info">← Retour aux demandes</a>
    </div>
    <table class="data-table">
        <thead>
            <tr><th>ID</th><th>Demande ID</th><th>Type</th><th>Montant</th><th>Atelier</th><th>Message</th><th>Date</th><th>Actions</th></tr>
        </thead>
        <tbody>
            <?php foreach ($reponses as $r): ?>
            <tr>
                <td><?= $r['id'] ?></td>
                <td><?= $r['demande_id'] ?></td>
                <td><?= htmlspecialchars($r['type_nom']) ?></td>
                <td><?= $r['montant'] ? number_format($r['montant'], 2) . ' €' : '-' ?></td>
                <td><?= htmlspecialchars($r['atelier_nom'] ?? '-') ?></td>
                <td><?= htmlspecialchars(substr($r['message_admin'], 0, 50)) ?></td>
                <td><?= $r['created_at'] ?></td>
                <td class="actions">
                    <a href="index.php?action=edit_reponse&id=<?= $r['id'] ?>" class="btn-edit">✏️</a>
                    <a href="index.php?action=delete_reponse&id=<?= $r['id'] ?>" onclick="return confirm('Supprimer ?')" class="btn-delete">🗑️</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>