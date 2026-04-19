<?php
    include "C:/xampp/htdocs/GreenSecure/Controller/ControlOffre.php";
    include "C:/xampp/htdocs/GreenSecure/Controller/Controlnscription.php";

    $CntrlOffre=new controlOffre();
    $offre=$CntrlOffre->listeOffre();

    $ControlInscri=new Controlnscription();
    if($_SERVER["REQUEST_METHOD"]=="POST"){
        $valid = true;

        if (empty($_POST['Payment_method'])) $valid = false;
        if (empty($_POST['date_souscription'])) $valid = false;
        if (empty($_POST['date_expiration'])) $valid = false;
        if (empty($_POST['Montant_paye'])) $valid = false;

        if ($valid) {
            $inscri=new Inscription(
                new DateTime($_POST['date_souscription']),
                new DateTime($_POST['date_expiration']),
                "pending",
                $_POST['Payment_method'],
                (int)$_POST['Montant_paye'],
                new DateTime(),
                $_POST['Title'] ?? ''
            );
            if(empty($_POST['InscriptionID'])){
                $ok=$ControlInscri->addInscription($inscri);
                if($ok){
                    header("Location: InscriptionPage.php?action=add&OffreID=" . urlencode($_POST['Title']) . "&success=1");
                    exit();
                }
            }
        }
    }
    $action = $_GET['action'] ?? 'list';
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Inscription</title>
        <link rel="stylesheet" href="./assets/css/frontOffice.css">
        <script src="./assets/js/pageInscri.js" defer></script>
    </head>
    
    <body>
        <div class="animated-bg"></div>

        <div class="floating-shape shape1"></div>
        <div class="floating-shape shape2"></div>
        <div class="floating-shape shape3"></div>
        <div class="floating-shape shape4"></div>
        <div class="floating-shape shape5"></div>
        <div class="bord">
            <div class="logo_area">
                <img src="../images/logo.png" alt="logo" height="50" width="65">
            </div>
            <div class="slogon">
                <h1>GreenSecure</h1>
                <p>SLOGON</p>
            </div>
            <div class="links_area">
                <a href="http://localhost/GreenSecure/View/Frontoffice/Finance.php">Offre</a>
                <a href="#declaration">Declaration</a>
                <a href="#contacts">Contact</a>
                <a href="http://localhost/GreenSecure/View/Backoffice/addOffre.php">Managment</a>
            </div>
            <button type="button" name="sign-in">Sign up</button>
        </div>
        <div class="offre">
            <?php if($action=='list'){?>
                <h1>Choisir un Offre</h1>
                <?php if(!empty($offre)){
                    foreach($offre as $o){ 
                        if($o['Status']!=='inactive'){?>
                            <a class="card" href="InscriptionPage.php?action=add&OffreID=<?= urlencode($o['Title'])?>">
                                <div class="card-header"><h2><?= htmlspecialchars($o['Title']) ?></h2></div>
                                <div class="card-body">
                                    <div class="line">
                                        <span class="label">Prix</span>
                                        <span class="value"><?= htmlspecialchars($o['Prix_mensuel']) ?></span>
                                    </div>

                                    <div class="line">
                                        <span class="label">Début</span>
                                        <span class="value"><?= htmlspecialchars($o['Date_Debut']) ?></span>
                                    </div>

                                    <div class="line">
                                        <span class="label">Fin</span>
                                        <span class="value"><?= htmlspecialchars($o['Date_Fin']) ?></span>
                                    </div>
                                </div>
                            </a>
                        <?php } ?>
                    <?php }?>
                <?php } else{?>
                    <p>Aucun offre d'assurance disponible pour le moment.</p>
                <?php } ?>
            <?php } ?>
        </div>
        <div class="inscri" id="formulaire">
            <?php if($action == 'add'){ ?>
                <div class="card" style="align-items: center;">
                    <h1 style="text-align: center; color:white;">Remplir ce formulaire</h1>
                    <form method="post" class="form" onsubmit="return validerFormulaire()">
                        <input type="hidden" name="InscriptionID" value="<?= $_GET['InscriptionID'] ?? '' ?>">
                        <input type="hidden" name="Title" value="<?= htmlspecialchars($_GET['OffreID'] ?? '') ?>">
                        <select id="Payment_method" name="Payment_method">
                            <option value="">--Veuillez choisir la methode de payment--</option>
                            <option value="Carte">Carte</option>
                            <option>Virement</option>
                            <option>Cheque</option>
                            <option>Especes</option>
                        </select>
                        <label>Donner la date de souscription:</label>
                        <input type="date" name="date_souscription">
                        <label>Donner la date d'expiration:</label>
                        <input type="date" name="date_expiration">
                        <label>Montant Paye</label>
                        <input type="number" id="Montant_paye" name="Montant_paye" min=0>
                        <div id="variables-container"></div>
                        <button type="submit" value="Add abonne">Submit</button>
                        <p id="form-message"></p>
                        <script>
                            setTimeout(() => {
                                const msg = document.querySelector(".success")
                                if(msg) msg.style.display = "none"
                            }, 13000)
                        </script>
                        <?php if(isset($_GET['success'])): ?>
                            <script>
                                alert("Formulaire vaide ✔");
                            </script>
                        <?php endif; ?>
                    </form>
                </div>
            <?php } ?>
        </div>
        <div class="contacts" id="contacts">
            <p>📍 Tunis, Tunisie &nbsp;</p>
            <p>&nbsp;📞 +216 70 123 456 &nbsp;|&nbsp; ✉️ contact@greensecure.tn</p>
        </div>
        <script>
            const colors = ["#6FAF4C", "#A67C52", "#b11e1e", "#1c64ca", "#FFC300"];

            document.querySelectorAll(".offre .card").forEach(card => {
                const randomColor = colors[Math.floor(Math.random() * colors.length)];
                card.style.setProperty("--card-color", randomColor);
            })
        </script>
    </body>
</html>