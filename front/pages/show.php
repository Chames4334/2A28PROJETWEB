<?php
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$query = "SELECT * FROM demande_constat WHERE id = ?";
$stmt = $db->prepare($query);
$stmt->execute([$id]);
$demande = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$demande) {
    header("Location: index.php?action=historique");
    exit();
}

$query2 = "SELECT r.*, t.nom as type_nom, a.nom as atelier_nom
           FROM reponse_constat r
           LEFT JOIN type_reponse t ON r.type_reponse_id = t.id
           LEFT JOIN ateliers a ON r.id_atelier = a.id
           WHERE r.demande_id = ?";
$stmt2 = $db->prepare($query2);
$stmt2->execute([$id]);
$reponse = $stmt2->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>AS Assurance - Suivi #<?= $id ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #6FAF4C 0%, #A67C52 100%); min-height: 100vh; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 20px; padding: 30px; text-align: center; border-top: 4px solid #6FAF4C; }
        h1 { color: #A67C52; margin-bottom: 20px; }
        .message-box { background: #f8f9fa; border-radius: 10px; padding: 30px; margin: 20px 0; }
        .btn { display: inline-block; margin-top: 20px; padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; }
    </style>
</head>
<body>
<div class="container">
    <h1>🚗 Suivi de ma demande #<?= $id ?></h1>
    
    <div class="message-box">
        <?php if ($reponse): ?>
            <?php if (!empty($reponse['message_admin'])): ?>
                <p style="font-size: 18px;"><?= nl2br(htmlspecialchars($reponse['message_admin'])) ?></p>
            <?php else: ?>
                <p style="font-size: 18px;">✅ Votre demande a été traitée.</p>
            <?php endif; ?>
            <br>
            <a href="../../index.php?action=generate_pdf&id=<?= $id ?>" target="_blank" class="btn" style="background: #A67C52;">📥 Télécharger le PDF</a>
        <?php else: ?>
            <p style="font-size: 18px;">⏳ Votre demande est en cours de traitement.</p>
            <p>Un conseiller vous répondra dans les plus brefs délais.</p>
        <?php endif; ?>
    </div>
    
    <a href="index.php?action=historique" class="btn">← Retour</a>
</div>


</body>
</html>