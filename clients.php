<?php
require_once 'app/config/config.php';
require_once 'app/config/database.php';
require_once 'app/models/Auth.php';

$auth = new Auth();
$auth->requireAuth();

$db = Database::getInstance();
$page_title = "Gestión de Clientes";

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $fname = trim($_POST['fname']);
                $lname = trim($_POST['lname']);
                $mi = trim($_POST['mi']);
                $address = trim($_POST['address']);
                $contact = trim($_POST['contact']);
                $category_id = (int)$_POST['category_id'];
                $zone_id = !empty($_POST['zone_id']) ? (int)$_POST['zone_id'] : null;
                $contract_number = trim($_POST['contract_number']);
                
                if (!empty($fname) && !empty($lname) && !empty($address)) {
                    $sql = "INSERT INTO owners (fname, lname, mi, address, contact, category_id, zone_id, contract_number, status) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'active')";
                    
                    try {
                        $db->query($sql, [$fname, $lname, $mi, $address, $contact, $category_id, $zone_id, $contract_number]);
                        $success_message = "Cliente agregado exitosamente.";
                    } catch (Exception $e) {
                        $error_message = "Error al agregar cliente: " . $e->getMessage();
                    }
                }
                break;
                
            case 'edit':
                $id = (int)$_POST['id'];
                $fname = trim($_POST['fname']);
                $lname = trim($_POST['lname']);
                $mi = trim($_POST['mi']);
                $address = trim($_POST['address']);
                $contact = trim($_POST['contact']);
                $category_id = (int)$_POST['category_id'];
                $zone_id = !empty($_POST['zone_id']) ? (int)$_POST['zone_id'] : null;
                $contract_number = trim($_POST['contract_number']);
                $status = $_POST['status'];
                
                if ($id > 0 && !empty($fname) && !empty($lname)) {
                    $sql = "UPDATE owners SET fname=?, lname=?, mi=?, address=?, contact=?, category_id=?, zone_id=?, contract_number=?, status=? WHERE id=?";
                    
                    try {
                        $db->query($sql, [$fname, $lname, $mi, $address, $contact, $category_id, $zone_id, $contract_number, $status, $id]);
                        $success_message = "Cliente actualizado exitosamente.";
                    } catch (Exception $e) {
                        $error_message = "Error al actualizar cliente: " . $e->getMessage();
                    }
                }
                break;
        }
    }
}

// Get search parameters
$search = trim($_GET['search'] ?? '');
$category_filter = $_GET['category'] ?? '';
$zone_filter = $_GET['zone'] ?? '';
$status_filter = $_GET['status'] ?? '';

// Build query
$where_conditions = [];
$params = [];

if (!empty($search)) {
    $where_conditions[] = "(CONCAT(fname, ' ', lname) LIKE ? OR address LIKE ? OR contact LIKE ? OR contract_number LIKE ?)";
    $search_param = "%$search%";
    $params = array_merge($params, [$search_param, $search_param, $search_param, $search_param]);
}

if (!empty($category_filter)) {
    $where_conditions[] = "o.category_id = ?";
    $params[] = $category_filter;
}

if (!empty($zone_filter)) {
    $where_conditions[] = "o.zone_id = ?";
    $params[] = $zone_filter;
}

if (!empty($status_filter)) {
    $where_conditions[] = "o.status = ?";
    $params[] = $status_filter;
} else {
    $where_conditions[] = "o.status != 'deleted'";
}

$where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";

// Get clients with pagination
$page = max(1, (int)($_GET['page'] ?? 1));
$per_page = 20;
$offset = ($page - 1) * $per_page;

$sql = "SELECT o.*, c.name as category_name, z.name as zone_name 
        FROM owners o 
        LEFT JOIN categories c ON o.category_id = c.id 
        LEFT JOIN zones z ON o.zone_id = z.id 
        $where_clause 
        ORDER BY o.fname, o.lname 
        LIMIT $per_page OFFSET $offset";

$clients = [];
$result = $db->query($sql, $params);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $clients[] = $row;
    }
}

// Get total count for pagination
$count_sql = "SELECT COUNT(*) as total FROM owners o $where_clause";
$count_result = $db->query($count_sql, $params);
$total_clients = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_clients / $per_page);

// Get categories and zones for dropdowns
$categories = [];
$result = $db->query("SELECT * FROM categories WHERE status = 1 ORDER BY name");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
}

$zones = [];
$result = $db->query("SELECT * FROM zones WHERE status = 1 ORDER BY name");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $zones[] = $row;
    }
}

ob_start();
?>

