





<?php
session_start();
require_once "conexion.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = trim($_POST['nombre']);
    $correo = trim($_POST['correo']);
    $password = $_POST['password'];

    // Verifica que el correo no exista ya
    $check = $conn->prepare("SELECT id FROM usuarios WHERE correo = :correo LIMIT 1");
    $check->bindParam(':correo', $correo);
    $check->execute();

    if ($check->rowCount() > 0) {
        header("Location: registro.html?error=existe");
        exit();
    }

    // Hashea la contraseña antes de guardarla
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $foto = "IMG/default.png"; // foto por defecto, ajusta si tienes otra
    $rol = "usuarios"; // valor por defecto según tu ENUM

    $sql = "INSERT INTO usuarios (nombre, correo, contrasena, foto, rol, fecha_registro) 
            VALUES (:nombre, :correo, :contrasena, :foto, :rol, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':correo', $correo);
    $stmt->bindParam(':contrasena', $hash);
    $stmt->bindParam(':foto', $foto);
    $stmt->bindParam(':rol', $rol);

    try {
        $stmt->execute();
        $_SESSION['user_id'] = $conn->lastInsertId();
        $_SESSION['user_nombre'] = $nombre;
        header("Location: dashboard.html");
        exit();
    } catch (PDOException $e) {
        header("Location: registro.html?error=1");
        exit();
    }
}
?>