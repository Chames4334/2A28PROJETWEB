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
 *   reaction: Reaction,
 *   auth: AuthController,
 *   posts: PostController,
 *   replies: ReplyController,
 *   admin: AdminController,
 *   reactions: ReactionController
 * }
 */
function forum_app(PDO $pdo): array
{
    $user = new User($pdo);
    $post = new Post($pdo);
    $reply = new Reply($pdo);
    $reaction = new Reaction($pdo);

    return [
        'user'      => $user,
        'post'      => $post,
        'reply'     => $reply,
        'reaction'  => $reaction,
        'auth'      => new AuthController($user),
        'posts'     => new PostController($post, $reply, $reaction),
        'replies'   => new ReplyController($post, $reply),
        'admin'     => new AdminController($post, $reply, $user),
        'reactions' => new ReactionController($post, $reply, $reaction),
    ];
}
