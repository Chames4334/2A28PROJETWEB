<?php
require_once __DIR__ . '/../models/Conge.php';

class CongeController {
    public function index() {
        $model = new Conge();
        $conges = $model->all();
        require __DIR__ . '/../views/frontoffice/list.php';
    }

    public function create() {
        $model = new Conge();
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $model->setData($_POST);
            $errors = $model->validate();

            if (empty($errors)) {
                if ($model->save()) {
                    header('Location: index.php?action=index');
                    exit;
                }
                $errors[] = 'Impossible d\'enregistrer le congé.';
            }
        }

        require __DIR__ . '/../views/frontoffice/create.php';
    }

    public function edit($id) {
        $model = new Conge();
        $conge = $model->find((int)$id);
        $errors = [];

        if (!$conge) {
            header('Location: index.php?action=adminIndex');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $model->setData($_POST);
            $model->setId((int)$id);
            $errors = $model->validate();

            if (empty($errors)) {
                if ($model->save()) {
                    header('Location: index.php?action=adminIndex');
                    exit;
                }
                $errors[] = 'Impossible de mettre à jour le congé.';
            }
        }

        require __DIR__ . '/../views/backoffice/edit.php';
    }

    public function delete($id) {
        $model = new Conge();
        if ($model->delete((int)$id)) {
            header('Location: index.php?action=adminIndex');
            exit;
        }
        header('Location: index.php?action=adminIndex');
        exit;
    }

    public function adminIndex() {
        $model = new Conge();
        $conges = $model->all();
        require __DIR__ . '/../views/backoffice/list.php';
    }
}
