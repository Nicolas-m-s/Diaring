<?php
require "conexion.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: registrarse.html');
    exit;
}

$nombre = trim($_POST['nombre'] ?? '');
$correo = trim($_POST['correo'] ?? '');
$contrasena = $_POST['contrasena'] ?? '';
$confirmarContrasena = $_POST['confirmar_contrasena'] ?? '';

if ($nombre === '' || !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    exit('Completa un nombre de usuario y un correo válido.');
}

if (strlen($contrasena) < 8) {
    exit('La contraseña debe tener al menos 8 caracteres.');
}

if ($contrasena !== $confirmarContrasena) {
    exit('Las contraseñas no coinciden.');
}

$consulta = mysqli_prepare($conexion, 'SELECT id FROM usuario WHERE correo = ? OR nombre = ? LIMIT 1');
mysqli_stmt_bind_param($consulta, 'ss', $correo, $nombre);
mysqli_stmt_execute($consulta);
mysqli_stmt_store_result($consulta);

if (mysqli_stmt_num_rows($consulta) > 0) {
    mysqli_stmt_close($consulta);
    exit('El correo o nombre de usuario ya está registrado.');
}

mysqli_stmt_close($consulta);

$contrasenaHash = password_hash($contrasena, PASSWORD_DEFAULT);
$stmt = mysqli_prepare($conexion, 'INSERT INTO usuario (nombre, correo, contrasena) VALUES (?, ?, ?)');
mysqli_stmt_bind_param($stmt, 'sss', $nombre, $correo, $contrasenaHash);

if (mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);
    mysqli_close($conexion);
    header('Location: /Diaring/iniciar_sesion.html?registro=exitoso');
    exit;
} else {
    echo 'Error al guardar el registro: ' . mysqli_error($conexion);
}

mysqli_stmt_close($stmt);
mysqli_close($conexion);

?>