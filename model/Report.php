<?php
// model/Report.php
class Report {
    private $id;
    private $reporter_id;
    private $post_id;
    private $reply_id;
    private $raison;
    private $statut;
    private $created_at;

    public function __construct($reporter_id, $raison, $post_id = null, $reply_id = null, $statut = 'en_attente') {
        $this->reporter_id = $reporter_id;
        $this->raison      = $raison;
        $this->post_id     = $post_id;
        $this->reply_id    = $reply_id;
        $this->statut      = $statut;
    }

    public function getId()         { return $this->id; }
    public function getReporterId() { return $this->reporter_id; }
    public function getPostId()     { return $this->post_id; }
    public function getReplyId()    { return $this->reply_id; }
    public function getRaison()     { return $this->raison; }
    public function getStatut()     { return $this->statut; }
    public function getCreatedAt()  { return $this->created_at; }

    public function setId($id)                  { $this->id = $id; }
    public function setReporterId($reporter_id) { $this->reporter_id = $reporter_id; }
    public function setPostId($post_id)         { $this->post_id = $post_id; }
    public function setReplyId($reply_id)       { $this->reply_id = $reply_id; }
    public function setRaison($raison)          { $this->raison = $raison; }
    public function setStatut($statut)          { $this->statut = $statut; }
    public function setCreatedAt($created_at)   { $this->created_at = $created_at; }
}
