<style>
    h2 { color: #0A2F6C; }
    .table-container { background: white; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); padding: 20px; }
    table { width: 100%; border-collapse: collapse; }
    th { border-bottom: 2px solid #0A2F6C; padding: 10px; text-align: left; }
    td { padding: 10px; border-bottom: 1px solid #eee; }
    .qr-box { background: #eee; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: 5px; cursor: help; }
</style>

<div class="table-container">
    <h2>Historique des déclarations</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th><th>Date</th><th>Lieu</th><th>Statut</th><th>QR Code</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($constats as $c): ?>
            <tr>
                <td>#<?= $c['id'] ?></td>
                <td><?= $c['date_accident'] ?></td>
                <td><?= $c['lieu_accident'] ?></td>
                <td><b style="color:#2E7D32"><?= $c['statut'] ?></b></td>
                <td><div class="qr-box" title="Scanner pour voir le dossier">QR</div></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>