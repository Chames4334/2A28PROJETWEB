<?php
// controller/ControlReaction.php
include_once __DIR__ . '/../config.php';
include_once __DIR__ . '/../model/Reaction.php';

class ControlReaction {

    public function getAllReactionsAdmin() {
        $db = config::getConnexion();
        try {
            $sql = "
                SELECT rc.*,
                       u.nom, u.prenom,
                       p.titre   AS post_titre,
                       r.contenu AS reply_contenu
                FROM reaction rc
                LEFT JOIN users u ON u.id = rc.user_id
                LEFT JOIN post  p ON p.id = rc.post_id
                LEFT JOIN reply r ON r.id = rc.reply_id
                ORDER BY rc.created_at DESC
            ";
            return $db->query($sql);
        } catch (Exception $e) { die('Erreur: ' . $e->getMessage()); }
    }

    public function getReactionById($id) {
        $db = config::getConnexion();
        try {
            $req = $db->prepare("SELECT * FROM reaction WHERE id = :id");
            $req->execute(['id' => $id]);
            return $req->fetch();
        } catch (Exception $e) { die('Erreur: ' . $e->getMessage()); }
    }

    // Get existing reaction of a user on a post OR reply
    public function getUserReaction($user_id, $post_id = null, $reply_id = null) {
        $db = config::getConnexion();
        try {
            if ($post_id) {
                $req = $db->prepare("SELECT * FROM reaction WHERE user_id = :u AND post_id = :p");
                $req->execute(['u' => $user_id, 'p' => $post_id]);
            } else {
                $req = $db->prepare("SELECT * FROM reaction WHERE user_id = :u AND reply_id = :r");
                $req->execute(['u' => $user_id, 'r' => $reply_id]);
            }
            return $req->fetch();
        } catch (Exception $e) { die('Erreur: ' . $e->getMessage()); }
    }

    // Toggle: add / change / remove reaction
    public function toggleReaction($user_id, $type, $post_id = null, $reply_id = null) {
        $db = config::getConnexion();
        try {
            $existing = $this->getUserReaction($user_id, $post_id, $reply_id);

            if ($existing) {
                if ($existing['type_reaction'] === $type) {
                    // Same type → remove (toggle off)
                    $db->prepare("DELETE FROM reaction WHERE id = :id")->execute(['id' => $existing['id']]);
                    return 'removed';
                } else {
                    // Different type → switch like ↔ dislike
                    $req = $db->prepare("UPDATE reaction SET type_reaction = :type WHERE id = :id");
                    $req->execute(['type' => $type, 'id' => $existing['id']]);
                    return 'changed';
                }
            } else {
                // New reaction
                $sql = "INSERT INTO reaction (user_id, post_id, reply_id, type_reaction)
                        VALUES (:u, :p, :r, :t)";
                $req = $db->prepare($sql);
                $req->execute([
                    'u' => $user_id,
                    'p' => $post_id,
                    'r' => $reply_id,
                    't' => $type,
                ]);
                return 'added';
            }
        } catch (Exception $e) { die('Erreur: ' . $e->getMessage()); }
    }

    public function deleteReaction($id) {
        $db = config::getConnexion();
        try {
            $req = $db->prepare("DELETE FROM reaction WHERE id = :id");
            $req->execute(['id' => $id]);
            return true;
        } catch (Exception $e) { die('Erreur: ' . $e->getMessage()); }
    }

    public function countReactions() {
        $db = config::getConnexion();
        return $db->query("SELECT COUNT(*) FROM reaction")->fetchColumn();
    }

    public function countByType($type) {
        $db = config::getConnexion();
        $req = $db->prepare("SELECT COUNT(*) FROM reaction WHERE type_reaction = :t");
        $req->execute(['t' => $type]);
        return $req->fetchColumn();
    }
}
