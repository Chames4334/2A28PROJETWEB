var timer;
 
var rechercheInput = document.getElementById("recherche");
var triSelect      = document.getElementById("tri");
 
function fetchRows() {
    var query = encodeURIComponent(rechercheInput.value);
    fetch("Subscription.php?ajax=1&recherche=" + query)
        .then(function(res) { return res.text(); })
        .then(function(html) {
            var tbody = document.querySelector(".table tbody");
            if (tbody) {
                tbody.innerHTML = html;
                if (triSelect && triSelect.value) {
                    sortTable(triSelect.value);
                }
            }
        });
}
if (rechercheInput) {
    rechercheInput.addEventListener("keyup", function() {
        clearTimeout(timer);
        timer = setTimeout(fetchRows, 300);
    });
}
function sortTable(criterion) {
    var tbody = document.querySelector(".table tbody");
    if (!tbody) return;
 
    var rows = Array.from(tbody.querySelectorAll("tr"));
 
    rows.sort(function(a, b) {
        var achoix = a.cells[1] ? a.cells[1].textContent.trim() : '';
        var bchoix = b.cells[1] ? b.cells[1].textContent.trim() : '';
        var aDate  = a.cells[5] ? new Date(a.cells[5].textContent.trim()) : 0;
        var bDate  = b.cells[5] ? new Date(b.cells[5].textContent.trim()) : 0;
 
        if (criterion === 'az')   return achoix.localeCompare(bchoix);
        if (criterion === 'za')   return bchoix.localeCompare(achoix);
        if (criterion === 'date') return aDate - bDate;
        return 0;
    });
 
    rows.forEach(function(row, i) {
        var numCell = row.querySelector(".row-number");
        if (numCell) numCell.textContent = i + 1;
        tbody.appendChild(row);
    });
}
 
if (triSelect) {
    triSelect.addEventListener("change", function() {
        sortTable(this.value);
    });
}

document.addEventListener("DOMContentLoaded", function () {

    const methodSelect = document.getElementById("Payment_method");
    const container = document.getElementById("variables-container");

    methodSelect.addEventListener("change", function () {

        container.innerHTML = ""; // clear old fields

        if (this.value === "Carte") {

            container.innerHTML = `
                <hr style="margin:15px 0; border:1px solid #ccc;">
                
                <div class="var-field-row">
                    <label>Numéro de carte:</label>
                    <input type="number" name="cardNumber" placeholder="XXXX-XXXX-XXXX-XXXX">
                </div>

                <div class="var-field-row">
                    <label>Code Postale:</label>
                    <input type="number" name="PostalCode">
                </div>

                <div class="var-field-row">
                    <label>Adresse de facturation:</label>
                    <input type="text" name="adresse">
                </div>

                <div class="var-field-row">
                    <label>Region:</label>
                    <select name="region">
                        <option value="">--Choisir--</option>
                        <option value="Tunis">Tunis</option>
                        <option value="Ariana">Ariana</option>
                        <option value="Manouba">Manouba</option>
                        <option value="Nabeul">Nabeul</option>
                        <option value="Bizerte">Bizert</option>
                        <option value="Sfax">Sfax</option>
                        <option value="Tataouine">Tataouine</option>
                    </select>
                </div>
            `;
        }
    });

});
function validerInscription(){
    let isValid=true

    var method=document.getElementById("Payment_method")
    var methodValue=method.value
    removeMsg(method)

    if(methodValue === ""){
        showMsg(method,"*Choisir un methode",false)
        isValid=false
    }
    else if(methodValue === "Carte"){
        showMsg(method,"Payment methode valide ✓",true)
        
        const Number = document.querySelector("input[name='cardNumber']")
        const postal = document.querySelector("input[name='PostalCode']")
        const adress = document.querySelector("input[name='adresse']")
        const region = document.querySelector("select[name='region']")

        if (!Number.value || Number.value.length < 15) {
            showMsg(Number, "*Numéro de carte invalide", false)
            isValid = false
        } else {
            showMsg(Number, "Valide ✓", true)
        }

        // CVV
        if (!postal.value || postal.value.length < 4) {
            showMsg(postal, "*Code postal invalide", false)
            isValid = false
        } else {
            showMsg(postal, "Valide ✓", true)
        }

        // Holder
        if (!adress.value.trim()) {
            showMsg(adress, "*L'adresse est obligatoire", false)
            isValid = false
        } else {
            showMsg(adress, "Valide ✓", true)
        }

        // Type
        if (!region.value) {
            showMsg(region, "*Choisir votre region", false)
            isValid = false
        } else {
            showMsg(region, "Valide ✓", true)
        }
    } else 
        showMsg(method,"Payment methode valide ✓",true)

    var Souscription=document.querySelector("input[name='date_souscription']")
    var SouscriptionValue=Souscription.value
    removeMsg(Souscription)

    if(!SouscriptionValue){
        showMsg(Souscription,"*La date de souscription est obligatoire",false)
        isValid=false
    }else {
        const today = new Date().toISOString().split("T")[0]
        if(SouscriptionValue >= today){
            showMsg(Souscription, "Date Souscription valide ✓", true)
        }else {
            showMsg(Souscription, "*La date doit être dans le futur.", false)
            isValid = false
        }
    }
    var Expiration=document.querySelector("input[name='date_expiration']")
    var ExpirationValue=Expiration.value
    removeMsg(Expiration)

    if(!ExpirationValue){
        showMsg(Expiration,"*La date d'expiration est obligation",false)
        isValid=false
    }
    else if(SouscriptionValue >= ExpirationValue)
    {
        showMsg(Expiration, "*La date d'expiration doit être supérieur au date de souscription.", false)
        isValid = false
    }
    else {
        const day=new Date().toISOString().split("T")[0]
        if(ExpirationValue >= day){
            showMsg(Expiration,"Date Expiration valide ✓",true)
        } else{
            showMsg(Expiration,"*La date doit être dans le futur",false)
            isValid=false
        }
    }

    var montant=document.getElementById("Montant_paye")
    var montantValue=montant.value
    removeMsg(montant)

    if(!montantValue || montantValue <= 100){
        showMsg(montant,"*Montant paye invalide",false)
        isValid=false
    } else
        showMsg(montant,"Montant paye valide ✓",true)

    var choix=document.getElementById("Choix")
    var choixValue=choix.value
    removeMsg(choix)

    if(choixValue === ""){
        showMsg(choix,"*Choisir un offre",false)
        isValid=false
    } else 
        showMsg(choix,"Choix valide ✓",true)
    return isValid
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