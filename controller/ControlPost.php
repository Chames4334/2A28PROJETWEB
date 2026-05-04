<?php
// controller/ControlPost.php
include_once __DIR__ . '/../config.php';
include_once __DIR__ . '/../model/Post.php';
include_once __DIR__ . '/ControlAI.php';

class ControlPost {

    private function tableExists($table) {
        $db = config::getConnexion();
        try {
            $req = $db->prepare("
                SELECT COUNT(*)
                FROM information_schema.TABLES
                WHERE TABLE_SCHEMA = DATABASE()
                AND TABLE_NAME = :table_name
            ");
            $req->execute(['table_name' => $table]);
            return (int)$req->fetchColumn() > 0;
        } catch (Exception $e) { return false; }
    }

    private function columnExists($table, $column) {
        $db = config::getConnexion();
        try {
            $req = $db->prepare("
                SELECT COUNT(*)
                FROM information_schema.COLUMNS
                WHERE TABLE_SCHEMA = DATABASE()
                AND TABLE_NAME = :table_name
                AND COLUMN_NAME = :column_name
            ");
            $req->execute(['table_name' => $table, 'column_name' => $column]);
            return (int)$req->fetchColumn() > 0;
        } catch (Exception $e) { return false; }
    }

    public function tagsTableExists() {
        return $this->tableExists('tags');
    }

    public function tagSystemReady() {
        return $this->tagsTableExists() && $this->columnExists('post', 'tag_id');
    }

    private function getTagSelectSql() {
        return $this->tagSystemReady()
            ? "t.name AS tag_name, t.color AS tag_color"
            : "NULL AS tag_name, NULL AS tag_color";
    }

    private function getTagJoinSql() {
        return $this->tagSystemReady()
            ? "LEFT JOIN tags t ON t.id = p.tag_id"
            : "";
    }

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
            $tagSelect = $this->getTagSelectSql();
            $tagJoin = $this->getTagJoinSql();
            $sql = "
                SELECT p.*,
                       u.nom, u.prenom,
                       $tagSelect,
                       (SELECT COUNT(*) FROM reply    r  WHERE r.post_id  = p.id AND r.statut = 'actif')       AS nb_replies,
                       (SELECT COUNT(*) FROM reaction rc WHERE rc.post_id = p.id AND rc.type_reaction = 'like')    AS nb_likes,
                       (SELECT COUNT(*) FROM reaction rc WHERE rc.post_id = p.id AND rc.type_reaction = 'dislike') AS nb_dislikes
                FROM post p
                LEFT JOIN users u ON u.id = p.user_id
                $tagJoin
                WHERE p.statut = 'actif'
                ORDER BY $orderBy
            ";
            return $db->query($sql);
        } catch (Exception $e) { die('Erreur: ' . $e->getMessage()); }
    }

