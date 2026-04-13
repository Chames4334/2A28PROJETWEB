<?php
// controller/ControlUser.php
include_once __DIR__ . '/../config.php';
include_once __DIR__ . '/../model/User.php';

class ControlUser {
    
    // Récupérer tous les utilisateurs avec leurs rôles
    public function listeUser() {
        $db = config::getConnexion();
        try {
            $sql = "
                SELECT u.*, GROUP_CONCAT(r.nom SEPARATOR ', ') as roles_noms
                FROM users u
                LEFT JOIN user_roles ur ON ur.user_id = u.id
                LEFT JOIN roles r ON r.id = ur.role_id
                GROUP BY u.id
                ORDER BY u.created_at DESC
            ";
            $liste = $db->query($sql);
            return $liste;
        } catch(Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }
    
    // Récupérer un utilisateur par ID
    public function getUserById($id) {
        $db = config::getConnexion();
        try {
            $sql = "
                SELECT u.*, GROUP_CONCAT(r.id SEPARATOR ',') as roles_ids
                FROM users u
                LEFT JOIN user_roles ur ON ur.user_id = u.id
                LEFT JOIN roles r ON r.id = ur.role_id
                WHERE u.id = :id
                GROUP BY u.id
            ";
            $req = $db->prepare($sql);
            $req->execute(['id' => $id]);
            return $req->fetch();
        } catch(Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }
    
    // Ajouter un utilisateur
    public function addUser($user, $roles = []) {
        $db = config::getConnexion();
        try {
            $db->beginTransaction();
            
            $sql = "INSERT INTO users (nom, prenom, email, password_hash, phone, address, status) 
                    VALUES (:nom, :prenom, :email, :password, :phone, :address, :status)";
            $req = $db->prepare($sql);
            $req->execute([
                'nom' => $user->getNom(),
                'prenom' => $user->getPrenom(),
                'email' => $user->getEmail(),
                'password' => password_hash($user->getPassword(), PASSWORD_DEFAULT),
                'phone' => $user->getPhone(),
                'address' => $user->getAddress(),
                'status' => $user->getStatus()
            ]);
            
            $userId = $db->lastInsertId();
            
            // Ajouter les rôles
            if (!empty($roles)) {
                $sql2 = "INSERT INTO user_roles (user_id, role_id) VALUES (:user_id, :role_id)";
                $req2 = $db->prepare($sql2);
                foreach ($roles as $roleId) {
                    $req2->execute(['user_id' => $userId, 'role_id' => $roleId]);
                }
            }
            
            $db->commit();
            return true;
        } catch(Exception $e) {
            $db->rollBack();
            die('Erreur: ' . $e->getMessage());
        }
    }
    
    // Mettre à jour un utilisateur
    public function updateUser($user, $id, $roles = []) {
        $db = config::getConnexion();
        try {
            $db->beginTransaction();
            
            if (!empty($user->getPassword())) {
                $sql = "UPDATE users SET nom=:nom, prenom=:prenom, email=:email, 
                        password_hash=:password, phone=:phone, address=:address, status=:status 
                        WHERE id=:id";
                $req = $db->prepare($sql);
                $req->execute([
                    'nom' => $user->getNom(),
                    'prenom' => $user->getPrenom(),
                    'email' => $user->getEmail(),
                    'password' => password_hash($user->getPassword(), PASSWORD_DEFAULT),
                    'phone' => $user->getPhone(),
                    'address' => $user->getAddress(),
                    'status' => $user->getStatus(),
                    'id' => $id
                ]);
            } else {
                $sql = "UPDATE users SET nom=:nom, prenom=:prenom, email=:email, 
                        phone=:phone, address=:address, status=:status 
                        WHERE id=:id";
                $req = $db->prepare($sql);
                $req->execute([
                    'nom' => $user->getNom(),
                    'prenom' => $user->getPrenom(),
                    'email' => $user->getEmail(),
                    'phone' => $user->getPhone(),
                    'address' => $user->getAddress(),
                    'status' => $user->getStatus(),
                    'id' => $id
                ]);
            }
            
            // Mettre à jour les rôles
            $sql2 = "DELETE FROM user_roles WHERE user_id = :user_id";
            $req2 = $db->prepare($sql2);
            $req2->execute(['user_id' => $id]);
            
            if (!empty($roles)) {
                $sql3 = "INSERT INTO user_roles (user_id, role_id) VALUES (:user_id, :role_id)";
                $req3 = $db->prepare($sql3);
                foreach ($roles as $roleId) {
                    $req3->execute(['user_id' => $id, 'role_id' => $roleId]);
                }
            }
            
            $db->commit();
            return true;
        } catch(Exception $e) {
            $db->rollBack();
            die('Erreur: ' . $e->getMessage());
        }
    }
    
    // Supprimer un utilisateur
    public function deleteUser($id) {
        $db = config::getConnexion();
        try {
            $sql = "DELETE FROM users WHERE id = :id";
            $req = $db->prepare($sql);
            $req->execute(['id' => $id]);
            return true;
        } catch(Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }
    
    // Statistiques
    public function countUsers() {
        $db = config::getConnexion();
        try {
            return $db->query("SELECT COUNT(*) FROM users")->fetchColumn();
        } catch(Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }
    
    public function countByStatus($status) {
        $db = config::getConnexion();
        try {
            $sql = "SELECT COUNT(*) FROM users WHERE status = :status";
            $req = $db->prepare($sql);
            $req->execute(['status' => $status]);
            return $req->fetchColumn();
        } catch(Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }
    
    // Récupérer tous les rôles
    public function getAllRoles() {
        $db = config::getConnexion();
        try {
            return $db->query("SELECT * FROM roles ORDER BY nom")->fetchAll();
        } catch(Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }
    
    // Vérifier si email existe
    public function emailExists($email, $excludeId = 0) {
        $db = config::getConnexion();
        try {
            $sql = "SELECT id FROM users WHERE email = :email AND id != :id";
            $req = $db->prepare($sql);
            $req->execute(['email' => $email, 'id' => $excludeId]);
            return $req->fetch() !== false;
        } catch(Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }
    
    // Login
    public function login($email, $password) {
        $db = config::getConnexion();
        try {
            $sql = "
                SELECT u.*, GROUP_CONCAT(r.nom SEPARATOR ',') as roles
                FROM users u
                LEFT JOIN user_roles ur ON ur.user_id = u.id
                LEFT JOIN roles r ON r.id = ur.role_id
                WHERE u.email = :email
                GROUP BY u.id
            ";
            $req = $db->prepare($sql);
            $req->execute(['email' => $email]);
            $user = $req->fetch();
            
            if ($user && password_verify($password, $user['password_hash'])) {
                $role = 'client';
                if (strpos($user['roles'], 'admin') !== false) $role = 'admin';
                elseif (strpos($user['roles'], 'agent') !== false) $role = 'agent';
                
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_nom'] = $user['nom'];
                $_SESSION['user_prenom'] = $user['prenom'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $role;
                
                return true;
            }
            return false;
        } catch(Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }
    
    // Inscription
    public function register($nom, $prenom, $email, $password) {
        $db = config::getConnexion();
        try {
            $db->beginTransaction();
            
            $sql = "INSERT INTO users (nom, prenom, email, password_hash, status) 
                    VALUES (:nom, :prenom, :email, :password, 'pending')";
            $req = $db->prepare($sql);
            $req->execute([
                'nom' => $nom,
                'prenom' => $prenom,
                'email' => $email,
                'password' => password_hash($password, PASSWORD_DEFAULT)
            ]);
            
            $userId = $db->lastInsertId();
            
            // Récupérer rôle client
            $sql2 = "SELECT id FROM roles WHERE nom = 'client'";
            $roleClient = $db->query($sql2)->fetch();
            
            if ($roleClient) {
                $sql3 = "INSERT INTO user_roles (user_id, role_id) VALUES (:user_id, :role_id)";
                $req3 = $db->prepare($sql3);
                $req3->execute(['user_id' => $userId, 'role_id' => $roleClient['id']]);
            }
            
            $db->commit();
            return true;
        } catch(Exception $e) {
            $db->rollBack();
            die('Erreur: ' . $e->getMessage());
        }
    }
    
    // Logout
    public function logout() {
        session_destroy();
        return true;
    }
}