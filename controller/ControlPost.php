<?php
// controller/ControlPost.php
include_once __DIR__ . '/../config.php';
include_once __DIR__ . '/../model/Post.php';

class ControlPost {

    private function getOrderBy($sort = 'date', $direction = 'desc') {
        $sortMap = [
            'date'        => 'p.created_at',
            'reply_count' => 'nb_replies',
            'count'       => '(nb_likes + nb_dislikes)',
        ];

        $sortColumn = $sortMap[$sort] ?? $sortMap['date'];
        $direction = strtolower($direction) === 'asc' ? 'ASC' : 'DESC';

        return "p.is_pinned DESC, $sortColumn $direction";
    }

    public function listePosts($sort = 'date', $direction = 'desc') {
        $db = config::getConnexion();
        try {
            $orderBy = $this->getOrderBy($sort, $direction);
            $sql = "
                SELECT p.*,
                       u.nom, u.prenom,
                       (SELECT COUNT(*) FROM reply    r  WHERE r.post_id  = p.id AND r.statut = 'actif')       AS nb_replies,
                       (SELECT COUNT(*) FROM reaction rc WHERE rc.post_id = p.id AND rc.type_reaction = 'like')    AS nb_likes,
                       (SELECT COUNT(*) FROM reaction rc WHERE rc.post_id = p.id AND rc.type_reaction = 'dislike') AS nb_dislikes
                FROM post p
                LEFT JOIN users u ON u.id = p.user_id
                WHERE p.statut != 'supprime'
                ORDER BY $orderBy
            ";
            return $db->query($sql);
        } catch (Exception $e) { die('Erreur: ' . $e->getMessage()); }
    }

    public function searchPosts($query, $sort = 'date', $direction = 'desc') {
        $db = config::getConnexion();
        try {
            $orderBy = $this->getOrderBy($sort, $direction);
            $sql = "
                SELECT p.*,
                       u.nom, u.prenom,
                       (SELECT COUNT(*) FROM reply    r  WHERE r.post_id  = p.id AND r.statut = 'actif')       AS nb_replies,
                       (SELECT COUNT(*) FROM reaction rc WHERE rc.post_id = p.id AND rc.type_reaction = 'like')    AS nb_likes,
                       (SELECT COUNT(*) FROM reaction rc WHERE rc.post_id = p.id AND rc.type_reaction = 'dislike') AS nb_dislikes
                FROM post p
                LEFT JOIN users u ON u.id = p.user_id
                WHERE p.statut != 'supprime'
                AND (p.titre LIKE ? OR p.contenu LIKE ?)
                ORDER BY $orderBy
            ";
            $stmt = $db->prepare($sql);
            $stmt->execute(['%' . $query . '%', '%' . $query . '%']);
            return $stmt;
        } catch (Exception $e) { die('Erreur: ' . $e->getMessage()); }
    }

    public function listePostsAdmin() {
        $db = config::getConnexion();
        try {
            $sql = "
                SELECT p.*,
                       u.nom, u.prenom,
                       (SELECT COUNT(*) FROM reply    r  WHERE r.post_id  = p.id)                                  AS nb_replies,
                       (SELECT COUNT(*) FROM reaction rc WHERE rc.post_id = p.id AND rc.type_reaction = 'like')    AS nb_likes,
                       (SELECT COUNT(*) FROM reaction rc WHERE rc.post_id = p.id AND rc.type_reaction = 'dislike') AS nb_dislikes,
                       (SELECT COUNT(*) FROM report   rp WHERE rp.post_id = p.id)                                  AS nb_reports
                FROM post p
                LEFT JOIN users u ON u.id = p.user_id
                ORDER BY p.is_pinned DESC, p.created_at DESC
            ";
            return $db->query($sql);
        } catch (Exception $e) { die('Erreur: ' . $e->getMessage()); }
    }

    public function getForumUserById($id) {
        $db = config::getConnexion();
        try {
            $req = $db->prepare("SELECT id, nom, prenom, email, created_at FROM users WHERE id = :id");
            $req->execute(['id' => $id]);
            return $req->fetch();
        } catch (Exception $e) { die('Erreur: ' . $e->getMessage()); }
    }

