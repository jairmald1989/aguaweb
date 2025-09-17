<?php
require_once 'app/config/config.php';

// Demo authentication check
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

// Only admin or lecturas role can access reading capture
if ($_SESSION['role'] !== ROLE_ADMIN && $_SESSION['role'] !== ROLE_LECTURAS) {
    header('HTTP/1.0 403 Forbidden');
    die('Acceso denegado. Solo usuarios autorizados pueden tomar lecturas.');
}

$page_title = "Toma de Lecturas";

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'save_reading') {
        $success_message = "Lectura guardada exitosamente (Demo).";
    }
}

// Demo data
$clients_with_meters = [
    [
        'id' => 1,
        'name' => 'Juan Carlos Pérez González',
        'address' => 'Av. Principal 123, Urb. Los Jardines',
        'meter_serial' => 'MT-001',
        'last_reading' => 1250.50,
        'last_reading_date' => '2025-08-15',
        'zone_name' => 'Zona Norte'
    ],
    [
        'id' => 2,
        'name' => 'María Elena González López',
        'address' => 'Jr. Comercio 456, Centro Histórico',
        'meter_serial' => 'MT-002',
        'last_reading' => 890.25,
        'last_reading_date' => '2025-08-14',
        'zone_name' => 'Zona Centro'
    ],
    [
        'id' => 3,
        'name' => 'Roberto Martínez Silva',
        'address' => 'Av. Industrial 789, Parque Industrial',
        'meter_serial' => 'MT-003',
        'last_reading' => 2150.75,
        'last_reading_date' => '2025-08-13',
        'zone_name' => 'Zona Sur'
    ],
    [
        'id' => 4,
        'name' => 'Ana Sofía Rodríguez Vargas',
        'address' => 'Calle Las Flores 321, Villa Hermosa',
        'meter_serial' => 'MT-004',
        'last_reading' => 675.30,
        'last_reading_date' => '2025-08-12',
        'zone_name' => 'Zona Norte'
    ]
];

$search = trim($_GET['search'] ?? '');
$selected_client = null;

if (!empty($search)) {
    $clients_with_meters = array_filter($clients_with_meters, function($client) use ($search) {
        return stripos($client['name'], $search) !== false || 
               stripos($client['address'], $search) !== false ||
               stripos($client['meter_serial'], $search) !== false;
    });
}

