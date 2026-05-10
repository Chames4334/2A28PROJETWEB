document.addEventListener('DOMContentLoaded', function() {
    const btnAnalyze = document.getElementById('btn-analyze-calendar');

    btnAnalyze.addEventListener('click', function() {
        // On récupère le mois/année actuel affiché sur le calendrier (approximatif pour l'instant)
        const now = new Date();
        const month = now.getMonth() + 1;
        const year = now.getFullYear();

        // Ouvrir le rapport dans une nouvelle fenêtre/onglet
        const url = `?action=ai_report_view&month=${month}&year=${year}`;
        window.open(url, '_blank', 'width=900,height=800,scrollbars=yes');
    });
});
