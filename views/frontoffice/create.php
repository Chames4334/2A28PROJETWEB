<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Nouvelle demande</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="assets/js/validation.js"></script>
</head>
<body>
    <div class="page-shell">
        <header class="page-header">
            <div>
                <p class="breadcrumb">Espace</p>
                <h1>Nouvelle demande de congé</h1>
            </div>
            <div class="header-actions">
                <a class="button button-secondary" href="?action=index">Retour à la liste</a>
            </div>
        </header>

        <section class="form-card">
            <?php if (!empty($errors)): ?>
                <div class="error-box">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>




            <form id="form-conge" method="post" action="?action=create">
                <div class="form-container" style="position: relative;">
                    <!-- Section Assistant Intelligent -->
                    <div class="ai-assistant-panel" style="margin-bottom: 20px; padding: 15px; background: rgba(111, 175, 76, 0.05); border: 1px solid rgba(111, 175, 76, 0.2); border-radius: 12px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                            <h3 style="margin: 0; font-size: 1.05rem; color: #2d462f; display: flex; align-items: center; gap: 8px;">
                                <span>✨</span> Assistant de planification
                            </h3>
                            <button type="button" id="btn-load-suggestions" class="button button-secondary" style="padding: 6px 12px; font-size: 0.85rem;">Suggérer des dates</button>
                        </div>
                        <div id="ai-suggestions-pills" style="display: flex; gap: 10px; flex-wrap: wrap; margin-top: 10px;">
                            <!-- Les pilules de suggestion apparaîtront ici -->
                        </div>
                    </div>

                    <label for="date_debut">Date de début</label>
                    <input type="date" id="date_debut" name="date_debut" value="<?php echo htmlspecialchars($_POST['date_debut'] ?? ''); ?>">

                    <label for="date_fin">Date de fin</label>
                    <input type="date" id="date_fin" name="date_fin" value="<?php echo htmlspecialchars($_POST['date_fin'] ?? ''); ?>">

                    <!-- Indicateur de charge dynamique (Feu Vert/Orange/Rouge) -->
                    <div id="ai-realtime-analysis" style="display: none; margin: 10px 0 20px 0; padding: 12px; border-radius: 8px; font-size: 0.95rem; font-weight: 600; transition: all 0.3s ease;">
                        <span id="ai-status-icon" style="margin-right: 8px;"></span>
                        <span id="ai-analysis-msg"></span>
                        <ul id="ai-analysis-details" style="margin-top: 8px; margin-bottom: 0; padding-left: 20px; font-weight: normal; font-size: 0.85rem;"></ul>
                    </div>

                    <label for="type_conge">Type de congé</label>
                    <select id="type_conge" name="type_conge">
                        <option value="">-- Sélectionner --</option>
                        <option value="Congé payé">Congé payé</option>
                        <option value="Congé maladie">Congé maladie</option>
                        <option value="Congé sans solde">Congé sans solde</option>
                    </select>

                    <label for="motif">Motif</label>
                    <textarea id="motif" name="motif" placeholder="Décrivez brièvement votre besoin..."><?php echo htmlspecialchars($_POST['motif'] ?? ''); ?></textarea>

                    <input type="hidden" name="statut" value="en_attente">

                    <button class="button button-primary" type="submit" style="width: 100%; margin-top: 20px;">Confirmer la demande</button>
                </div>
            </form>
            <script src="assets/js/ai_decision_support.js"></script>
        </section>
    </div>
</body>
</html>