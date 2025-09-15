<?php
session_start();
if (!isset($_SESSION['id'])) {
    echo '<script>window.location="index.php"</script>';
    exit();
}

include 'db.php';

$response = array('success' => false, 'message' => '', 'added' => 0, 'skipped' => 0);

if (isset($_POST['import_csv']) && isset($_FILES['csv_file'])) {
    $file = $_FILES['csv_file'];
    
    // Validate file upload
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $response['message'] = 'Error al subir el archivo.';
        echo json_encode($response);
        exit();
    }
    
    // Validate file extension
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if ($file_extension !== 'csv') {
        $response['message'] = 'El archivo debe ser de tipo CSV.';
        echo json_encode($response);
        exit();
    }
    
    // Open and read CSV file
    $handle = fopen($file['tmp_name'], 'r');
    if (!$handle) {
        $response['message'] = 'No se pudo leer el archivo CSV.';
        echo json_encode($response);
        exit();
    }
    
    // Skip header row
    fgetcsv($handle);
    
    $added = 0;
    $skipped = 0;
    
    while (($data = fgetcsv($handle)) !== FALSE) {
        // Skip empty rows
        if (count($data) < 5 || empty(trim($data[0])) || empty(trim($data[1]))) {
            continue;
        }
        
        $lname = mysqli_real_escape_string($conn, trim($data[0]));
        $fname = mysqli_real_escape_string($conn, trim($data[1]));
        $mi = mysqli_real_escape_string($conn, trim($data[2]));
        $address = mysqli_real_escape_string($conn, trim($data[3]));
        $contact = mysqli_real_escape_string($conn, trim($data[4]));
        
        // Check for duplicates by mi (cedula) or contact (email/phone)
        $check_query = "SELECT id FROM owners WHERE mi = '$mi' OR contact = '$contact'";
        $check_result = mysqli_query($conn, $check_query);
        
        if (mysqli_num_rows($check_result) > 0) {
            $skipped++;
            continue;
        }
        
        // Insert new client
        $insert_query = "INSERT INTO owners (lname, fname, mi, address, contact) VALUES ('$lname', '$fname', '$mi', '$address', '$contact')";
        
        if (mysqli_query($conn, $insert_query)) {
            // Also insert into tempo_bill for meter reading
            $tempo_query = "INSERT INTO tempo_bill (Client, Prev) VALUES ('$fname', '0')";
            mysqli_query($conn, $tempo_query);
            $added++;
        } else {
            $skipped++;
        }
    }
    
    fclose($handle);
    
    $response['success'] = true;
    $response['added'] = $added;
    $response['skipped'] = $skipped;
    
    if ($added > 0 && $skipped > 0) {
        $response['message'] = "Importación completada: $added clientes agregados, $skipped omitidos por duplicados.";
    } elseif ($added > 0) {
        $response['message'] = "Importación exitosa: $added clientes agregados.";
    } elseif ($skipped > 0) {
        $response['message'] = "No se agregaron clientes: $skipped registros omitidos por duplicados.";
    } else {
        $response['message'] = "No se procesaron registros válidos.";
    }
    
} else {
    $response['message'] = 'Datos de importación inválidos.';
}

echo json_encode($response);
?>