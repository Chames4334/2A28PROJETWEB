<?php

declare(strict_types=1);

class ReplyController
{
    public function __construct(
        private Post $postModel,
        private Reply $replyModel
    ) {
    }

    public function store(): void
    {
        if (current_user_id() === null) {
            flash_set('error', 'Connexion requise pour répondre.');
            redirect('login.php');
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php');
        }

        if (!csrf_verify($_POST['_csrf'] ?? null)) {
            $pid = (int) ($_POST['post_id'] ?? 0);
            flash_set('error', 'Session expirée.');
            redirect($pid > 0 ? 'post.php?id=' . $pid : 'index.php');
        }

        $postId = (int) ($_POST['post_id'] ?? 0);
        $contenu = trim((string) ($_POST['contenu'] ?? ''));
        $userId = (int) current_user_id();

        if ($postId <= 0) {
            flash_set('error', 'Sujet invalide.');
            redirect('index.php');
        }

        if ($contenu === '') {
            flash_set('error', 'Le message ne peut pas être vide.');
            redirect('post.php?id=' . $postId);
        }

        $post = $this->postModel->findActiveById($postId);
        if ($post === null) {
            flash_set('error', 'Sujet introuvable.');
            redirect('index.php');
        }

        $this->replyModel->create($postId, $userId, $contenu);
        flash_set('success', 'Réponse publiée.');
        redirect('post.php?id=' . $postId);
    }

    public function destroy(int $replyId): void
    {
        if (current_user_id() === null) {
            flash_set('error', 'Connexion requise.');
            redirect('login.php');
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php');
        }

        if (!csrf_verify($_POST['_csrf'] ?? null)) {
            flash_set('error', 'Session expirée.');
            redirect('index.php');
        }

        $postId = (int) ($_POST['post_id'] ?? 0);
        $userId = (int) current_user_id();

        $reply = $this->replyModel->findActiveById($replyId);
        if ($reply === null || (int) $reply['user_id'] !== $userId) {
            flash_set('error', 'Action non autorisée.');
            redirect($postId > 0 ? 'post.php?id=' . $postId : 'index.php');
        }

        $this->replyModel->softDeleteByAuthor($replyId, $userId);
        flash_set('success', 'Réponse retirée.');
        redirect($postId > 0 ? 'post.php?id=' . $postId : 'index.php');
    }

    public function edit(int $replyId): void
    {
        if (current_user_id() === null) {
            flash_set('error', 'Connexion requise.');
            redirect('login.php');
        }
        $uid = (int) current_user_id();
        $reply = $this->replyModel->findActiveById($replyId);
        if ($reply === null || (int) $reply['user_id'] !== $uid) {
            flash_set('error', 'Vous ne pouvez pas modifier cette réponse.');
            redirect('index.php');
        }
        $post = $this->postModel->findActiveById((int) $reply['post_id']);
        if ($post === null) {
            flash_set('error', 'Sujet introuvable.');
            redirect('index.php');
        }
        view('front_office/reply/edit', [
            'pageTitle' => 'Modifier ma réponse',
            'reply'     => $reply,
            'post'      => $post,
            'errors'    => [],
        ]);
    }

    public function update(int $replyId): void
    {
        if (current_user_id() === null) {
            flash_set('error', 'Connexion requise.');
            redirect('login.php');
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('reply_edit.php?id=' . $replyId);
        }
        if (!csrf_verify($_POST['_csrf'] ?? null)) {
            flash_set('error', 'Session expirée.');
            redirect('reply_edit.php?id=' . $replyId);
        }
        $uid = (int) current_user_id();
        $reply = $this->replyModel->findActiveById($replyId);
        if ($reply === null || (int) $reply['user_id'] !== $uid) {
            flash_set('error', 'Action non autorisée.');
            redirect('index.php');
        }
        $postId = (int) $reply['post_id'];
        $contenu = trim((string) ($_POST['contenu'] ?? ''));
        if ($contenu === '') {
            $post = $this->postModel->findActiveById($postId);
            view('front_office/reply/edit', [
                'pageTitle' => 'Modifier ma réponse',
                'reply'     => array_merge($reply, ['contenu' => $contenu]),
                'post'      => $post ?? ['id' => $postId, 'titre' => ''],
                'errors'    => ['Le message ne peut pas être vide.'],
            ]);
            return;
        }
        $this->replyModel->updateContentByAuthor($replyId, $uid, $contenu);
        flash_set('success', 'Réponse mise à jour.');
        redirect('post.php?id=' . $postId);
    }
}
