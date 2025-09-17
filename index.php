<?php 
require_once 'app/config/config.php';

// Check if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error_message = '';
if (isset($_GET['err'])) {
    $error_message = $_GET['msg'] ?? 'Error de autenticación';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - <?php echo APP_NAME; ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .login-container {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 1rem 3rem rgba(0,0,0,0.175);
            overflow: hidden;
            max-width: 900px;
            width: 100%;
        }
        
        .login-image {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.8) 0%, rgba(118, 75, 162, 0.8) 100%),
                        url('img/water-background.jpg') center/cover;
            min-height: 500px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-align: center;
        }
        
        .login-form {
            padding: 3rem;
        }
        
        .form-floating .form-control {
            border: 1px solid #dee2e6;
            border-radius: 0.5rem;
        }
        
        .form-floating .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 0.5rem;
            padding: 0.75rem 2rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .btn-login:hover {
            background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
            transform: translateY(-1px);
            box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15);
        }
        
        .brand-logo {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        
        .alert {
            border-radius: 0.5rem;
            border: none;
        }
        
        @media (max-width: 768px) {
            .login-container {
                margin: 1rem;
            }
            
            .login-form {
                padding: 2rem;
            }
            
            .login-image {
                min-height: 300px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="login-container">
                    <div class="row g-0">
                        <div class="col-lg-6">
                            <div class="login-image">
                                <div>
                                    <div class="brand-logo">
                                        <i class="bi bi-droplet-fill"></i>
                                    </div>
                                    <h2 class="fw-bold">AguaWeb</h2>
                                    <p class="lead">Sistema de Gestión de Agua Potable</p>
                                    <hr class="border-light my-4" style="width: 60%; margin: 0 auto;">
                                    <p>Administra clientes, lecturas, facturación y cobros de manera eficiente</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="login-form">
                                <div class="text-center mb-4">
                                    <h3 class="fw-bold text-dark mb-2">Iniciar Sesión</h3>
                                    <p class="text-muted">Ingrese sus credenciales para acceder al sistema</p>
                                </div>
                                
                                <?php if (!empty($error_message)): ?>
                                    <div class="alert alert-danger d-flex align-items-center" role="alert">
                                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                        <?php echo htmlspecialchars($error_message); ?>
                                    </div>
                                <?php endif; ?>
                                
                                <form action="process.php" method="POST" class="needs-validation" novalidate>
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="username" name="username" 
                                               placeholder="Usuario" required>
                                        <label for="username">
                                            <i class="bi bi-person me-2"></i>Usuario
                                        </label>
                                        <div class="invalid-feedback">
                                            Por favor ingrese su usuario.
                                        </div>
                                    </div>
                                    
                                    <div class="form-floating mb-4">
                                        <input type="password" class="form-control" id="password" name="password" 
                                               placeholder="Contraseña" required>
                                        <label for="password">
                                            <i class="bi bi-lock me-2"></i>Contraseña
                                        </label>
                                        <div class="invalid-feedback">
                                            Por favor ingrese su contraseña.
                                        </div>
                                    </div>
                                    
                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-primary btn-login">
                                            <i class="bi bi-box-arrow-in-right me-2"></i>
                                            Ingresar
                                        </button>
                                    </div>
                                </form>
                                
                                <hr class="my-4">
                                
                                <div class="text-center">
                                    <small class="text-muted">
                                        <?php echo APP_NAME; ?> v<?php echo APP_VERSION; ?>
                                        <br>
                                        &copy; <?php echo date('Y'); ?> Todos los derechos reservados
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Bootstrap form validation
        (function() {
            'use strict';
            window.addEventListener('load', function() {
                var forms = document.getElementsByClassName('needs-validation');
                var validation = Array.prototype.filter.call(forms, function(form) {
                    form.addEventListener('submit', function(event) {
                        if (form.checkValidity() === false) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        form.classList.add('was-validated');
                    }, false);
                });
            }, false);
        })();
        
        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>
</body>
</html>