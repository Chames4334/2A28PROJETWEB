<?php
// controller/ControlUser.php
include_once __DIR__ . '/../config.php';
include_once __DIR__ . '/../model/User.php';

class ControlUser {
    
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
    
    public function addUser($user, $roles = []) {
        $db = config::getConnexion();
        try {
            $db->beginTransaction();
            
            $sql = "INSERT INTO users (nom, prenom, email, password_hash, phone, address, status, email_verified) 
                    VALUES (:nom, :prenom, :email, :password, :phone, :address, :status, 1)";
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
    
    public function getAllRoles() {
        $db = config::getConnexion();
        try {
            return $db->query("SELECT * FROM roles ORDER BY nom")->fetchAll();
        } catch(Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }
    
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
    
    public function canAttemptLogin($email) {
        $db = config::getConnexion();
        $sql = "SELECT login_attempts, locked_until FROM users WHERE email = :email";
        $req = $db->prepare($sql);
        $req->execute(['email' => $email]);
        $user = $req->fetch();
        
        if (!$user) return true;
        if ($user['locked_until'] && new DateTime() < new DateTime($user['locked_until'])) {
            return false;
        }
        return true;
    }
    
    public function incrementLoginAttempts($email) {
        $db = config::getConnexion();
        $sql = "UPDATE users SET login_attempts = login_attempts + 1, last_attempt_time = NOW() WHERE email = :email";
        $req = $db->prepare($sql);
        $req->execute(['email' => $email]);
        
        $sql2 = "SELECT login_attempts FROM users WHERE email = :email";
        $req2 = $db->prepare($sql2);
        $req2->execute(['email' => $email]);
        $attempts = $req2->fetchColumn();
        
        if ($attempts >= MAX_LOGIN_ATTEMPTS) {
            $sql3 = "UPDATE users SET locked_until = DATE_ADD(NOW(), INTERVAL " . LOCKOUT_TIME . " MINUTE) WHERE email = :email";
            $req3 = $db->prepare($sql3);
            $req3->execute(['email' => $email]);
        }
    }
    
    public function resetLoginAttempts($email) {
        $db = config::getConnexion();
        $sql = "UPDATE users SET login_attempts = 0, locked_until = NULL, last_attempt_time = NULL WHERE email = :email";
        $req = $db->prepare($sql);
        $req->execute(['email' => $email]);
    }
    
    public function getLoginAttemptsLeft($email) {
        $db = config::getConnexion();
        $sql = "SELECT login_attempts, locked_until FROM users WHERE email = :email";
        $req = $db->prepare($sql);
        $req->execute(['email' => $email]);
        $user = $req->fetch();
        
        if (!$user) return MAX_LOGIN_ATTEMPTS;
        if ($user['locked_until'] && new DateTime() < new DateTime($user['locked_until'])) return 0;
        
        return MAX_LOGIN_ATTEMPTS - $user['login_attempts'];
    }
    
    public function loginWithAttempts($email, $password) {
        if (!$this->canAttemptLogin($email)) {
            return ['success' => false, 'error' => 'Trop de tentatives. Compte bloqué 15 minutes.'];
        }
        
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
            
            if ($user && $user['email_verified'] == 0) {
                return ['success' => false, 'error' => 'Veuillez vérifier votre email avant de vous connecter.'];
            }
            
            if ($user && password_verify($password, $user['password_hash'])) {
                $this->resetLoginAttempts($email);
                
                $role = 'client';
                if (strpos($user['roles'], 'admin') !== false) $role = 'admin';
                elseif (strpos($user['roles'], 'agent') !== false) $role = 'agent';
                
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_nom'] = $user['nom'];
                $_SESSION['user_prenom'] = $user['prenom'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $role;
                
                return ['success' => true];
            }
            
            $this->incrementLoginAttempts($email);
            $attemptsLeft = $this->getLoginAttemptsLeft($email);
            
            return ['success' => false, 'error' => "Email ou mot de passe incorrect. Il vous reste $attemptsLeft tentative(s)."];
        } catch(Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }
    
    public function registerWithVerification($nom, $prenom, $email, $password) {
        $db = config::getConnexion();
        try {
            $db->beginTransaction();
            
            $verificationToken = bin2hex(random_bytes(32));
            
            $sql = "INSERT INTO users (nom, prenom, email, password_hash, status, verification_token, email_verified) 
                    VALUES (:nom, :prenom, :email, :password, 'active', :token, 1)";
            $req = $db->prepare($sql);
            $req->execute([
                'nom' => $nom,
                'prenom' => $prenom,
                'email' => $email,
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'token' => $verificationToken
            ]);
            
            $userId = $db->lastInsertId();
            
            $roleClient = $db->query("SELECT id FROM roles WHERE nom = 'client'")->fetch();
            if ($roleClient) {
                $sql2 = "INSERT INTO user_roles (user_id, role_id) VALUES (:user_id, :role_id)";
                $req2 = $db->prepare($sql2);
                $req2->execute(['user_id' => $userId, 'role_id' => $roleClient['id']]);
            }
            
            $db->commit();
            return true;
        } catch(Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }
    
    public function verifyEmail($token) {
        $db = config::getConnexion();
        $sql = "UPDATE users SET email_verified = 1, verification_token = NULL, status = 'active' 
                WHERE verification_token = :token AND email_verified = 0";
        $req = $db->prepare($sql);
        $req->execute(['token' => $token]);
        return $req->rowCount() > 0;
    }
    
    public function generateResetToken($email) {
        $db = config::getConnexion();
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        $sql = "UPDATE users SET reset_token = :token, reset_expires = :expires WHERE email = :email";
        $req = $db->prepare($sql);
        $req->execute(['token' => $token, 'expires' => $expires, 'email' => $email]);
        
        if ($req->rowCount() > 0) {
            $resetLink = BASE_URL . "view/auth/reset_password.php?token=" . $token;
            $_SESSION['reset_link'] = $resetLink;
            $_SESSION['reset_email'] = $email;
            return true;
        }
        return false;
    }
    
    public function resetPassword($token, $newPassword) {
        $db = config::getConnexion();
        $sql = "SELECT id FROM users WHERE reset_token = :token AND reset_expires > NOW()";
        $req = $db->prepare($sql);
        $req->execute(['token' => $token]);
        $user = $req->fetch();
        
        if ($user) {
            $sql2 = "UPDATE users SET password_hash = :password, reset_token = NULL, reset_expires = NULL WHERE id = :id";
            $req2 = $db->prepare($sql2);
            $req2->execute([
                'password' => password_hash($newPassword, PASSWORD_DEFAULT),
                'id' => $user['id']
            ]);
            return true;
        }
        return false;
    }
    
    public function uploadProfilePhoto($userId, $file) {
        $targetDir = BASE_PATH . "view/assets/uploads/";
        
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'error' => "Erreur lors du téléchargement."];
        }
        
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        if (!in_array($extension, $allowed)) {
            return ['success' => false, 'error' => "Format non autorisé. Utilisez JPG, PNG ou GIF."];
        }
        
        if ($file['size'] > 2 * 1024 * 1024) {
            return ['success' => false, 'error' => "Fichier trop volumineux (max 2MB)."];
        }
        
        $new_filename = "user_" . $userId . "_" . time() . "_" . rand(1000, 9999) . "." . $extension;
        $targetFile = $targetDir . $new_filename;
        
        if (move_uploaded_file($file['tmp_name'], $targetFile)) {
            $db = config::getConnexion();
            $sql = "UPDATE users SET profile_photo = :photo WHERE id = :id";
            $req = $db->prepare($sql);
            $req->execute(['photo' => $new_filename, 'id' => $userId]);
            
            return ['success' => true, 'filename' => $new_filename];
        }
        
        return ['success' => false, 'error' => "Erreur lors de l'enregistrement."];
    }
    
    public function generateCaptcha() {
        $num1 = rand(1, 9);
        $num2 = rand(1, 9);
        $_SESSION['captcha_result'] = $num1 + $num2;
        return "$num1 + $num2 = ?";
    }
    
    public function verifyCaptcha($userResult) {
        return isset($_SESSION['captcha_result']) && $userResult == $_SESSION['captcha_result'];
    }
    
    // ============================================================
    // GESTION DU PROFIL PAR L'UTILISATEUR (FRONTOFFICE)
    // ============================================================

    public function updateMyProfile($userId, $data) {
        $db = config::getConnexion();
        try {
            $sql = "UPDATE users SET 
                    nom = :nom, 
                    prenom = :prenom, 
                    email = :email, 
                    phone = :phone, 
                    address = :address 
                    WHERE id = :id";
            $req = $db->prepare($sql);
            $req->execute([
                'nom' => $data['nom'],
                'prenom' => $data['prenom'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'address' => $data['address'] ?? null,
                'id' => $userId
            ]);
            
            $_SESSION['user_nom'] = $data['nom'];
            $_SESSION['user_prenom'] = $data['prenom'];
            $_SESSION['user_email'] = $data['email'];
            
            return ['success' => true, 'message' => 'Profil mis à jour avec succès'];
        } catch(Exception $e) {
            return ['success' => false, 'error' => 'Erreur lors de la mise à jour'];
        }
    }

    public function deactivateMyAccount($userId) {
        $db = config::getConnexion();
        try {
            $sql = "UPDATE users SET status = 'blocked' WHERE id = :id";
            $req = $db->prepare($sql);
            $req->execute(['id' => $userId]);
            
            session_destroy();
            
            return ['success' => true, 'message' => 'Votre compte a été désactivé'];
        } catch(Exception $e) {
            return ['success' => false, 'error' => 'Erreur lors de la désactivation'];
        }
    }

    public function reactivateMyAccount($userId) {
        $db = config::getConnexion();
        try {
            $sql = "UPDATE users SET status = 'active' WHERE id = :id";
            $req = $db->prepare($sql);
            $req->execute(['id' => $userId]);
            return ['success' => true, 'message' => 'Votre compte a été réactivé'];
        } catch(Exception $e) {
            return ['success' => false, 'error' => 'Erreur lors de la réactivation'];
        }
    }

    public function deleteMyAccount($userId) {
        $db = config::getConnexion();
        try {
            $db->beginTransaction();
            
            $sql1 = "DELETE FROM user_roles WHERE user_id = :id";
            $req1 = $db->prepare($sql1);
            $req1->execute(['id' => $userId]);
            
            $sql2 = "DELETE FROM users WHERE id = :id";
            $req2 = $db->prepare($sql2);
            $req2->execute(['id' => $userId]);
            
            $db->commit();
            
            session_destroy();
            
            return ['success' => true, 'message' => 'Votre compte a été supprimé'];
        } catch(Exception $e) {
            $db->rollBack();
            return ['success' => false, 'error' => 'Erreur lors de la suppression'];
        }
    }
}