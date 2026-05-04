<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Détails de la demande</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #6FAF4C 0%, #A67C52 100%); min-height: 100vh; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; background: white; border-radius: 15px; padding: 30px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); }
        h1 { color: #A67C52; margin-bottom: 20px; border-left: 5px solid #6FAF4C; padding-left: 15px; }
        .detail-card { background: #f8f9fa; border-radius: 10px; padding: 20px; margin: 20px 0; }
        .detail-card p { padding: 10px; border-bottom: 1px solid #ddd; }
        .detail-card p:last-child { border-bottom: none; }
        .status { padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; }
        .status-soumis { background: #ffc107; color: #333; }
        .status-en_cours { background: #A67C52; color: white; }
        .status-accepte { background: #28a745; color: white; }
        .status-refuse { background: #dc3545; color: white; }
        .btn { padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; display: inline-block; font-size: 14px; margin-right: 10px; transition: transform 0.2s; }
        .btn:hover { transform: translateY(-2px); }
        .btn-primary { background: #6FAF4C; color: white; }
        .btn-secondary { background: #6c757d; color: white; }
        .btn-danger { background: #dc3545; color: white; }
    </style>
</head>
<body>
<div class="container">
    <h1>📄 Détails de la demande #<?= $this->demande->getId() ?></h1>
    
    <div class="detail-card">
        <p><strong>Nom :</strong> <?= htmlspecialchars($this->demande->getNom()) ?></p>
        <p><strong>Prénom :</strong> <?= htmlspecialchars($this->demande->getPrenom()) ?></p>
        <p><strong>Email :</strong> <?= htmlspecialchars($this->demande->getEmail()) ?></p>
        <p><strong>Téléphone :</strong> <?= htmlspecialchars($this->demande->getTelephone()) ?></p>
        <p><strong>Lieu de l'accident :</strong> <?= htmlspecialchars($this->demande->getLieuAccident()) ?></p>
        <p><strong>Date de l'accident :</strong> <?= $this->demande->getDateAccident() ?></p>
        <p><strong>Description :</strong><br><?= nl2br(htmlspecialchars($this->demande->getDescription())) ?></p>
        <p><strong>Statut :</strong> <span class="status status-<?= $this->demande->getStatut() ?>"><?= $this->demande->getStatut() ?></span></p>
        <p><strong>Créé le :</strong> <?= $this->demande->getCreatedAt() ?></p>
    </div>
    
    <div>
        <a href="index.php?action=generate_pdf&id=<?= $this->demande->getId() ?>" class="btn btn-primary" target="_blank" style="background: #dc3545;">📄 PDF</a>
        <a href="index.php?action=edit_demande&id=<?= $this->demande->getId() ?>" class="btn btn-primary">✏️ Modifier</a>
        <a href="index.php?action=delete_demande&id=<?= $this->demande->getId() ?>" class="btn btn-danger" onclick="return confirm('Supprimer cette demande ?')">🗑️ Supprimer</a>
        <a href="index.php?action=demandes" class="btn btn-secondary">← Retour</a>
    </div>
</div>
    
    <?php if (!empty($reponse)): ?>
    <div class="container" style="margin-top:18px; max-width:800px;">
        <h2 style="margin-bottom:10px;color:#6FAF4C;">💬 Réponse enregistrée</h2>
        <div class="detail-card">
            <p><strong>Type :</strong> <?= htmlspecialchars($reponse['type_nom'] ?? '—') ?><?php if(!empty($reponse['type_categorie'])): ?> (<?= htmlspecialchars($reponse['type_categorie']) ?>)<?php endif; ?></p>
            <?php if(!empty($reponse['type_categorie']) && $reponse['type_categorie'] === 'atelier'): ?>
                <p><strong>Gouvernorat :</strong> <?= htmlspecialchars($reponse['atelier_gouv'] ?? '—') ?></p>
                <p><strong>Atelier :</strong> <?= htmlspecialchars($reponse['atelier_nom'] ?? '—') ?></p>
                <p><strong>Montant :</strong> <?= isset($reponse['montant']) && $reponse['montant'] !== null ? number_format($reponse['montant'], 3, ',', ' ') . ' TND' : '—' ?></p>
            <?php elseif(!empty($reponse['type_categorie']) && $reponse['type_categorie'] === 'remboursement'): ?>
                <p><strong>Montant remboursé proposé :</strong> <?= isset($reponse['montant']) ? number_format($reponse['montant'], 3, ',', ' ') . ' TND' : '—' ?></p>
            <?php else: ?>
                <p><strong>Atelier :</strong> <?= htmlspecialchars($reponse['atelier_nom'] ?? '—') ?></p>
                <p><strong>Montant :</strong> <?= isset($reponse['montant']) ? number_format($reponse['montant'], 3, ',', ' ') . ' TND' : '—' ?></p>
            <?php endif; ?>
            <p><strong>Message admin :</strong><br><?= nl2br(htmlspecialchars($reponse['message_admin'] ?? 'Aucun message')) ?></p>
        </div>
    </div>
    <?php endif; ?>

    
</body>
</html>