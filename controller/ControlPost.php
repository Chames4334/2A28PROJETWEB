<?php
// controller/ControlPost.php
include_once __DIR__ . '/../config.php';
include_once __DIR__ . '/../model/Post.php';

class ControlPost {

    public function listePosts() {
        $db = config::getConnexion();
        try {
            $sql = "
                SELECT p.*,
                       u.nom, u.prenom,
                       (SELECT COUNT(*) FROM reply    r  WHERE r.post_id  = p.id AND r.statut = 'actif')       AS nb_replies,
                       (SELECT COUNT(*) FROM reaction rc WHERE rc.post_id = p.id AND rc.type_reaction = 'like')    AS nb_likes,
                       (SELECT COUNT(*) FROM reaction rc WHERE rc.post_id = p.id AND rc.type_reaction = 'dislike') AS nb_dislikes
                FROM post p
                LEFT JOIN users u ON u.id = p.user_id
                WHERE p.statut != 'supprime'
                ORDER BY p.is_pinned DESC, p.created_at DESC
            ";
            return $db->query($sql);
        } catch (Exception $e) { die('Erreur: ' . $e->getMessage()); }
    }

    public function searchPosts($query) {
        $db = config::getConnexion();
        try {
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
                ORDER BY p.is_pinned DESC, p.created_at DESC
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
                       (SELECT COUNT(*) FROM reaction rc WHERE rc.post_id = p.id AND rc.type_reaction = 'dislike') AS nb_dislikes
                FROM post p
                LEFT JOIN users u ON u.id = p.user_id
                ORDER BY p.is_pinned DESC, p.created_at DESC
            ";
            return $db->query($sql);
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
