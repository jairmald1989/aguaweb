<?php
require_once 'app/config/config.php';

// Demo authentication check
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

// Only admin can access zone management
if ($_SESSION['role'] !== ROLE_ADMIN) {
    header('HTTP/1.0 403 Forbidden');
    die('Acceso denegado. Solo administradores pueden gestionar zonas.');
}

$page_title = "Gestión de Zonas";

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $success_message = "Zona agregada exitosamente (Demo).";
                break;
            case 'edit':
                $success_message = "Zona actualizada exitosamente (Demo).";
                break;
        }
    }
}

// Demo data
$zones = [
    [
        'id' => 1,
        'name' => 'Zona Norte',
        'description' => 'Incluye sectores residenciales del norte de la ciudad',
        'status' => 1,
        'clients_count' => 45,
        'created_at' => '2025-01-01 10:00:00'
    ],
    [
        'id' => 2,
        'name' => 'Zona Sur',
        'description' => 'Área industrial y comercial del sur',
        'status' => 1,
        'clients_count' => 32,
        'created_at' => '2025-01-15 14:30:00'
    ],
    [
        'id' => 3,
        'name' => 'Zona Centro',
        'description' => 'Centro histórico y comercial',
        'status' => 1,
        'clients_count' => 28,
        'created_at' => '2025-02-01 09:15:00'
    ],
    [
        'id' => 4,
        'name' => 'Zona Este',
        'description' => 'Nuevos desarrollos habitacionales',
        'status' => 1,
        'clients_count' => 18,
        'created_at' => '2025-02-10 11:45:00'
    ],
    [
        'id' => 5,
        'name' => 'Zona Oeste',
        'description' => 'Área en desarrollo',
        'status' => 0,
        'clients_count' => 5,
        'created_at' => '2025-01-20 16:20:00'
    ]
];

$search = trim($_GET['search'] ?? '');
$status_filter = $_GET['status'] ?? '';

// Filter zones based on search criteria
if (!empty($search)) {
    $zones = array_filter($zones, function($zone) use ($search) {
        return stripos($zone['name'], $search) !== false || 
               stripos($zone['description'], $search) !== false;
    });
}

if (!empty($status_filter)) {
    $zones = array_filter($zones, function($zone) use ($status_filter) {
        return $zone['status'] == $status_filter;
    });
}

$total_zones = count($zones);

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
        <i class="bi bi-geo-alt me-2"></i>
        Gestión de Zonas
    </h1>
    <div>
        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addZoneModal">
            <i class="bi bi-plus-circle me-2"></i>Nueva Zona
        </button>
    </div>
</div>

<!-- Search and Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-6">
                <label for="search" class="form-label">Buscar</label>
                <input type="text" class="form-control" id="search" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                       placeholder="Nombre o descripción...">
            </div>
            <div class="col-md-4">
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
                <a href="zones.php" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-clockwise"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Zones Cards -->
<div class="row">
    <?php if (empty($zones)): ?>
        <div class="col-12">
            <div class="text-center py-5">
                <i class="bi bi-geo-alt display-1 text-muted"></i>
                <h5 class="mt-3">No se encontraron zonas</h5>
                <p class="text-muted">Comience agregando una nueva zona de servicio.</p>
            </div>
        </div>
    <?php else: ?>
        <?php foreach ($zones as $zone): ?>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="card-title mb-0"><?php echo htmlspecialchars($zone['name']); ?></h6>
                        <?php if ($zone['status']): ?>
                            <span class="badge bg-success">Activa</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">Inactiva</span>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <p class="card-text text-muted">
                            <?php echo htmlspecialchars($zone['description']); ?>
                        </p>
                        
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="border-end">
                                    <h4 class="text-primary mb-0"><?php echo $zone['clients_count']; ?></h4>
                                    <small class="text-muted">Clientes</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <h4 class="text-info mb-0"><?php echo $zone['id']; ?></h4>
                                <small class="text-muted">ID Zona</small>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                Creada: <?php echo date('d/m/Y', strtotime($zone['created_at'])); ?>
                            </small>
                            <div class="btn-group btn-group-sm">
                                <button type="button" class="btn btn-outline-primary" 
                                        onclick="editZone(<?php echo htmlspecialchars(json_encode($zone)); ?>)"
                                        title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <a href="clients.php?zone=<?php echo $zone['id']; ?>" 
                                   class="btn btn-outline-info" title="Ver clientes">
                                    <i class="bi bi-people"></i>
                                </a>
                                <button type="button" class="btn btn-outline-warning" 
                                        onclick="toggleZoneStatus(<?php echo $zone['id']; ?>, <?php echo $zone['status']; ?>)"
                                        title="<?php echo $zone['status'] ? 'Desactivar' : 'Activar'; ?>">
                                    <i class="bi bi-<?php echo $zone['status'] ? 'eye-slash' : 'eye'; ?>"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Zone Statistics -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-bar-chart me-2"></i>
                    Estadísticas por Zona
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead class="table-light">
                            <tr>
                                <th>Zona</th>
                                <th>Estado</th>
                                <th>Clientes</th>
                                <th>% del Total</th>
                                <th>Fecha Creación</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $total_clients = array_sum(array_column($zones, 'clients_count'));
                            foreach ($zones as $zone): 
                                $percentage = $total_clients > 0 ? ($zone['clients_count'] / $total_clients) * 100 : 0;
                            ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($zone['name']); ?></strong></td>
                                    <td>
                                        <?php if ($zone['status']): ?>
                                            <span class="badge bg-success">Activa</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Inactiva</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo $zone['clients_count']; ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="progress flex-grow-1 me-2" style="height: 8px;">
                                                <div class="progress-bar" role="progressbar" 
                                                     style="width: <?php echo $percentage; ?>%"></div>
                                            </div>
                                            <span class="text-muted"><?php echo number_format($percentage, 1); ?>%</span>
                                        </div>
                                    </td>
                                    <td><?php echo date('d/m/Y', strtotime($zone['created_at'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Zone Modal -->
<div class="modal fade" id="addZoneModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-plus-circle me-2"></i>Agregar Nueva Zona
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="add">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nombre de la Zona *</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                        <div class="form-text">Ej: Zona Norte, Sector Industrial, etc.</div>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Descripción</label>
                        <textarea class="form-control" id="description" name="description" rows="3" 
                                  placeholder="Descripción opcional de la zona..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-2"></i>Crear Zona
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Zone Modal -->
<div class="modal fade" id="editZoneModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-pencil me-2"></i>Editar Zona
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_name" class="form-label">Nombre de la Zona *</label>
                        <input type="text" class="form-control" id="edit_name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_description" class="form-label">Descripción</label>
                        <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="edit_status" class="form-label">Estado</label>
                        <select class="form-select" id="edit_status" name="status">
                            <option value="1">Activa</option>
                            <option value="0">Inactiva</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-2"></i>Actualizar Zona
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
function editZone(zone) {
    document.getElementById("edit_id").value = zone.id;
    document.getElementById("edit_name").value = zone.name;
    document.getElementById("edit_description").value = zone.description;
    document.getElementById("edit_status").value = zone.status;
    
    new bootstrap.Modal(document.getElementById("editZoneModal")).show();
}

function toggleZoneStatus(zoneId, currentStatus) {
    const action = currentStatus ? "desactivar" : "activar";
    if (confirm(`¿Está seguro que desea ${action} esta zona?`)) {
        // Demo alert
        alert(`Zona ${action}da exitosamente (Demo)`);
        location.reload();
    }
}
</script>';

require_once 'templates/layout.php';
?>