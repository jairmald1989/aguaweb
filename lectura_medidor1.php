<?php
session_start();

if (isset($_POST['guardar'])) {
    include 'db.php';
    
    $owners_id = $_POST['owners_id'];
    $lectura_anterior = $_POST['lectura_anterior'];
    $lectura_actual = $_POST['lectura_actual'];
    $fecha = $_POST['fecha'];
    
    // Obtener información del cliente
    $result = mysqli_query($conn, "SELECT fname FROM owners WHERE id = '$owners_id'");
    if ($row = mysqli_fetch_array($result)) {
        $fname = $row['fname'];
        
        // Calcular consumo
        $consumo = $lectura_actual - $lectura_anterior;
        
        // Validar que la lectura actual sea mayor que la anterior
        if ($consumo < 0) {
            echo '<script>alert("Error: La lectura actual debe ser mayor que la lectura anterior");</script>';
            echo '<script>window.history.back();</script>';
            exit;
        }
        
        // Actualizar tempo_bill con la nueva lectura
        mysqli_query($conn, "UPDATE tempo_bill SET Prev = '$lectura_actual' WHERE Client = '$fname'");
        
        // Verificar si la actualización fue exitosa
        if (mysqli_affected_rows($conn) > 0 || mysqli_error($conn) == '') {
            echo '<script>alert("Lectura guardada exitosamente");</script>';
            echo '<script>window.parent.location.reload();</script>';
        } else {
            echo '<script>alert("Error al guardar la lectura");</script>';
            echo '<script>window.history.back();</script>';
        }
    } else {
        echo '<script>alert("Error: Cliente no encontrado");</script>';
        echo '<script>window.history.back();</script>';
    }
} else {
    header("Location: clients.php");
}
?>