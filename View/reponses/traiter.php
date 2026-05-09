<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Traiter la déclaration #<?php echo htmlspecialchars((string) $demande_id); ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <h1>⚙️ Traiter la déclaration #<?php echo htmlspecialchars((string) $demande_id); ?></h1>

        <p style="margin-bottom: 16px;">
            <a href="index.php?action=historique" class="btn btn-secondary">← Retour à l'historique</a>
            <a href="index.php?action=show_demande&id=<?php echo (int) $demande_id; ?>&return=historique" class="btn btn-info">👁️ Voir la demande</a>
        </p>

        <div class="detail-card" style="margin-bottom: 24px;">
            <strong>Client :</strong>
            <?php echo htmlspecialchars(trim(($demande->nom ?? '') . ' ' . ($demande->prenom ?? ''))); ?>
            &nbsp;|&nbsp;
            <strong>Lieu :</strong> <?php echo htmlspecialchars($demande->lieu_accident ?? ''); ?>
            &nbsp;|&nbsp;
            <strong>Statut :</strong>
            <span class="status status-<?php echo htmlspecialchars($demande->statut ?? ''); ?>"><?php echo htmlspecialchars($demande->statut ?? ''); ?></span>
        </div>

        <h2 style="font-size: 1.2em; margin-bottom: 12px;">Réponses enregistrées (CRUD)</h2>
        <p style="color:#666; margin-bottom:12px; font-size:14px;">Les boutons Modifier et Supprimer s’appliquent à chaque réponse, à côté du traitement de cette demande.</p>

        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Type</th>
                    <th>Message</th>
                    <th>Date</th>
                    <th>Modifier</th>
                    <th>Supprimer</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reponses as $r): ?>
                <tr>
                    <td><?php echo (int) $r['id']; ?></td>
                    <td><?php echo htmlspecialchars($r['type_reponse_nom'] ?? ''); ?></td>
                    <td><?php echo nl2br(htmlspecialchars($r['contenu'] ?? '')); ?></td>
                    <td><?php echo htmlspecialchars($r['created_at'] ?? ''); ?></td>
                    <td class="actions">
                        <a href="index.php?action=edit_reponse&id=<?php echo (int) $r['id']; ?>&demande_id=<?php echo (int) $demande_id; ?>" class="btn-edit">✏️ Modifier</a>
                    </td>
                    <td class="actions">
                        <a href="index.php?action=delete_reponse&id=<?php echo (int) $r['id']; ?>&demande_id=<?php echo (int) $demande_id; ?>"
                           onclick="return confirm('Supprimer cette réponse ?');" class="btn-delete">🗑️ Supprimer</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php if (empty($reponses)): ?>
            <p style="text-align:center;color:#999;">Aucune réponse pour l’instant — utilisez le formulaire ci‑dessous.</p>
        <?php endif; ?>

        <h2 style="font-size: 1.2em; margin: 28px 0 12px;">Nouvelle réponse</h2>
        <form method="POST" action="index.php?action=save_reponse&id=<?php echo (int) $demande_id; ?>" class="form-container">
            <div class="form-group">
                <label for="type_reponse_id">Type de réponse</label>
                <select name="type_reponse_id" id="type_reponse_id" required>
                    <option value="">— Choisir —</option>
                    <?php foreach ($types_reponse as $t): ?>
                        <option value="<?php echo (int) $t['id']; ?>"><?php echo htmlspecialchars($t['nom']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="contenu">Message / contenu</label>
                <textarea name="contenu" id="contenu" rows="5" required placeholder="Texte de la réponse administrative"></textarea>
            </div>
            <button type="submit" class="btn btn-success">💾 Enregistrer la réponse</button>
        </form>
    </div>
</body>
</html>
