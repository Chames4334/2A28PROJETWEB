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
    private $status;
    
    public function __construct($nom, $prenom, $email, $password, $phone = null, $address = null, $status = 'pending') {
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->email = $email;
        $this->password = $password;
        $this->phone = $phone;
        $this->address = $address;
        $this->status = $status;
    }
    
    // Getters
    public function getId() { return $this->id; }
    public function getNom() { return $this->nom; }
    public function getPrenom() { return $this->prenom; }
    public function getEmail() { return $this->email; }
    public function getPassword() { return $this->password; }
    public function getPhone() { return $this->phone; }
    public function getAddress() { return $this->address; }
    public function getStatus() { return $this->status; }
    
    // Setters
    public function setId($id) { $this->id = $id; }
    public function setNom($nom) { $this->nom = $nom; }
    public function setPrenom($prenom) { $this->prenom = $prenom; }
    public function setEmail($email) { $this->email = $email; }
    public function setPassword($password) { $this->password = $password; }
    public function setPhone($phone) { $this->phone = $phone; }
    public function setAddress($address) { $this->address = $address; }
    public function setStatus($status) { $this->status = $status; }
}