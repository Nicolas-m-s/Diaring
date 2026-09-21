<?php
session_start();
require_once "conexion.php";
 
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usuario = trim($_POST['usuario'] ?? '');
    $password = $_POST['password'] ?? '';
    if ($usuario === '' || $password === '') {
        header("Location: login.php?error=1");
        exit();
    }
 
    $sql = "SELECT id, nombre, correo, contrasena, rol FROM usuarios 
            WHERE correo = ? OR nombre = ? LIMIT 1";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ss", $usuario, $usuario);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $user = $resultado->fetch_assoc();
 
    if ($user && password_verify($password, $user['contrasena'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['id_usuario'] = $user['id'];
        $_SESSION['user_nombre'] = $user['nombre'];
        $_SESSION['user_rol'] = $user['rol'];
        header("Location: index.php");
        exit();
    } else {
        header("Location: login.php?error=1");
        exit();
    }
}
?>