<?php 
require_once("../database.php");
$pdo = getConnection();

$sql = "SELECT u.id_usuario, u.nombre, u.apellido, u.nickname, u.email, r.nombre AS rol 
        FROM usuario u 
        INNER JOIN rol r ON u.rol_id = r.id_rol
        ORDER BY u.id_usuario ASC";
$stmt = $pdo->query($sql);
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>