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

            <!-- Suggestions intelligentes -->
            <?php if (!empty($suggestions)): ?>
                <div class="panel panel-light" style="margin-bottom: 24px; padding: 15px; border-left: 4px solid #6FAF4C;">
                    <h3 style="margin-top:0; color:#556b44;">💡 Meilleures périodes suggérées</h3>
                    <p style="font-size:0.9rem; color:#666; margin-bottom: 12px;">Ces périodes ne contiennent aucune absence dans l'équipe :</p>
                    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                        <?php foreach ($suggestions as $sug): ?>
                            <button type="button" class="pill tone-ok" style="cursor: pointer; border: 1px solid #2ca95a;" 
                                    onclick="document.getElementById('date_debut').value='<?php echo $sug['debut']; ?>'; document.getElementById('date_fin').value='<?php echo $sug['fin']; ?>'; checkWorkload();">
                                <?php echo htmlspecialchars($sug['label']); ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <form id="form-conge" method="post" action="?action=create" onsubmit="return validateCongeForm();">
                <label for="date_debut">Date de début</label>
                <input type="date" id="date_debut" name="date_debut" value="<?php echo htmlspecialchars($_POST['date_debut'] ?? ''); ?>">

                <label for="date_fin">Date de fin</label>
                <input type="date" id="date_fin" name="date_fin" value="<?php echo htmlspecialchars($_POST['date_fin'] ?? ''); ?>">

                <!-- Indicateur de charge dynamique -->
                <div id="workload-indicator" style="display: none; margin: 10px 0 20px 0; padding: 12px; border-radius: 8px; font-size: 0.95rem; font-weight: 600;">
                    <!-- Rempli par JavaScript -->
                </div>

                <label for="type_conge">Type de congé</label>
                <select id="type_conge" name="type_conge">
                    <option value="">-- Sélectionner --</option>
                    <option value="Congé payé">Congé payé</option>
                    <option value="Congé maladie">Congé maladie</option>
                    <option value="Congé sans solde">Congé sans solde</option>
                </select>

                <label for="motif">Motif</label>
                <textarea id="motif" name="motif"><?php echo htmlspecialchars($_POST['motif'] ?? ''); ?></textarea>

                <input type="hidden" name="statut" value="en_attente">

                <button class="button button-primary" type="submit">Envoyer</button>
            </form>
        </section>
    </div>

    <script>
        const workloadData = <?php echo $workloadJson ?? '{}'; ?>;
        const dateDebutInput = document.getElementById('date_debut');
        const dateFinInput = document.getElementById('date_fin');
        const indicator = document.getElementById('workload-indicator');

        function checkWorkload() {
            const startStr = dateDebutInput.value;
            const endStr = dateFinInput.value;

            if (!startStr || !endStr) {
                indicator.style.display = 'none';
                return;
            }

            const start = new Date(startStr);
            const end = new Date(endStr);

            if (start > end) {
                indicator.style.display = 'block';
                indicator.style.backgroundColor = '#fee';
                indicator.style.color = '#c33';
                indicator.innerHTML = '⚠️ La date de fin doit être après la date de début.';
                return;
            }

            let maxAbsents = 0;
            let d = new Date(startStr);

            while (d <= end) {
                // Ignore les week-ends
                const day = d.getDay();
                if (day !== 0 && day !== 6) {
                    const dStr = d.toISOString().split('T')[0];
                    const absents = workloadData[dStr] || 0;
                    if (absents > maxAbsents) {
                        maxAbsents = absents;
                    }
                }
                d.setDate(d.getDate() + 1);
            }

            indicator.style.display = 'block';

            if (maxAbsents === 0) {
                indicator.style.backgroundColor = '#eefbf3';
                indicator.style.color = '#1f7a3e';
                indicator.style.border = '1px solid #2ca95a';
                indicator.innerHTML = '🟢 Période idéale : Aucun employé n\'est absent durant cette période.';
            } else if (maxAbsents === 1) {
                indicator.style.backgroundColor = '#fdf4ea';
                indicator.style.color = '#a7560b';
                indicator.style.border = '1px solid #f2994a';
                indicator.innerHTML = '🟠 Charge modérée : 1 employé est déjà absent. La demande est acceptable.';
            } else {
                indicator.style.backgroundColor = '#fee';
                indicator.style.color = '#c33';
                indicator.style.border = '1px solid #d9534f';
                indicator.innerHTML = '🔴 Risque de refus : La limite de 2 employés absents simultanément est atteinte ou dépassée sur cette période.';
            }
        }

        dateDebutInput.addEventListener('change', checkWorkload);
        dateFinInput.addEventListener('change', checkWorkload);

        // Check on load if dates are pre-filled
        window.addEventListener('DOMContentLoaded', checkWorkload);
    </script>
</body>
</html>