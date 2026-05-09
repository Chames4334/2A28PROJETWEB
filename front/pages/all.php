<?php
require_once __DIR__ . '/../../Model/Config/Database.php';
$database = new Database();
$db = $database->getConnection();

// Recent demandes
$q1 = "SELECT * FROM demande_constat ORDER BY created_at DESC LIMIT 50";
$s1 = $db->prepare($q1); $s1->execute(); $demandes = $s1->fetchAll(PDO::FETCH_ASSOC);

// Recent réponses
$q2 = "SELECT r.*, t.nom as type_nom, t.categorie as type_categorie, a.nom as atelier_nom, a.gouvernorat as atelier_gouv, CONCAT(d.prenom,' ',d.nom) as client_nom FROM reponse_constat r LEFT JOIN type_reponse t ON r.type_reponse_id = t.id LEFT JOIN ateliers a ON r.id_atelier = a.id LEFT JOIN demande_constat d ON r.demande_id = d.id ORDER BY r.created_at DESC LIMIT 50";
$s2 = $db->prepare($q2); $s2->execute(); $reponses = $s2->fetchAll(PDO::FETCH_ASSOC);

// Ateliers
$q3 = "SELECT * FROM ateliers ORDER BY gouvernorat, nom";
$s3 = $db->prepare($q3); $s3->execute(); $ateliers = $s3->fetchAll(PDO::FETCH_ASSOC);

// Types
$q4 = "SELECT * FROM type_reponse ORDER BY id";
$s4 = $db->prepare($q4); $s4->execute(); $types = $s4->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Front - Portail</title>
    <link rel="stylesheet" href="/gs_assurance/front/assets/css/style-front.css">
    <script>
        const BASE_PATH = '/gs_assurance';
        function toggleDecl() {
            const f = document.getElementById('declForm');
            f.style.display = f.style.display === 'block' ? 'none' : 'block';
            if(f.style.display === 'block') f.scrollIntoView({behavior:'smooth'});
        }
        function onGouvChange(el){
            const g = el.value; const sel = document.getElementById('id_atelier');
            const err = document.getElementById('ajaxError'); err.textContent='';
            sel.innerHTML = '<option>Chargement...</option>';
            fetch(BASE_PATH + '/ajax_get_ateliers.php?gouvernorat='+encodeURIComponent(g))
                .then(r=>{ if(!r.ok) throw new Error('Erreur réseau'); return r.json(); })
                .then(list=>{
                    sel.innerHTML = '<option value="">-- Choisir --</option>';
                    list.forEach(a=>{ const o=document.createElement('option'); o.value=a.id; o.text=a.nom + (a.adresse? ' - ' + a.adresse : ''); sel.appendChild(o); });
                }).catch(e=>{ sel.innerHTML='<option value="">Aucun</option>'; err.textContent='Impossible de charger les ateliers (vérifier le serveur).'; });
        }

        function onTypeChange() {
            const atelierBlock = document.getElementById('atelierBlock');
            const montantBlock = document.getElementById('montantBlock');
            const mode = document.querySelector('input[name="type_mode"]:checked');
            if(!mode) { atelierBlock.style.display='none'; montantBlock.style.display='none'; return; }
            if(mode.value === 'atelier') { atelierBlock.style.display='block'; montantBlock.style.display='none'; }
            else if(mode.value === 'remboursement') { atelierBlock.style.display='none'; montantBlock.style.display='block'; }
        }

        window.addEventListener('DOMContentLoaded', ()=>{
            // wire radios
            document.querySelectorAll('input[name="type_mode"]').forEach(r=>r.addEventListener('change', onTypeChange));
            onTypeChange();
        });
    </script>
