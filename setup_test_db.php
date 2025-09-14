<?php
// Test database connection using SQLite for development
try {
    $pdo = new PDO('sqlite:test_aguaweb.db');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create tables if they don't exist
    $pdo->exec("CREATE TABLE IF NOT EXISTS owners (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        lname VARCHAR(60) NOT NULL,
        fname VARCHAR(60) NOT NULL,
        mi VARCHAR(2) NOT NULL,
        address VARCHAR(60) NOT NULL,
        contact VARCHAR(15) NOT NULL
    )");
    
    $pdo->exec("CREATE TABLE IF NOT EXISTS tempo_bill (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        Prev VARCHAR(40) NOT NULL,
        Client VARCHAR(30) NOT NULL
    )");
    
    $pdo->exec("CREATE TABLE IF NOT EXISTS bill (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        owners_id INTEGER NOT NULL,
        prev VARCHAR(20) NOT NULL,
        pres VARCHAR(20) NOT NULL,
        price VARCHAR(20) NOT NULL,
        date VARCHAR(20) NOT NULL
    )");
    
    $pdo->exec("CREATE TABLE IF NOT EXISTS facturacion_mensual (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        owners_id INTEGER NOT NULL,
        mes VARCHAR(20) NOT NULL,
        consumo VARCHAR(20) NOT NULL,
        monto VARCHAR(20) NOT NULL,
        fecha_emision VARCHAR(20) NOT NULL,
        pagada INTEGER NOT NULL DEFAULT 0
    )");
    
    $pdo->exec("CREATE TABLE IF NOT EXISTS user (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username VARCHAR(50) NOT NULL,
        password VARCHAR(60) NOT NULL,
        name VARCHAR(60) NOT NULL
    )");
    
    // Insert test data if tables are empty
    $result = $pdo->query("SELECT COUNT(*) as count FROM owners");
    $count = $result->fetch()['count'];
    
    if ($count == 0) {
        // Insert test clients
        $pdo->exec("INSERT INTO owners (lname, fname, mi, address, contact) VALUES 
            ('García', 'Juan', '01', 'Calle 123', '1234567890'),
            ('López', 'María', '02', 'Avenida 456', '0987654321'),
            ('Martínez', 'Carlos', '03', 'Plaza Central', '5551234567')");
        
        // Insert corresponding tempo_bill entries
        $pdo->exec("INSERT INTO tempo_bill (Client, Prev) VALUES 
            ('Juan', '100'),
            ('María', '150'),
            ('Carlos', '200')");
        
        // Insert test user
        $pdo->exec("INSERT INTO user (username, password, name) VALUES 
            ('admin', 'admin', 'Administrator')");
    }
    
    echo "Test database setup completed successfully!\n";
    
} catch(PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>