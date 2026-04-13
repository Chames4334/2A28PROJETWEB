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
        if(!empty($_POST['OffreID'])){
            $of->updateOffre($offre,$_POST['OffreID']);
        }
        else
            $of->addOffre($offre);
        header("Location: addOffre.php");
    }
    if (isset($_GET['delete'])) {
        $of->deleteOffre($_GET['delete']);
    }
    
    $offre=$of->listeOffre();

    $action = $_GET['action'] ?? 'list';
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Offres</title>
        <link rel="stylesheet" href="./assets/css/font.css">
    </head>
    <body>
        <div class="layout">
            <div class="sidebar">
                <img src="../images/logo.png" alt="logo" height="65" width="90">
                <h2>GreenSecure</h2>
                <a href="#">Dashboard</a>
                <a href="#">Offres</a>
                <a href="#">Utilisateurs</a>
                <a href="#">Paramètres</a>
            </div>
            <div class="main">
                <div class="topbar" style="font-size: larger;">
                    <h1>Offres</h1>
                    <?php if ($action == 'list') { ?>
                    <a href="addOffre.php?action=add" class="btn-primary">
                        + Add Offer
                    </a>
                    <?php } else { ?>
                        <a href="addOffre.php" class="btn-primary">
                            ← Back
                        </a>
                    <?php } ?>
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
                            <form method="post" class="form">
                                <label>Choisir le type d'offre:</label>
                                <select name="Type">
                                    <option>Assurance de voiture</option>
                                    <option>Assurance de vie</option>
                                    <option>Assurance d'habitation</option>
                                </select>
                                <input type="hidden" name="OffreID" value="<?= $_GET['OffreID'] ?? '' ?>">
                                <br><br>
                                <label>Title:</label>
                                <input type="text" name="Title" placeholder="donner votre title">
                                <br><br>
                                <label>Prix mensuel:</label>
                                <input type="text" placeholder="donner votre prix mensuel" name="Prix_mensuel">
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
                                <button type="submit">Submit</button>
                            </form>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </body>
</html>