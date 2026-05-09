document.addEventListener('DOMContentLoaded', function() {
    const dateDebutInput = document.getElementById('date_debut');
    const dateFinInput = document.getElementById('date_fin');
    const realtimePanel = document.getElementById('ai-realtime-analysis');
    const loadSuggestionsBtn = document.getElementById('btn-load-suggestions');
    const suggestionsContainer = document.getElementById('ai-suggestions-pills');

    // --- Suggestions ---
    async function loadSuggestions() {
        if (!suggestionsContainer) return;
        
        suggestionsContainer.innerHTML = '<span style="font-size:0.9rem; color:#666;">Chargement des suggestions...</span>';
        
        try {
            const response = await fetch('?action=ai_suggestions&id_employe=1');
            const data = await response.json();
            
            suggestionsContainer.innerHTML = '';
            
            if (data.suggestions && Array.isArray(data.suggestions) && data.suggestions.length > 0) {
                data.suggestions.forEach((s, index) => {
                    const pill = document.createElement('button');
                    pill.type = 'button';
                    // Apply premium style inline or via class
                    pill.className = 'suggestion-pill';
                    pill.style.padding = '8px 16px';
                    pill.style.borderRadius = '20px';
                    pill.style.border = '1px solid #6FAF4C';
                    pill.style.background = '#eefbf3';
                    pill.style.color = '#2d462f';
                    pill.style.fontWeight = '600';
                    pill.style.cursor = 'pointer';
                    pill.style.transition = 'all 0.2s ease';
                    pill.style.fontSize = '0.9rem';
                    
                    pill.onmouseover = () => {
                        pill.style.background = '#6FAF4C';
                        pill.style.color = '#fff';
                        pill.style.transform = 'translateY(-2px)';
                        pill.style.boxShadow = '0 4px 8px rgba(111, 175, 76, 0.2)';
                    };
                    pill.onmouseout = () => {
                        pill.style.background = '#eefbf3';
                        pill.style.color = '#2d462f';
                        pill.style.transform = 'none';
                        pill.style.boxShadow = 'none';
                    };

                    pill.textContent = s.label;
                    pill.title = s.reason;
                    
                    pill.addEventListener('click', () => {
                        dateDebutInput.value = s.debut;
                        dateFinInput.value = s.fin;
                        analyzeRequest(); // Trigger realtime analysis
                    });
                    
                    // Simple micro-animation on load
                    pill.style.opacity = '0';
                    pill.style.transform = 'translateY(10px)';
                    suggestionsContainer.appendChild(pill);
                    
                    setTimeout(() => {
                        pill.style.opacity = '1';
                        pill.style.transform = 'none';
                    }, index * 100);
                });
            } else {
                suggestionsContainer.innerHTML = '<span style="font-size:0.9rem; color:#666;">Aucune suggestion optimale trouvée pour le moment.</span>';
            }
        } catch (error) {
            console.error(error);
            suggestionsContainer.innerHTML = '<span style="font-size:0.9rem; color:#c33;">Erreur lors du chargement.</span>';
        }
    }
    
    if (loadSuggestionsBtn) {
        loadSuggestionsBtn.addEventListener('click', loadSuggestions);
        // Load once automatically on startup
        loadSuggestions();
    }

    // --- Analyse en temps réel ---
    let analysisTimeout;
    [dateDebutInput, dateFinInput].forEach(input => {
        if (input) {
            input.addEventListener('change', () => {
                clearTimeout(analysisTimeout);
                analysisTimeout = setTimeout(analyzeRequest, 800);
            });
        }
    });

    async function analyzeRequest() {
        const debut = dateDebutInput.value;
        const fin = dateFinInput.value;
        
        const typeEl = document.getElementById('type_conge');
        const motifEl = document.getElementById('motif');
        
        const type = typeEl ? typeEl.value : '';
        const motif = motifEl ? motifEl.value : '';

        if (!debut || !fin) return;
        
        if (new Date(debut) > new Date(fin)) {
            updateAnalysisUI({
                status: 'conflict',
                message: 'La date de fin doit être après la date de début.',
                points_positifs: [],
                points_negatifs: []
            });
            return;
        }

        realtimePanel.style.display = 'block';
        document.getElementById('ai-status-icon').textContent = '⏳';
        document.getElementById('ai-analysis-msg').textContent = 'Analyse en cours...';
        document.getElementById('ai-analysis-details').innerHTML = '';
        
        try {
            const response = await fetch('?action=ai_analyze', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ date_debut: debut, date_fin: fin, type_conge: type, motif: motif, id_employe: 1 })
            });
            const data = await response.json();
            
            updateAnalysisUI(data);
        } catch (error) {
            console.error(error);
            updateAnalysisUI({
                status: 'risky',
                message: 'Erreur de connexion au moteur d\'analyse.',
                points_positifs: [],
                points_negatifs: []
            });
        }
    }

    function updateAnalysisUI(data) {
        if (!realtimePanel) return;
        
        const icon = document.getElementById('ai-status-icon');
        const msg = document.getElementById('ai-analysis-msg');
        const details = document.getElementById('ai-analysis-details');

        realtimePanel.style.display = 'block';
        msg.textContent = data.message;
        
        details.innerHTML = '';
        if (data.points_positifs) {
            data.points_positifs.forEach(pt => {
                details.innerHTML += `<li style="color: #1f7a3e;">✓ ${pt}</li>`;
            });
        }
        if (data.points_negatifs) {
            data.points_negatifs.forEach(pt => {
                details.innerHTML += `<li style="color: #c33;">⚠️ ${pt}</li>`;
            });
        }
        
        const config = {
            optimal: { icon: '🟢', bg: '#eefbf3', color: '#1f7a3e', border: '#6FAF4C' },
            risky: { icon: '🟠', bg: '#fdf4ea', color: '#a7560b', border: '#f2994a' },
            conflict: { icon: '🔴', bg: '#fee', color: '#c33', border: '#d9534f' }
        };

        const style = config[data.status] || config.risky;
        realtimePanel.style.backgroundColor = style.bg;
        realtimePanel.style.color = style.color;
        realtimePanel.style.border = `1px solid ${style.border}`;
        icon.textContent = style.icon;
    }
});
