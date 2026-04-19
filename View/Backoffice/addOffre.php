<?php
    include 'C:/xampp/htdocs/GreenSecure/Controller/ControlOffre.php';

    $of=new controlOffre();
    if(isset($_POST['Title'])){
        $offre=new Offre(
            $_POST['Title'],
            $_POST['Type'],
            (int)$_POST['Prix_mensuel'],
            new DateTime ($_POST['Date_Debut']),
            new DateTime ($_POST['Date_Fin']),
            $_POST['Status'],
        );
        if(!empty($_POST['OffreID']))
            $of->updateOffre($offre,$_POST['OffreID']);
        else
            $of->addOffre($offre);
        header("Location: addOffre.php");
    }
    if (isset($_GET['delete'])) {
        $of->deleteOffre($_GET['delete']);
    }

    $offre=$of->listeOffre();
    $types  = $of->listeTypes();

    $o_edit = null;
    if (isset($_GET['OffreID'])) {
        foreach($offre as $o) {
            if($o['OffreID'] == $_GET['OffreID']) {
                $o_edit = $o;
                break;
            }
        }
    }
    $action = $_GET['action'] ?? 'list';
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Offres</title>
        <link rel="stylesheet" href="./assets/css/style.css">
        <script src="./assets/js/script.js"></script>
    </head>
    <body>
        <div class="layout">
            <div class="sidebar">
                <img src="../images/logo.png" alt="logo" height="65" width="90">
                <h2>GreenSecure</h2>
                <a href="#">Dashboard</a>
                <a href="#">Offres</a>
                <a href="./addType.php">Assurance Types</a>
                <a href="./Subscription.php">subscriptions</a>
            </div>
            <div class="main">
                <div class="topbar" style="font-size: larger;">
                    <h1>Offres</h1>
                    <div  style="text-align: right;">
                        <?php if ($action == 'list') { ?>
                        <a href="addOffre.php?action=add" class="btn-primary">
                            + Add Offer
                        </a>
                        <?php } else { ?>
                            <a href="addOffre.php" class="btn-primary">
                                ← Back
                            </a>
                        <?php } ?>
                        <a class="btn-primary" href="../FrontOffice/Finance.php">FrontOffice</a>
                    </div>
                </div>
                <div class="content">
                    <?php if ($action == 'list') { ?>
                        <div class="card">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Title</th>
                                        <th>Type</th>
                                        <th>Prix</th>
                                        <th>Début</th>
                                        <th>Fin</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $i = 1; 
                                    foreach ($offre as $o) { ?>
                                        <tr>
                                            <td class="row-number"><?= $i++ ?></td>

                                            <td><?= $o['Title'] ?></td>
                                            <td><?= $o['Type'] ?></td>
                                            <td><?= $o['Prix_mensuel'] ?><label>DT</label></td>
                                            <td><?= $o['Date_Debut'] ?></td>
                                            <td><?= $o['Date_Fin'] ?></td>
                                            <td>
                                                <span class="status <?= $o['Status'] ?>">
                                                    <?= $o['Status'] ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a class="link-btn" href="addOffre.php?action=add&OffreID=<?= $o['OffreID'] ?>" onclick="return confirm('Update this offre?')">Edit</a>
                                            </td>
                                            <td>
                                                <a class="link-btn" href="addOffre.php?delete=<?= $o['OffreID'] ?>" onclick="return confirm('Delete this offre?')">Delete</a>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    <?php } ?>
                    <?php if ($action == 'add') { ?>
                        <div class="card">
                        <h1>Ajouter un Offre</h1>
                            <form method="post" class="form" onsubmit="return validerFormulaire()" novalidate>
                                <label>Choisir le type d'offre:</label>
                                <select id="Type" name="Type">
                                    <option value="">--Veuillez choisir une option--</option>
                                    <?php foreach($types as $t){ ?>
                                        <option value="<?= htmlspecialchars($t['Type']) ?>">
                                            <?= ($o_edit && $o_edit['Type'] == $t['Type']) ? 'selected' : '' ?>
                                            <?= htmlspecialchars($t['Type']) ?>
                                        </option>
                                    <?php } ?>
                                </select>
                                <input type="hidden" name="OffreID" value="<?= $_GET['OffreID'] ?? '' ?>">
                                <br><br>
                                <label>Title:</label>
                                <input type="text" id="Title" name="Title" placeholder="donner votre title">
                                <br><br>
                                <label>Prix mensuel:</label>
                                <input type="text" placeholder="donner votre prix mensuel" id="Prix_mensuel" name="Prix_mensuel">
                                <br><br>
                                <label>Date début:</label>
                                <input type="date" name="Date_Debut">
                                <br><br>
                                <label>Date fin:</label>
                                <input type="date" name="Date_Fin">
                                <br><br>
                                <label>Status:</label>
                                <select name="Status">
                                    <option>active</option>
                                    <option>inactive</option>
                                    <option>archived</option>
                                </select>
                                <br><br>
                                <button type="submit" value="Add Offre">Submit</button>
                            </form>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </body>
</html>