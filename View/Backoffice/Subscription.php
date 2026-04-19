<?php
    include "C:/xampp/htdocs/GreenSecure/Controller/Controlnscription.php";
    include "C:/xampp/htdocs/GreenSecure/Controller/ControlOffre.php";

    $CntrlInscri=new Controlnscription();
    if(isset($_POST['Payment_status'])){
        $inscri= new Inscription(
            new DateTime($_POST['date_souscription']),
            new DateTime($_POST['date_expiration']),
            $_POST['Payment_status'],
            $_POST['Payment_method'],
            (int)$_POST['Montant_paye'],
            new DateTime(),
            $_POST['Choix']
        );
        if(!empty($_POST['InscriptionID']))
            $CntrlInscri->updateInscription($inscri,$_POST['InscriptionID']);
        else
            $CntrlInscri->addInscription($inscri);
        header("location: Subscription.php");
    }

    if (isset($_GET['delete'])) {
        $CntrlInscri->deleteInscription($_GET['delete']);
    }
    $controlOffre=new controlOffre();
    $offre=$controlOffre->listeOffre();

    $subscription=$CntrlInscri->listeInscription();
    $action = $_GET['action'] ?? 'list';

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Subscription</title>
        <link rel="stylesheet" href="./assets/css/style.css">
        <script src="./assets/js/inscription.js"></script>
    </head>
    <body>
        <div class="layout">
            <div class="sidebar">
                <img src="../images/logo.png" alt="logo" height="65" width="90">
                <h2>GreenSecure</h2>
                <a href="#">Dashboard</a>
                <a href="./addOffre.php">Offres</a>
                <a href="./addType.php">Assurance Types</a>
                <a href="#">subscriptions</a>
            </div>
            <div class="main">
                <div class="topbar" style="font-size: larger;">
                    <h1>Abonnement</h1>
                    <div  style="text-align: right;">
                        <?php if ($action == 'list') { ?>
                        <a href="Subscription.php?action=add" class="btn-primary">
                            + Add New Subscription
                        </a>
                        <?php } else { ?>
                            <a href="Subscription.php" class="btn-primary">
                                ← Back
                            </a>
                        <?php } ?>
                        <a class="btn-primary" href="../FrontOffice/Finance.php">
                            FrontOffice
                        </a>
                    </div>
                </div>
                <div class="content">
                    <?php if ($action == 'list') { ?>
                        <div class="card">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Date souscription</th>
                                        <th>Date expiration</th>
                                        <th>Payment status</th>
                                        <th>Payment method</th>
                                        <th>Montant paye</th>
                                        <th>Choix d'offre</th>
                                        <th>Created At</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $i = 1; 
                                    foreach ($subscription as $s) { ?>
                                        <tr>
                                            <td class="row-number"><?= $i++ ?></td>

                                            <td><?= $s['date_souscription'] ?></td>
                                            <td><?= $s['date_expiration'] ?></td>
                                            <td><?= $s['Payment_status'] ?></td>
                                            <td><?= $s['Payment_method'] ?></td>
                                            <td><?= $s['Montant_paye'] ?></td>
                                            <td><?= $s['Choix'] ?></td>
                                            <td><?= $s['Created_AT'] ?></td>
                                            <td>
                                                <a class="link-btn" href="Subscription.php?action=add&InscriptionID=<?= $s['InscriptionID'] ?>" onclick="return confirm('Update this offre?')">Edit</a>
                                            </td>
                                            <td>
                                                <a class="link-btn" href="Subscription.php?delete=<?= $s['InscriptionID'] ?>" onclick="return confirm('Delete this offre?')">Delete</a>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    <?php } ?>
                    <?php if($action=='add'){ ?>
                        <div class="card">
                            <h1>Ajouter un Abonnement</h1>
                            <form method="post" enctype="multipart/form-data" class="form" onsubmit="return validerInscription()">
                                <input type="hidden" name="InscriptionID" value="<?= $_GET['InscriptionID'] ?? '' ?>">
                                <label>methode de payment:</label>
                                <select id="Payment_method" name="Payment_method">
                                    <option>--Veuillez choisir la methode de payment--</option>
                                    <option value="Carte">Carte</option>
                                    <option>Virement</option>
                                    <option>Cheque</option>
                                    <option>Especes</option>
                                </select>
                                <label>Date de souscription:</label>
                                <input type="date" name="date_souscription">
                                <label>Date d'expiration:</label>
                                <input type="date" name="date_expiration">
                                <label>Status du payment:</label>
                                <select id="Payment_status" name="Payment_status">
                                    <option>Pending</option>
                                    <option>Paid</option>
                                    <option>Failed</option>
                                    <option>Refunded</option>
                                </select>
                                <label>Montant paye:</label>
                                <input type="number" id="Montant_paye" name="Montant_paye">
                                <label>Choisir un offre</label>
                                <select id="Choix" name="Choix">
                                    <option>--Veuillez choisir une offre--</option>
                                    <?php foreach($offre as $o){ ?>
                                        <option value="<?= htmlspecialchars($o['Title']) ?>">
                                            <?= htmlspecialchars($o['Title']) ?>
                                        </option>
                                    <?php } ?>
                                </select>
                                <div id="variables-container"></div>
                                <button type="submit" value="Add abonne">Submit</button>
                            </form>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </body>
</html>