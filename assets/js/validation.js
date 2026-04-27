function clearFieldErrors() {
    document.querySelectorAll('.field-error').forEach(el => el.remove());
    document.querySelectorAll('input, textarea, select').forEach(el => el.classList.remove('input-error'));
}

function addFieldError(fieldId, errorMessage) {
    const field = document.getElementById(fieldId);
    if (!field) return;
    
    field.classList.add('input-error');
    
    const errorDiv = document.createElement('div');
    errorDiv.className = 'field-error';
    errorDiv.textContent = '✗ ' + errorMessage;
    
    field.parentNode.insertBefore(errorDiv, field.nextSibling);
}

function validateCongeForm() {
    clearFieldErrors();
    
    var dateDebut = document.getElementById('date_debut').value;
    var dateFin = document.getElementById('date_fin').value;
    var typeConge = document.getElementById('type_conge').value;
    var motif = document.getElementById('motif').value;
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
        var start = new Date(dateDebut);
        var end = new Date(dateFin);
        if (start > end) {
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
    
    var dateTraitement = document.getElementById('date_traitement').value;
    var decision = document.getElementById('decision').value;
    var idConge = document.getElementById('id_conge').value;
    var hasErrors = false;

    if (idConge === '') {
        addFieldError('id_conge', 'Un congé associé est requis.');
        hasErrors = true;
    }

    if (dateTraitement === '') {
        addFieldError('date_traitement', 'La date de traitement est requise.');
        hasErrors = true;
    }

    if (decision === '') {
        addFieldError('decision', 'La décision est requise.');
        hasErrors = true;
    }

    return !hasErrors;
}