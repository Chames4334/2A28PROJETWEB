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
                    <input type="number" name="cardNumber">
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
        const Number = document.querySelector("input[name='cardNumber']");
        const postal = document.querySelector("input[name='PostalCode']");
        const adress = document.querySelector("input[name='adresse']");
        const region = document.querySelector("select[name='region']");

        // Card Number
        if (!Number || !adress || !postal || !region) {
            console.log("Card inputs not found");
            return false;
        }
        if (!Number.value || Number.value.length < 8) {
            showMsg(Number, "*Numéro de carte invalide", false);
            isValid = false;
        } else {
            showMsg(Number, "Valide ✓", true);
        }

        // CVV
        if (!postal.value || postal.value.length < 4) {
            showMsg(cvv, "*Code postal invalide", false);
            isValid = false;
        } else {
            showMsg(cvv, "Valide ✓", true);
        }

        // Holder
        if (!adress.value.trim()) {
            showMsg(adress, "*L'adresse est obligatoire", false);
            isValid = false;
        } else {
            showMsg(holder, "Valide ✓", true);
        }

        // Type
        if (!region.value) {
            showMsg(region, "*Choisir votre region", false);
            isValid = false;
        } else {
            showMsg(region, "Valide ✓", true);
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
        const today = new Date().toISOString()
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
        const day=new Date().toISOString()
        if(ExpirationValue >= day){
            showMsg(Expiration,"Date Expiration valide ✓",true)
        } else{
            showMsg(Expiration,"*La date doit être dans le futur",false)
            isValid=false
        }
    }
    return isValid
}