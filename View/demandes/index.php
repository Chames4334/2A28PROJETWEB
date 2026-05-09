<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Demandes de constat</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, rgba(111,175,76,0.12) 0%, rgba(255,255,255,0.9) 48%, rgba(166,124,82,0.08) 100%); min-height: 100vh; padding: 20px; color: #2b2b2b; }
        @keyframes adminAurora { 0% { background-position: 0% 0%, 100% 0%, 100% 100%, 0% 100%, 50% 50%; } 50% { background-position: 12% 8%, 88% 18%, 78% 90%, 8% 84%, 50% 50%; } 100% { background-position: 20% 14%, 74% 8%, 92% 76%, 18% 96%, 50% 50%; } }
        .container { max-width: 1400px; margin: 0 auto; background: rgba(255,255,255,0.85); border-radius: 18px; padding: 28px; box-shadow: 0 12px 36px rgba(0,0,0,0.08); backdrop-filter: blur(6px); border: 1px solid rgba(166,124,82,0.08); }
        h1 { color: #2b2b2b; margin-bottom: 18px; border-left: 4px solid #6faf4c; padding-left: 12px; }
        .action-buttons { margin-bottom: 24px; display: flex; gap: 12px; flex-wrap: wrap; }
        .btn,
        .btn-view,
        .btn-edit,
        .btn-delete,
        .modal-buttons button,
        #contactModal button {
            padding: 9px 18px;
            border: 1px solid rgba(0,0,0,0.06);
            border-radius: 999px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-size: 14px;
            font-weight: 600;
            letter-spacing: 0.2px;
            color: #fff;
            box-shadow: 0 8px 22px rgba(0,0,0,0.06);
            backdrop-filter: blur(6px);
            transition: transform 0.25s ease, box-shadow 0.25s ease, filter 0.25s ease, background 0.25s ease;
        }
        .btn:hover,
        .btn-view:hover,
        .btn-edit:hover,
        .btn-delete:hover,
        .modal-buttons button:hover,
        #contactModal button:hover {
            transform: translateY(-3px) scale(1.01);
            box-shadow: 0 18px 34px rgba(166, 124, 82, 0.28);
            filter: saturate(1.06);
        }
        .btn-success {
            background: linear-gradient(135deg, #6FAF4C 0%, #89C56B 100%);
            color: white;
        }
        .btn-info,
        .btn-view,
        #contactModal button[onclick*="sms"] {
            background: linear-gradient(135deg, #a67c52 0%, #6faf4c 100%) !important;
            color: white;
        }
        .btn-warning {
            background: linear-gradient(135deg, #ffd86f 0%, #f7b733 100%);
            color: #433108;
            box-shadow: 0 12px 30px rgba(247, 183, 51, 0.24);
        }
            background: linear-gradient(135deg, #6faf4c 0%, #ffffff 45%, #a67c52 100%);
            color: white;
        }
        .btn-edit,
        #contactModal button[onclick*="email"] {
            background: linear-gradient(135deg, #6FAF4C 0%, #A67C52 100%) !important;
            color: white;
        }
        .btn-delete,
        .btn-confirm {
            background: linear-gradient(135deg, #A67C52 0%, #8E5F37 100%);
            color: white;
        }
        .btn-cancel-modal {
            background: linear-gradient(135deg, #A67C52 0%, #6FAF4C 100%);
            color: white;
        }
        #contactModal button[onclick*="lesdeux"] {
            background: linear-gradient(135deg, #6FAF4C 0%, #A67C52 100%) !important;
            color: white;
        }
        #contactModal button[onclick*="closeContactModal"] {
            background: linear-gradient(135deg, #A67C52 0%, #6FAF4C 100%) !important;
            color: white;
        }
        .data-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-top: 20px;
            background: transparent;
            border: 1px solid rgba(166,124,82,0.28);
            border-radius: 22px;
            overflow: hidden;
            box-shadow: 0 18px 38px rgba(166,124,82,0.18);
            backdrop-filter: blur(4px);
        }
        .data-table thead {
            background: linear-gradient(135deg, rgba(111,175,76,0.62) 0%, rgba(166,124,82,0.78) 100%);
            color: #ffffff;
        }
        .data-table th,
        .data-table td {
            padding: 14px 16px;
            text-align: left;
            border-bottom: 1px solid rgba(166,124,82,0.12);
            color: #2b2b2b;
        }
        .data-table th {
            font-weight: 700;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 0.08em;
        }
        .data-table tbody tr {
            background: rgba(255,255,255,0.95);
            transition: background 0.15s ease, transform 0.15s ease;
        }
        .data-table tbody tr:nth-child(even) {
            background: rgba(245,245,245,0.95);
        }
        .data-table tbody tr:hover {
            background: rgba(239,244,236,0.95);
        }
        .data-table tbody tr:last-child td {
            border-bottom: none;
        }
        .actions { display: flex; gap: 8px; flex-wrap: wrap; align-items: center; }
        .success { background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 20px; border-left: 4px solid #28a745; }
        
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            justify-content: center;
            align-items: center;
        }
        .modal-content {
            background: white;
            padding: 25px;
            border-radius: 10px;
            text-align: center;
            max-width: 400px;
            margin: auto;
            box-shadow: 0 5px 20px rgba(0,0,0,0.3);
            border-top: 4px solid #dc3545;
        }
        .modal-content p { margin-bottom: 20px; font-size: 16px; color: #333; }
        .modal-buttons { display: flex; gap: 10px; justify-content: center; flex-wrap: wrap; }
    </style>
</head>
<!-- Modal Contacter -->
<div id="contactModal" class="modal" style="display:none;position:fixed;z-index:1000;left:0;top:0;width:100%;height:100%;background:rgba(0,0,0,0.5);justify-content:center;align-items:center;">
    <div style="background:white;padding:30px;border-radius:15px;max-width:400px;text-align:center;">
        <h3 style="color:#A67C52;margin-bottom:20px;">📱 Envoyer une notification</h3>
        <p style="margin-bottom:20px;">Choisissez le moyen de contact :</p>
        <div style="display:flex;gap:15px;justify-content:center;">
            <button onclick="envoyerNotification('sms')" style="background:#A67C52;color:white;padding:10px 20px;border:none;border-radius:5px;cursor:pointer;">💬 SMS</button>
            <button onclick="envoyerNotification('email')" style="background:#28a745;color:white;padding:10px 20px;border:none;border-radius:5px;cursor:pointer;">📧 Email</button>
            <button onclick="envoyerNotification('lesdeux')" style="background:#6FAF4C;color:white;padding:10px 20px;border:none;border-radius:5px;cursor:pointer;">📱📧 Les deux</button>
        </div>
        <button onclick="closeContactModal()" style="margin-top:20px;background:#6c757d;color:white;padding:8px 20px;border:none;border-radius:5px;cursor:pointer;">Fermer</button>
    </div>
</div>

<script>
function openContactModal() {
    document.getElementById('contactModal').style.display = 'flex';
}
function closeContactModal() {
    document.getElementById('contactModal').style.display = 'none';
}
function envoyerNotification(type) {
    closeContactModal();
    let url = 'index.php?action=send_contact&type=' + type;
    
    if (type === 'sms' || type === 'lesdeux') {
        var telephone = prompt("Entrez le numéro de téléphone (ex: 97123456 ou +21697123456) :");
        if (!telephone || telephone.trim() === '') return;
        url += '&tel=' + encodeURIComponent(telephone.trim());
    }
    
    if (type === 'email' || type === 'lesdeux') {
        var email = prompt("Entrez l'adresse email de destination :");
        if (!email || email.trim() === '') return;
        url += '&email=' + encodeURIComponent(email.trim());
    }
    
    window.location.href = url;
}
</script>
<body>
<div class="container">
    <h1>📋 Demandes de Constat</h1>
    
    <?php if(isset($_GET['success'])): ?>
        <div class="success">✅ Demande créée avec succès !</div>
    <?php endif; ?>
    <?php if(isset($_GET['update'])): ?>
        <div class="success">✏️ Demande modifiée avec succès !</div>
    <?php endif; ?>
    <?php if(isset($_GET['delete'])): ?>
        <div class="success">🗑️ Demande supprimée avec succès !</div>
    <?php endif; ?>
    <?php if(isset($_SESSION['notif_msg'])): ?>
        <div class="success" style="background: #f8f9fa; color: #333; border-left-color: #A67C52;">
            <?= htmlspecialchars($_SESSION['notif_msg']) ?>
        </div>
        <?php unset($_SESSION['notif_msg']); ?>
    <?php endif; ?>
    
    <div class="action-buttons">
        <a href="index.php?action=create_demande" class="btn btn-success">➕ Nouvelle Demande</a>
        <?php
        // Small summary for the Réponses button: count of en_cours and minimal temps_restant
        try {
            require_once __DIR__ . '/../../Model/Config/Database.php';
            $database = new Database();
            $db = $database->getConnection();
            $q = "SELECT 
                    SUM(CASE WHEN r.statut_voiture = 'en_cours' THEN 1 ELSE 0 END) as en_cours_cnt,
                    MIN(NULLIF(r.temps_restant, '')) as min_temps
                  FROM reponse_constat r";
            $s = $db->prepare($q);
            $s->execute();
            $summary = $s->fetch(PDO::FETCH_ASSOC);
            $enCours = isset($summary['en_cours_cnt']) ? (int)$summary['en_cours_cnt'] : 0;
            $minTemps = $summary['min_temps'];
        } catch (Exception $e) {
            $enCours = 0; $minTemps = null;
        }
        $badge = '';
        if ($enCours > 0) {
            $badge = ' — ' . $enCours . " en cours" . ($minTemps ? (" • " . $minTemps . " jours restants") : '');
        }
        ?>
        <a href="index.php?action=type_reponses&partial=1" class="btn btn-info open-reponses">💬 Réponses<?= htmlspecialchars($badge) ?></a>
        <a href="index.php?action=stats_type_reponse" class="btn btn-info">📊 Statistiques</a>
        <a href="javascript:void(0)" onclick="openContactModal()" class="btn btn-info">📱 Contacter</a>
    </div>
    
    <!-- Barre recherche + tri -->
    <div style="display:flex;align-items:center;gap:10px;margin-bottom:18px;flex-wrap:wrap;">
        <input type="text" id="searchInput" placeholder="Rechercher par nom..."
            style="padding:9px 16px;border:1.5px solid #ddd;border-radius:8px;font-size:14px;
                   background:#fff;color:#333;min-width:220px;outline:none;
                   transition:border-color 0.2s,box-shadow 0.2s;"
            onfocus="this.style.borderColor='#6FAF4C';this.style.boxShadow='0 0 0 3px rgba(111,175,76,0.13)'"
            onblur="this.style.borderColor='#ddd';this.style.boxShadow='none'">

        <select id="sortSelect"
            style="padding:9px 14px;border:1.5px solid #ddd;border-radius:8px;font-size:14px;
                   background:#fff;color:#333;cursor:pointer;outline:none;
                   transition:border-color 0.2s;">
            <option value="">Trier par défaut</option>
            <option value="nom_asc">Nom A→Z</option>
            <option value="nom_desc">Nom Z→A</option>
            <option value="date_asc">Date ↑</option>
            <option value="date_desc">Date ↓</option>
            <option value="lieu_asc">Lieu A→Z</option>
        </select>

        <button onclick="applySearchSort()"
            style="padding:9px 20px;background:linear-gradient(135deg,#6FAF4C,#4d8a30);
                   color:white;border:none;border-radius:8px;font-size:14px;font-weight:600;
                   cursor:pointer;display:inline-flex;align-items:center;gap:7px;
                   box-shadow:0 4px 14px rgba(111,175,76,0.32);transition:all 0.2s;">
            🔍 Filtrer
        </button>

        <span id="resultCount" style="font-size:13px;color:#888;margin-left:4px;"></span>
    </div>

    <table class="data-table" id="demandeTable">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Email</th>
                <th>Téléphone</th>
                <th>Lieu</th>
                <th>Date accident</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($demandes as $d): ?>
            <tr>
                <td><?= htmlspecialchars($d['nom']) ?></td>
                <td><?= htmlspecialchars($d['prenom']) ?></td>
                <td><?= htmlspecialchars($d['email']) ?></td>
                <td><?= htmlspecialchars($d['telephone']) ?></td>
                <td><?= htmlspecialchars($d['lieu_accident']) ?></td>
                <td><?= $d['date_accident'] ?></td>
                <td class="actions">
                    <a href="index.php?action=show_demande&id=<?= $d['id'] ?>" class="btn-view">👁️ Voir</a>
                    <a href="index.php?action=generate_pdf&id=<?= $d['id'] ?>" target="_blank" class="btn-view" style="background: #dc3545;">📄 PDF</a>
                    <a href="index.php?action=edit_demande&id=<?= $d['id'] ?>" class="btn-edit">✏️ Modifier</a>
                    <a href="index.php?action=type_reponses&partial=1&demande_id=<?= $d['id'] ?>" class="btn-view open-reponses" style="background:#8fbf7a;">💬 Réponses</a>
                    <button type="button" class="btn-delete" onclick="openModal(<?= $d['id'] ?>, '<?= htmlspecialchars($d['nom'] . ' ' . $d['prenom']) ?>')">🗑️ Supprimer</button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div id="deleteModal" class="modal">
    <div class="modal-content">
        <p>⚠️ Êtes-vous sûr de vouloir supprimer cette demande ?</p>
        <p id="modalDemandeName" style="font-weight: bold; color: #dc3545;"></p>
        <div class="modal-buttons">
            <button class="btn-confirm" id="confirmDelete">Oui, supprimer</button>
            <button class="btn-cancel-modal" id="cancelDelete">Annuler</button>
        </div>
    </div>
</div>

<!-- AJAX modal for responses -->
<div id="ajaxModal" class="modal" style="display:none;">
    <div class="modal-content" id="ajaxModalContent" style="max-width:900px;width:95%;background:#fff;padding:18px;border-radius:12px;">
        <div style="text-align:left;margin-bottom:12px;display:flex;justify-content:space-between;align-items:center;">
            <h3 style="margin:0;color:#6FAF4C">💬 Réponses</h3>
            <button class="btn-cancel-modal" onclick="closeAjaxModal()">Fermer</button>
        </div>
        <div id="ajaxBody">Chargement…</div>
    </div>
</div>

<script>
// Intercept clicks on response links and load partial into modal
document.addEventListener('click', function(e){
    var target = e.target.closest('a.open-reponses');
    if (!target) return;
    e.preventDefault();
    var url = target.getAttribute('href');
    var modal = document.getElementById('ajaxModal');
    var body = document.getElementById('ajaxBody');
    modal.style.display = 'flex';
    body.innerHTML = 'Chargement en cours...';
    fetch(url)
        .then(function(r){ if(!r.ok) throw new Error('Network'); return r.text(); })
        .then(function(html){ body.innerHTML = html; enableModalAjaxHandlers(); })
        .catch(function(){ body.innerHTML = '<div style="padding:20px;color:#a00">Impossible de charger le contenu.</div>'; });
});
function closeAjaxModal(){ document.getElementById('ajaxModal').style.display = 'none'; }
// close when clicking outside
window.addEventListener('click', function(e){ var m = document.getElementById('ajaxModal'); if(e.target==m) closeAjaxModal(); });

// After loading partial into modal, intercept its search form and links that request partial content
function enableModalAjaxHandlers(){
    var modal = document.getElementById('ajaxModal');
    var body = document.getElementById('ajaxBody');
    // Intercept the search form inside modal
    var form = modal.querySelector('#typesSearchForm');
    if(form){
        form.addEventListener('submit', function(ev){
            ev.preventDefault();
            var params = new URLSearchParams(new FormData(form));
            var url = form.getAttribute('action') + '?' + params.toString();
            body.innerHTML = 'Chargement…';
            fetch(url).then(function(r){ if(!r.ok) throw new Error('Network'); return r.text(); }).then(function(html){ body.innerHTML = html; enableModalAjaxHandlers(); }).catch(function(){ body.innerHTML = '<div style="padding:20px;color:#a00">Erreur de chargement.</div>'; });
        });
    }
    // Intercept internal partial links (e.g., pagination or filters) that include partial=1
    var anchors = modal.querySelectorAll('a');
    anchors.forEach(function(a){
        var href = a.getAttribute('href');
        if(!href) return;
        if(href.indexOf('partial=1') !== -1){
            a.addEventListener('click', function(ev){ ev.preventDefault(); var url = href; body.innerHTML = 'Chargement…'; fetch(url).then(function(r){ if(!r.ok) throw new Error('Network'); return r.text(); }).then(function(html){ body.innerHTML = html; enableModalAjaxHandlers(); }).catch(function(){ body.innerHTML = '<div style="padding:20px;color:#a00">Erreur de chargement.</div>'; }); });
        }
    });
}
</script>

<script>
    var deleteId = null;
    function openModal(id, nom) {
        deleteId = id;
        document.getElementById('modalDemandeName').innerHTML = 'Demande - ' + nom;
        document.getElementById('deleteModal').style.display = 'flex';
    }
    function closeModal() {
        document.getElementById('deleteModal').style.display = 'none';
        deleteId = null;
    }
    document.getElementById('confirmDelete').onclick = function() {
        if (deleteId) window.location.href = 'index.php?action=delete_demande&id=' + deleteId;
    };
    document.getElementById('cancelDelete').onclick = function() { closeModal(); };
    window.onclick = function(event) {
        var modal = document.getElementById('deleteModal');
        if (event.target == modal) closeModal();
    };
    
    // Réponses quick view -> open show page in new tab
    function openShowResponse(id) {
        window.open('index.php?action=show_demande&id=' + id, '_blank');
    }
</script>

<script>
// ── Recherche + Tri tableau demandes ──
function getRows() {
    return Array.from(document.querySelectorAll('#demandeTable tbody tr'));
}

function applySearchSort() {
    var search = document.getElementById('searchInput').value.trim().toLowerCase();
    var sort   = document.getElementById('sortSelect').value;
    var rows   = getRows();
    var tbody  = document.querySelector('#demandeTable tbody');

    // Filtrer
    var visible = rows.filter(function(row) {
        if (!search) return true;
        var nom    = (row.cells[0] ? row.cells[0].textContent : '').toLowerCase();
        var prenom = (row.cells[1] ? row.cells[1].textContent : '').toLowerCase();
        var email  = (row.cells[2] ? row.cells[2].textContent : '').toLowerCase();
        var lieu   = (row.cells[4] ? row.cells[4].textContent : '').toLowerCase();
        return nom.includes(search) || prenom.includes(search) || email.includes(search) || lieu.includes(search);
    });

    var hidden = rows.filter(function(r) { return !visible.includes(r); });
    hidden.forEach(function(r) { r.style.display = 'none'; });
    visible.forEach(function(r) { r.style.display = ''; });

    // Trier les visibles
    if (sort) {
        visible.sort(function(a, b) {
            var va, vb;
            if (sort === 'nom_asc' || sort === 'nom_desc') {
                va = (a.cells[0] ? a.cells[0].textContent : '').trim().toLowerCase();
                vb = (b.cells[0] ? b.cells[0].textContent : '').trim().toLowerCase();
                return sort === 'nom_asc' ? va.localeCompare(vb) : vb.localeCompare(va);
            }
            if (sort === 'date_asc' || sort === 'date_desc') {
                va = a.cells[5] ? a.cells[5].textContent.trim() : '';
                vb = b.cells[5] ? b.cells[5].textContent.trim() : '';
                return sort === 'date_asc' ? va.localeCompare(vb) : vb.localeCompare(va);
            }
            if (sort === 'lieu_asc') {
                va = (a.cells[4] ? a.cells[4].textContent : '').trim().toLowerCase();
                vb = (b.cells[4] ? b.cells[4].textContent : '').trim().toLowerCase();
                return va.localeCompare(vb);
            }
            return 0;
        });
        visible.forEach(function(r) { tbody.appendChild(r); });
    }

    // Compteur
    document.getElementById('resultCount').textContent =
        visible.length + ' résultat' + (visible.length !== 1 ? 's' : '');
}

// Recherche en temps réel à la frappe
document.getElementById('searchInput').addEventListener('input', applySearchSort);
document.getElementById('sortSelect').addEventListener('change', applySearchSort);

// Init compteur
applySearchSort();
</script>

</body>
</html>