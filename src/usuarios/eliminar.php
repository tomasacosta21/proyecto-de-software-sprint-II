<?php 
require_once("../database.php");
$pdo = getConnection();

$id = intval($_GET["id"]);

try {
    $stmt = $pdo->prepare("DELETE FROM usuario WHERE id_usuario = ?");
    $stmt->execute([$id]);
    echo "✅ Usuario eliminado.";
} catch (PDOException $e) {
    echo "❌ Error al eliminar: " . $e->getMessage();
}
?>