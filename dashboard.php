<?php
require_once 'app/config/config.php';
require_once 'app/config/database.php';
require_once 'app/models/Auth.php';

$auth = new Auth();
$auth->requireAuth();

$db = Database::getInstance();
$page_title = "Dashboard";

// Get statistics
$stats = [];

// Total users
$result = $db->query("SELECT COUNT(*) as count FROM user WHERE status = 1");
$stats['users'] = $result->fetch_assoc()['count'];

// Total clients
$result = $db->query("SELECT COUNT(*) as count FROM owners WHERE status = 'active'");
$stats['clients'] = $result->fetch_assoc()['count'];

// Total bills this month
$result = $db->query("SELECT COUNT(*) as count FROM bill WHERE MONTH(created_at) = MONTH(CURRENT_DATE()) AND YEAR(created_at) = YEAR(CURRENT_DATE())");
$stats['bills_month'] = $result->fetch_assoc()['count'];

// Total revenue this month
$result = $db->query("SELECT COALESCE(SUM(price), 0) as total FROM bill WHERE MONTH(created_at) = MONTH(CURRENT_DATE()) AND YEAR(created_at) = YEAR(CURRENT_DATE())");
$stats['revenue_month'] = $result->fetch_assoc()['total'];

// Recent bills for chart
$result = $db->query("
    SELECT DATE(created_at) as date, COUNT(*) as count, SUM(price) as revenue 
    FROM bill 
    WHERE created_at >= DATE_SUB(CURRENT_DATE(), INTERVAL 30 DAY) 
    GROUP BY DATE(created_at) 
    ORDER BY date DESC 
    LIMIT 30
");

$chart_data = [];
while ($row = $result->fetch_assoc()) {
    $chart_data[] = $row;
}

// Recent activity
$result = $db->query("
    SELECT b.*, o.fname, o.lname 
    FROM bill b 
    JOIN owners o ON b.owners_id = o.id 
    ORDER BY b.created_at DESC 
    LIMIT 10
");

$recent_bills = [];
while ($row = $result->fetch_assoc()) {
    $recent_bills[] = $row;
}

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