<!-- Success/Error Messages -->
<?php if (isset($success_message)): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i><?php echo $success_message; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (isset($error_message)): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle me-2"></i><?php echo $error_message; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">
        <i class="bi bi-people me-2"></i>
        Gestión de Clientes
    </h1>
    <div>
        <button type="button" class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#addClientModal">
            <i class="bi bi-person-plus me-2"></i>Nuevo Cliente
        </button>
        <div class="btn-group">
            <button type="button" class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
                <i class="bi bi-download me-2"></i>Exportar
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="export_clients.php?format=excel"><i class="bi bi-file-earmark-excel me-2"></i>Excel</a></li>
                <li><a class="dropdown-item" href="export_clients.php?format=pdf"><i class="bi bi-file-earmark-pdf me-2"></i>PDF</a></li>
            </ul>
        </div>
    </div>
</div>

<!-- Search and Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-4">
                <label for="search" class="form-label">Buscar</label>
                <input type="text" class="form-control" id="search" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                       placeholder="Nombre, dirección, teléfono o contrato...">
            </div>
            <div class="col-md-2">
                <label for="category" class="form-label">Categoría</label>
                <select class="form-select" id="category" name="category">
                    <option value="">Todas</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo $category['id']; ?>" <?php echo $category_filter == $category['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($category['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label for="zone" class="form-label">Zona</label>
                <select class="form-select" id="zone" name="zone">
                    <option value="">Todas</option>
                    <?php foreach ($zones as $zone): ?>
                        <option value="<?php echo $zone['id']; ?>" <?php echo $zone_filter == $zone['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($zone['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label for="status" class="form-label">Estado</label>
                <select class="form-select" id="status" name="status">
                    <option value="">Todos</option>
                    <option value="active" <?php echo $status_filter === 'active' ? 'selected' : ''; ?>>Activo</option>
                    <option value="inactive" <?php echo $status_filter === 'inactive' ? 'selected' : ''; ?>>Inactivo</option>
                    <option value="suspended" <?php echo $status_filter === 'suspended' ? 'selected' : ''; ?>>Suspendido</option>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="bi bi-search"></i>
                </button>
                <a href="clients.php" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-clockwise"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Clients Table -->
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">
            Clientes Registrados (<?php echo number_format($total_clients); ?>)
        </h5>
    </div>
    <div class="card-body p-0">
        <?php if (empty($clients)): ?>
            <div class="text-center py-5">
                <i class="bi bi-people display-1 text-muted"></i>
                <h5 class="mt-3">No se encontraron clientes</h5>
                <p class="text-muted">Comience agregando un nuevo cliente.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Nombre Completo</th>
                            <th>Dirección</th>
                            <th>Contacto</th>
                            <th>Categoría</th>
                            <th>Zona</th>
                            <th>Contrato</th>
                            <th>Estado</th>
                            <th width="150">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($clients as $client): ?>
                            <tr>
                                <td>
                                    <strong><?php echo htmlspecialchars($client['fname'] . ' ' . $client['lname']); ?></strong>
                                    <?php if (!empty($client['mi'])): ?>
                                        <br><small class="text-muted">CI: <?php echo htmlspecialchars($client['mi']); ?></small>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($client['address']); ?></td>
                                <td><?php echo htmlspecialchars($client['contact']); ?></td>
                                <td>
                                    <span class="badge bg-info">
                                        <?php echo htmlspecialchars($client['category_name'] ?? 'Sin categoría'); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($client['zone_name'] ?? 'Sin zona'); ?></td>
                                <td><?php echo htmlspecialchars($client['contract_number'] ?? 'N/A'); ?></td>
                                <td>
                                    <?php
                                    $status_class = [
                                        'active' => 'bg-success',
                                        'inactive' => 'bg-secondary',
                                        'suspended' => 'bg-warning'
                                    ];
                                    $status_text = [
                                        'active' => 'Activo',
                                        'inactive' => 'Inactivo',
                                        'suspended' => 'Suspendido'
                                    ];
                                    ?>
                                    <span class="badge <?php echo $status_class[$client['status']] ?? 'bg-secondary'; ?>">
                                        <?php echo $status_text[$client['status']] ?? $client['status']; ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-primary" 
                                                onclick="editClient(<?php echo htmlspecialchars(json_encode($client)); ?>)"
                                                title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <a href="client_details.php?id=<?php echo $client['id']; ?>" 
                                           class="btn btn-outline-info" title="Ver detalles">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="billing.php?client_id=<?php echo $client['id']; ?>" 
                                           class="btn btn-outline-success" title="Facturar">
                                            <i class="bi bi-receipt"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
        <div class="card-footer">
            <nav>
                <ul class="pagination pagination-sm justify-content-center mb-0">
                    <?php if ($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo $page-1; ?>&<?php echo http_build_query($_GET); ?>">Anterior</a>
                        </li>
                    <?php endif; ?>
                    
                    <?php for ($i = max(1, $page-2); $i <= min($total_pages, $page+2); $i++): ?>
                        <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?>&<?php echo http_build_query($_GET); ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>
                    
                    <?php if ($page < $total_pages): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo $page+1; ?>&<?php echo http_build_query($_GET); ?>">Siguiente</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    <?php endif; ?>
</div>

<!-- Add Client Modal -->
<div class="modal fade" id="addClientModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-person-plus me-2"></i>Agregar Nuevo Cliente
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="add">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="fname" class="form-label">Nombres *</label>
                            <input type="text" class="form-control" id="fname" name="fname" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="lname" class="form-label">Apellidos *</label>
                            <input type="text" class="form-control" id="lname" name="lname" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="mi" class="form-label">Cédula/DNI</label>
                            <input type="text" class="form-control" id="mi" name="mi">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="contact" class="form-label">Teléfono</label>
                            <input type="text" class="form-control" id="contact" name="contact">
                        </div>
                        <div class="col-12 mb-3">
                            <label for="address" class="form-label">Dirección *</label>
                            <textarea class="form-control" id="address" name="address" rows="2" required></textarea>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="category_id" class="form-label">Categoría</label>
                            <select class="form-select" id="category_id" name="category_id" required>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo $category['id']; ?>">
                                        <?php echo htmlspecialchars($category['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="zone_id" class="form-label">Zona</label>
                            <select class="form-select" id="zone_id" name="zone_id">
                                <option value="">Seleccionar zona</option>
                                <?php foreach ($zones as $zone): ?>
                                    <option value="<?php echo $zone['id']; ?>">
                                        <?php echo htmlspecialchars($zone['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="contract_number" class="form-label">Número de Contrato</label>
                            <input type="text" class="form-control" id="contract_number" name="contract_number">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-2"></i>Guardar Cliente
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Client Modal -->
<div class="modal fade" id="editClientModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-pencil me-2"></i>Editar Cliente
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="editForm">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_fname" class="form-label">Nombres *</label>
                            <input type="text" class="form-control" id="edit_fname" name="fname" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_lname" class="form-label">Apellidos *</label>
                            <input type="text" class="form-control" id="edit_lname" name="lname" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_mi" class="form-label">Cédula/DNI</label>
                            <input type="text" class="form-control" id="edit_mi" name="mi">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_contact" class="form-label">Teléfono</label>
                            <input type="text" class="form-control" id="edit_contact" name="contact">
                        </div>
                        <div class="col-12 mb-3">
                            <label for="edit_address" class="form-label">Dirección *</label>
                            <textarea class="form-control" id="edit_address" name="address" rows="2" required></textarea>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="edit_category_id" class="form-label">Categoría</label>
                            <select class="form-select" id="edit_category_id" name="category_id" required>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo $category['id']; ?>">
                                        <?php echo htmlspecialchars($category['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="edit_zone_id" class="form-label">Zona</label>
                            <select class="form-select" id="edit_zone_id" name="zone_id">
                                <option value="">Seleccionar zona</option>
                                <?php foreach ($zones as $zone): ?>
                                    <option value="<?php echo $zone['id']; ?>">
                                        <?php echo htmlspecialchars($zone['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="edit_contract_number" class="form-label">Número de Contrato</label>
                            <input type="text" class="form-control" id="edit_contract_number" name="contract_number">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="edit_status" class="form-label">Estado</label>
                            <select class="form-select" id="edit_status" name="status" required>
                                <option value="active">Activo</option>
                                <option value="inactive">Inactivo</option>
                                <option value="suspended">Suspendido</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-2"></i>Actualizar Cliente
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
function editClient(client) {
    document.getElementById("edit_id").value = client.id;
    document.getElementById("edit_fname").value = client.fname;
    document.getElementById("edit_lname").value = client.lname;
    document.getElementById("edit_mi").value = client.mi;
    document.getElementById("edit_address").value = client.address;
    document.getElementById("edit_contact").value = client.contact;
    document.getElementById("edit_category_id").value = client.category_id;
    document.getElementById("edit_zone_id").value = client.zone_id || "";
    document.getElementById("edit_contract_number").value = client.contract_number || "";
    document.getElementById("edit_status").value = client.status;
    
    new bootstrap.Modal(document.getElementById("editClientModal")).show();
}
</script>';

require_once 'templates/layout.php';
?>