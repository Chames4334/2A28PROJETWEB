<?php 
    include "C:/xampp/htdocs/GreenSecure/Controller/ControlTypes.php";

    $cntrlF=new ControlTypes();
    $Finance=$cntrlF->listeType();
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Page Financiére</title>
        <link rel="stylesheet" href="./assets/css/frontOffice.css"/>
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
                <a href="#finance">Offre</a>
                <a href="#declaration">Declaration</a>
                <a href="#contacts">Contact</a>
                <a href="http://localhost/GreenSecure/View/Backoffice/addOffre.php">Managment</a>
            </div>
            <button type="button" name="sign-in">Sign up</button>
        </div>
        <div class="choix" id="finance">
            <h1>Choisir le type d'assurance</h1>
            <?php if (!empty($Finance)) { ?>
                <?php foreach($Finance as $f){  ?>
                    <a class="card" href="http://localhost/GreenSecure/View/FrontOffice/InscriptionPage.php?TypeID=<?= $f['TypeID'] ?>">
                        <img src="../Backoffice/images/<?= $f['Image'] ?>" alt="image">
                        <h2><?= htmlspecialchars($f['Titre']) ?></h2>
                        <p><?= htmlspecialchars($f['Description']) ?></p>
                    </a>
                <?php } ?>
            <?php } else {?>
                <p>Aucun type d'assurance disponible.</p>
            <?php } ?>
        </div>
        <div class="contacts" id="contacts">
            <p>📍 Tunis, Tunisie &nbsp;</p>
            <p>&nbsp;📞 +216 70 123 456 &nbsp;|&nbsp; ✉️ contact@greensecure.tn</p>
        </div>
    </body>
</html>