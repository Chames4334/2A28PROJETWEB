<?php
    include "C:/xampp/htdocs/GreenSecure/Controller/Controlnscription.php";
    include "C:/xampp/htdocs/GreenSecure/Controller/ControlOffre.php";
    include "C:/xampp/htdocs/GreenSecure/Controller/ControlTypes.php";

    $cntrlType=new ControlTypes();
    $cntrlOffre=new controlOffre();
    $cntrlInscri=new Controlnscription();

    $type = $_GET['type'] ?? '';

    $data = [];
    $title="";

    switch ($type) {

        case "offre":
            $data=$cntrlOffre->listeOffre('');
            $title="Liste des Offres";
            break;

        case "Type":
            $data =$cntrlType->listeType('');
            $title="Liste des Types";
            break;

        case "inscription":
            $data=$cntrlInscri->listeInscription('');
            $title="Liste des Inscriptions";
            break;

        default:
            die("Type invalide");
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Export PDF</title>

        <style>
            body { font-family: Arial; }
            h2 { text-align:center; }

            table {
                width: 100%;
                border-collapse: collapse;
            }

            th {
                background: #d0e4f5;
            }

            th, td {
                border: 1px solid black;
                padding: 6px;
                text-align: center;
            }

            @media print {
                button { display:none; }
            }
        </style>
    </head>

    <body>
        <button onclick="window.print()">📄 Export PDF</button>
        <h2><?= $title ?></h2>
        <table>
            <tbody>
                <?php foreach ($data as $row): ?>
                    <tr>
                        <?php $rowWithoutId = array_slice($row, 1); ?>
                        <?php foreach ($rowWithoutId as $cell): ?>
                            <td><?= htmlspecialchars($cell) ?></td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>

            </tbody>
        </table>
    </body>
</html>