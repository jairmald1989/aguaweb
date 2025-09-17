<?php
require_once 'app/config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    
    if (empty($username) || empty($password)) {
        header('Location: index.php?err=1&msg=' . urlencode('Por favor complete todos los campos'));
        exit;
    }
    
    // Demo authentication - accept admin/admin
    if ($username === 'admin' && $password === 'admin') {
        // Successful login - set demo session
        $_SESSION['user_id'] = 1;
        $_SESSION['username'] = 'admin';
        $_SESSION['name'] = 'Administrador Demo';
        $_SESSION['role'] = ROLE_ADMIN;
        $_SESSION['permissions'] = ['all'];
        $_SESSION['login_time'] = time();
        
        header('Location: dashboard.php');
        exit;
    } else {
        // Failed login
        header('Location: index.php?err=1&msg=' . urlencode('Usuario o contraseña incorrectos'));
        exit;
    }
} else {
    header('Location: index.php');
    exit;
}
?>
