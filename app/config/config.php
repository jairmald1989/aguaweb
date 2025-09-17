<?php
// Configuration file for Water Billing System

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'jairmald1989_agua');
define('DB_PASS', 'iDepyi%fs=eI');
define('DB_NAME', 'jairmald1989_agua');

// Application settings
define('APP_NAME', 'Sistema de Gestión de Agua Potable');
define('APP_VERSION', '2.0.0');
define('APP_URL', '');

// File upload settings
define('UPLOAD_PATH', '../public/uploads/');
define('READINGS_UPLOAD_PATH', '../public/uploads/readings/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_IMAGE_TYPES', ['jpg', 'jpeg', 'png', 'gif']);

// Security settings
define('SESSION_TIMEOUT', 3600); // 1 hour in seconds
define('PASSWORD_MIN_LENGTH', 6);

// Email settings
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_SECURE', 'tls');

// User roles
define('ROLE_ADMIN', 'administrador');
define('ROLE_COBRADOR', 'cobrador');
define('ROLE_TESORERO', 'tesorero');
define('ROLE_LECTURAS', 'toma_lecturas');

// Timezone
date_default_timezone_set('America/Lima');

// Error reporting (set to 0 in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}