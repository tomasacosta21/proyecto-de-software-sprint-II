<?php
require_once("../database.php");
$pdo = getConnection();

$id = intval($_GET["id"]);
$stmt = $pdo->prepare("SELECT * FROM usuario WHERE id_usuario = ?");
$stmt->execute([$id]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    die("Usuario no encontrado.");
}

// Obtener roles
$roles = $pdo->query("SELECT id_rol, nombre FROM rol")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = trim($_POST["nombre"]);
    $apellido = trim($_POST["apellido"]);
    $nickname = trim($_POST["nickname"]);
    $email = trim($_POST["email"]);
    $rol_id = intval($_POST["rol_id"]);

    try {
        $sql = "UPDATE usuario SET nombre=?, apellido=?, nickname=?, email=?, rol_id=? WHERE id_usuario=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nombre, $apellido, $nickname, $email, $rol_id, $id]);
        echo "✅ Usuario actualizado.";
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            echo "⚠️ Error: email o nickname ya están en uso.";
        } else {
            echo "❌ Error al actualizar: " . $e->getMessage();
        }
    }
}
?>