<?php
session_start();
require "conexion.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: iniciar_sesion.html');
    exit;
}

$identificador = trim($_POST['identificador'] ?? '');
$contrasena = $_POST['contrasena'] ?? '';

if ($identificador === '' || $contrasena === '') {
    exit('Completa el usuario o correo y la contraseña.');
}

$stmt = mysqli_prepare($conexion, 'SELECT id, nombre, correo, contrasena FROM usuario WHERE correo = ? OR nombre = ? LIMIT 1');
mysqli_stmt_bind_param($stmt, 'ss', $identificador, $identificador);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
$usuario = mysqli_fetch_assoc($resultado);

if (!$usuario || !password_verify($contrasena, $usuario['contrasena'])) {
    mysqli_stmt_close($stmt);
    mysqli_close($conexion);
    exit('El usuario, correo o contraseña no son correctos.');
}

$_SESSION['usuario_id'] = $usuario['id'];
$_SESSION['usuario_nombre'] = $usuario['nombre'];
$_SESSION['usuario_correo'] = $usuario['correo'];

mysqli_stmt_close($stmt);
mysqli_close($conexion);

header('Location: /Diaring/cuenta.php');
exit;
?>
