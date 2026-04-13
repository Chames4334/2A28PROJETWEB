<?php
/**
 * Instanciation des modèles et contrôleurs (MVC léger).
 */

declare(strict_types=1);

/**
 * @return array{
 *   user: User,
 *   post: Post,
 *   reply: Reply,
 *   auth: AuthController,
 *   posts: PostController,
 *   replies: ReplyController
 * }
 */
function forum_app(PDO $pdo): array
{
    $user = new User($pdo);
    $post = new Post($pdo);
    $reply = new Reply($pdo);

    return [
        'user'    => $user,
        'post'    => $post,
        'reply'   => $reply,
        'auth'    => new AuthController($user),
        'posts'   => new PostController($post, $reply),
        'replies' => new ReplyController($post, $reply),
    ];
}
