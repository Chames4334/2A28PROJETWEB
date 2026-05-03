<?php
// controller/ControlReport.php
include_once __DIR__ . '/../config.php';
include_once __DIR__ . '/../model/Report.php';

class ControlReport {

    public function hasUserReported($user_id, $post_id = null, $reply_id = null) {
        $db = config::getConnexion();
        try {
            if ($post_id) {
                $req = $db->prepare("SELECT COUNT(*) FROM report WHERE reporter_id = :u AND post_id = :p");
                $req->execute(['u' => $user_id, 'p' => $post_id]);
            } else {
                $req = $db->prepare("SELECT COUNT(*) FROM report WHERE reporter_id = :u AND reply_id = :r");
                $req->execute(['u' => $user_id, 'r' => $reply_id]);
            }
            return $req->fetchColumn() > 0;
        } catch (Exception $e) { die('Erreur: ' . $e->getMessage()); }
    }

    public function addReport($report) {
        $db = config::getConnexion();
        try {
            if ($this->hasUserReported($report->getReporterId(), $report->getPostId(), $report->getReplyId())) {
                return false;
            }

            $sql = "INSERT INTO report (reporter_id, post_id, reply_id, raison, statut)
                    VALUES (:reporter_id, :post_id, :reply_id, :raison, :statut)";
            $req = $db->prepare($sql);
            $req->execute([
                'reporter_id' => $report->getReporterId(),
                'post_id'     => $report->getPostId(),
                'reply_id'    => $report->getReplyId(),
                'raison'      => $report->getRaison(),
                'statut'      => $report->getStatut(),
            ]);
            return $db->lastInsertId();
        } catch (Exception $e) { die('Erreur: ' . $e->getMessage()); }
    }

    public function getReportedTargets($query = '', $types = ['post', 'reply'], $sort = 'recent') {
        $db = config::getConnexion();
        try {
            $includePosts = in_array('post', $types, true);
            $includeReplies = in_array('reply', $types, true);
            $orderBy = $sort === 'most_reported' ? 'report_count DESC, latest_report_at DESC' : 'latest_report_at DESC';
            $params = [];
            $parts = [];

            if ($includePosts) {
                $postWhere = "rp.post_id IS NOT NULL";
                if ($query !== '') {
                    $postWhere .= " AND (p.titre LIKE :post_q OR p.contenu LIKE :post_q)";
                    $params['post_q'] = '%' . $query . '%';
                }
                $parts[] = "
                    SELECT 'post' AS target_type,
                           p.id AS target_id,
                           p.titre AS title,
                           p.contenu AS content,
                           COUNT(rp.id) AS report_count,
                           MAX(rp.created_at) AS latest_report_at
                    FROM report rp
                    INNER JOIN post p ON p.id = rp.post_id
                    WHERE $postWhere
                    GROUP BY p.id, p.titre, p.contenu
                ";
            }

            if ($includeReplies) {
                $replyWhere = "rp.reply_id IS NOT NULL";
                if ($query !== '') {
                    $replyWhere .= " AND (r.contenu LIKE :reply_q OR p.titre LIKE :reply_q)";
                    $params['reply_q'] = '%' . $query . '%';
                }
                $parts[] = "
                    SELECT 'reply' AS target_type,
                           r.id AS target_id,
                           p.titre AS title,
                           r.contenu AS content,
                           COUNT(rp.id) AS report_count,
                           MAX(rp.created_at) AS latest_report_at
                    FROM report rp
                    INNER JOIN reply r ON r.id = rp.reply_id
                    INNER JOIN post p ON p.id = r.post_id
                    WHERE $replyWhere
                    GROUP BY r.id, p.titre, r.contenu
                ";
            }

            if (empty($parts)) return [];

            $sql = implode(" UNION ALL ", $parts) . " ORDER BY $orderBy";
            $req = $db->prepare($sql);
            $req->execute($params);
            return $req->fetchAll();
        } catch (Exception $e) { die('Erreur: ' . $e->getMessage()); }
    }

    public function getReportsForTarget($type, $id) {
        $db = config::getConnexion();
        try {
            if ($type === 'post') {
                $sql = "
                    SELECT rp.*, u.nom, u.prenom, u.email,
                           p.titre AS title, p.contenu AS content, p.id AS target_post_id
                    FROM report rp
                    INNER JOIN post p ON p.id = rp.post_id
                    LEFT JOIN users u ON u.id = rp.reporter_id
                    WHERE rp.post_id = :id
                    ORDER BY rp.created_at DESC
                ";
            } else {
                $sql = "
                    SELECT rp.*, u.nom, u.prenom, u.email,
                           p.titre AS title, r.contenu AS content, r.post_id AS target_post_id
                    FROM report rp
                    INNER JOIN reply r ON r.id = rp.reply_id
                    INNER JOIN post p ON p.id = r.post_id
                    LEFT JOIN users u ON u.id = rp.reporter_id
                    WHERE rp.reply_id = :id
                    ORDER BY rp.created_at DESC
                ";
            }
            $req = $db->prepare($sql);
            $req->execute(['id' => $id]);
            return $req->fetchAll();
        } catch (Exception $e) { die('Erreur: ' . $e->getMessage()); }
    }

    public function getReportById($id) {
        $db = config::getConnexion();
        try {
            $req = $db->prepare("SELECT * FROM report WHERE id = :id");
            $req->execute(['id' => $id]);
            return $req->fetch();
        } catch (Exception $e) { die('Erreur: ' . $e->getMessage()); }
    }

    public function deleteReport($id) {
        $db = config::getConnexion();
        try {
            $req = $db->prepare("DELETE FROM report WHERE id = :id");
            $req->execute(['id' => $id]);
            return true;
        } catch (Exception $e) { die('Erreur: ' . $e->getMessage()); }
    }
}
