<?php 
require_once("../database.php");
$pdo = getConnection();

$roles = $pdo->query("SELECT id_rol, nombre FROM rol")->fetchAll(PDO::FETCH_ASSOC);

// Si se solicita formato JSON para roles
if (isset($_GET['format']) && $_GET['format'] === 'json') {
    header('Content-Type: application/json');
    echo json_encode($roles);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = trim($_POST["nombre"]);
    $apellido = trim($_POST["apellido"]);
    $nickname = trim($_POST["nickname"]);
    $email = trim($_POST["email"]);
    $rol_id = intval($_POST["rol_id"]);

    try {
        $sql = "INSERT INTO usuario (nombre, apellido, nickname, email, rol_id) VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nombre, $apellido, $nickname, $email, $rol_id]);
        echo "✅ Usuario creado con éxito.";
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            echo "Error: el email o nickname ya están en uso.";
        } else {
            echo "Error al insertar: " . $e->getMessage();
        }
    }
}
?>