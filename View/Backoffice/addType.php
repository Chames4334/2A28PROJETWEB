<?php
    include "C:/xampp/htdocs/GreenSecure/Controller/ControlTypes.php";

    $defaultImage="./images/default.png";
    $Cntrlt=new ControlTypes();
    if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
        $search = strtolower(trim($_GET['recherche'] ?? ''));
        $type=$Cntrlt->listeType($search);
        $i = 1;
        foreach ($type as $t) { ?>
            <tr>
                <td class="row-number"><?= $i++ ?></td>
                <td><?= htmlspecialchars($t['Titre']) ?></td>
                <td><?= htmlspecialchars($t['Description']) ?></td>
                <td><?= htmlspecialchars($t['Published_AT']) ?></td>
                <td>
                    <a class="link-btn"
                    href="addType.php?action=add&TypeID=<?= $t['TypeID'] ?>">Edit</a>
                </td>
                <td>
                    <a class="link-btn"
                    href="addType.php?delete=<?= $t['TypeID'] ?>"
                    onclick="return confirm('Delete this type?')">Delete</a>
                </td>
            </tr>
        <?php }
        exit;
    }
    if(isset($_POST['Titre'])){
        if(isset($_FILES['image']) && $_FILES['image']['error'] === 0){
            $imageName = time() . '_' . $_FILES['image']['name'];
            move_uploaded_file($_FILES['image']['tmp_name'], __DIR__ . "/images/". $imageName);
        } else {
            $imageName = $_POST['old_image'] ?? "default.png";
        }
        $type=new AssuranceType(
            $_POST['Titre'],
            $_POST['Description'],
            $imageName,
            new DateTime()
        );
        if(!empty($_POST['TypeID']))
            $Cntrlt->updateType($type,$_POST['TypeID']);
        else
            $Cntrlt->addType($type);
        header("Location: addType.php");
    }

    if (isset($_GET['delete'])) {
        $Cntrlt->deleteType($_GET['delete']);
    }

    $type=$Cntrlt->listeType('');

    $action = $_GET['action'] ?? 'list';

    $t_edit = null;
    if (isset($_GET['TypeID'])) {
        foreach ($type as $t) {
            if ($t['TypeID'] == $_GET['TypeID']) {
                $t_edit = $t;
                break;
            }
        }
    }

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Types</title>
        <link rel="stylesheet" href="./assets/css/front.css">
        <script defer src="./assets/js/Type.js"></script>
        <style>
            .var-field-row {
                display: flex;
                align-items: center;
                gap: 12px;
                margin: 8px 0;
                flex-wrap: nowrap;
            }
            .var-field-row label {
                min-width: 90px;
                white-space: nowrap;
                flex-shrink: 0;
                font-weight: 600;
                margin: 0;
            }
            .var-field-row input {
                flex: 1;
                padding: 8px 12px;
                border: 1px solid #ccc;
                border-radius: 6px;
                margin: 0;
                min-width: 0;
            }
            .var-field-row select {
                flex: 0 0 130px;
                padding: 8px 12px;
                border: 1px solid #ccc;
                border-radius: 6px;
                margin: 0;
                background: white;
                cursor: pointer;
            }
        </style>
    </head>
    <body>
        <div class="layout">
            <div class="sidebar">
                <img src="../images/logo.png" alt="logo" height="65" width="90">
                <h2>GreenSecure</h2>
                <a href="#">Dashboard</a>
                <a href="./addOffre.php">Offres</a>
                <a href="./addType.php">Assurance Types</a>
            </div>
            <div class="main">
                <div class="topbar" style="font-size: larger;">
                    <h1>Types</h1>
                    <div  style="text-align: right;">
                        <?php if ($action == 'list') { ?>
                        <a href="addType.php?action=add" class="btn-primary">
                            + Add New Type
                        </a>
                        <?php } else { ?>
                            <a href="addType.php" class="btn-primary">
                                ← Back
                            </a>
                        <?php } ?>
                        <a class="btn-primary" href="./Statistique.php?addType">Statestique</a>
                        <a class="btn-primary" href="../../View/FrontOffice/Finance.php">FrontOffice</a>
                        <a id="themeToggle" class="btn-primary">Dark Mode</a>
                    </div>
                </div>
                <div class="content">
                    <?php if ($action == 'list') { ?>
                        <div class="card">
                            <div class="filter">
                                <input type="text" id="recherche" name="recherche" placeholder="🔍rech..." autocomplete="off">
                                <select id="tri" name="tri">
                                    <option value="az">A-Z</option>
                                    <option value="za">Z-A</option>
                                    <option value="date">D'apres la date</option>
                                </select>
                            </div>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Titre</th>
                                        <th>Description</th>
                                        <th>published AT</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $i = 1; 
                                    foreach ($type as $t) { ?>
                                        <tr>
                                            <td class="row-number"><?= $i++ ?></td>

                                            <td><?= $t['Titre'] ?></td>
                                            <td><?= $t['Description'] ?></td>
                                            <td><?= $t['Published_AT'] ?></td>
                                            <td>
                                                <a class="link-btn" href="addType.php?action=add&TypeID=<?= $t['TypeID'] ?>" onclick="return confirm('Update this offre?')">Edit</a>
                                            </td>
                                            <td>
                                                <a class="link-btn" href="addType.php?delete=<?= $t['TypeID'] ?>" onclick="return confirm('Delete this offre?')">Delete</a>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    <?php } ?>
                    <?php if ($action == 'add') { ?>
                        <div class="card">
                            <h1><?= $t_edit ? 'Modifier le Type' : 'Ajouter un Type' ?></h1>
                            <form method="post" enctype="multipart/form-data" class="form" onsubmit="return validerType()">
                                <input type="hidden" name="TypeID" value="<?= $_GET['TypeID'] ?? '' ?>">
                                <label>Titre:</label>
                                <input type="text" id="Titre" name="Titre" placeholder="donner un Titre pour cet assurance" value="<?= htmlspecialchars($t_edit['Titre'] ?? '') ?>">
                                <label>Description:</label>
                                <textarea id="Description" name="Description" cols="80" rows="5" placeholder="donner une description pour cet assurance"><?= htmlspecialchars($t_edit['Description'] ?? '') ?></textarea>
                                <label>Select Photo</label>
                                <div style="text-align: center; margin: 15px 0;">
                                    <label for="imageUpload" style="cursor: pointer;">
                                        <img id="preview" src="<?= ($t_edit && !empty($t_edit['Image'])) ? './images/' . htmlspecialchars($t_edit['Image']) : $defaultImage ?>" height="150" width="150" style="align-items: center;">
                                    </label>
                                </div>
                                <input type="file" id="imageUpload" name="image" accept="image/*" style="border-radius: 10px; border: 2px solid #ccc; display:none;">
                                <input type="hidden" name="old_image" value="<?= htmlspecialchars($t_edit['Image'] ?? 'default.png') ?>">
                                <button type="submit" value="Add Type">Submit</button>
                            </form>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </body>
</html>