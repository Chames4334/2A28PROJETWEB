<?php
// view/assets/forum_nav.php
// Usage – include this file and call:
//   forumNav('front', 'liste')   inside front office
//   forumNav('back',  'liste')   inside back office
// $active = current page slug for highlighting

function forumNav(string $side, string $active = '') {
    $isAdmin = isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';

    // Both front and back office pages sit at:
    //   view/frontoffice/forum/  →  3 levels deep from view/
    //   view/backoffice/forum/   →  3 levels deep from view/
    // So absolute paths from web root (assuming / is project root).
    $assets = '/view/assets/';          // view/assets/
    $auth   = '/view/auth/';            // view/auth/ (2 levels up from forum/)
    $front  = '/view/frontoffice/';     // view/frontoffice/
    $back   = '/view/backoffice/';      // view/backoffice/

    $forumF = '/view/frontoffice/forum/';  // frontoffice/forum/
    $forumB = '/view/backoffice/forum/';   // backoffice/forum/

    if ($side === 'front') {
        // ── FRONT OFFICE NAV ────────────────────────────────────
        ?>
        <nav class="forum-nav">
            <a class="logo-wrap" href="liste.php">
                <h1>🌿 Green Assurance</h1>
            </a>
            <div class="nav-links">
                <a href="liste.php" <?= $active==='liste' ? 'class="active"' : '' ?>>
                    <i class="fas fa-comments"></i> Forum
                </a>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="ajout.php" <?= $active==='ajout' ? 'class="active"' : '' ?>>
                        <i class="fas fa-plus-circle"></i> Nouveau Post
                    </a>

                    <?php if ($isAdmin): ?>
                        <!-- ★ SWITCH TO BACKOFFICE ★ -->
                        <a href="<?= $forumB ?>liste.php" class="btn-switch-bo" title="Accéder au Back Office">
                            <i class="fas fa-cogs"></i> Back Office
                        </a>
                    <?php endif; ?>

                    <span class="nav-user">
                        <i class="fas fa-user-circle"></i>
                        <?= htmlspecialchars($_SESSION['user_prenom'] ?? '') ?>
                    </span>
                    <a href="<?= $auth ?>logout.php" class="btn-logout">
                        <i class="fas fa-sign-out-alt"></i> Déconnexion
                    </a>
                <?php else: ?>
                    <a href="<?= $auth ?>login.php" class="btn-login">
                        <i class="fas fa-sign-in-alt"></i> Connexion
                    </a>
                <?php endif; ?>
            </div>
        </nav>
        <?php

    } else {
        // ── BACK OFFICE SIDEBAR ──────────────────────────────────
        $tab = $_GET['tab'] ?? 'posts';
        ?>
        <div class="sidebar">
            <div class="sb-logo">
                <h1>🌿 Green Assurance</h1>
                <small>BackOffice Forum</small>
            </div>
            <nav>
                <div class="sb-section">Forum</div>
                <a href="stats.php" class="<?= $active==='stats' ? 'active' : '' ?>">
                    <i class="fas fa-chart-simple"></i> Statistiques
                </a>
                <a href="liste.php?tab=posts"     class="<?= ($active==='liste'&&$tab==='posts')     ? 'active' : '' ?>">
                    <i class="fas fa-file-alt"></i> Posts
                </a>
                <a href="liste.php?tab=replies"   class="<?= ($active==='liste'&&$tab==='replies')   ? 'active' : '' ?>">
                    <i class="fas fa-comments"></i> Réponses
                </a>
                <a href="liste.php?tab=reactions" class="<?= ($active==='liste'&&$tab==='reactions') ? 'active' : '' ?>">
                    <i class="fas fa-heart"></i> Réactions
                </a>
                <a href="reports.php" class="<?= $active==='reports' ? 'active' : '' ?>">
                    <i class="fas fa-flag"></i> Signalements
                </a>
                <details class="sb-dropdown" <?= in_array($active, ['scores','moderation'], true) ? 'open' : '' ?>>
                    <summary><i class="fas fa-shield-halved"></i> Scores IA</summary>
                    <a href="scores.php" class="<?= $active==='scores' ? 'active' : '' ?>">
                        <i class="fas fa-list-check"></i> Scores
                    </a>
                    <a href="moderation.php" class="<?= $active==='moderation' ? 'active' : '' ?>">
                        <i class="fas fa-triangle-exclamation"></i> Modération
                    </a>
                </details>
                <a href="tags.php" class="<?= $active==='tags' ? 'active' : '' ?>">
                    <i class="fas fa-tags"></i> Tags
                </a>
                <a href="ajout.php" class="<?= $active==='ajout' ? 'active' : '' ?>">
                    <i class="fas fa-plus-circle"></i> Nouveau Post
                </a>

                <div class="sb-section">Navigation</div>

                <!-- ★ SWITCH TO FRONTOFFICE ★ -->
                <a href="<?= $forumF ?>liste.php" class="btn-switch-fo">
                    <i class="fas fa-eye"></i> Front Office Forum
                </a>

                <div class="sb-section">Compte</div>
                <span class="sb-user">
                    <i class="fas fa-user-circle"></i>
                    <?= htmlspecialchars(($_SESSION['user_prenom'] ?? '') . ' ' . ($_SESSION['user_nom'] ?? '')) ?>
                </span>
                <a href="<?= $auth ?>logout.php" class="sb-logout">
                    <i class="fas fa-sign-out-alt"></i> Déconnexion
                </a>
            </nav>
        </div>
        <?php
    }
}
?>
