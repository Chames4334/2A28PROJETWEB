<?php

declare(strict_types=1);

class ReactionController
{
    public function __construct(
        private Post $postModel,
        private Reply $replyModel,
        private Reaction $reactionModel
    ) {
    }

    public function store(): void
    {
        if (current_user_id() === null) {
            flash_set('error', 'Connexion requise pour réagir.');
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

        $userId = (int) current_user_id();
        $target = (string) ($_POST['target'] ?? '');
        $type = (string) ($_POST['type'] ?? '');
        $redirectPostId = (int) ($_POST['post_id'] ?? 0);

        if (!in_array($type, ['like', 'dislike'], true)) {
            flash_set('error', 'Type de réaction invalide.');
            redirect($redirectPostId > 0 ? 'post.php?id=' . $redirectPostId : 'index.php');
        }

        if ($target === 'post') {
            $postId = (int) ($_POST['target_id'] ?? 0);
            if ($postId <= 0) {
                redirect('index.php');
            }
            $post = $this->postModel->findActiveById($postId);
            if ($post === null) {
                flash_set('error', 'Sujet introuvable.');
                redirect('index.php');
            }
            $this->reactionModel->applyToPost($userId, $postId, $type);
            redirect('post.php?id=' . $postId);
        }

        if ($target === 'reply') {
            $replyId = (int) ($_POST['target_id'] ?? 0);
            if ($replyId <= 0 || $redirectPostId <= 0) {
                redirect('index.php');
            }
            $reply = $this->replyModel->findActiveById($replyId);
            if ($reply === null || (int) $reply['post_id'] !== $redirectPostId) {
                flash_set('error', 'Réponse introuvable.');
                redirect($redirectPostId > 0 ? 'post.php?id=' . $redirectPostId : 'index.php');
            }
            $this->reactionModel->applyToReply($userId, $replyId, $type);
            redirect('post.php?id=' . $redirectPostId);
        }

        flash_set('error', 'Cible invalide.');
        redirect($redirectPostId > 0 ? 'post.php?id=' . $redirectPostId : 'index.php');
    }
}
