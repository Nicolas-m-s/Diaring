<?php
require "conexion.php";

$id = intval($_GET['id']);

$curso = mysqli_query($conexion, "
    SELECT c.*, i.nombre AS nombre_institucion 
    FROM cursos c
    JOIN instituciones i ON c.id_institucion = i.id
    WHERE c.id = $id
");
$curso = mysqli_fetch_assoc($curso);

$acompanantes = mysqli_query($conexion, "
    SELECT s.*, u.nombre AS nombre_acompanante
    FROM servicios_acompanamiento s
    JOIN usuarios u ON s.id_usuario = u.id
    WHERE s.id_curso = $id AND s.activo = 1
");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Diaring - <?= htmlspecialchars($curso['titulo']) ?></title>
    <link rel="stylesheet" href="CSS/styles.css">
</head>
<body>
    <header>
        <div class="logo-area"><h1>Diaring</h1></div>
        <nav>
            <ul>
                <li><a href="index.php">Inicio</a></li>
                <li><a href="catalogo.php" class="active">Catalogo</a></li>
                <li><a href="recursos.php">Recursos</a></li>
                <li><a href="ensena.php">Enseña en diaring</a></li>
                <li><a href="perfil.php">Perfil</a></li>
                <li><a href="login.php" class="nav-login">Iniciar Sesión</a></li>
                <li><a href="registro.php" class="nav-register">Registrarse</a></li>
            </ul>
        </nav>
    </header>

    <main class="course-detail">
        <img src="uploads/cursos/<?= htmlspecialchars($curso['imagen']) ?>" alt="<?= htmlspecialchars($curso['titulo']) ?>">

        <h1><?= htmlspecialchars($curso['titulo']) ?></h1>
        <p><strong>Institución:</strong> <?= htmlspecialchars($curso['nombre_institucion']) ?></p>
        <p><strong>Duración:</strong> <?= $curso['duracion_horas'] ?> horas</p>
        <p><strong>Área:</strong> <?= htmlspecialchars($curso['area']) ?></p>
        <p><strong>Certificado:</strong> <?= $curso['certificado_gratis'] ? 'Gratis' : 'Con costo' ?></p>

        <h2>Sobre este curso</h2>
        <p><?= htmlspecialchars($curso['descripcion']) ?></p>

        <a href="<?= htmlspecialchars($curso['link_original']) ?>" target="_blank" class="btn-primary">Ir al curso</a>

        <h2>Acompañantes disponibles</h2>
        <?php if (mysqli_num_rows($acompanantes) > 0): ?>
            <div class="acompanantes-grid">
                <?php while ($a = mysqli_fetch_assoc($acompanantes)): ?>
                    <div class="acompanante-card">
                        <h3><?= htmlspecialchars($a['nombre_acompanante']) ?></h3>
                        <p>Modalidad: <?= $a['modalidad'] == 'basica' ? 'Básica' : 'Completado' ?></p>
                        <p>Precio: $<?= number_format($a['precio'], 0) ?></p>
                        <?php if ($a['requiere_videollamada']): ?>
                            <p>⚠️ Este servicio requiere videollamadas</p>
                        <?php endif; ?>
                        <a href="contratar.php?id_servicio=<?= $a['id'] ?>" class="btn-primary">Contratar</a>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p>Aún no hay acompañantes disponibles para este curso.</p>
        <?php endif; ?>
    </main>

    <footer>
        <div class="footer-container">
            <div class="footer-col">
                <h3>Diaring</h3>
                <p>Creado por estudiantes, para estudiantes.</p>
            </div>
        </div>
        <div class="footer-bottom">
            <span>© 2026 Diaring. Todos los derechos reservados.</span>
        </div>
    </footer>
</body>
</html>