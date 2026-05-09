<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Réponses aux sinistres</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: linear-gradient(135deg, #6FAF4C 0%, #A67C52 100%); padding: 20px; }
        .container { max-width: 1000px; margin: 0 auto; background: white; border-radius: 15px; padding: 30px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); }
        h1 { color: #A67C52; margin-bottom: 20px; border-left: 5px solid #6FAF4C; padding-left: 15px; }
        .btn { background: #6FAF4C; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: linear-gradient(135deg, #6FAF4C, #A67C52); color: white; padding: 12px; text-align: left; }
        td { padding: 12px; border-bottom: 1px solid #ddd; }
        tr:hover { background: rgba(111,175,76,0.1); }
        .msg-pret { background: #d4edda; color: #155724; padding: 8px; border-radius: 5px; }
        .msg-cours { background: #fff3cd; color: #856404; padding: 8px; border-radius: 5px; }
        .msg-attente { background: #cce5ff; color: #004085; padding: 8px; border-radius: 5px; }
    </style>
</head>
<body>
<div class="container">
    <h1>🔧 Suivi des réparations</h1>
    <div style="display:flex;gap:10px;align-items:center;margin-bottom:12px;">
        <a href="index.php?action=demandes" class="btn">← Retour aux demandes</a>
        <?php $currentDemande = $_GET['demande_id'] ?? null; ?>
        <a href="index.php?action=create_reponse<?= $currentDemande ? '&demande_id=' . (int)$currentDemande : '' ?>" class="btn">➕ Ajouter réponse</a>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>Demande N°</th>
                <th>Client</th>
                <th>Type</th>
                <th>Gouvernorat / Atelier</th>
                <th>Montant</th>
                <th>Message</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if(empty($reponses)): ?>
                <tr><td colspan="8" style="text-align:center;">Aucune réponse</td></tr>
            <?php else: ?>
                <?php foreach($reponses as $r): ?>
                <tr>
                    <td><strong>#<?= $r['demande_id'] ?></strong></td>
                    <td><?= htmlspecialchars($r['client_nom'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($r['type_nom'] ?? '—') ?> <?php if(!empty($r['type_categorie'])): ?>(<?= htmlspecialchars($r['type_categorie']) ?>)<?php endif; ?></td>
                    <td><?= htmlspecialchars($r['atelier_gouv'] ?? '-') ?> <?= !empty($r['atelier_nom']) ? ' / ' . htmlspecialchars($r['atelier_nom']) : '' ?></td>
                    <td><?= isset($r['montant']) && $r['montant'] !== null ? number_format($r['montant'], 3, ',', ' ') . ' TND' : '-' ?></td>
                    <td>
                        <?php if(!empty($r['message_admin'])): ?>
                            <?= nl2br(htmlspecialchars($r['message_admin'])) ?>
                        <?php else: ?>
                            <?php if($r['statut_voiture'] == 'pret'): ?>
                                <div class="msg-pret">✅ Voiture PRÊTE</div>
                            <?php elseif($r['statut_voiture'] == 'termine'): ?>
                                <div class="msg-pret">✅ Réparation terminée</div>
                            <?php elseif($r['statut_voiture'] == 'en_cours'): ?>
                                <div class="msg-cours">🔧 En réparation - Reste <?= $r['temps_restant'] ?? '?' ?> jours</div>
                            <?php else: ?>
                                <div class="msg-attente">⏳ En attente</div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </td>
                    <td><?= date('d/m/Y', strtotime($r['created_at'])) ?></td>
                    <td>
                        <a href="index.php?action=show_demande&id=<?= $r['demande_id'] ?>" class="btn">👁️ Voir</a>
                        <a href="index.php?action=edit_reponse&id=<?= $r['id'] ?>" class="btn">✏️ Modifier</a>
                        <a href="index.php?action=delete_reponse&id=<?= $r['id'] ?>" class="btn" onclick="return confirm('Supprimer cette réponse ?')">🗑️ Supprimer</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>