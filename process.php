<?php
require_once 'app/config/config.php';
require_once 'app/models/Auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    
    if (empty($username) || empty($password)) {
        header('Location: index.php?err=1&msg=' . urlencode('Por favor complete todos los campos'));
        exit;
    }
    
    $auth = new Auth();
    
    if ($auth->login($username, $password)) {
        // Successful login
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
