function clearFieldErrors() {
    document.querySelectorAll('.field-error').forEach(el => el.remove());
    document.querySelectorAll('input, textarea, select').forEach(el => el.classList.remove('input-error'));
}

function addFieldError(fieldId, errorMessage) {
    const field = document.getElementById(fieldId);
    if (!field) return;
    
    // Ajouter classe d'erreur au champ
    field.classList.add('input-error');
    
    // Créer et insérer le message d'erreur
    const errorDiv = document.createElement('div');
    errorDiv.className = 'field-error';
    errorDiv.textContent = '✗ ' + errorMessage;
    
    field.parentNode.insertBefore(errorDiv, field.nextSibling);
}

function validateCongeForm() {
    clearFieldErrors();
    
    var dateDebut = document.getElementById('date_debut').value.trim();
    var dateFin = document.getElementById('date_fin').value.trim();
    var typeConge = document.getElementById('type_conge').value.trim();
    var motif = document.getElementById('motif').value.trim();
    var hasErrors = false;

    if (dateDebut === '') {
        addFieldError('date_debut', 'La date de début est requise.');
        hasErrors = true;
    }

    if (dateFin === '') {
        addFieldError('date_fin', 'La date de fin est requise.');
        hasErrors = true;
    }

    if (dateDebut !== '' && dateFin !== '') {
        var start = Date.parse(dateDebut);
        var end = Date.parse(dateFin);
        if (isNaN(start) || isNaN(end)) {
            addFieldError('date_debut', 'Les dates doivent être au format AAAA-MM-JJ.');
            hasErrors = true;
        } else if (start > end) {
            addFieldError('date_fin', 'La date de fin doit être après la date de début.');
            hasErrors = true;
        }
    }

    if (typeConge === '') {
        addFieldError('type_conge', 'Le type de congé est requis.');
        hasErrors = true;
    }

    if (motif === '') {
        addFieldError('motif', 'Le motif est requis.');
        hasErrors = true;
    }

    return !hasErrors;
}

function validateTraitementForm() {
    clearFieldErrors();
    
    var dateTraitement = document.getElementById('date_traitement').value.trim();
    var decision = document.getElementById('decision').value.trim();
    var idConge = document.getElementById('id_conge').value.trim();
    var hasErrors = false;

    if (idConge === '') {
        addFieldError('id_conge', 'Un congé associé est requis.');
        hasErrors = true;
    }

    if (dateTraitement === '') {
        addFieldError('date_traitement', 'La date de traitement est requise.');
        hasErrors = true;
    } else {
        var timestamp = Date.parse(dateTraitement);
        if (isNaN(timestamp)) {
            addFieldError('date_traitement', 'Format invalide (utilisez AAAA-MM-JJ).');
            hasErrors = true;
        }
    }

    if (decision === '') {
        addFieldError('decision', 'La décision est requise.');
        hasErrors = true;
    }

    return !hasErrors;
}
