function validerFormulaire(){
    let isValid = true

    var type=document.getElementById('Type')
    var typeValue=type.value
    removeMsg(type)

    if(typeValue === ""){
        showMsg(type,"*Veuillez Choisir une option",false)
        isValid=false
    } else {
        showMsg(type,"Type valide ✓",true)
        isValid=true
    }

    var title=document.getElementById("Title")
    var titleValue=title.value
    removeMsg(title)

    if(titleValue === ""){
        showMsg(title,"*Le Titre est obligatoire", false)
        isValid=false
    }
    else if((titleValue.length<3)){
        showMsg(title,"*Le Titre doit avoir au moins 3 caractères", false)
        isValid=false
    }else {
        showMsg(title, "Titre valide ✓", true)
    }

    var Prix=document.getElementById("Prix_mensuel")
    var prixValue=Prix.value
    removeMsg(Prix)

    if(prixValue === ""){
        showMsg(Prix,"*Le Prix est obligatoire", false)
        isValid=false
    }
    else if(isNaN(prixValue)){
        showMsg(Prix, "*Le prix doit être un nombre valide.", false)
        isValid = false
    }
    else if(Number(prixValue) < 100){
        showMsg(Prix, "*Le prix doit être au moins 100.", false)
        isValid = false
    }
    else if(Number(prixValue) > 99999){
        showMsg(Prix, "*Le prix semble trop élevé.", false)
        isValid = false
    }
    else{
        showMsg(Prix, "Prix valide ✓", true)
    }

    const dateDebut=document.querySelector("input[name='Date_Debut']")
    const Debut = dateDebut.value
    removeMsg(dateDebut)
    if (!Debut) {
        showMsg(dateDebut, "*La date de début est obligatoire.", false)
        isValid = false
    } else {
        const today = new Date().toISOString()
        if(Debut >= today){
            showMsg(dateDebut, "Date valide ✓", true)
        }else {
            showMsg(dateDebut, "*La date doit être dans le futur.", false)
            isValid = false
        }
    }
    const dateFin=document.querySelector("input[name='Date_Fin']")
    const Fin = dateFin.value
    removeMsg(dateFin)
    if (!Fin) {
        showMsg(dateFin, "*La date de Fin est obligatoire.", false)
        isValid = false
    }
    else if(Debut >= Fin)
    {
        showMsg(dateFin, "*La date de Fin doit être supérieur au date de debut.", false)
        isValid = false
    }else {
        const tommorow = new Date().toISOString()
        if(Fin >= tommorow){
            showMsg(dateFin, "Date valide ✓", true)
        }else {
            showMsg(dateFin, "*La date doit être dans le futur.", false)
            isValid = false
        }
    }
    return isValid;
}
function showMsg(input, message, success) {
    const msg = document.createElement("span")
    msg.className = "validation-msg " + (success ? "msg-success" : "msg-error")
    msg.textContent = message
    input.insertAdjacentElement("afterend", msg)
}
function removeMsg(input) {
    const next = input.nextElementSibling
    if (next && next.classList.contains("validation-msg")) {
        next.remove()
    }
}