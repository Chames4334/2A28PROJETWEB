<?php

declare(strict_types=1);

class PostController
{
    public function __construct(
        private Post $postModel,
        private Reply $replyModel,
        private Reaction $reactionModel
    ) {
    }

    public function index(): void
    {
        $posts = $this->postModel->getAllActiveWithMeta();
        view('front_office/post/index', [
            'pageTitle' => 'Forum — sujets récents',
            'posts'     => $posts,
        ]);
    }

    public function show(int $id): void
    {
        $post = $this->postModel->findActiveById($id);
        if ($post === null) {
            http_response_code(404);
            view('front_office/post/not_found', ['pageTitle' => 'Sujet introuvable']);
            return;
        }

        $replies = $this->replyModel->getActiveTopLevelByPostId($id);
        $uid = current_user_id();
        $replyIds = array_map(static fn (array $r): int => (int) $r['id'], $replies);
        $reactionSummary = $this->reactionModel->summarizeForPostPage($id, $uid, $replyIds);

        view('front_office/post/show', [
            'pageTitle'         => $post['titre'],
            'post'              => $post,
            'replies'           => $replies,
            'canEdit'           => $uid !== null && (int) $post['user_id'] === $uid,
            'reactionSummary'   => $reactionSummary,
        ]);
    }

    public function create(): void
    {
        $this->requireAuth();
        view('front_office/post/create', [
            'pageTitle' => 'Nouveau sujet',
            'errors'    => [],
            'old'       => ['titre' => '', 'contenu' => ''],
        ]);
    }

    public function store(): void
    {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('create.php');
        }

        if (!csrf_verify($_POST['_csrf'] ?? null)) {
            view('front_office/post/create', [
                'pageTitle' => 'Nouveau sujet',
                'errors'    => ['Session expirée. Réessayez.'],
                'old'       => $_POST,
            ]);
            return;
        }

        $titre = trim((string) ($_POST['titre'] ?? ''));
        $contenu = trim((string) ($_POST['contenu'] ?? ''));

        $errors = [];
        if ($titre === '') {
            $errors[] = 'Le titre est obligatoire.';
        }
        if ($contenu === '') {
            $errors[] = 'Le contenu est obligatoire.';
        }

        if ($errors !== []) {
            view('front_office/post/create', [
                'pageTitle' => 'Nouveau sujet',
                'errors'    => $errors,
                'old'       => ['titre' => $titre, 'contenu' => $contenu],
            ]);
            return;
        }

        $userId = (int) current_user_id();
        $newId = $this->postModel->create($userId, $titre, $contenu);
        flash_set('success', 'Sujet publié.');
        redirect('post.php?id=' . $newId);
    }

    public function mine(): void
    {
        $this->requireAuth();
        $userId = (int) current_user_id();
        $posts = $this->postModel->getActiveByUserId($userId);
        view('front_office/post/mine', [
            'pageTitle' => 'Mes sujets',
            'posts'     => $posts,
        ]);
    }

    public function edit(int $id): void
    {
        $this->requireAuth();
        $uid = (int) current_user_id();
        $post = $this->postModel->findActiveById($id);
        if ($post === null || (int) $post['user_id'] !== $uid) {
            http_response_code(403);
            flash_set('error', 'Vous ne pouvez pas modifier ce sujet.');
            redirect('index.php');
        }

        view('front_office/post/edit', [
            'pageTitle' => 'Modifier le sujet',
            'post'      => $post,
            'errors'    => [],
        ]);
    }

    public function update(int $id): void
    {
        $this->requireAuth();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('post_edit.php?id=' . $id);
        }

        $uid = (int) current_user_id();
        if (!csrf_verify($_POST['_csrf'] ?? null)) {
            flash_set('error', 'Session expirée.');
            redirect('post_edit.php?id=' . $id);
        }

        $titre = trim((string) ($_POST['titre'] ?? ''));
        $contenu = trim((string) ($_POST['contenu'] ?? ''));

        $errors = [];
        if ($titre === '') {
            $errors[] = 'Le titre est obligatoire.';
        }
        if ($contenu === '') {
            $errors[] = 'Le contenu est obligatoire.';
        }

        $post = $this->postModel->findActiveById($id);
        if ($post === null || (int) $post['user_id'] !== $uid) {
            http_response_code(403);
            flash_set('error', 'Action non autorisée.');
            redirect('index.php');
        }

        if ($errors !== []) {
            view('front_office/post/edit', [
                'pageTitle' => 'Modifier le sujet',
                'post'      => array_merge($post, ['titre' => $titre, 'contenu' => $contenu]),
                'errors'    => $errors,
            ]);
            return;
        }

        $this->postModel->updateContent($id, $uid, $titre, $contenu);
        flash_set('success', 'Sujet mis à jour.');
        redirect('post.php?id=' . $id);
    }

    public function destroy(int $id): void
    {
        $this->requireAuth();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('post.php?id=' . $id);
        }

        $uid = (int) current_user_id();
        if (!csrf_verify($_POST['_csrf'] ?? null)) {
            flash_set('error', 'Session expirée.');
            redirect('post.php?id=' . $id);
        }

        if (!$this->postModel->softDeleteByAuthor($id, $uid)) {
            flash_set('error', 'Impossible de supprimer ce sujet.');
            redirect('post.php?id=' . $id);
        }

        flash_set('success', 'Sujet retiré du forum.');
        redirect('index.php');
    }

    private function requireAuth(): void
    {
        if (current_user_id() === null) {
            flash_set('error', 'Connexion requise.');
            redirect('login.php');
        }
    }
}