</head>
<body>
<div class="container">
    <header>
        <h1>AS ASSURANCE - Portail</h1>
        <div>
            <nav>
                <a href="index.php">Back-office</a>
                <a href="index.php?action=front_all">Portail</a>
                <a href="index.php?action=declaration">Déclaration (page dédiée)</a>
            </nav>
        </div>
    </header>

    <div style="margin-bottom:12px">
        <a class="btn btn-primary" href="javascript:toggleDecl()">➕ Nouvelle déclaration</a>
        <span class="small"> (ouvre le formulaire ci-contre)</span>
    </div>

    <div class="grid">
        <div>
            <div class="panel">
                <h3>Demandes récentes</h3>
                <table class="data-table">
                    <thead><tr><th>#</th><th>Client</th><th>Téléphone</th><th>Date</th><th>Actions</th></tr></thead>
                    <tbody>
                    <?php foreach($demandes as $d): ?>
                        <tr>
                            <td><?= $d['id'] ?></td>
                            <td><?= htmlspecialchars($d['prenom'].' '.$d['nom']) ?></td>
                            <td><?= htmlspecialchars($d['telephone']) ?></td>
                            <td><?= $d['created_at'] ?></td>
                            <td><a class="btn" href="<?= 'index.php?action=show_demande&id='.$d['id'] ?>">Voir</a></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="panel" style="margin-top:12px">
                <h3>Réponses récentes</h3>
                <table class="data-table">
                    <thead><tr><th>#</th><th>Demande</th><th>Type</th><th>Montant</th><th>Actions</th></tr></thead>
                    <tbody>
                    <?php foreach($reponses as $r): ?>
                        <tr>
                            <td><?= $r['id'] ?></td>
                            <td><?= htmlspecialchars($r['client_nom'] ?? ('#'.$r['demande_id'])) ?></td>
                            <td><?= htmlspecialchars($r['type_nom'] ?? '—') ?></td>
                            <td><?= isset($r['montant']) ? number_format($r['montant'],3,',',' ') . ' TND' : '-' ?></td>
                            <td><a class="btn" href="<?= 'index.php?action=edit_reponse&id='.$r['id'] ?>">Modifier</a></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <aside>
            <div class="panel">
                <h3>Formulaire de déclaration</h3>
                <div id="declForm" style="display:none;">
                    <form method="POST" action="index.php?action=store_demande">
                        <div class="form-group"><label>Nom</label><input name="nom" placeholder="Nom" required></div>
                        <div class="row-2"><div class="form-group"><label>Prénom</label><input name="prenom" placeholder="Prénom" required></div><div class="form-group"><label>Téléphone</label><input name="telephone" placeholder="Téléphone" required></div></div>
                        <div class="form-group"><label>Email</label><input name="email" type="email" placeholder="Email" required></div>
                        <div class="row-2"><div class="form-group"><label>Lieu</label><input name="lieu_accident" placeholder="Lieu de l'accident" required></div><div class="form-group"><label>Date</label><input name="date_accident" type="date" required></div></div>
                        <div class="form-group"><label>Description</label><textarea name="description" rows="4" placeholder="Description"></textarea></div>
                        <div class="form-group"><label>Type</label>
                            <label style="margin-right:12px"><input type="radio" name="type_mode" value="atelier"> Atelier</label>
                            <label><input type="radio" name="type_mode" value="remboursement"> Remboursement</label>
                        </div>
                        <div id="atelierBlock" class="form-group" style="display:none">
                            <label>Gouvernorat</label>
                            <select name="gouvernorat" onchange="onGouvChange(this)">
                                <option value="">-- Gouvernorat --</option>
                                <?php
                                $gq = $db->prepare("SELECT DISTINCT gouvernorat FROM ateliers WHERE gouvernorat IS NOT NULL AND gouvernorat <> '' ORDER BY gouvernorat"); $gq->execute(); $glist = $gq->fetchAll(PDO::FETCH_COLUMN);
                                foreach($glist as $gg) echo '<option value="'.htmlspecialchars($gg).'">'.htmlspecialchars($gg).'</option>';
                                ?>
                            </select>
                            <label style="margin-top:8px">Atelier</label>
                            <select id="id_atelier" name="id_atelier"><option value="">-- Atelier --</option></select>
                        </div>
                        <div id="montantBlock" class="form-group" style="display:none">
                            <label>Montant (TND)</label>
                            <input name="montant_client" type="number" step="0.01" placeholder="Montant (si remboursement)">
                        </div>
                        <div id="ajaxError" class="small" style="color:#ffdddd;margin-bottom:8px"></div>
                        <div class="form-group"><button class="btn btn-primary" type="submit">Envoyer déclaration</button></div>
                    </form>
                </div>
                <p class="small">Vous pouvez aussi utiliser la page <a href="index.php?action=declaration">Déclaration</a> pour un formulaire complet.</p>
            </div>
        </aside>
    </div>

    <footer style="margin-top:18px;text-align:center;color:#777">© AS Assurance</footer>

</div>
</body>
</html>