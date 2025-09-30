<?php 
require_once("../database.php");
$pdo = getConnection();

$sql = "SELECT u.id_usuario, u.nombre, u.apellido, u.nickname, u.email, u.rol_id, r.nombre AS rol 
        FROM usuario u 
        INNER JOIN rol r ON u.rol_id = r.id_rol
        ORDER BY u.id_usuario ASC";
$stmt = $pdo->query($sql);
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Si se solicita formato JSON
if (isset($_GET['format']) && $_GET['format'] === 'json') {
    header('Content-Type: application/json');
    echo json_encode($usuarios);
    exit;
}
?>