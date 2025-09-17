<?php
require_once 'app/config/config.php';

// Demo authentication check
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$page_title = "Dashboard";

// Demo statistics
$stats = [
    'users' => 8,
    'clients' => 156,
    'bills_month' => 45,
    'revenue_month' => 12500.75
];

// Demo chart data
$chart_data = [
    ['date' => '2025-09-01', 'count' => 12, 'revenue' => 1200],
    ['date' => '2025-09-02', 'count' => 8, 'revenue' => 850],
    ['date' => '2025-09-03', 'count' => 15, 'revenue' => 1450],
    ['date' => '2025-09-04', 'count' => 20, 'revenue' => 2100],
    ['date' => '2025-09-05', 'count' => 18, 'revenue' => 1800],
    ['date' => '2025-09-06', 'count' => 22, 'revenue' => 2300],
    ['date' => '2025-09-07', 'count' => 16, 'revenue' => 1650]
];

// Demo recent bills
$recent_bills = [
    ['id' => 1, 'fname' => 'Juan', 'lname' => 'Pérez', 'price' => 45.50],
    ['id' => 2, 'fname' => 'María', 'lname' => 'González', 'price' => 38.75],
    ['id' => 3, 'fname' => 'Carlos', 'lname' => 'Rodríguez', 'price' => 52.20],
    ['id' => 4, 'fname' => 'Ana', 'lname' => 'López', 'price' => 41.30],
    ['id' => 5, 'fname' => 'Pedro', 'lname' => 'Martínez', 'price' => 47.85]
];

ob_start();
?>

<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card">
            <div class="d-flex align-items-center">
                <div class="stats-icon bg-primary bg-gradient text-white">
                    <i class="bi bi-people"></i>
                </div>
                <div class="flex-grow-1">
                    <h4 class="fw-bold text-primary mb-0"><?php echo number_format($stats['clients']); ?></h4>
                    <p class="text-muted mb-0">Clientes Activos</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card">
            <div class="d-flex align-items-center">
                <div class="stats-icon bg-success bg-gradient text-white">
                    <i class="bi bi-receipt"></i>
                </div>
                <div class="flex-grow-1">
                    <h4 class="fw-bold text-success mb-0"><?php echo number_format($stats['bills_month']); ?></h4>
                    <p class="text-muted mb-0">Facturas Este Mes</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card">
            <div class="d-flex align-items-center">
                <div class="stats-icon bg-warning bg-gradient text-white">
                    <i class="bi bi-currency-dollar"></i>
                </div>
                <div class="flex-grow-1">
                    <h4 class="fw-bold text-warning mb-0">S/. <?php echo number_format($stats['revenue_month'], 2); ?></h4>
                    <p class="text-muted mb-0">Ingresos Este Mes</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card">
            <div class="d-flex align-items-center">
                <div class="stats-icon bg-info bg-gradient text-white">
                    <i class="bi bi-person-gear"></i>
                </div>
                <div class="flex-grow-1">
                    <h4 class="fw-bold text-info mb-0"><?php echo number_format($stats['users']); ?></h4>
                    <p class="text-muted mb-0">Usuarios del Sistema</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-bar-chart me-2"></i>
                    Ingresos de los Últimos 30 Días
                </h5>
            </div>
            <div class="card-body">
                <canvas id="revenueChart" width="400" height="200"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-clock-history me-2"></i>
                    Actividad Reciente
                </h5>
            </div>
            <div class="card-body">
                <?php if (empty($recent_bills)): ?>
                    <p class="text-muted text-center">No hay actividad reciente</p>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($recent_bills as $bill): ?>
                            <div class="list-group-item px-0 py-2 border-0">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1"><?php echo htmlspecialchars($bill['fname'] . ' ' . $bill['lname']); ?></h6>
                                        <small class="text-muted">Factura #<?php echo $bill['id']; ?></small>
                                    </div>
                                    <span class="badge bg-success">S/. <?php echo number_format($bill['price'], 2); ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-pie-chart me-2"></i>
                    Distribución por Categoría
                </h5>
            </div>
            <div class="card-body">
                <canvas id="categoryChart" width="400" height="300"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-speedometer2 me-2"></i>
                    Accesos Rápidos
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="billing.php" class="btn btn-primary">
                        <i class="bi bi-receipt me-2"></i>
                        Nueva Factura
                    </a>
                    <a href="clients.php" class="btn btn-success">
                        <i class="bi bi-person-plus me-2"></i>
                        Agregar Cliente
                    </a>
                    <?php if ($_SESSION['role'] === ROLE_ADMIN || $_SESSION['role'] === ROLE_LECTURAS): ?>
                    <a href="reading_capture.php" class="btn btn-info">
                        <i class="bi bi-camera me-2"></i>
                        Tomar Lectura
                    </a>
                    <?php endif; ?>
                    <a href="payments.php" class="btn btn-warning">
                        <i class="bi bi-credit-card me-2"></i>
                        Procesar Pago
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();

$additional_js = '
<script>
// Revenue Chart
const revenueCtx = document.getElementById("revenueChart").getContext("2d");
const revenueChart = new Chart(revenueCtx, {
    type: "line",
    data: {
        labels: ' . json_encode(array_reverse(array_column($chart_data, 'date'))) . ',
        datasets: [{
            label: "Ingresos (S/.)",
            data: ' . json_encode(array_reverse(array_column($chart_data, 'revenue'))) . ',
            borderColor: "rgb(75, 192, 192)",
            backgroundColor: "rgba(75, 192, 192, 0.1)",
            tension: 0.1,
            fill: true
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return "S/. " + value.toFixed(2);
                    }
                }
            }
        }
    }
});

// Category Chart (placeholder data)
const categoryCtx = document.getElementById("categoryChart").getContext("2d");
const categoryChart = new Chart(categoryCtx, {
    type: "doughnut",
    data: {
        labels: ["Residencial", "Comercial", "Industrial"],
        datasets: [{
            data: [65, 25, 10],
            backgroundColor: [
                "#36A2EB",
                "#FF6384",
                "#FFCE56"
            ]
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: "bottom"
            }
        }
    }
});
</script>';

include 'templates/layout.php';
?>