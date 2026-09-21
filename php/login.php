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
        header("Location: perfil.php");
        exit();
    } else {
        header("Location: login.php?error=1");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diaring - Iniciar sesión</title>
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
                <li><a href="registro.php" class="nav-register">Registrarse</a></li>
            </ul>
        </nav>
    </header>

    <main class="auth-section">
        <div class="auth-container">
            <div class="auth-form-wrapper">
                <h2>Certifícate. <span>sin excusas</span></h2>
                <p class="auth-subtitle">Continúa donde lo dejaste</p>
                <?php if (isset($_GET['error'])): ?>
                    <p style="color: #b91c1c;" role="alert">El correo o la contraseña no son correctos.</p>
                <?php endif; ?>
                <form action="login.php" method="post">
                    <div class="form-group">
                        <label for="usuario">Correo electrónico o nombre de usuario</label>
                        <input id="usuario" type="text" name="usuario" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Contraseña</label>
                        <input id="password" type="password" name="password" required>
                    </div>
                    <button type="submit" class="btn-auth">Iniciar sesión</button>
                </form>
                <div class="auth-link">
                    ¿No te has registrado? <a href="registro.php">Regístrate aquí</a>
                </div>
            </div>
            <div class="auth-image-wrapper">
                <img src="../assets/IMG/ini.jpg" alt="Graduados celebrando">
            </div>
        </div>
    </main>
</body>
</html>