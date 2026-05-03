<?php
    include "C:/xampp/htdocs/GreenSecure/Controller/ControlOffre.php";
    include "C:/xampp/htdocs/GreenSecure/Controller/ControlTypes.php";
    include "C:/xampp/htdocs/GreenSecure/Controller/Controlnscription.php";

    $CntrlInscri=new Controlnscription();
    $Cntrlt=new ControlTypes();
    $of=new controlOffre();

    
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Statestique</title>
        <link rel="stylesheet" href="./assets/css/font.css">
        <script></script>
    </head>
    <body>
        <div class="layout">
            <div class="main">
                <div class="topbar" style="font-size: larger;">
                    <h1 style="text-align: center;">Statistiques</h1>
                    <div  style="text-align: left;">
                        <a class="btn-primary">← Back</a>
                    </div>
                    <div class="content">
                        <div class="card">
                            <select>
                                
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>