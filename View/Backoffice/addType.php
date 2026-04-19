<?php
    include "C:/xampp/htdocs/GreenSecure/Controller/ControlTypes.php";

    $defaultImage="./images/default.png";
    $Cntrlt=new ControlTypes();
    if(isset($_POST['Titre'])){
        if(isset($_FILES['image']) && $_FILES['image']['error'] === 0){
            $imageName = time() . '_' . $_FILES['image']['name'];
            move_uploaded_file($_FILES['image']['tmp_name'], __DIR__ . "/images/". $imageName);
        } else {
            $imageName = "default.png";
        }
        $type=new AssuranceType(
            $_POST['Titre'],
            $_POST['Description'],
            $imageName,
            new DateTime()
        );
        /*$type->loadVariablesFromPost();*/
        if(!empty($_POST['TypeID']))
            $Cntrlt->updateType($type,$_POST['TypeID']);
        else
            $Cntrlt->addType($type);
        header("Location: addType.php");
    }

    if (isset($_GET['delete'])) {
        $Cntrlt->deleteType($_GET['delete']);
    }

    $type=$Cntrlt->listeType();

    $action = $_GET['action'] ?? 'list';

    /*$editVariables = [];
    if ($action === 'add' && !empty($_GET['TypeID'])) {
        foreach ($type as $row) {
            if ($row['TypeID'] == $_GET['TypeID'] && !empty($row['variables_json'])) {
                $tmp = new AssuranceType('', '',"default.png", new DateTime(), []);
                $tmp->loadVariablesFromJson($row['variables_json']);
                $editVariables = $tmp->getVariables();
                break;
            }
        }
    }*/


?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Types</title>
        <link rel="stylesheet" href="./assets/css/style.css">
        <script defer src="./assets/js/type.js"></script>
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
                <a href="./Subscription.php">subscriptions</a>
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
                        <a class="btn-primary" href="../../View/FrontOffice/Finance.php">FrontOffice</a>
                    </div>
                </div>
                <div class="content">
                    <?php if ($action == 'list') { ?>
                        <div class="card">
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
                            <h1>Ajouter un Type</h1>
                            <form method="post" enctype="multipart/form-data" class="form" onsubmit="return validerType()">
                                <input type="hidden" name="TypeID" value="<?= $_GET['TypeID'] ?? '' ?>">
                                <label>Titre:</label>
                                <input type="text" id="Titre" name="Titre" placeholder="donner un Titre pour cet assurance">
                                <label>Description:</label>
                                <textarea id="Description" name="Description" cols="80" rows="5" placeholder="donner une description pour cet assurance"></textarea>
                                <label>Select Photo</label>
                                <div style="text-align: center; margin: 15px 0;">
                                    <label for="imageUpload" style="cursor: pointer;">
                                        <img id="preview" src="<?= $defaultImage ?>" height="150" width="150" style="align-items: center;">
                                    </label>
                                </div>
                                <input type="file" id="imageUpload" name="image" accept="image/*" style="border-radius: 10px; border: 2px solid #ccc; display:none;">
                                <!--<div class="row">
                                    <label>Nombre des variable dans ce type:</label>
                                    <input type="number" id="nmbr" placeholder="ex: 2">
                                    <button type="button" onclick="genererVariables()">click</button>
                                </div>
                                <div id="variables-container" style="margin-top: 10px;">
                                    <?php /*
                                    foreach ($editVariables as $idx => $v): ?>
                                        <div class="var-field-row">
                                            <label>Var <?= $idx + 1 ?>:</label>
                                            <input type="text" name="var_name[]" value="<?= htmlspecialchars($v->getName()) ?>" placeholder="Nom de la variable">
                                            <select name="var_type[]">
                                                <?php foreach (['VARCHAR', 'INT', 'DATE'] as $opt): ?>
                                                    <option value="<?= $opt ?>"
                                                        <?= $v->getType() === $opt ? 'selected' : '' ?>>
                                                        <?= $opt ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    <?php endforeach; */?>
                                </div>-->
                                <button type="submit" value="Add Type">Submit</button>
                            </form>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </body>
</html>