document.addEventListener("DOMContentLoaded", function () {

    const methodSelect = document.getElementById("Payment_method")
    const container = document.getElementById("variables-container")

    methodSelect.addEventListener("change", function () {

        container.innerHTML = ""

        if (this.value === "Carte") {
            container.innerHTML = `
                <hr style="margin:15px 0; border:1px solid #ccc;">
                
                <div class="var-field-row">
                    <label>Numéro de carte:</label>
                    <input type="number" name="cardNumber">
                </div>

                <div class="var-field-row">
                    <label>Code Postale:</label>
                    <input type="number" name="PostalCode">
                </div>

                <div class="var-field-row">
                    <label>Adresse de facturation:</label>
                    <input type="text" name="adresse" placeholder="Ex: 12 Rue de Habib Bourguiba">
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
                        <option value="Tataouine">Tataouine</option>
                    </select>
                </div>
            `;
        }
    });
});
function validerFormulaire(){
    let isValid=true

    var method=document.getElementById("Payment_method")
    var methodValue=method.value
    if (!method) return false;
    resetState(method)

    if(methodValue === ""){
        setError(method)
        isValid=false
    }
    else if(methodValue === "Carte"){
        setSuccess(method)
        const Number = document.querySelector("input[name='cardNumber']")
        const postal = document.querySelector("input[name='PostalCode']")
        const adress = document.querySelector("input[name='adresse']")
        const region = document.querySelector("select[name='region']")

        // Card Number
        resetState(Number)
        if (!Number.value || Number.value.length < 15) {
            setError(Number)
            isValid = false
        } else {
            setSuccess(Number)
        }

        // CVV
        resetState(postal)
        if (!postal.value || postal.value.length < 4) {
            setError(postal)
            isValid = false
        } else {
            setSuccess(postal)
        }

        resetState(adress)
        // Holder
        if (!adress.value.trim()) {
            setError(adress)
            isValid = false
        } else {
            setSuccess(adress)
        }

        resetState(region)
        // Type
        if (!region.value) {
            setError(region)
            isValid = false
        } else {
            setSuccess(region)
        }
    } else 
        setSuccess(method)

    var Souscription=document.querySelector("input[name='date_souscription']")
    var SouscriptionValue=Souscription.value
    resetState(Souscription)

    if(!SouscriptionValue){
        setError(Souscription)
        isValid=false
    }else {
        const today = new Date().toISOString().split("T")[0]
        if(SouscriptionValue >= today){
            setSuccess(Souscription)
        }else {
            setError(Souscription)
            isValid = false
        }
    }
    var Expiration=document.querySelector("input[name='date_expiration']")
    var ExpirationValue=Expiration.value
    resetState(Expiration)

    if(!ExpirationValue){
        setError(Expiration)
        isValid=false
    }
    else if(SouscriptionValue >= ExpirationValue)
    {
        setError(Expiration)
        isValid = false
    }
    else {
        const day=new Date().toISOString().split("T")[0]
        if(ExpirationValue >= day){
            setSuccess(Expiration)
        } else{
            setError(Expiration)
            isValid=false
        }
    }
    var montant = document.getElementById("Montant_paye")
    var montantValue = montant.value

    resetState(montant)

    if (!montantValue || montantValue <= 0) {
        setError(montant)
        isValid = false
    }/*else if(montantValue >=){

    } */else {
        setSuccess(montant)
    }
    const message = document.getElementById("form-message")

    if (isValid) {
        message.textContent = "Submit validé avec succès ✔"
        message.className = "success"
    } else {
        message.textContent = "*Erreur dans le formulaire"
        message.className = "error"
    }
    return isValid
}
function setError(input) {
    input.classList.remove("input-success")
    input.classList.add("input-error")
}

function setSuccess(input) {
    input.classList.remove("input-error")
    input.classList.add("input-success")
}

function resetState(input) {
    input.classList.remove("input-error", "input-success")
}