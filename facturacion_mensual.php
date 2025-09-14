<?php
session_start();

if (isset($_POST['generar_facturacion'])) {
    include 'db.php';
    
    $mes = $_POST['mes'];
    $fecha_emision = date('Y-m-d H:i:s');
    
    // Verificar si ya existe facturación para este mes
    $check = mysqli_query($conn, "SELECT COUNT(*) as count FROM facturacion_mensual WHERE mes = '$mes'");
    $check_result = mysqli_fetch_array($check);
    
    if ($check_result['count'] > 0) {
        echo '<script>alert("Ya existe facturación para el mes seleccionado");</script>';
        echo '<script>window.history.back();</script>';
        exit;
    }
    
    // Obtener todos los clientes
    $clientes = mysqli_query($conn, "SELECT * FROM owners");
    
    $facturas_generadas = 0;
    
    while ($cliente = mysqli_fetch_array($clientes)) {
        $owners_id = $cliente['id'];
        $fname = $cliente['fname'];
        
        // Obtener la lectura actual del cliente
        $lectura_query = mysqli_query($conn, "SELECT Prev FROM tempo_bill WHERE Client = '$fname'");
        if ($lectura_result = mysqli_fetch_array($lectura_query)) {
            $lectura_actual = $lectura_result['Prev'];
            
            // Buscar la última factura para obtener la lectura anterior
            $ultima_factura = mysqli_query($conn, "SELECT * FROM bill WHERE owners_id = '$owners_id' ORDER BY id DESC LIMIT 1");
            
            if ($factura_anterior = mysqli_fetch_array($ultima_factura)) {
                $lectura_anterior = $factura_anterior['pres'];
            } else {
                // Si no hay factura anterior, usar 0 como lectura anterior
                $lectura_anterior = 0;
            }
            
            // Calcular consumo
            $consumo = $lectura_actual - $lectura_anterior;
            
            if ($consumo < 0) {
                $consumo = 0; // Evitar consumos negativos
            }
            
            // Calcular monto con sistema escalonado (similar a addbill.php)
            $monto = calcular_monto_escalonado($consumo);
            
            // Insertar en facturacion_mensual
            mysqli_query($conn, "INSERT INTO facturacion_mensual (owners_id, mes, consumo, monto, fecha_emision, pagada) 
                                VALUES ('$owners_id', '$mes', '$consumo', '$monto', '$fecha_emision', 0)");
            
            if (mysqli_affected_rows($conn) > 0) {
                $facturas_generadas++;
            }
        }
    }
    
    echo '<script>alert("Facturación generada exitosamente para ' . $facturas_generadas . ' clientes");</script>';
    echo '<script>window.parent.location.reload();</script>';
}

function calcular_monto_escalonado($consumo) {
    // Sistema de precios escalonado por categorías
    $monto = 0;
    
    if ($consumo <= 10) {
        // Consumo básico: 5 por unidad
        $monto = $consumo * 5;
    } elseif ($consumo <= 30) {
        // Hasta 10: 5 por unidad, de 11 a 30: 8 por unidad
        $monto = (10 * 5) + (($consumo - 10) * 8);
    } elseif ($consumo <= 50) {
        // Hasta 10: 5, de 11 a 30: 8, de 31 a 50: 12 por unidad
        $monto = (10 * 5) + (20 * 8) + (($consumo - 30) * 12);
    } else {
        // Más de 50: 15 por unidad adicional
        $monto = (10 * 5) + (20 * 8) + (20 * 12) + (($consumo - 50) * 15);
    }
    
    return $monto;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Generar Facturación Mensual</title>
    <link rel="stylesheet" type="text/css" href="css/bootstrap/dist/css/bootstrap.css"/>
</head>
<body>
    <div class="container">
        <h2 align="center">Generar Facturación Mensual</h2>
        
        <form method="post" action="">
            <div class="form-group">
                <label for="mes">Mes/Año:</label>
                <input type="month" name="mes" class="form-control" value="<?php echo date('Y-m'); ?>" required="required" />
            </div>
            
            <div class="alert alert-info">
                <strong>Sistema de Precios Escalonado:</strong><br>
                • 0-10 unidades: $5 por unidad<br>
                • 11-30 unidades: $8 por unidad<br>
                • 31-50 unidades: $12 por unidad<br>
                • Más de 50 unidades: $15 por unidad
            </div>
            
            <br />
            <input type="submit" name="generar_facturacion" value="Generar Facturación" class="btn btn-primary form-control"/>
        </form>
    </div>
</body>
</html>