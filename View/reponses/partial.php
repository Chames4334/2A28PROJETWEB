<?php
// Partial list of responses (expects $reponses to be defined)
if (!isset($reponses)) { echo '<div>Aucune donnée.</div>'; return; }
?>
<div style="max-height:520px;overflow:auto;padding:8px;">
    <table style="width:100%;border-collapse:collapse">
        <thead>
            <tr>
                <th style="text-align:left;padding:8px">Demande N°</th>
                <th style="text-align:left;padding:8px">Client</th>
                <th style="text-align:left;padding:8px">Type</th>
                <th style="text-align:left;padding:8px">Atelier</th>
                <th style="text-align:left;padding:8px">Montant</th>
                <th style="text-align:left;padding:8px">Message</th>
                <th style="text-align:left;padding:8px">Date</th>
            </tr>
        </thead>
        <tbody>
            <?php if(empty($reponses)): ?>
                <tr><td colspan="7" style="text-align:center;padding:12px">Aucune réponse</td></tr>
            <?php else: foreach($reponses as $r): ?>
                <tr>
                    <td style="padding:8px">#<?= $r['demande_id'] ?></td>
                    <td style="padding:8px"><?= htmlspecialchars($r['client_nom'] ?? '-') ?></td>
                    <td style="padding:8px"><?= htmlspecialchars($r['type_nom'] ?? '—') ?></td>
                    <td style="padding:8px"><?= htmlspecialchars(($r['atelier_gouv'] ?? '-') . (!empty($r['atelier_nom']) ? ' / '.$r['atelier_nom'] : '')) ?></td>
                    <td style="padding:8px"><?= isset($r['montant']) && $r['montant']!==null ? number_format($r['montant'],3,',',' ') . ' TND' : '-' ?></td>
                    <td style="padding:8px"><?= htmlspecialchars(substr($r['message_admin'] ?? '',0,80)) ?></td>
                    <td style="padding:8px"><?= date('d/m/Y', strtotime($r['created_at'])) ?></td>
                </tr>
            <?php endforeach; endif; ?>
        </tbody>
    </table>
</div>
