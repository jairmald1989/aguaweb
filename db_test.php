<?php
// Test database connection using SQLite for development testing
try {
    $pdo = new PDO('sqlite:test_aguaweb.db');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // For compatibility with mysqli code, we'll create a mock connection object
    class MockMySQLiConnection {
        private $pdo;
        
        public function __construct($pdo) {
            $this->pdo = $pdo;
        }
        
        public function query($sql) {
            return $this->pdo->query($sql);
        }
        
        public function prepare($sql) {
            return $this->pdo->prepare($sql);
        }
        
        public function lastInsertId() {
            return $this->pdo->lastInsertId();
        }
    }
    
    $conn = new MockMySQLiConnection($pdo);
    
} catch(PDOException $e) {
    die('Could not connect: ' . $e->getMessage());
}

// For compatibility with existing mysqli functions, create wrapper functions
function mysqli_query($conn, $sql) {
    try {
        global $pdo;
        $result = $pdo->query($sql);
        return $result;
    } catch(PDOException $e) {
        return false;
    }
}

function mysqli_fetch_array($result) {
    if ($result) {
        return $result->fetch(PDO::FETCH_BOTH);
    }
    return false;
}

function mysqli_affected_rows($conn) {
    global $pdo;
    return $pdo->rowCount();
}

function mysqli_num_rows($result) {
    if ($result) {
        return $result->rowCount();
    }
    return 0;
}

function mysqli_error($conn) {
    global $pdo;
    $errorInfo = $pdo->errorInfo();
    return $errorInfo[2] ?? '';
}
?>