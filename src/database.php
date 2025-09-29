<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "abm-ps";

$conn = mysqli_connect($host, $user, $pass, $db);

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
?>