    public function getPostsByUser($user_id, $sort = 'recent') {
        $db = config::getConnexion();
        try {
            $orderBy = $sort === 'likes' ? 'nb_likes DESC, p.created_at DESC' : 'p.created_at DESC';
            $sql = "
                SELECT p.*,
                       u.nom, u.prenom,
                       (SELECT COUNT(*) FROM reply    r  WHERE r.post_id  = p.id AND r.statut = 'actif')       AS nb_replies,
                       (SELECT COUNT(*) FROM reaction rc WHERE rc.post_id = p.id AND rc.type_reaction = 'like')    AS nb_likes,
                       (SELECT COUNT(*) FROM reaction rc WHERE rc.post_id = p.id AND rc.type_reaction = 'dislike') AS nb_dislikes
                FROM post p
                LEFT JOIN users u ON u.id = p.user_id
                WHERE p.user_id = :user_id AND p.statut != 'supprime'
                ORDER BY $orderBy
            ";
            $req = $db->prepare($sql);
            $req->execute(['user_id' => $user_id]);
            return $req->fetchAll();
        } catch (Exception $e) { die('Erreur: ' . $e->getMessage()); }
    }

    public function getPostById($id) {
        $db = config::getConnexion();
        try {
            $sql = "
                SELECT p.*,
                       u.nom, u.prenom,
                       (SELECT COUNT(*) FROM reaction rc WHERE rc.post_id = p.id AND rc.type_reaction = 'like')    AS nb_likes,
                       (SELECT COUNT(*) FROM reaction rc WHERE rc.post_id = p.id AND rc.type_reaction = 'dislike') AS nb_dislikes
                FROM post p
                LEFT JOIN users u ON u.id = p.user_id
                WHERE p.id = :id
            ";
            $req = $db->prepare($sql);
            $req->execute(['id' => $id]);
            return $req->fetch();
        } catch (Exception $e) { die('Erreur: ' . $e->getMessage()); }
    }

    public function addPost($post) {
        $db = config::getConnexion();
        try {
            $sql = "INSERT INTO post (user_id, titre, contenu, is_pinned, statut)
                    VALUES (:user_id, :titre, :contenu, :is_pinned, :statut)";
            $req = $db->prepare($sql);
            $req->execute([
                'user_id'   => $post->getUserId(),
                'titre'     => $post->getTitre(),
                'contenu'   => $post->getContenu(),
                'is_pinned' => $post->getIsPinned(),
                'statut'    => $post->getStatut(),
            ]);
            return $db->lastInsertId();
        } catch (Exception $e) { die('Erreur: ' . $e->getMessage()); }
    }

    public function updatePost($post, $id) {
        $db = config::getConnexion();
        try {
            $sql = "UPDATE post
                    SET titre     = :titre,
                        contenu   = :contenu,
                        is_pinned = :is_pinned,
                        statut    = :statut
                    WHERE id = :id";
            $req = $db->prepare($sql);
            $req->execute([
                'titre'     => $post->getTitre(),
                'contenu'   => $post->getContenu(),
                'is_pinned' => $post->getIsPinned(),
                'statut'    => $post->getStatut(),
                'id'        => $id,
            ]);
            return true;
        } catch (Exception $e) { die('Erreur: ' . $e->getMessage()); }
    }

    public function deletePost($id) {
        $db = config::getConnexion();
        try {
            $req = $db->prepare("UPDATE post SET statut = 'supprime' WHERE id = :id");
            $req->execute(['id' => $id]);
            return true;
        } catch (Exception $e) { die('Erreur: ' . $e->getMessage()); }
    }

    public function hardDeletePost($id) {
        $db = config::getConnexion();
        try {
            $db->beginTransaction();
            $db->prepare("DELETE FROM reaction WHERE post_id = :id")->execute(['id' => $id]);
            $db->prepare("DELETE FROM reply    WHERE post_id = :id")->execute(['id' => $id]);
            $db->prepare("DELETE FROM post     WHERE id      = :id")->execute(['id' => $id]);
            $db->commit();
            return true;
        } catch (Exception $e) {
            $db->rollBack();
            die('Erreur: ' . $e->getMessage());
        }
    }

    public function togglePin($id) {
        $db = config::getConnexion();
        try {
            $req = $db->prepare("UPDATE post SET is_pinned = IF(is_pinned = 1, 0, 1) WHERE id = :id");
            $req->execute(['id' => $id]);
            return true;
        } catch (Exception $e) { die('Erreur: ' . $e->getMessage()); }
    }

    public function countPosts() {
        $db = config::getConnexion();
        return $db->query("SELECT COUNT(*) FROM post WHERE statut != 'supprime'")->fetchColumn();
    }

    public function countPostsByStatut($statut) {
        $db = config::getConnexion();
        $req = $db->prepare("SELECT COUNT(*) FROM post WHERE statut = :s");
        $req->execute(['s' => $statut]);
        return $req->fetchColumn();
    }
}
