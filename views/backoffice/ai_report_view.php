<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rapport d'Analyse IA - Planning</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body { background: #f4f7f6; padding: 40px; }
        .report-container {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 15px 50px rgba(0,0,0,0.1);
        }
        .report-header {
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 20px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .verdict-badge {
            padding: 8px 20px;
            border-radius: 30px;
            font-weight: 800;
            text-transform: uppercase;
        }
        .verdict-tendu { background: #fff3e0; color: #e65100; }
        .verdict-critique { background: #ffebee; color: #c62828; }
        .verdict-favorable { background: #e8f5e9; color: #2e7d32; }
        
        .section { margin-bottom: 30px; }
        .section h3 { color: #556b44; margin-bottom: 15px; border-left: 4px solid #6b7d62; padding-left: 15px; }
        
        .meter-container { margin: 20px 0; }
        .meter-bar { height: 12px; background: #eee; border-radius: 6px; overflow: hidden; margin-top: 10px; }
        .meter-fill { height: 100%; background: #6FAF4C; width: 0; transition: width 1.5s ease; }
        
        .risk-item { background: #fff5f5; padding: 15px; border-radius: 10px; margin-bottom: 10px; border-left: 4px solid #d9534f; }
        .recommendation { background: #f0f7f0; padding: 20px; border-radius: 12px; font-style: italic; color: #333; line-height: 1.6; }
        
        @media print {
            .no-print { display: none; }
            body { padding: 0; background: white; }
            .report-container { box-shadow: none; border: 1px solid #ccc; }
        }
    </style>
</head>
<body>
    <div class="report-container">
        <header class="report-header">
            <div>
                <h1>Rapport d'Analyse IA</h1>
                <p style="color: #666;">Planning du mois : <span id="report-date">...</span></p>
            </div>
            <span id="verdict-badge" class="verdict-badge">Analyse...</span>
        </header>

        <div id="loading" style="text-align: center; padding: 50px;">
            <div class="loading-spinner"></div>
            <p style="margin-top: 20px; color: #888;">L'IA analyse votre planning en temps réel...</p>
        </div>

        <div id="report-content" style="display: none;">
            <div class="section">
                <h3>Résumé de la situation</h3>
                <p id="summary" style="font-size: 1.1rem; line-height: 1.6;"></p>
            </div>

            <div class="section">
                <h3>Disponibilité de l'équipe</h3>
                <div class="meter-container">
                    <div style="display: flex; justify-content: space-between;">
                        <span>Taux de présence effectif</span>
                        <span id="availability-percent" style="font-weight: 700;">0%</span>
                    </div>
                    <div class="meter-bar"><div id="meter-fill" class="meter-fill"></div></div>
                </div>
            </div>

            <div class="section">
                <h3>Points de vigilance & Risques</h3>
                <div id="risks-list"></div>
            </div>

            <div class="section">
                <h3>Recommandations RH</h3>
                <div id="recommendation" class="recommendation"></div>
            </div>
            
            <div style="margin-top: 50px; text-align: center;" class="no-print">
                <button onclick="window.print()" class="button button-secondary">Imprimer le rapport</button>
                <button onclick="window.close()" class="button button-light">Fermer la fenêtre</button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', async function() {
            const params = new URLSearchParams(window.location.search);
            const month = params.get('month') || (new Date().getMonth() + 1);
            const year = params.get('year') || new Date().getFullYear();
            
            document.getElementById('report-date').textContent = `${month}/${year}`;

            try {
                const response = await fetch(`?action=ai_calendar_analysis&month=${month}&year=${year}`);
                const data = await response.json();
                
                if (data.error) throw new Error(data.error);
                
                renderReport(data);
            } catch (error) {
                alert("Erreur d'analyse : " + error.message);
            }
        });

        function renderReport(data) {
            document.getElementById('loading').style.display = 'none';
            document.getElementById('report-content').style.display = 'block';

            const badge = document.getElementById('verdict-badge');
            badge.textContent = data.verdict;
            badge.className = 'verdict-badge verdict-' + data.verdict;

            document.getElementById('summary').textContent = data.summary;
            document.getElementById('availability-percent').textContent = data.team_availability_score + '%';
            document.getElementById('meter-fill').style.width = data.team_availability_score + '%';

            const risksList = document.getElementById('risks-list');
            if (data.risks && data.risks.length > 0) {
                data.risks.forEach(r => {
                    const div = document.createElement('div');
                    div.className = 'risk-item';
                    div.innerHTML = `<strong>${r.date} :</strong> ${r.reason}`;
                    risksList.appendChild(div);
                });
            } else {
                risksList.innerHTML = '<p>Aucun risque critique identifié pour ce mois.</p>';
            }

            document.getElementById('recommendation').textContent = data.recommendations;
        }
    </script>
</body>
</html>
