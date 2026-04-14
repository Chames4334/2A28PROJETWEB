function validateCongeForm() {
    var dateDebut = document.getElementById('date_debut').value.trim();
    var dateFin = document.getElementById('date_fin').value.trim();
    var typeConge = document.getElementById('type_conge').value.trim();
    var motif = document.getElementById('motif').value.trim();
    var idEmploye = document.getElementById('id_employe').value.trim();
    var errors = [];

    if (dateDebut === '') {
        errors.push('La date de début est requise.');
    }

    if (dateFin === '') {
        errors.push('La date de fin est requise.');
    }

    if (dateDebut !== '' && dateFin !== '') {
        var start = Date.parse(dateDebut);
        var end = Date.parse(dateFin);
        if (isNaN(start) || isNaN(end)) {
            errors.push('Les dates doivent être au format AAAA-MM-JJ.');
        } else if (start > end) {
            errors.push('La date de début doit être antérieure à la date de fin.');
        }
    }

    if (typeConge === '') {
        errors.push('Le type de congé est requis.');
    }

    if (motif === '') {
        errors.push('Le motif est requis.');
    }

    if (idEmploye === '' || !/^[0-9]+$/.test(idEmploye)) {
        errors.push('L\'ID employé est requis et doit être un nombre.');
    }

    if (errors.length > 0) {
        alert(errors.join('\n'));
        return false;
    }

    return true;
}
