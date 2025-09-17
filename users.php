<?php
require_once 'app/config/config.php';

// Demo authentication check
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

// Only admin can access user management
if ($_SESSION['role'] !== ROLE_ADMIN) {
    header('HTTP/1.0 403 Forbidden');
    die('Acceso denegado. Solo administradores pueden gestionar usuarios.');
}

$page_title = "Gestión de Usuarios";

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $success_message = "Usuario agregado exitosamente (Demo).";
                break;
            case 'edit':
                $success_message = "Usuario actualizado exitosamente (Demo).";
                break;
        }
    }
}

// Demo data
$roles = [
    ['name' => ROLE_ADMIN, 'description' => 'Administrador del sistema'],
    ['name' => ROLE_COBRADOR, 'description' => 'Encargado de cobros'],
    ['name' => ROLE_TESORERO, 'description' => 'Encargado de tesorería'],
    ['name' => ROLE_LECTURAS, 'description' => 'Encargado de tomar lecturas']
];

$users = [
    [
        'id' => 1,
        'username' => 'admin',
        'name' => 'Administrador Demo',
        'role' => ROLE_ADMIN,
        'status' => 1,
        'created_at' => '2025-01-01 10:00:00'
    ],
    [
        'id' => 2,
        'username' => 'cobrador1',
        'name' => 'Juan Pérez',
        'role' => ROLE_COBRADOR,
        'status' => 1,
        'created_at' => '2025-01-15 14:30:00'
    ],
    [
        'id' => 3,
        'username' => 'tesorero1',
        'name' => 'María González',
        'role' => ROLE_TESORERO,
        'status' => 1,
        'created_at' => '2025-02-01 09:15:00'
    ],
    [
        'id' => 4,
        'username' => 'lector1',
        'name' => 'Carlos Rodríguez',
        'role' => ROLE_LECTURAS,
        'status' => 1,
        'created_at' => '2025-02-10 11:45:00'
    ],
    [
        'id' => 5,
        'username' => 'cobrador2',
        'name' => 'Ana López',
        'role' => ROLE_COBRADOR,
        'status' => 0,
        'created_at' => '2025-01-20 16:20:00'
    ]
];

$search = trim($_GET['search'] ?? '');
$role_filter = $_GET['role'] ?? '';
$status_filter = $_GET['status'] ?? '';

// Filter users based on search criteria
if (!empty($search)) {
    $users = array_filter($users, function($user) use ($search) {
        return stripos($user['name'], $search) !== false || 
               stripos($user['username'], $search) !== false;
    });
}

if (!empty($role_filter)) {
    $users = array_filter($users, function($user) use ($role_filter) {
        return $user['role'] === $role_filter;
    });
}

if (!empty($status_filter)) {
    $users = array_filter($users, function($user) use ($status_filter) {
        return $user['status'] == $status_filter;
    });
}

$total_users = count($users);

ob_start();
?>

<!-- Success/Error Messages -->
<?php if (isset($success_message)): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i><?php echo $success_message; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">
        <i class="bi bi-person-gear me-2"></i>
        Gestión de Usuarios
    </h1>
    <div>
        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addUserModal">
            <i class="bi bi-person-plus me-2"></i>Nuevo Usuario
        </button>
    </div>
</div>

