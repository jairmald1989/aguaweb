<?php
include 'db.php';

if (isset($_POST['factura_id']) && !empty($_POST['factura_id'])) {
    $factura_id = $_POST['factura_id'];
    
    $result = mysqli_query($conn, "UPDATE facturacion_mensual SET pagada = 1 WHERE id = '$factura_id'");
    
    if (mysqli_affected_rows($conn) > 0) {
        echo 'success';
    } else {
        echo 'error';
    }
} else {
    echo 'error';
}
?>