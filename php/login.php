



<?php
session_start();
require_once "conexion.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usuario = trim($_POST['usuario']);
    $password = $_POST['password'];

    $sql = "SELECT id, nombre, correo, contrasena, rol FROM usuarios 
            WHERE correo = :usuario OR nombre = :usuario LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':usuario', $usuario);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['contrasena'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_nombre'] = $user['nombre'];
        $_SESSION['user_rol'] = $user['rol'];
        header("Location: dashboard.html");
        exit();
    } else {
        header("Location: login.html?error=1");
        exit();
    }
}
?>