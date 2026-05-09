const toggleBtn = document.getElementById("themeToggle");

if (localStorage.getItem("theme") === "dark") {
    document.body.classList.add("dark");
    if (toggleBtn) toggleBtn.textContent = "Light Mode";
}

if (toggleBtn) {
    toggleBtn.addEventListener("click", () => {
        document.body.classList.toggle("dark");

        if (document.body.classList.contains("dark")) {
            localStorage.setItem("theme", "dark");
            toggleBtn.textContent = "Light Mode";
        } else {
            localStorage.setItem("theme", "light");
            toggleBtn.textContent = "Dark Mode";
        }
    });
}

document.addEventListener("click", function(e) {
    const row = e.target.closest(".clickable-row");

    if (row) {
        // Prevent clicks on buttons
        if (e.target.closest("a")) return;

        const id = row.getAttribute("data-id");
        window.location.href = "./subscription.php?OffreID=" + id;
    }
});

var timer;
 
var rechercheInput = document.getElementById("recherche");
var triSelect      = document.getElementById("tri");
 
function fetchRows() {
    var query = encodeURIComponent(rechercheInput.value);
    fetch("addOffre.php?ajax=1&recherche=" + query)
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
        var aTitle = a.cells[1] ? a.cells[1].textContent.trim() : '';
        var bTitle = b.cells[1] ? b.cells[1].textContent.trim() : '';
        var aPrix  = a.cells[3] ? parseFloat(a.cells[3].textContent) : 0;
        var bPrix  = b.cells[3] ? parseFloat(b.cells[3].textContent) : 0;
        var aDate  = a.cells[4] ? new Date(a.cells[4].textContent.trim()) : 0;
        var bDate  = b.cells[4] ? new Date(b.cells[4].textContent.trim()) : 0;
 
        if (criterion === 'az')   return aTitle.localeCompare(bTitle);
        if (criterion === 'za')   return bTitle.localeCompare(aTitle);
        if (criterion === 'prix') return aPrix - bPrix;
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
        const today = new Date().toISOString().split("T")[0]
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
        const tommorow = new Date().toISOString().split("T")[0]
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