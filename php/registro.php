<?php
session_start();
require_once "conexion.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Validar que lleguen todos los campos
    if (empty($_POST['nombre']) || empty($_POST['correo']) || empty($_POST['password'])) {
        header("Location: registro.php?error=vacio");
        exit();
    }

    $nombre = trim($_POST['nombre'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validar formato de correo
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        header("Location: registro.php?error=correo");
        exit();
    }

    // Validar longitud mínima de contraseña
    if (strlen($password) < 8) {
        header("Location: registro.php?error=password_corta");
        exit();
    }

    // Verifica que el correo no exista ya
    $check = $conexion->prepare("SELECT id FROM usuarios WHERE correo = ? LIMIT 1");
    $check->bind_param("s", $correo);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        header("Location: registro.php?error=existe");
        exit();
    }
    $check->close();

    // Hashea la contraseña antes de guardarla
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $foto = "IMG/default.png";
    $rol = "usuario"; // OJO: en tu tabla el ENUM es 'usuario', no 'usuarios'

    $sql = "INSERT INTO usuarios (nombre, correo, contrasena, foto, rol, fecha_registro) 
            VALUES (?, ?, ?, ?, ?, NOW())";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("sssss", $nombre, $correo, $hash, $foto, $rol);

    if ($stmt->execute()) {
        session_regenerate_id(true);

        $_SESSION['user_id'] = $stmt->insert_id;
        $_SESSION['id_usuario'] = $stmt->insert_id;
        $_SESSION['user_nombre'] = $nombre;
        $_SESSION['user_rol'] = $rol;
        $_SESSION['user_foto'] = $foto;

        $stmt->close();
        header("Location: perfil.php");
        exit();
    } else {
        $stmt->close();
        header("Location: registro.php?error=1");
        exit();
    }
}
?>