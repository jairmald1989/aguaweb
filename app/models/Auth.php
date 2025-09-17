<?php
require_once 'app/config/database.php';

class Auth {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function login($username, $password) {
        $sql = "SELECT u.*, r.permissions FROM user u 
                LEFT JOIN roles r ON u.role = r.name 
                WHERE u.username = ? AND u.status = 1";
        
        $result = $this->db->query($sql, [$username]);
        
        if ($result && $result->num_rows > 0) {
            $user = $result->fetch_assoc();
            
            // Simple password verification (in production, use password_hash/password_verify)
            if ($password === $user['password']) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['permissions'] = $user['permissions'] ? explode(',', $user['permissions']) : [];
                $_SESSION['login_time'] = time();
                
                return true;
            }
        }
        
        return false;
    }
    
    public function logout() {
        session_destroy();
        header('Location: ../index.php');
        exit;
    }
    
    public function isLoggedIn() {
        return isset($_SESSION['user_id']) && 
               isset($_SESSION['login_time']) && 
               (time() - $_SESSION['login_time']) < SESSION_TIMEOUT;
    }
    
    public function requireAuth() {
        if (!$this->isLoggedIn()) {
            header('Location: ../index.php');
            exit;
        }
        
        // Update last activity time
        $_SESSION['login_time'] = time();
    }
    
    public function hasRole($role) {
        return isset($_SESSION['role']) && $_SESSION['role'] === $role;
    }
    
    public function hasPermission($permission) {
        if (isset($_SESSION['role']) && $_SESSION['role'] === ROLE_ADMIN) {
            return true; // Admin has all permissions
        }
        
        return isset($_SESSION['permissions']) && 
               (in_array($permission, $_SESSION['permissions']) || in_array('all', $_SESSION['permissions']));
    }
    
    public function requireRole($role) {
        $this->requireAuth();
        if (!$this->hasRole($role)) {
            header('HTTP/1.0 403 Forbidden');
            die('Access denied. Required role: ' . $role);
        }
    }
    
    public function requirePermission($permission) {
        $this->requireAuth();
        if (!$this->hasPermission($permission)) {
            header('HTTP/1.0 403 Forbidden');
            die('Access denied. Required permission: ' . $permission);
        }
    }
    
    public function getCurrentUser() {
        if ($this->isLoggedIn()) {
            return [
                'id' => $_SESSION['user_id'],
                'username' => $_SESSION['username'],
                'name' => $_SESSION['name'],
                'role' => $_SESSION['role']
            ];
        }
        return null;
    }
}