    public function searchPosts($query, $sort = 'date', $direction = 'desc') {
        $db = config::getConnexion();
        try {
            $orderBy = $this->getOrderBy($sort, $direction);
            $tagSelect = $this->getTagSelectSql();
            $tagJoin = $this->getTagJoinSql();
            $sql = "
                SELECT p.*,
                       u.nom, u.prenom,
                       $tagSelect,
                       (SELECT COUNT(*) FROM reply    r  WHERE r.post_id  = p.id AND r.statut = 'actif')       AS nb_replies,
                       (SELECT COUNT(*) FROM reaction rc WHERE rc.post_id = p.id AND rc.type_reaction = 'like')    AS nb_likes,
                       (SELECT COUNT(*) FROM reaction rc WHERE rc.post_id = p.id AND rc.type_reaction = 'dislike') AS nb_dislikes
                FROM post p
                LEFT JOIN users u ON u.id = p.user_id
                $tagJoin
                WHERE p.statut = 'actif'
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
            $tagSelect = $this->getTagSelectSql();
            $tagJoin = $this->getTagJoinSql();
            $sql = "
                SELECT p.*,
                       u.nom, u.prenom,
                       $tagSelect,
                       (SELECT COUNT(*) FROM reply    r  WHERE r.post_id  = p.id)                                  AS nb_replies,
                       (SELECT COUNT(*) FROM reaction rc WHERE rc.post_id = p.id AND rc.type_reaction = 'like')    AS nb_likes,
                       (SELECT COUNT(*) FROM reaction rc WHERE rc.post_id = p.id AND rc.type_reaction = 'dislike') AS nb_dislikes,
                       (SELECT COUNT(*) FROM report   rp WHERE rp.post_id = p.id)                                  AS nb_reports
                FROM post p
                LEFT JOIN users u ON u.id = p.user_id
                $tagJoin
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
            $tagSelect = $this->getTagSelectSql();
            $tagJoin = $this->getTagJoinSql();
            $sql = "
                SELECT p.*,
                       u.nom, u.prenom,
                       $tagSelect,
                       (SELECT COUNT(*) FROM reply    r  WHERE r.post_id  = p.id AND r.statut = 'actif')       AS nb_replies,
                       (SELECT COUNT(*) FROM reaction rc WHERE rc.post_id = p.id AND rc.type_reaction = 'like')    AS nb_likes,
                       (SELECT COUNT(*) FROM reaction rc WHERE rc.post_id = p.id AND rc.type_reaction = 'dislike') AS nb_dislikes
                FROM post p
                LEFT JOIN users u ON u.id = p.user_id
                $tagJoin
                WHERE p.user_id = :user_id AND p.statut = 'actif'
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
            $tagSelect = $this->getTagSelectSql();
            $tagJoin = $this->getTagJoinSql();
            $sql = "
                SELECT p.*,
                       u.nom, u.prenom,
                       $tagSelect,
                       (SELECT COUNT(*) FROM reaction rc WHERE rc.post_id = p.id AND rc.type_reaction = 'like')    AS nb_likes,
                       (SELECT COUNT(*) FROM reaction rc WHERE rc.post_id = p.id AND rc.type_reaction = 'dislike') AS nb_dislikes
                FROM post p
                LEFT JOIN users u ON u.id = p.user_id
                $tagJoin
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
            if ($this->tagSystemReady()) {
                $sql = "INSERT INTO post (user_id, titre, contenu, is_pinned, statut, tag_id)
                        VALUES (:user_id, :titre, :contenu, :is_pinned, :statut, :tag_id)";
            } else {
                $sql = "INSERT INTO post (user_id, titre, contenu, is_pinned, statut)
                        VALUES (:user_id, :titre, :contenu, :is_pinned, :statut)";
            }
            $req = $db->prepare($sql);
            $params = [
                'user_id'   => $post->getUserId(),
                'titre'     => $post->getTitre(),
                'contenu'   => $post->getContenu(),
                'is_pinned' => $post->getIsPinned(),
                'statut'    => $post->getStatut(),
            ];
            if ($this->tagSystemReady()) $params['tag_id'] = $post->getTagId();
            $req->execute($params);
            $postId = $db->lastInsertId();
            $aiCtrl = new ControlAI();
            $score = $aiCtrl->scorePost($postId, $post->getTitre(), $post->getContenu());
            if ($score !== null && $score > ControlAI::LOW_SCORE_THRESHOLD && $post->getStatut() === 'actif') {
                $this->addAutoResponderReply($postId, $aiCtrl, $post->getTitre(), $post->getContenu());
            }
            return $postId;
        } catch (Exception $e) { die('Erreur: ' . $e->getMessage()); }
    }