// Get selected client for reading
if (isset($_GET['client_id'])) {
    $client_id = (int)$_GET['client_id'];
    foreach ($clients_with_meters as $client) {
        if ($client['id'] === $client_id) {
            $selected_client = $client;
            break;
        }
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

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">
        <i class="bi bi-camera me-2"></i>
        Toma de Lecturas
    </h1>
    <div>
        <span class="badge bg-info fs-6">
            <i class="bi bi-person me-1"></i>
            <?php echo $_SESSION['name']; ?>
        </span>
    </div>
</div>

<?php if (!$selected_client): ?>
    <!-- Client Search -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="bi bi-search me-2"></i>
                Buscar Cliente para Lectura
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-8">
                    <input type="text" class="form-control form-control-lg" name="search" 
                           value="<?php echo htmlspecialchars($search); ?>" 
                           placeholder="Buscar por nombre, dirección o número de medidor..." autofocus>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary btn-lg w-100">
                        <i class="bi bi-search me-2"></i>Buscar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Client Results -->
    <?php if (!empty($search)): ?>
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    Resultados de Búsqueda (<?php echo count($clients_with_meters); ?>)
                </h5>
            </div>
            <div class="card-body p-0">
                <?php if (empty($clients_with_meters)): ?>
                    <div class="text-center py-5">
                        <i class="bi bi-search display-1 text-muted"></i>
                        <h5 class="mt-3">No se encontraron clientes</h5>
                        <p class="text-muted">Intente con otros términos de búsqueda.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Cliente</th>
                                    <th>Dirección</th>
                                    <th>Medidor</th>
                                    <th>Última Lectura</th>
                                    <th>Zona</th>
                                    <th width="120">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($clients_with_meters as $client): ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo htmlspecialchars($client['name']); ?></strong>
                                        </td>
                                        <td><?php echo htmlspecialchars($client['address']); ?></td>
                                        <td>
                                            <span class="badge bg-secondary"><?php echo $client['meter_serial']; ?></span>
                                        </td>
                                        <td>
                                            <strong><?php echo number_format($client['last_reading'], 2); ?> m³</strong>
                                            <br>
                                            <small class="text-muted"><?php echo date('d/m/Y', strtotime($client['last_reading_date'])); ?></small>
                                        </td>
                                        <td><?php echo $client['zone_name']; ?></td>
                                        <td>
                                            <a href="?client_id=<?php echo $client['id']; ?>" class="btn btn-primary btn-sm">
                                                <i class="bi bi-camera"></i> Tomar Lectura
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php else: ?>
        <!-- Quick Access -->
        <div class="row">
            <?php foreach (array_slice($clients_with_meters, 0, 6) as $client): ?>
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-title"><?php echo htmlspecialchars($client['name']); ?></h6>
                            <p class="card-text">
                                <small class="text-muted"><?php echo htmlspecialchars($client['address']); ?></small><br>
                                <strong>Medidor:</strong> <?php echo $client['meter_serial']; ?><br>
                                <strong>Última:</strong> <?php echo $client['last_reading']; ?> m³
                            </p>
                            <a href="?client_id=<?php echo $client['id']; ?>" class="btn btn-primary btn-sm">
                                <i class="bi bi-camera me-1"></i> Tomar Lectura
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

<?php else: ?>
    <!-- Reading Form -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-speedometer2 me-2"></i>
                            Registro de Lectura
                        </h5>
                        <a href="reading_capture.php" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-arrow-left me-1"></i>Volver
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Client Info -->
                    <div class="alert alert-info">
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Cliente:</strong> <?php echo htmlspecialchars($selected_client['name']); ?><br>
                                <strong>Dirección:</strong> <?php echo htmlspecialchars($selected_client['address']); ?>
                            </div>
                            <div class="col-md-6">
                                <strong>Medidor:</strong> <?php echo $selected_client['meter_serial']; ?><br>
                                <strong>Zona:</strong> <?php echo $selected_client['zone_name']; ?>
                            </div>
                        </div>
                    </div>

                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="save_reading">
                        <input type="hidden" name="client_id" value="<?php echo $selected_client['id']; ?>">
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="previous_reading" class="form-label">Lectura Anterior</label>
                                <input type="number" class="form-control" id="previous_reading" 
                                       value="<?php echo $selected_client['last_reading']; ?>" readonly>
                                <small class="form-text text-muted">
                                    Fecha: <?php echo date('d/m/Y', strtotime($selected_client['last_reading_date'])); ?>
                                </small>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="current_reading" class="form-label">Lectura Actual *</label>
                                <input type="number" class="form-control" id="current_reading" name="current_reading" 
                                       step="0.01" min="<?php echo $selected_client['last_reading']; ?>" required>
                                <small class="form-text text-muted">
                                    Debe ser mayor o igual a <?php echo $selected_client['last_reading']; ?>
                                </small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="consumption" class="form-label">Consumo Calculado</label>
                            <input type="number" class="form-control bg-light" id="consumption" readonly>
                            <small class="form-text text-muted">Se calcula automáticamente</small>
                        </div>

                        <div class="mb-3">
                            <label for="reading_photo" class="form-label">Foto del Medidor *</label>
                            <input type="file" class="form-control" id="reading_photo" name="reading_photo" 
                                   accept="image/*" capture="camera" required>
                            <small class="form-text text-muted">
                                Tome una foto clara del medidor que muestre la lectura actual
                            </small>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Observaciones</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3" 
                                      placeholder="Ingrese cualquier observación sobre el medidor o la lectura..."></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="reading_date" class="form-label">Fecha de Lectura</label>
                            <input type="date" class="form-control" id="reading_date" name="reading_date" 
                                   value="<?php echo date('Y-m-d'); ?>" required>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="bi bi-save me-2"></i>Guardar Lectura
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Reading Instructions -->
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-info-circle me-2"></i>
                        Instrucciones
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="bi bi-1-circle text-primary me-2"></i>
                            Verifique que el número de serie del medidor coincida
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-2-circle text-primary me-2"></i>
                            Ingrese la lectura actual exactamente como aparece
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-3-circle text-primary me-2"></i>
                            Tome una foto clara del medidor
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-4-circle text-primary me-2"></i>
                            Anote cualquier anomalía en observaciones
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-5-circle text-primary me-2"></i>
                            Verifique que el consumo calculado sea razonable
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Photo Preview -->
            <div class="card mt-3" id="photoPreview" style="display: none;">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-image me-2"></i>
                        Vista Previa
                    </h6>
                </div>
                <div class="card-body text-center">
                    <img id="previewImage" class="img-fluid rounded" style="max-height: 200px;">
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php
$content = ob_get_clean();

$additional_js = '
<script>
// Calculate consumption automatically
document.getElementById("current_reading").addEventListener("input", function() {
    const previous = parseFloat(document.getElementById("previous_reading").value) || 0;
    const current = parseFloat(this.value) || 0;
    const consumption = Math.max(0, current - previous);
    document.getElementById("consumption").value = consumption.toFixed(2);
});

// Photo preview
document.getElementById("reading_photo").addEventListener("change", function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById("previewImage").src = e.target.result;
            document.getElementById("photoPreview").style.display = "block";
        };
        reader.readAsDataURL(file);
    }
});

// Form validation
document.querySelector("form").addEventListener("submit", function(e) {
    const current = parseFloat(document.getElementById("current_reading").value);
    const previous = parseFloat(document.getElementById("previous_reading").value);
    
    if (current < previous) {
        e.preventDefault();
        alert("La lectura actual no puede ser menor que la lectura anterior");
        return false;
    }
    
    const consumption = current - previous;
    if (consumption > 1000) {
        if (!confirm("El consumo parece muy alto (" + consumption.toFixed(2) + " m³). ¿Está seguro que la lectura es correcta?")) {
            e.preventDefault();
            return false;
        }
    }
});
</script>';

require_once 'templates/layout.php';
?>