<!-- Search and Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-4">
                <label for="search" class="form-label">Buscar</label>
                <input type="text" class="form-control" id="search" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                       placeholder="Nombre o usuario...">
            </div>
            <div class="col-md-3">
                <label for="role" class="form-label">Rol</label>
                <select class="form-select" id="role" name="role">
                    <option value="">Todos los roles</option>
                    <?php foreach ($roles as $role): ?>
                        <option value="<?php echo $role['name']; ?>" <?php echo $role_filter === $role['name'] ? 'selected' : ''; ?>>
                            <?php echo ucfirst(str_replace('_', ' ', $role['name'])); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label for="status" class="form-label">Estado</label>
                <select class="form-select" id="status" name="status">
                    <option value="">Todos</option>
                    <option value="1" <?php echo $status_filter === '1' ? 'selected' : ''; ?>>Activo</option>
                    <option value="0" <?php echo $status_filter === '0' ? 'selected' : ''; ?>>Inactivo</option>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="bi bi-search"></i>
                </button>
                <a href="users.php" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-clockwise"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Users Table -->
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">
            Usuarios del Sistema (<?php echo number_format($total_users); ?>)
        </h5>
    </div>
    <div class="card-body p-0">
        <?php if (empty($users)): ?>
            <div class="text-center py-5">
                <i class="bi bi-person-gear display-1 text-muted"></i>
                <h5 class="mt-3">No se encontraron usuarios</h5>
                <p class="text-muted">Comience agregando un nuevo usuario.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Usuario</th>
                            <th>Nombre Completo</th>
                            <th>Rol</th>
                            <th>Estado</th>
                            <th>Fecha Creación</th>
                            <th width="150">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td>
                                    <strong><?php echo htmlspecialchars($user['username']); ?></strong>
                                    <?php if ($user['id'] === $_SESSION['user_id']): ?>
                                        <span class="badge bg-primary ms-2">Tú</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($user['name']); ?></td>
                                <td>
                                    <?php
                                    $role_colors = [
                                        ROLE_ADMIN => 'bg-danger',
                                        ROLE_COBRADOR => 'bg-success',
                                        ROLE_TESORERO => 'bg-warning',
                                        ROLE_LECTURAS => 'bg-info'
                                    ];
                                    $role_names = [
                                        ROLE_ADMIN => 'Administrador',
                                        ROLE_COBRADOR => 'Cobrador',
                                        ROLE_TESORERO => 'Tesorero',
                                        ROLE_LECTURAS => 'Toma Lecturas'
                                    ];
                                    ?>
                                    <span class="badge <?php echo $role_colors[$user['role']] ?? 'bg-secondary'; ?>">
                                        <?php echo $role_names[$user['role']] ?? $user['role']; ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($user['status']): ?>
                                        <span class="badge bg-success">Activo</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Inactivo</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php echo date('d/m/Y H:i', strtotime($user['created_at'])); ?>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-primary" 
                                                onclick="editUser(<?php echo htmlspecialchars(json_encode($user)); ?>)"
                                                title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <?php if ($user['id'] !== $_SESSION['user_id']): ?>
                                            <button type="button" class="btn btn-outline-warning" 
                                                    onclick="toggleUserStatus(<?php echo $user['id']; ?>, <?php echo $user['status']; ?>)"
                                                    title="<?php echo $user['status'] ? 'Desactivar' : 'Activar'; ?>">
                                                <i class="bi bi-<?php echo $user['status'] ? 'ban' : 'check-circle'; ?>"></i>
                                            </button>
                                        <?php endif; ?>
                                        <button type="button" class="btn btn-outline-info" 
                                                onclick="resetPassword(<?php echo $user['id']; ?>)"
                                                title="Resetear contraseña">
                                            <i class="bi bi-key"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-person-plus me-2"></i>Agregar Nuevo Usuario
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="add">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="username" class="form-label">Usuario *</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                        <div class="form-text">Solo letras, números y guiones bajos. Mínimo 3 caracteres.</div>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña *</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                        <div class="form-text">Mínimo 6 caracteres.</div>
                    </div>
                    <div class="mb-3">
                        <label for="name" class="form-label">Nombre Completo *</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">Rol *</label>
                        <select class="form-select" id="role" name="role" required>
                            <option value="">Seleccionar rol</option>
                            <?php foreach ($roles as $role): ?>
                                <option value="<?php echo $role['name']; ?>">
                                    <?php echo ucfirst(str_replace('_', ' ', $role['name'])); ?>
                                    - <?php echo $role['description']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-2"></i>Crear Usuario
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-pencil me-2"></i>Editar Usuario
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_username" class="form-label">Usuario *</label>
                        <input type="text" class="form-control" id="edit_username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_name" class="form-label">Nombre Completo *</label>
                        <input type="text" class="form-control" id="edit_name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_role" class="form-label">Rol *</label>
                        <select class="form-select" id="edit_role" name="role" required>
                            <?php foreach ($roles as $role): ?>
                                <option value="<?php echo $role['name']; ?>">
                                    <?php echo ucfirst(str_replace('_', ' ', $role['name'])); ?>
                                    - <?php echo $role['description']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_status" class="form-label">Estado</label>
                        <select class="form-select" id="edit_status" name="status">
                            <option value="1">Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                    </div>
                    <hr>
                    <div class="mb-3">
                        <label for="new_password" class="form-label">Nueva Contraseña</label>
                        <input type="password" class="form-control" id="new_password" name="new_password">
                        <div class="form-text">Dejar en blanco para mantener la contraseña actual.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-2"></i>Actualizar Usuario
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();

$additional_js = '
<script>
function editUser(user) {
    document.getElementById("edit_id").value = user.id;
    document.getElementById("edit_username").value = user.username;
    document.getElementById("edit_name").value = user.name;
    document.getElementById("edit_role").value = user.role;
    document.getElementById("edit_status").value = user.status;
    
    new bootstrap.Modal(document.getElementById("editUserModal")).show();
}

function toggleUserStatus(userId, currentStatus) {
    const action = currentStatus ? "desactivar" : "activar";
    if (confirm(`¿Está seguro que desea ${action} este usuario?`)) {
        // Demo alert
        alert(`Usuario ${action}do exitosamente (Demo)`);
        location.reload();
    }
}

function resetPassword(userId) {
    if (confirm("¿Está seguro que desea resetear la contraseña de este usuario?")) {
        const newPassword = prompt("Ingrese la nueva contraseña:");
        if (newPassword && newPassword.length >= 6) {
            alert("Contraseña actualizada exitosamente (Demo)");
        } else if (newPassword) {
            alert("La contraseña debe tener al menos 6 caracteres");
        }
    }
}
</script>';

require_once 'templates/layout.php';
?>