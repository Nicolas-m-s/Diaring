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
    <link rel="stylesheet" href="../CSS/styles.css">
</head>
<body>
<header>
    <div class="logo-area">
        <a href="index.php" class="logo-link">
            <img src="../assets/IMG/Diaring_logo.png" alt="Logo de Diaring" class="logo">

        </a>
    </div>
        <nav class="main_nav">
            <ul>
                <li><a href="index.php" class="active">Inicio</a></li>
                <li><a href="catalogo.php">Catalogo</a></li>
                <li><a href="recursos.php">Recursos</a></li>
                <li><a href="ensena.php">Enseña en diaring</a></li>
                <li><a href="perfil.php">Perfil</a></li>
            </ul>
        </nav>

        <div class="auth-area">
            <a href="login.php" class="nav-login">Iniciar Sesión</a>
            <a href="registro.php" class="nav-register">Registrarse</a>
        </div>
    </header>

    <main class="course-detail">
        <?php
        $imagenCurso = trim((string) $curso['imagen']);
        $imagenUrl = $imagenCurso !== ''
            ? '../uploads/cursos/' . rawurlencode($imagenCurso)
            : '../assets/IMG/inicio.jpg';
        ?>
        <img src="<?= htmlspecialchars($imagenUrl, ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($curso['titulo'], ENT_QUOTES, 'UTF-8') ?>">

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
        <div class="footer-col">
            <h4>Redes</h4>
            <div class="footer-social">
                <a href="https://www.instagram.com/diaringaprendizaje/" aria-label="Instagram">
                    <img src="../assets/IMG/insta.png" alt="Instagram">
                </a>
                <a href="https://x.com/diaring01" aria-label="X">
                    <img src="../assets/IMG/twi-removebg-preview.png" alt="X">
                </a>
                <a href="https://www.tiktok.com/@diaringaprendizaje?lang=es-419" aria-label="TikTok">
                    <img src="../assets/IMG/tiktok.png" alt="TikTok">
                </a>
                <a href="https://mail.google.com/mail/u/0/?tab=rm&ogbl#inbox?compose=..." aria-label="Gmail">
                    <img src="../assets/IMG/gm-removebg-preview.png" alt="Gmail">
                </a>
            </div>
        </div>
        <div class="footer-col">
            <h4>Acerca de Diaring</h4>
            <p>Lo que hay detrás de los cursos</p>
        </div>
        <div class="footer-col">
            <h4>Contáctanos</h4>
            <p>Si necesitas ayuda, comunícate con nosotros y te apoyaremos.</p>
        </div>
    </div>
    <div class="footer-bottom">
        <span>© 2026 Diaring. Todos los derechos reservados.</span>
    </div>
</footer>
</body>
</html>