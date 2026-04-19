const imageInput = document.getElementById("imageUpload")
const preview = document.getElementById("preview")

imageInput.addEventListener("change", function () {
    const file = this.files[0]
    if (file) {
        const reader = new FileReader()
        reader.onload = function (e) {
            preview.src = e.target.result
        }
        reader.readAsDataURL(file)
    }
});

/*function genererVariables(){
    var n=document.getElementById("nmbr")
    var nValue=n.value
    const container = document.getElementById("variables-container")
    removeMsg(n)

    if (!nValue || nValue < 1 || nValue > 6){
        showMsg(n,"*Saisir un nombre between 1 et 6", false)
        return
    }else{
        showMsg(n, "Nombre valide ✓", true);

        container.innerHTML = '';
        for(let i=0;i<Number(nValue);i++){
            const row = document.createElement('div');
            row.className = 'var-field-row'

            const label = document.createElement('label');
            label.textContent = `Variable ${i+1} :`

            const nameInput = document.createElement('input');
            nameInput.type = 'text';
            nameInput.name = `var_name_${i+1}`;
            nameInput.placeholder = `Nom variable ${i+1}`

            const typeSelect = document.createElement('select');
            typeSelect.name = `var_type_${i+1}`;
            typeSelect.style.flex = '0 0 130px';
            ['INT', 'VARCHAR', 'DATE'].forEach(opt => {
                const option = document.createElement('option');
                option.value = opt;
                option.textContent = opt;
                typeSelect.appendChild(option);
            });

            row.appendChild(label)
            row.appendChild(nameInput)
            row.appendChild(typeSelect)
            container.appendChild(row)
        }
    }
}*/
let titres=[]

function validerType(){
    let valid=true

    var Titre=document.getElementById("Titre")
    var TitreValue=Titre.value
    removeMsg(Titre)

    if(TitreValue === ""){
        showMsg(Titre,"*Le Titre est obligatoire", false)
        valid=false
    }
    else if (titres.includes(TitreValue)) {
        showMsg(Titre,"*Titre déjà utilisé", false)
        valid = false
    }
    else if((TitreValue.length<3)){
        showMsg(Titre,"*Le Titre doit avoir au moins 3 caractères", false)
        valid=false
    }else {
        showMsg(Titre, "Titre valide ✓", true)
        titres.push(TitreValue)
    }

    var desc=document.getElementById("Description")
    var descValue=desc.value
    removeMsg(desc)

    if(descValue === ""){
        showMsg(desc,"*Le Description est obligatoire", false)
        valid=false
    }
    else if((descValue.length<15)){
        showMsg(desc,"*Le Description doit avoir au moins 15 caractères", false)
        valid=false
    }else {
        showMsg(desc, "Description valide ✓", true)
    }

    /*var nombr=document.getElementById("nmbr")
    var nbrValue=nombr.value
    removeMsg(nombr)

    if(nbrValue === ""){
        showMsg(nombr,"*Saisir un nombre", false)
        valid=false
    }
    else if(Number(nbrValue)<1 || Number(nbrValue)>6){
        showMsg(nombr,"*Saisir un nombre between 1 et 6", false)
        valid=false
    }else {
        showMsg(nombr, "Nombre valide ✓", true)
    }

    const nbr=Number(nbrValue)
    for(let i=1;i<=nbr;i++){

        var name=document.querySelector('input[name="var_name_${i}"]')
        var nameValue=name.value
        removeMsg(name)

        var type=document.querySelector('select[name="var_type_${i}"]')
        var typeValue=type.value
        removeMsg(type)

        if (!name || !type) continue;
        if(nameValue === ""){
            showMsg(name,"Saisir le nom du "+i+" variable",false)
            valid=false
        }
        else {
            showMsg(name,"Variable valider",true)
        }
    }*/
    return valid
}
function showMsg(input, message, success) {
    const msg = document.createElement("span")
    msg.className = "validation-msg " + (success ? "msg-success" : "msg-error")
    msg.textContent = message
    input.insertAdjacentElement("afterend", msg)
}
function removeMsg(input) {
    let next = input.nextElementSibling
    while (next && next.classList.contains("validation-msg")) {
        const temp = next
        next = next.nextElementSibling
        temp.remove()
    }
}