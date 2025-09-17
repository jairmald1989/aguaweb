<?php
// Legacy database connection for backward compatibility
$conn = mysqli_connect('localhost', 'jairmald1989_agua', 'iDepyi%fs=eI',"jairmald1989_agua");
if (!$conn) {
    die('Could not connect: ' . mysqli_error($conn));
}

// Set charset
mysqli_set_charset($conn, "utf8mb4");
