<?php
// model/Reaction.php
class Reaction {
    private $id;
    private $user_id;
    private $post_id;
    private $reply_id;
    private $type_reaction;
    private $created_at;

    public function __construct($user_id, $type_reaction, $post_id = null, $reply_id = null) {
        $this->user_id       = $user_id;
        $this->type_reaction = $type_reaction;
        $this->post_id       = $post_id;
        $this->reply_id      = $reply_id;
    }

    public function getId()           { return $this->id; }
    public function getUserId()       { return $this->user_id; }
    public function getPostId()       { return $this->post_id; }
    public function getReplyId()      { return $this->reply_id; }
    public function getTypeReaction() { return $this->type_reaction; }
    public function getCreatedAt()    { return $this->created_at; }

    public function setId($id)                     { $this->id = $id; }
    public function setUserId($user_id)            { $this->user_id = $user_id; }
    public function setPostId($post_id)            { $this->post_id = $post_id; }
    public function setReplyId($reply_id)          { $this->reply_id = $reply_id; }
    public function setTypeReaction($type_reaction){ $this->type_reaction = $type_reaction; }
    public function setCreatedAt($created_at)      { $this->created_at = $created_at; }
}
