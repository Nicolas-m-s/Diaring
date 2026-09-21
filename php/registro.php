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
    $foto = "assets/IMG/ini.jpg";
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
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diaring - Registrarse</title>
    <link rel="stylesheet" href="../CSS/styles.css">
</head>
<body>
    <header>
        <div class="logo-area"><h1>Diaring</h1></div>
        <nav>
            <ul>
                <li><a href="index.php">Inicio</a></li>
                <li><a href="catalogo.php">Catálogo</a></li>
                <li><a href="recursos.php">Recursos</a></li>
                <li><a href="ensena.php">Enseña en Diaring</a></li>
                <li><a href="login.php" class="nav-login">Iniciar sesión</a></li>
            </ul>
        </nav>
    </header>

    <main class="auth-section">
        <div class="auth-container">
            <div class="auth-form-wrapper">
                <h2>Certifícate. <span>sin excusas</span></h2>
                <p class="auth-subtitle">Nuestros cursos te esperan.</p>
                <?php
                $mensajes = [
                    'vacio' => 'Completa todos los campos.',
                    'correo' => 'Escribe un correo válido.',
                    'password_corta' => 'La contraseña debe tener al menos 8 caracteres.',
                    'existe' => 'Ese correo ya está registrado.',
                    '1' => 'No se pudo completar el registro.',
                ];
                $codigoError = $_GET['error'] ?? '';
                if (isset($mensajes[$codigoError])):
                ?>
                    <p style="color: #b91c1c;" role="alert"><?= htmlspecialchars($mensajes[$codigoError]) ?></p>
                <?php endif; ?>
                <form action="registro.php" method="post">
                    <div class="form-group">
                        <label for="correo">Correo electrónico</label>
                        <input id="correo" type="email" name="correo" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Contraseña</label>
                        <input id="password" type="password" name="password" minlength="8" required>
                    </div>
                    <div class="form-group">
                        <label for="nombre">Nombre de usuario</label>
                        <input id="nombre" type="text" name="nombre" required>
                    </div>
                    <button type="submit" class="btn-auth">Registrarse</button>
                </form>
                <div class="auth-link">¿Ya te has registrado? <a href="login.php">Inicia sesión aquí</a></div>
            </div>
            <div class="auth-image-wrapper">
                <img src="../assets/IMG/ini.jpg" alt="Graduados celebrando">
            </div>
        </div>
    </main>
</body>
</html>