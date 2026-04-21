<?php

declare(strict_types=1);

class AuthController
{
    public function __construct(
        private User $userModel
    ) {
    }

    public function showLogin(): void
    {
        if (current_user_id() !== null) {
            redirect('index.php');
        }
        view('front_office/auth/login', [
            'pageTitle' => 'Connexion',
            'error'     => null,
        ]);
    }

    public function processLogin(): void
    {
        if (current_user_id() !== null) {
            redirect('index.php');
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('login.php');
        }

        if (!csrf_verify($_POST['_csrf'] ?? null)) {
            view('front_office/auth/login', [
                'pageTitle' => 'Connexion',
                'error'     => 'Session expirée. Réessayez.',
            ]);
            return;
        }

        $email = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');

        if ($email === '' || $password === '') {
            view('front_office/auth/login', [
                'pageTitle' => 'Connexion',
                'error'     => 'Email et mot de passe obligatoires.',
            ]);
            return;
        }

        $user = $this->userModel->findByEmail($email);
        if ($user === null || !password_verify($password, (string) $user['password_hash'])) {
            view('front_office/auth/login', [
                'pageTitle' => 'Connexion',
                'error'     => 'Identifiants incorrects.',
            ]);
            return;
        }

        if (($user['status'] ?? '') !== 'active') {
            view('front_office/auth/login', [
                'pageTitle' => 'Connexion',
                'error'     => 'Ce compte n’est pas autorisé à se connecter.',
            ]);
            return;
        }

        $_SESSION['user_id'] = (int) $user['id'];
        flash_set('success', 'Bienvenue !');
        redirect('index.php');
    }

    public function showRegister(): void
    {
        if (current_user_id() !== null) {
            redirect('index.php');
        }
        view('front_office/auth/register', [
            'pageTitle' => 'Inscription',
            'errors'    => [],
            'old'       => ['nom' => '', 'prenom' => '', 'email' => ''],
        ]);
    }

    public function processRegister(): void
    {
        if (current_user_id() !== null) {
            redirect('index.php');
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('register.php');
        }

        if (!csrf_verify($_POST['_csrf'] ?? null)) {
            view('front_office/auth/register', [
                'pageTitle' => 'Inscription',
                'errors'    => ['Session expirée. Réessayez.'],
                'old'       => $_POST,
            ]);
            return;
        }

        $nom = trim((string) ($_POST['nom'] ?? ''));
        $prenom = trim((string) ($_POST['prenom'] ?? ''));
        $email = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        $password2 = (string) ($_POST['password_confirm'] ?? '');

        $errors = [];
        if ($nom === '') {
            $errors[] = 'Le nom est obligatoire.';
        }
        if ($prenom === '') {
            $errors[] = 'Le prénom est obligatoire.';
        }
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email invalide.';
        }
        if (strlen($password) < 8) {
            $errors[] = 'Le mot de passe doit contenir au moins 8 caractères.';
        }
        if ($password !== $password2) {
            $errors[] = 'Les mots de passe ne correspondent pas.';
        }

        if ($email !== '' && $this->userModel->emailExists($email)) {
            $errors[] = 'Cet email est déjà utilisé.';
        }

        if ($errors !== []) {
            view('front_office/auth/register', [
                'pageTitle' => 'Inscription',
                'errors'    => $errors,
                'old'       => [
                    'nom'    => $nom,
                    'prenom' => $prenom,
                    'email'  => $email,
                ],
            ]);
            return;
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);
        $newId = $this->userModel->create($nom, $prenom, $email, $hash);
        $_SESSION['user_id'] = $newId;
        flash_set('success', 'Compte créé. Bienvenue !');
        redirect('index.php');
    }

    public function logout(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
        redirect('login.php');
    }
}
