<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Calendrier des Congés</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- Intégration de FullCalendar -->
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js'></script>
    <style>
        .calendar-container {
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            margin-top: 20px;
        }
        
        /* Légende */
        .calendar-legend {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            padding: 15px;
            background: #f9f9f9;
            border-radius: 8px;
            align-items: center;
        }
        .legend-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.9rem;
            font-weight: 600;
        }
        .legend-color {
            width: 16px;
            height: 16px;
            border-radius: 4px;
        }
        
        /* Customisation de FullCalendar */
        .fc-event {
            cursor: pointer;
            border: none;
            padding: 3px 5px;
            font-size: 0.85rem;
            border-radius: 4px;
        }
        .fc-toolbar-title {
            font-size: 1.5rem !important;
            color: #333;
        }
        .fc-button-primary {
            background-color: #6b7d62 !important;
            border-color: #6b7d62 !important;
        }
        .fc-button-primary:hover {
            background-color: #556b44 !important;
        }
        .fc-day-today {
            background-color: #f0f7f2 !important;
        }
    </style>
</head>
<body>
    <div class="page-shell modern-shell">
        <header class="page-header modern-header">
            <div>
                <p class="breadcrumb">Espace Administration</p>
                <h1>Calendrier Global des Absences</h1>
            </div>
            <div class="header-actions">
                <a class="button button-secondary" href="?action=adminIndex">Retour au Tableau de Bord</a>
            </div>
        </header>

        <section class="calendar-container">
            <div class="calendar-legend">
                <div class="legend-item"><div class="legend-color" style="background: #2ca95a;"></div> Approuvé</div>
                <div class="legend-item"><div class="legend-color" style="background: #f2994a;"></div> En attente</div>
                <div class="legend-item"><div class="legend-color" style="background: #d9534f;"></div> Refusé</div>
                <div class="legend-item"><div class="legend-color" style="background: #ffebee; border: 1px solid #c62828;"></div> ⚠️ Surcharge (> 2 absents)</div>
            </div>
            
            <div id="calendar"></div>
        </section>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var eventsData = <?php echo $eventsJson ?? '[]'; ?>;

            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'fr',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek'
                },
                buttonText: {
                    today: "Aujourd'hui",
                    month: 'Mois',
                    week: 'Semaine'
                },
                events: eventsData,
                displayEventTime: false,
                firstDay: 1, // La semaine commence le Lundi
                eventClick: function(info) {
                    // On pourrait rediriger vers le traitement si on avait l'ID,
                    // mais on affiche au moins une petite alerte informative.
                    if (!info.event.display) { // Si ce n'est pas un background event
                        alert("Congé : " + info.event.title + "\nDu " + info.event.startStr + " au " + (info.event.endStr || info.event.startStr));
                    }
                }
            });

            calendar.render();
        });
    </script>
</body>
</html>
