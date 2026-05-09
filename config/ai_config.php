<?php
/**
 * Configuration pour l'Intelligence Artificielle (Gemini API)
 */
return [
    'gemini_api_key' => 'YOUR_GEMINI_API_KEY_HERE', // À remplacer par une clé valide
    'model' => 'gemini-1.5-flash',
    'base_url' => 'https://generativelanguage.googleapis.com/v1beta/models/',
    
    // Périodes de forte activité (pour l'aide à la décision)
    'peak_periods' => [
        ['start' => '12-01', 'end' => '12-31', 'label' => 'Fin d\'année / Inventaire'],
        ['start' => '07-01', 'end' => '08-15', 'label' => 'Saison Estivale'],
        ['start' => '03-15', 'end' => '04-15', 'label' => 'Clôture Fiscale'],
    ],
    
    // Paramètres de suggestion
    'max_suggestions' => 4,
    'days_lookahead' => 120, // Analyse sur les 4 prochains mois
    'max_absents_per_team' => 2, // Seuil critique pour le sous-effectif
];
