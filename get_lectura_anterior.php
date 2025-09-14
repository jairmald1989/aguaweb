<?php
include 'db.php';

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $client_id = $_GET['id'];
    
    // Obtener el nombre del cliente para buscar en tempo_bill
    $result = mysqli_query($conn, "SELECT fname FROM owners WHERE id = '$client_id'");
    if ($row = mysqli_fetch_array($result)) {
        $fname = $row['fname'];
        
        // Buscar la lectura anterior en tempo_bill
        $q = mysqli_query($conn, "SELECT Prev FROM tempo_bill WHERE Client = '$fname'");
        if ($results = mysqli_fetch_array($q)) {
            echo $results['Prev'];
        } else {
            echo '0';
        }
    } else {
        echo '0';
    }
} else {
    echo '0';
}
?>