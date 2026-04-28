<?php
// model/User.php
class User {
    private $id;
    private $nom;
    private $prenom;
    private $email;
    private $password;
    private $phone;
    private $address;
    private $profile_photo;
    private $status;
    private $email_verified;
    private $verification_token;
    
    public function __construct($nom, $prenom, $email, $password, $phone = null, $address = null, $status = 'pending') {
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->email = $email;
        $this->password = $password;
        $this->phone = $phone;
        $this->address = $address;
        $this->status = $status;
        $this->email_verified = 0;
        $this->verification_token = bin2hex(random_bytes(32));
    }
    
    // Getters
    public function getId() { return $this->id; }
    public function getNom() { return $this->nom; }
    public function getPrenom() { return $this->prenom; }
    public function getEmail() { return $this->email; }
    public function getPassword() { return $this->password; }
    public function getPhone() { return $this->phone; }
    public function getAddress() { return $this->address; }
    public function getProfilePhoto() { return $this->profile_photo; }
    public function getStatus() { return $this->status; }
    public function getEmailVerified() { return $this->email_verified; }
    public function getVerificationToken() { return $this->verification_token; }
    
    // Setters
    public function setId($id) { $this->id = $id; }
    public function setNom($nom) { $this->nom = $nom; }
    public function setPrenom($prenom) { $this->prenom = $prenom; }
    public function setEmail($email) { $this->email = $email; }
    public function setPassword($password) { $this->password = $password; }
    public function setPhone($phone) { $this->phone = $phone; }
    public function setAddress($address) { $this->address = $address; }
    public function setProfilePhoto($photo) { $this->profile_photo = $photo; }
    public function setStatus($status) { $this->status = $status; }
    public function setEmailVerified($verified) { $this->email_verified = $verified; }
}