<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "abm-ps";

// Función para obtener conexión PDO
function getConnection() {
    global $host, $user, $pass, $db;
    
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $pdo;
    } catch (PDOException $e) {
        die("Error de conexión: " . $e->getMessage());
    }
}

// Mantener compatibilidad con MySQLi para otros usos
$conn = mysqli_connect($host, $user, $pass, $db);

// Verificar conexión MySQLi
if ($conn->connect_error) {
    die("Error de conexión MySQLi: " . $conn->connect_error);
}
?>
