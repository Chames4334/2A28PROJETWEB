<?php

declare(strict_types=1);

class AdminController
{
    public function __construct(
        private Post $postModel,
        private Reply $replyModel,
        private User $userModel
    ) {
    }

    public function dashboard(): void
    {
        $this->requireAdmin();
        $this->adminView('back_office/admin/dashboard', [
            'pageTitle'       => 'Admin — tableau de bord',
            'adminNavActive'  => 'dashboard',
            'adminKpis'       => [
                'users_total'     => $this->userModel->countAll(),
                'posts_active'    => $this->postModel->countByStatut('actif'),
                'posts_masque'    => $this->postModel->countByStatut('masque'),
                'replies_active'  => $this->replyModel->countByStatut('actif'),
                'replies_masque'  => $this->replyModel->countByStatut('masque'),
            ],
        ]);
    }

    public function posts(): void
    {
        $this->requireAdmin();
        $posts = $this->postModel->getAllForAdmin();
        $this->adminView('back_office/admin/posts', [
            'pageTitle'       => 'Admin — tous les sujets',
            'posts'           => $posts,
            'adminNavActive'  => 'posts',
        ]);
    }

    public function editPost(int $id): void
    {
        $this->requireAdmin();
        $post = $this->postModel->findByIdForAdmin($id);
        if ($post === null) {
            flash_set('error', 'Sujet introuvable.');
            redirect('admin_posts.php');
        }
        $this->adminView('back_office/admin/post_edit', [
            'pageTitle'       => 'Admin — modifier un sujet',
            'post'            => $post,
            'errors'          => [],
            'adminNavActive'  => 'posts',
        ]);
    }

    public function updatePost(int $id): void
    {
        $this->requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('admin_post_edit.php?id=' . $id);
        }
        if (!csrf_verify($_POST['_csrf'] ?? null)) {
            flash_set('error', 'Session expirée.');
            redirect('admin_post_edit.php?id=' . $id);
        }
        $post = $this->postModel->findByIdForAdmin($id);
        if ($post === null) {
            flash_set('error', 'Sujet introuvable.');
            redirect('admin_posts.php');
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
            $this->adminView('back_office/admin/post_edit', [
                'pageTitle'       => 'Admin — modifier un sujet',
                'post'            => array_merge($post, ['titre' => $titre, 'contenu' => $contenu]),
                'errors'          => $errors,
                'adminNavActive'  => 'posts',
            ]);
            return;
        }
        $this->postModel->adminUpdate($id, $titre, $contenu);
        flash_set('success', 'Sujet mis à jour.');
        redirect('admin_posts.php');
    }

    public function deletePost(int $id): void
    {
        $this->requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('admin_posts.php');
        }
        if (!csrf_verify($_POST['_csrf'] ?? null)) {
            flash_set('error', 'Session expirée.');
            redirect('admin_posts.php');
        }
        if (!$this->postModel->adminSoftDelete($id)) {
            flash_set('error', 'Impossible de masquer ce sujet.');
            redirect('admin_posts.php');
        }
        flash_set('success', 'Sujet masqué.');
        redirect('admin_posts.php');
    }

    public function replies(): void
    {
        $this->requireAdmin();
        $replies = $this->replyModel->getAllForAdmin();
        $this->adminView('back_office/admin/replies', [
            'pageTitle'       => 'Admin — toutes les réponses',
            'replies'         => $replies,
            'adminNavActive'  => 'replies',
        ]);
    }

    public function editReply(int $id): void
    {
        $this->requireAdmin();
        $reply = $this->replyModel->findByIdForAdmin($id);
        if ($reply === null) {
            flash_set('error', 'Réponse introuvable.');
            redirect('admin_replies.php');
        }
        $this->adminView('back_office/admin/reply_edit', [
            'pageTitle'       => 'Admin — modifier une réponse',
            'reply'           => $reply,
            'errors'          => [],
            'adminNavActive'  => 'replies',
        ]);
    }

    public function updateReply(int $id): void
    {
        $this->requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('admin_reply_edit.php?id=' . $id);
        }
        if (!csrf_verify($_POST['_csrf'] ?? null)) {
            flash_set('error', 'Session expirée.');
            redirect('admin_reply_edit.php?id=' . $id);
        }
        $reply = $this->replyModel->findByIdForAdmin($id);
        if ($reply === null) {
            flash_set('error', 'Réponse introuvable.');
            redirect('admin_replies.php');
        }
        $contenu = trim((string) ($_POST['contenu'] ?? ''));
        if ($contenu === '') {
            $this->adminView('back_office/admin/reply_edit', [
                'pageTitle'       => 'Admin — modifier une réponse',
                'reply'           => array_merge($reply, ['contenu' => $contenu]),
                'errors'          => ['Le contenu est obligatoire.'],
                'adminNavActive'  => 'replies',
            ]);
            return;
        }
        $this->replyModel->adminUpdateContent($id, $contenu);
        flash_set('success', 'Réponse mise à jour.');
        redirect('admin_replies.php');
    }

    public function deleteReply(int $id): void
    {
        $this->requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('admin_replies.php');
        }
        if (!csrf_verify($_POST['_csrf'] ?? null)) {
            flash_set('error', 'Session expirée.');
            redirect('admin_replies.php');
        }
        if (!$this->replyModel->adminSoftDelete($id)) {
            flash_set('error', 'Impossible de masquer cette réponse.');
            redirect('admin_replies.php');
        }
        flash_set('success', 'Réponse masquée.');
        redirect('admin_replies.php');
    }

    /**
     * @param array<string, mixed> $data
     */
    private function adminView(string $relativePath, array $data): void
    {
        $data['layoutMainClass'] = 'main-content--admin';
        view($relativePath, $data);
    }

    private function requireAdmin(): void
    {
        $uid = current_user_id();
        if ($uid === null) {
            flash_set('error', 'Connexion requise.');
            redirect('login.php');
        }
        if (!$this->userModel->hasAdminRole($uid)) {
            flash_set('error', 'Accès refusé — réservé aux administrateurs.');
            redirect('index.php');
        }
    }
}
