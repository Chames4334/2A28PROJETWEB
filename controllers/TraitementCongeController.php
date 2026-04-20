<?php
require_once __DIR__ . '/../models/TraitementConge.php';

class TraitementCongeController {
    public function index() {
        $model = new TraitementConge();
        $traitements = $model->all();
        require __DIR__ . '/../views/frontoffice/traitements_list.php';
    }

    public function create() {
        $model = new TraitementConge();
        $conges = $model->getConges();
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $model->setData($_POST);
            $errors = $model->validate();

            if (empty($errors)) {
                if ($model->save()) {
                    header('Location: index.php?action=traitementIndex');
                    exit;
                }
                $errors[] = 'Impossible d\'enregistrer le traitement.';
            }
        }

        require __DIR__ . '/../views/frontoffice/traitements_create.php';
    }

    public function edit($id) {
        $model = new TraitementConge();
        $traitement = $model->find((int)$id);
        $conges = $model->getConges();
        $errors = [];

        if (!$traitement) {
            header('Location: index.php?action=traitementAdminIndex');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $model->setData($_POST);
            $model->setId((int)$id);
            $errors = $model->validate();

            if (empty($errors)) {
                if ($model->save()) {
                    header('Location: index.php?action=traitementAdminIndex');
                    exit;
                }
                $errors[] = 'Impossible de mettre à jour le traitement.';
            }
        }

        require __DIR__ . '/../views/backoffice/traitements_edit.php';
    }

    public function delete($id) {
        $model = new TraitementConge();
        if ($model->delete((int)$id)) {
            header('Location: index.php?action=traitementAdminIndex');
            exit;
        }
        header('Location: index.php?action=traitementAdminIndex');
        exit;
    }

    public function adminIndex() {
        $model = new TraitementConge();
        $traitements = $model->all();
        require __DIR__ . '/../views/backoffice/traitements_list.php';
    }
}
