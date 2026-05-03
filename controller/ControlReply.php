<?php
// controller/ControlReply.php
include_once __DIR__ . '/../config.php';
include_once __DIR__ . '/../model/Reply.php';

class ControlReply {

    public function getRepliesByPost($post_id) {
        $db = config::getConnexion();
        try {
            $sql = "
                SELECT r.*,
                       u.nom, u.prenom,
                       (SELECT COUNT(*) FROM reaction rc WHERE rc.reply_id = r.id AND rc.type_reaction = 'like')    AS nb_likes,
                       (SELECT COUNT(*) FROM reaction rc WHERE rc.reply_id = r.id AND rc.type_reaction = 'dislike') AS nb_dislikes
                FROM reply r
                LEFT JOIN users u ON u.id = r.user_id
                WHERE r.post_id = :post_id AND r.statut = 'actif'
                ORDER BY r.parent_reply_id ASC, r.created_at ASC
            ";
            $req = $db->prepare($sql);
            $req->execute(['post_id' => $post_id]);
            return $req->fetchAll();
        } catch (Exception $e) { die('Erreur: ' . $e->getMessage()); }
    }

    public function getAllRepliesAdmin() {
        $db = config::getConnexion();
        try {
            $sql = "
                SELECT r.*,
                       u.nom, u.prenom,
                       p.titre AS post_titre,
                       (SELECT COUNT(*) FROM report rp WHERE rp.reply_id = r.id) AS nb_reports
                FROM reply r
                LEFT JOIN users u ON u.id  = r.user_id
                LEFT JOIN post  p ON p.id  = r.post_id
                ORDER BY r.created_at DESC
            ";
            return $db->query($sql);
        } catch (Exception $e) { die('Erreur: ' . $e->getMessage()); }
    }

    public function getRepliesByUser($user_id, $sort = 'recent') {
        $db = config::getConnexion();
        try {
            $orderBy = $sort === 'likes' ? 'nb_likes DESC, r.created_at DESC' : 'r.created_at DESC';
            $sql = "
                SELECT r.*,
                       u.nom, u.prenom,
                       p.titre AS post_titre,
                       (SELECT COUNT(*) FROM reaction rc WHERE rc.reply_id = r.id AND rc.type_reaction = 'like')    AS nb_likes,
                       (SELECT COUNT(*) FROM reaction rc WHERE rc.reply_id = r.id AND rc.type_reaction = 'dislike') AS nb_dislikes
                FROM reply r
                LEFT JOIN users u ON u.id = r.user_id
                LEFT JOIN post  p ON p.id = r.post_id
                WHERE r.user_id = :user_id AND r.statut != 'supprime'
                ORDER BY $orderBy
            ";
            $req = $db->prepare($sql);
            $req->execute(['user_id' => $user_id]);
            return $req->fetchAll();
        } catch (Exception $e) { die('Erreur: ' . $e->getMessage()); }
    }

    public function getReplyById($id) {
        $db = config::getConnexion();
        try {
            $sql = "
                SELECT r.*, u.nom, u.prenom
                FROM reply r
                LEFT JOIN users u ON u.id = r.user_id
                WHERE r.id = :id
            ";
            $req = $db->prepare($sql);
            $req->execute(['id' => $id]);
            return $req->fetch();
        } catch (Exception $e) { die('Erreur: ' . $e->getMessage()); }
    }

    public function addReply($reply) {
        $db = config::getConnexion();
        try {
            $sql = "INSERT INTO reply (post_id, user_id, parent_reply_id, contenu, statut)
                    VALUES (:post_id, :user_id, :parent_reply_id, :contenu, :statut)";
            $req = $db->prepare($sql);
            $req->execute([
                'post_id'         => $reply->getPostId(),
                'user_id'         => $reply->getUserId(),
                'parent_reply_id' => $reply->getParentReplyId(),
                'contenu'         => $reply->getContenu(),
                'statut'          => $reply->getStatut(),
            ]);
            return $db->lastInsertId();
        } catch (Exception $e) { die('Erreur: ' . $e->getMessage()); }
    }

    public function updateReply($reply, $id) {
        $db = config::getConnexion();
        try {
            $sql = "UPDATE reply SET contenu = :contenu, statut = :statut WHERE id = :id";
            $req = $db->prepare($sql);
            $req->execute([
                'contenu' => $reply->getContenu(),
                'statut'  => $reply->getStatut(),
                'id'      => $id,
            ]);
            return true;
        } catch (Exception $e) { die('Erreur: ' . $e->getMessage()); }
    }

    public function deleteReply($id) {
        $db = config::getConnexion();
        try {
            $req = $db->prepare("UPDATE reply SET statut = 'supprime' WHERE id = :id");
            $req->execute(['id' => $id]);
            return true;
        } catch (Exception $e) { die('Erreur: ' . $e->getMessage()); }
    }

    public function hardDeleteReply($id) {
        $db = config::getConnexion();
        try {
            $db->prepare("DELETE FROM reaction WHERE reply_id = :id")->execute(['id' => $id]);
            $db->prepare("DELETE FROM reply    WHERE id       = :id")->execute(['id' => $id]);
            return true;
        } catch (Exception $e) { die('Erreur: ' . $e->getMessage()); }
    }

    public function countReplies() {
        $db = config::getConnexion();
        return $db->query("SELECT COUNT(*) FROM reply WHERE statut = 'actif'")->fetchColumn();
    }
}
