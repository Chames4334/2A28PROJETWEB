<?php
// model/Reply.php
class Reply {
    private $id;
    private $post_id;
    private $user_id;
    private $parent_reply_id;
    private $contenu;
    private $statut;
    private $created_at;

    public function __construct($post_id, $user_id, $contenu, $parent_reply_id = null, $statut = 'actif') {
        $this->post_id         = $post_id;
        $this->user_id         = $user_id;
        $this->contenu         = $contenu;
        $this->parent_reply_id = $parent_reply_id;
        $this->statut          = $statut;
    }

    public function getId()            { return $this->id; }
    public function getPostId()        { return $this->post_id; }
    public function getUserId()        { return $this->user_id; }
    public function getParentReplyId() { return $this->parent_reply_id; }
    public function getContenu()       { return $this->contenu; }
    public function getStatut()        { return $this->statut; }
    public function getCreatedAt()     { return $this->created_at; }

    public function setId($id)                       { $this->id = $id; }
    public function setPostId($post_id)              { $this->post_id = $post_id; }
    public function setUserId($user_id)              { $this->user_id = $user_id; }
    public function setParentReplyId($parent_reply_id){ $this->parent_reply_id = $parent_reply_id; }
    public function setContenu($contenu)             { $this->contenu = $contenu; }
    public function setStatut($statut)               { $this->statut = $statut; }
    public function setCreatedAt($created_at)        { $this->created_at = $created_at; }
}