    private function addAutoResponderReply($postId, $aiCtrl, $title, $content) {
        $reply = $aiCtrl->generateAutoReply($title, $content);
        if (!$reply) return false;

        $db = config::getConnexion();
        $req = $db->prepare("
            INSERT INTO reply (post_id, user_id, parent_reply_id, contenu, statut)
            VALUES (:post_id, :user_id, NULL, :contenu, 'actif')
        ");
        return $req->execute([
            'post_id' => $postId,
            'user_id' => ControlAI::AUTO_RESPONDER_USER_ID,
            'contenu' => $reply,
        ]);
    }

    public function updatePost($post, $id) {
        $db = config::getConnexion();
        try {
            if ($this->tagSystemReady()) {
                $sql = "UPDATE post
                        SET titre     = :titre,
                            contenu   = :contenu,
                            is_pinned = :is_pinned,
                            statut    = :statut,
                            tag_id    = :tag_id
                        WHERE id = :id";
            } else {
                $sql = "UPDATE post
                        SET titre     = :titre,
                            contenu   = :contenu,
                            is_pinned = :is_pinned,
                            statut    = :statut
                        WHERE id = :id";
            }
            $req = $db->prepare($sql);
            $params = [
                'titre'     => $post->getTitre(),
                'contenu'   => $post->getContenu(),
                'is_pinned' => $post->getIsPinned(),
                'statut'    => $post->getStatut(),
                'id'        => $id,
            ];
            if ($this->tagSystemReady()) $params['tag_id'] = $post->getTagId();
            $req->execute($params);
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

    public function getAllTags() {
        if (!$this->tagsTableExists()) return [];

        $db = config::getConnexion();
        try {
            return $db->query("SELECT id, name, color FROM tags ORDER BY name ASC")->fetchAll();
        } catch (Exception $e) { die('Erreur: ' . $e->getMessage()); }
    }

    public function tagExists($tag_id) {
        if ($tag_id === null) return true;
        if (!$this->tagsTableExists()) return false;

        $db = config::getConnexion();
        try {
            $req = $db->prepare("SELECT COUNT(*) FROM tags WHERE id = :id");
            $req->execute(['id' => $tag_id]);
            return (int)$req->fetchColumn() > 0;
        } catch (Exception $e) { die('Erreur: ' . $e->getMessage()); }
    }

    public function getTagById($id) {
        if (!$this->tagsTableExists()) return false;

        $db = config::getConnexion();
        try {
            $req = $db->prepare("SELECT id, name, color FROM tags WHERE id = :id");
            $req->execute(['id' => $id]);
            return $req->fetch();
        } catch (Exception $e) { die('Erreur: ' . $e->getMessage()); }
    }

    public function addTag($name, $color) {
        if (!$this->tagsTableExists()) return false;

        $db = config::getConnexion();
        try {
            $req = $db->prepare("INSERT INTO tags (name, color) VALUES (:name, :color)");
            $req->execute(['name' => $name, 'color' => $color]);
            return $db->lastInsertId();
        } catch (Exception $e) { die('Erreur: ' . $e->getMessage()); }
    }

    public function updateTag($id, $name, $color) {
        if (!$this->tagsTableExists()) return false;

        $db = config::getConnexion();
        try {
            $req = $db->prepare("UPDATE tags SET name = :name, color = :color WHERE id = :id");
            $req->execute(['name' => $name, 'color' => $color, 'id' => $id]);
            return true;
        } catch (Exception $e) { die('Erreur: ' . $e->getMessage()); }
    }

    public function deleteTag($id) {
        if (!$this->tagsTableExists()) return false;

        $db = config::getConnexion();
        try {
            $db->beginTransaction();
            if ($this->columnExists('post', 'tag_id')) {
                $db->prepare("UPDATE post SET tag_id = NULL WHERE tag_id = :id")->execute(['id' => $id]);
            }
            $db->prepare("DELETE FROM tags WHERE id = :id")->execute(['id' => $id]);
            $db->commit();
            return true;
        } catch (Exception $e) {
            $db->rollBack();
            die('Erreur: ' . $e->getMessage());
        }
    }

    public function getDailyPostCounts($days = 14) {
        $db = config::getConnexion();
        $days = max(1, min(90, (int)$days));
        try {
            $sql = "
                SELECT DATE(created_at) AS jour, COUNT(*) AS total
                FROM post
                WHERE statut != 'supprime'
                AND created_at >= DATE_SUB(CURDATE(), INTERVAL " . ($days - 1) . " DAY)
                GROUP BY DATE(created_at)
                ORDER BY jour ASC
            ";
            return $db->query($sql)->fetchAll();
        } catch (Exception $e) { die('Erreur: ' . $e->getMessage()); }
    }
}
