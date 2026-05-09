<?php
require_once '../../Model/Config/Database.php';
$database = new Database();
$db = $database->getConnection();

$query = "SELECT d.*, r.statut_voiture, r.temps_restant, r.message_admin 
          FROM demande_constat d 
          LEFT JOIN reponse_constat r ON d.id = r.demande_id 
          ORDER BY d.created_at DESC";
$stmt = $db->prepare($query);
$stmt->execute();
$demandes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Historique des déclarations</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: linear-gradient(135deg, rgba(111,175,76,0.08) 0%, rgba(255,255,255,0.98) 48%, rgba(166,124,82,0.04) 100%); padding: 20px; color: #2b2b2b; }
        .container { max-width: 1200px; margin: 0 auto; background: rgba(255,255,255,0.98); border-radius: 14px; padding: 24px; box-shadow: 0 12px 36px rgba(0,0,0,0.06); }
        h1 { color: #6FAF4C; margin-bottom: 18px; border-left: 4px solid #6FAF4C; padding-left: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: linear-gradient(135deg, #6FAF4C, #A67C52); color: white; padding: 12px; text-align: left; }
        td { padding: 12px; border-bottom: 1px solid #ddd; }
        .status-termine { background: #28a745; color: white; padding: 4px 12px; border-radius: 20px; font-size: 12px; display: inline-block; }
        .status-en_cours { background: #ffc107; color: #333; padding: 4px 12px; border-radius: 20px; font-size: 12px; display: inline-block; }
        .status-en_attente { background: #A67C52; color: white; padding: 4px 12px; border-radius: 20px; font-size: 12px; display: inline-block; }
        .btn { background: #6FAF4C; color: white; padding: 5px 15px; text-decoration: none; border-radius: 5px; }
        .message-box { background: #f8f9fa; padding: 10px; border-radius: 5px; font-size: 13px; }
    </style>
</head>
<body>
<div class="container">
    <h1>🚗 Historique de mes déclarations</h1>
    
    <table>
        <thead>
            <tr><th>N°</th><th>Lieu</th><th>Date accident</th><th>État de la voiture</th><th>Message</th><th>Action</th></tr>
        </thead>
        <tbody>
            <?php foreach($demandes as $d): ?>
            <tr>
                <td>#<?= $d['id'] ?></td>
                <td><?= htmlspecialchars($d['lieu_accident']) ?></td>
                <td><?= date('d/m/Y', strtotime($d['date_accident'])) ?></td>
                <td>
                    <?php if($d['statut_voiture'] == 'termine'): ?>
                        <span class="status-termine">✅ VOITURE PRÊTE</span>
                    <?php elseif($d['statut_voiture'] == 'en_cours'): ?>
                        <span class="status-en_cours">🔧 EN RÉPARATION - Reste <?= $d['temps_restant'] ?? '?' ?> jours</span>
                    <?php else: ?>
                        <span class="status-en_attente">⏳ EN ATTENTE DE PRISE EN CHARGE</span>
                    <?php endif; ?>
                </td>
                <td class="message-box">
                    <?= nl2br(htmlspecialchars($d['message_admin'] ?? 'Aucun message pour le moment')) ?>
                </td>
                <td><a href="show.php?id=<?= $d['id'] ?>" class="btn">Voir</a></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div style="margin-top:20px;">
        <a href="accueil.php" class="btn">← Retour</a>
        <a href="declaration.php" class="btn">📝 Nouvelle déclaration</a>
    </div>
</div>

</body>
</html>