<?php
// model/Post.php
class Post {
    private $id;
    private $user_id;
    private $titre;
    private $contenu;
    private $is_pinned;
    private $statut;
    private $created_at;

    public function __construct($user_id, $titre, $contenu, $is_pinned = 0, $statut = 'actif') {
        $this->user_id   = $user_id;
        $this->titre     = $titre;
        $this->contenu   = $contenu;
        $this->is_pinned = $is_pinned;
        $this->statut    = $statut;
    }

    public function getId()        { return $this->id; }
    public function getUserId()    { return $this->user_id; }
    public function getTitre()     { return $this->titre; }
    public function getContenu()   { return $this->contenu; }
    public function getIsPinned()  { return $this->is_pinned; }
    public function getStatut()    { return $this->statut; }
    public function getCreatedAt() { return $this->created_at; }

    public function setId($id)               { $this->id = $id; }
    public function setUserId($user_id)      { $this->user_id = $user_id; }
    public function setTitre($titre)         { $this->titre = $titre; }
    public function setContenu($contenu)     { $this->contenu = $contenu; }
    public function setIsPinned($is_pinned)  { $this->is_pinned = $is_pinned; }
    public function setStatut($statut)       { $this->statut = $statut; }
    public function setCreatedAt($created_at){ $this->created_at = $created_at; }
}
