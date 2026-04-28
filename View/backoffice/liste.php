<?php
include_once '../../controller/ControlUser.php';
$ctrl = new ControlUser();
$users = $ctrl->listeUser();
$stats = [
    'total' => $ctrl->countUsers(),
    'active' => $ctrl->countByStatus('active'),
    'blocked' => $ctrl->countByStatus('blocked'),
    'pending' => $ctrl->countByStatus('pending')
];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des utilisateurs - Green Assurance</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/style.css">
    <style>
        /* Styles pour la recherche et le tri */
        .search-sort-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
            padding: 15px 25px;
            background: rgba(255,255,255,0.9);
            border-bottom: 1px solid #f0f0f0;
        }
        .search-box {
            display: flex;
            align-items: center;
            gap: 10px;
            flex: 2;
            min-width: 250px;
        }
        .search-box input {
            flex: 1;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 25px;
            font-size: 0.9rem;
            outline: none;
            transition: 0.3s;
        }
        .search-box input:focus {
            border-color: olivedrab;
            box-shadow: 0 0 5px rgba(107,142,35,0.3);
        }
        .search-box button {
            background: olivedrab;
            border: none;
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            cursor: pointer;
            transition: 0.3s;
        }
        .search-box button:hover {
            background: #5a7a26;
        }
        .sort-buttons {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        .sort-btn {
            background: #f3f3f3;
            border: none;
            padding: 8px 15px;
            border-radius: 20px;
            cursor: pointer;
            font-size: 0.8rem;
            transition: 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        .sort-btn.active {
            background: olivedrab;
            color: white;
        }
        .sort-btn:hover:not(.active) {
            background: #e0e0e0;
        }
        .reset-btn {
            background: #ff6b6b;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 20px;
            cursor: pointer;
            font-size: 0.8rem;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        .reset-btn:hover {
            background: #ee5a5a;
        }
        .th-sortable {
            cursor: pointer;
            user-select: none;
            transition: 0.2s;
        }
        .th-sortable:hover {
            background: #e8e8e8;
        }
        .th-sortable i {
            margin-left: 5px;
            font-size: 0.7rem;
            opacity: 0.5;
        }
        .th-sortable.asc i {
            opacity: 1;
            transform: rotate(0deg);
            display: inline-block;
        }
        .th-sortable.desc i {
            opacity: 1;
            transform: rotate(180deg);
            display: inline-block;
        }
        .no-result {
            text-align: center;
            padding: 50px;
            color: #999;
        }
        .filter-stats {
            font-size: 0.8rem;
            color: #888;
            padding: 10px 25px;
            background: #f9f9f9;
            border-bottom: 1px solid #f0f0f0;
        }
    </style>
</head>
<body>

<div class="animated-bg"></div>
<div class="floating-shape shape1"></div>
<div class="floating-shape shape2"></div>

<nav class="bord">
    <img src="../assets/logo.png" alt="Green Assurance" class="logo-img">
    <div class="slogon">
        <h1>🌿 Green Assurance</h1>
        <small>BackOffice</small>
    </div>
    <a href="../frontoffice/accueil.php"><i class="fas fa-home"></i> Accueil</a>
    <a href="liste.php"><i class="fas fa-users"></i> Utilisateurs</a>
    <a href="ajout.php"><i class="fas fa-plus"></i> Nouveau</a>
    <a href="../frontoffice/profil.php?id=<?= $_SESSION['user_id'] ?? 1 ?>"><i class="fas fa-user"></i> Mon Profil</a>
    <a href="../auth/logout.php" class="btn-logout"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
</nav>

<div class="page-wrapper">

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success"><?= $_SESSION['success'] ?></div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<div class="stats-row">
    <div class="stat-card stat-total">
        <i class="fas fa-users"></i>
        <div><span class="stat-num"><?= $stats['total'] ?></span><span class="stat-label">Total</span></div>
    </div>
    <div class="stat-card stat-active">
        <i class="fas fa-check-circle"></i>
        <div><span class="stat-num"><?= $stats['active'] ?></span><span class="stat-label">Actifs</span></div>
    </div>
    <div class="stat-card stat-blocked">
        <i class="fas fa-ban"></i>
        <div><span class="stat-num"><?= $stats['blocked'] ?></span><span class="stat-label">Bloqués</span></div>
    </div>
    <div class="stat-card stat-pending">
        <i class="fas fa-clock"></i>
        <div><span class="stat-num"><?= $stats['pending'] ?></span><span class="stat-label">En attente</span></div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2><i class="fas fa-list"></i> Liste des utilisateurs</h2>
        <a href="ajout.php" class="btn btn-primary"><i class="fas fa-plus"></i> Nouvel utilisateur</a>
    </div>
    
    <!-- Barre de recherche et tri -->
    <div class="search-sort-bar">
        <div class="search-box">
            <i class="fas fa-search" style="color: #aaa;"></i>
            <input type="text" id="searchInput" placeholder="Rechercher par nom, prénom, email ou rôle...">
            <button id="clearSearchBtn"><i class="fas fa-times"></i> Effacer</button>
        </div>
        <div class="sort-buttons">
            <button class="sort-btn" data-sort="id"><i class="fas fa-sort-numeric-down"></i> ID</button>
            <button class="sort-btn" data-sort="name"><i class="fas fa-sort-alpha-down"></i> Nom</button>
            <button class="sort-btn" data-sort="email"><i class="fas fa-sort-alpha-down"></i> Email</button>
            <button class="sort-btn" data-sort="status"><i class="fas fa-sort"></i> Statut</button>
            <button class="sort-btn" data-sort="date"><i class="fas fa-sort-numeric-down"></i> Date</button>
            <button class="reset-btn" id="resetBtn"><i class="fas fa-undo-alt"></i> Réinitialiser</button>
        </div>
    </div>
    
    <div class="filter-stats" id="filterStats">
        Affichage de <span id="visibleCount">0</span> utilisateur(s) sur <span id="totalCount"><?= $users->rowCount() ?></span>
    </div>
    
    <div class="table-wrapper">
        <table class="data-table" id="usersTable">
            <thead>
                <tr>
                    <th class="th-sortable" data-sort="id">ID <i class="fas fa-sort"></i></th>
                    <th class="th-sortable" data-sort="name">Utilisateur <i class="fas fa-sort"></i></th>
                    <th class="th-sortable" data-sort="email">Email <i class="fas fa-sort"></i></th>
                    <th>Téléphone</th>
                    <th>Rôles</th>
                    <th class="th-sortable" data-sort="status">Statut <i class="fas fa-sort"></i></th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="tableBody">
                <?php foreach($users as $user): ?>
                <tr data-id="<?= $user['id'] ?>" data-name="<?= strtolower($user['prenom'] . ' ' . $user['nom']) ?>" data-email="<?= strtolower($user['email']) ?>" data-status="<?= $user['status'] ?>" data-date="<?= $user['created_at'] ?>">
                    <td class="col-id"><?= $user['id'] ?></td>
                    <td class="col-name">
                        <div class="user-name-cell">
                            <div class="avatar"><?= strtoupper(substr($user['prenom'],0,1).substr($user['nom'],0,1)) ?></div>
                            <span><?= htmlspecialchars($user['prenom'] . ' ' . $user['nom']) ?></span>
                        </div>
                    </td>
                    <td class="col-email"><?= htmlspecialchars($user['email']) ?></td>
                    <td><?= htmlspecialchars($user['phone'] ?? '—') ?></td>
                    <td>
                        <?php if(!empty($user['roles_noms'])): ?>
                            <?php foreach(explode(', ', $user['roles_noms']) as $role): ?>
                                <span class="badge badge-role"><?= ucfirst($role) ?></span>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <span class="text-muted">—</span>
                        <?php endif; ?>
                    </td>
                    <td class="col-status"><span class="badge badge-<?= $user['status'] ?>"><?= ucfirst($user['status']) ?></span></td>
                    <td class="action-btns">
                        <a href="../frontoffice/profil.php?id=<?= $user['id'] ?>" class="btn-icon btn-view" title="Voir"><i class="fas fa-eye"></i></a>
                        <a href="update.php?id=<?= $user['id'] ?>" class="btn-icon btn-edit" title="Modifier"><i class="fas fa-edit"></i></a>
                        <a href="delete.php?id=<?= $user['id'] ?>" class="btn-icon btn-delete" title="Supprimer" onclick="return confirm('Supprimer cet utilisateur ?')"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

</div>

<footer class="footer"><p>&copy; 2024 Green Assurance - Tous droits réservés</p></footer>

<script>
    // ============================================================
    // RECHERCHE ET TRI EN TEMPS RÉEL (SANS RECHARGEMENT)
    // ============================================================
    
    // Récupérer les éléments
    const searchInput = document.getElementById('searchInput');
    const clearBtn = document.getElementById('clearSearchBtn');
    const resetBtn = document.getElementById('resetBtn');
    const tableBody = document.getElementById('tableBody');
    const rows = Array.from(document.querySelectorAll('#tableBody tr'));
    const visibleCountSpan = document.getElementById('visibleCount');
    const totalCountSpan = document.getElementById('totalCount');
    const totalCount = rows.length;
    
    let currentSearchTerm = '';
    let currentSort = { column: null, order: 'asc' };
    
    // Mettre à jour l'affichage du nombre de résultats
    function updateVisibleCount() {
        const visibleRows = document.querySelectorAll('#tableBody tr:not([style*="display: none"])');
        visibleCountSpan.textContent = visibleRows.length;
    }
    
    // Fonction de recherche
    function filterRows() {
        const searchTerm = currentSearchTerm.toLowerCase();
        
        rows.forEach(row => {
            const name = row.getAttribute('data-name') || '';
            const email = row.getAttribute('data-email') || '';
            const roles = row.querySelector('td:nth-child(5)').innerText.toLowerCase();
            const status = row.getAttribute('data-status') || '';
            
            const matches = searchTerm === '' || 
                name.includes(searchTerm) || 
                email.includes(searchTerm) || 
                roles.includes(searchTerm) ||
                status.includes(searchTerm);
            
            row.style.display = matches ? '' : 'none';
        });
        
        updateVisibleCount();
        sortRows();
    }
    
    // Fonction de tri
    function sortRows() {
        if (!currentSort.column) return;
        
        const visibleRows = rows.filter(row => row.style.display !== 'none');
        
        visibleRows.sort((a, b) => {
            let aVal, bVal;
            
            switch(currentSort.column) {
                case 'id':
                    aVal = parseInt(a.getAttribute('data-id'));
                    bVal = parseInt(b.getAttribute('data-id'));
                    break;
                case 'name':
                    aVal = a.getAttribute('data-name') || '';
                    bVal = b.getAttribute('data-name') || '';
                    break;
                case 'email':
                    aVal = a.getAttribute('data-email') || '';
                    bVal = b.getAttribute('data-email') || '';
                    break;
                case 'status':
                    aVal = a.getAttribute('data-status') || '';
                    bVal = b.getAttribute('data-status') || '';
                    break;
                case 'date':
                    aVal = new Date(a.getAttribute('data-date')) || 0;
                    bVal = new Date(b.getAttribute('data-date')) || 0;
                    break;
                default:
                    return 0;
            }
            
            if (typeof aVal === 'string') {
                aVal = aVal.toLowerCase();
                bVal = bVal.toLowerCase();
            }
            
            if (aVal < bVal) return currentSort.order === 'asc' ? -1 : 1;
            if (aVal > bVal) return currentSort.order === 'asc' ? 1 : -1;
            return 0;
        });
        
        // Réorganiser le DOM
        visibleRows.forEach(row => tableBody.appendChild(row));
        
        // Mettre à jour les icônes des entêtes
        document.querySelectorAll('.th-sortable').forEach(th => {
            th.classList.remove('asc', 'desc');
            const thSort = th.getAttribute('data-sort');
            if (thSort === currentSort.column) {
                th.classList.add(currentSort.order);
            }
        });
    }
    
    // Appliquer le tri
    function applySort(column) {
        if (currentSort.column === column) {
            currentSort.order = currentSort.order === 'asc' ? 'desc' : 'asc';
        } else {
            currentSort.column = column;
            currentSort.order = 'asc';
        }
        sortRows();
    }
    
    // Réinitialiser tout (recherche + tri)
    function resetAll() {
        currentSearchTerm = '';
        currentSort = { column: null, order: 'asc' };
        searchInput.value = '';
        
        // Réafficher toutes les lignes
        rows.forEach(row => row.style.display = '');
        
        // Réinitialiser les entêtes
        document.querySelectorAll('.th-sortable').forEach(th => {
            th.classList.remove('asc', 'desc');
        });
        
        // Remettre l'ordre original (par ID)
        rows.sort((a, b) => {
            return parseInt(a.getAttribute('data-id')) - parseInt(b.getAttribute('data-id'));
        });
        rows.forEach(row => tableBody.appendChild(row));
        
        updateVisibleCount();
    }
    
    // Événements
    searchInput.addEventListener('input', (e) => {
        currentSearchTerm = e.target.value;
        filterRows();
    });
    
    clearBtn.addEventListener('click', () => {
        currentSearchTerm = '';
        searchInput.value = '';
        filterRows();
    });
    
    resetBtn.addEventListener('click', resetAll);
    
    // Tri par clic sur les entêtes
    document.querySelectorAll('.th-sortable').forEach(th => {
        th.addEventListener('click', () => {
            const column = th.getAttribute('data-sort');
            applySort(column);
        });
    });
    
    // Tri par les boutons
    document.querySelectorAll('.sort-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const column = btn.getAttribute('data-sort');
            applySort(column);
        });
    });
    
    // Initialisation
    totalCountSpan.textContent = totalCount;
    updateVisibleCount();
</script>

</body>
</html>