<?php
// Partial fragment for types listing used inside AJAX modal.
// Expects $typeReponses to be provided by the controller.
?>
<div style="padding:6px 2px;">
    <div style="display:flex;justify-content:space-between;align-items:center;gap:10px;margin-bottom:12px;flex-wrap:wrap;">
        <div style="display:flex;gap:8px;align-items:center;">
            <a href="index.php?action=create_type_reponse" class="btn btn-success">➕ Nouveau type</a>
            <a href="index.php?action=demandes" class="btn btn-info">← Demandes</a>
        </div>

        <form id="typesSearchForm" method="GET" action="index.php" style="display:flex;gap:8px;align-items:center;">
            <input type="hidden" name="action" value="type_reponses">
            <input type="hidden" name="partial" value="1">
            <input type="text" name="search" placeholder="Rechercher par nom..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" style="padding:8px;border:1px solid #ddd;border-radius:6px;">
            <select name="sort" style="padding:8px;border:1px solid #ddd;border-radius:6px;">
                <option value="id_asc" <?= (isset($_GET['sort']) && $_GET['sort']=='id_asc')? 'selected':'' ?>>Trier par défaut</option>
                <option value="nom_asc" <?= (isset($_GET['sort']) && $_GET['sort']=='nom_asc')? 'selected':'' ?>>Nom (A-Z)</option>
                <option value="nom_desc" <?= (isset($_GET['sort']) && $_GET['sort']=='nom_desc')? 'selected':'' ?>>Nom (Z-A)</option>
                <option value="date_desc" <?= (isset($_GET['sort']) && $_GET['sort']=='date_desc')? 'selected':'' ?>>Plus récents</option>
                <option value="date_asc" <?= (isset($_GET['sort']) && $_GET['sort']=='date_asc')? 'selected':'' ?>>Plus anciens</option>
            </select>
            <button type="submit" class="btn btn-info">🔍 Filtrer</button>
        </form>
    </div>

    <div style="overflow:auto;max-height:440px;padding-right:8px;">
        <table style="width:100%;border-collapse:collapse;border-radius:12px;overflow:hidden">
            <thead style="background:linear-gradient(135deg, rgba(111,175,76,0.62), rgba(166,124,82,0.78));color:#fff;">
                <tr>
                    <th style="padding:12px;text-align:left">Nom</th>
                    <th style="padding:12px;text-align:left">Description</th>
                    <th style="padding:12px;text-align:left">Catégorie</th>
                    <th style="padding:12px;text-align:left">Créé le</th>
                    <th style="padding:12px;text-align:left">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($typeReponses)): ?>
                    <tr><td colspan="5" style="text-align:center;padding:18px;color:#666">Aucun type trouvé</td></tr>
                <?php else: foreach($typeReponses as $t): ?>
                    <tr style="background:rgba(255,255,255,0.9);">
                        <td style="padding:12px;vertical-align:top;"><strong><?= htmlspecialchars($t['nom']) ?></strong></td>
                        <td style="padding:12px;vertical-align:top;max-width:360px;"><?= htmlspecialchars(substr($t['description'],0,120)) ?><?= strlen($t['description'])>120? '...':'' ?></td>
                        <td style="padding:12px;vertical-align:top;"><?= ($t['categorie'] ?? '') == 'atelier' ? '🔧 Atelier' : '💰 Remboursement' ?></td>
                        <td style="padding:12px;vertical-align:top;"><?= htmlspecialchars($t['created_at'] ?? '') ?></td>
                        <td style="padding:12px;vertical-align:top;">
                            <div class="actions" style="display:flex;gap:8px;flex-wrap:wrap;">
                                <a href="index.php?action=show_type_reponse&id=<?= $t['id'] ?>" class="btn-view">👁️ Voir</a>
                                <a href="index.php?action=edit_type_reponse&id=<?= $t['id'] ?>" class="btn-edit">✏️ Modifier</a>
                                <a href="index.php?action=delete_type_reponse&id=<?= $t['id'] ?>" class="btn-delete" onclick="return confirm('Supprimer ce type ?')">🗑️ Supprimer</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>
