<?php
require "conexion.php";

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id || $id < 1) {
    http_response_code(404);
    exit('Curso no encontrado.');
}

$stmt = $conexion->prepare("SELECT c.*, i.nombre AS nombre_institucion
    FROM cursos c
    JOIN instituciones i ON c.id_institucion = i.id
    WHERE c.id = ? AND c.estado = 'aprobado'");
$stmt->bind_param('i', $id);
$stmt->execute();
$curso = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$curso) {
    http_response_code(404);
    exit('Curso no encontrado.');
}

$stmt = $conexion->prepare("SELECT s.*, u.nombre AS nombre_acompanante
    FROM servicios_acompanamiento s
    JOIN usuarios u ON s.id_usuario = u.id
    WHERE s.id_curso = ? AND s.activo = 1");
$stmt->bind_param('i', $id);
$stmt->execute();
$acompanantes = $stmt->get_result();
$stmt->close();
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
        <img src="../uploads/cursos/<?= htmlspecialchars($curso['imagen'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($curso['titulo'], ENT_QUOTES, 'UTF-8') ?>">

        <h1><?= htmlspecialchars($curso['titulo'], ENT_QUOTES, 'UTF-8') ?></h1>
        <p><strong>Institución:</strong> <?= htmlspecialchars($curso['nombre_institucion'], ENT_QUOTES, 'UTF-8') ?></p>
        <p><strong>Duración:</strong> <?= $curso['duracion_horas'] ?> horas</p>
        <p><strong>Área:</strong> <?= htmlspecialchars($curso['area'], ENT_QUOTES, 'UTF-8') ?></p>
        <p><strong>Certificado:</strong> <?= $curso['certificado_gratis'] ? 'Gratis' : 'Con costo' ?></p>

        <h2>Sobre este curso</h2>
        <p><?= htmlspecialchars($curso['descripcion'], ENT_QUOTES, 'UTF-8') ?></p>

        <a href="<?= htmlspecialchars($curso['link_original'], ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer" class="btn-primary">Ir al curso</a>

        <h2>Acompañantes disponibles</h2>
        <?php if ($acompanantes->num_rows > 0): ?>
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