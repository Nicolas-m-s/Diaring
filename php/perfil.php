<?php
session_start();
require_once __DIR__ . '/conexion.php';

if (empty($_SESSION['user_id']) && empty($_SESSION['id_usuario'])) {
	header('Location: login.php');
	exit;
}

$usuarioId = (int) ($_SESSION['user_id'] ?? $_SESSION['id_usuario']);
$stmt = $conexion->prepare('SELECT id, nombre, correo, foto, rol, fecha_registro FROM usuarios WHERE id = ?');
$stmt->bind_param('i', $usuarioId);
$stmt->execute();
$usuario = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$usuario) {
	session_destroy();
	header('Location: login.php');
	exit;
}

$stmt = $conexion->prepare('SELECT c.id, c.titulo, c.imagen, c.estado, c.fecha_creacion, i.nombre AS institucion
	FROM cursos c
	JOIN instituciones i ON i.id = c.id_institucion
	WHERE c.id_usuario_creador = ?
	ORDER BY c.fecha_creacion DESC');
$stmt->bind_param('i', $usuarioId);
$stmt->execute();
$cursos = $stmt->get_result();
$stmt->close();

$stmt = $conexion->prepare('SELECT cert.id, cert.archivo_certificado, cert.estado, c.titulo, c.id AS curso_id
	FROM certificaciones cert
	JOIN cursos c ON c.id = cert.id_curso
	WHERE cert.id_usuario = ?
	ORDER BY cert.id DESC');
$stmt->bind_param('i', $usuarioId);
$stmt->execute();
$certificaciones = $stmt->get_result();
$stmt->close();

$stmt = $conexion->prepare('SELECT s.id, s.modalidad, s.precio, s.requiere_videollamada, s.descripcion, s.activo,
	c.titulo, cert.estado AS certificacion_estado
	FROM servicios_acompanamiento s
	JOIN cursos c ON c.id = s.id_curso
	JOIN certificaciones cert ON cert.id = s.id_certificacion
	WHERE s.id_usuario = ?
	ORDER BY s.id DESC');
$stmt->bind_param('i', $usuarioId);
$stmt->execute();
$servicios = $stmt->get_result();
$stmt->close();

$foto = '../assets/IMG/ti.png';
if (!empty($usuario['foto'])) {
	$fotoRelativa = ltrim($usuario['foto'], '/');
	if (is_file(__DIR__ . '/../' . $fotoRelativa)) {
		$foto = '../' . $fotoRelativa;
	}
}
function e(string $value): string
{
	return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Mi perfil | Diaring</title>
	<link rel="stylesheet" href="../CSS/styles.css">
</head>
<body>
	<header>
		<div class="logo-area"><h1>Diaring</h1></div>
		<nav>
			<ul>
				<li><a href="index.php">Inicio</a></li>
				<li><a href="catalogo.php">Catálogo</a></li>
				<li><a href="ensena.php">Enseña en Diaring</a></li>
				<li><a href="perfil.php" class="active">Perfil</a></li>
				<li><a href="logout.php" class="nav-login">Cerrar sesión</a></li>
			</ul>
		</nav>
	</header>

	<main>
		<section class="progress-page-header">
			<div class="progress-header-content">
				<span>Tu camino de aprendizaje</span>
				<h1>Mi progreso</h1>
				<p>Consulta tu actividad, tus cursos sugeridos y el estado de tus certificaciones.</p>
			</div>
		</section>

		<section class="profile-page-header">
			<h1>Mi Perfil</h1>
			<p>Gestiona tu aprendizaje y descubre cuánto has avanzado.</p>
		</section>

		<section class="profile-info-card">
			<div class="profile-avatar"><img src="../assets/IMG/ti.png" alt="Foto de <?= e($usuario['nombre']) ?>"></div>
			<div class="profile-details">
				<h2><?= e($usuario['nombre']) ?></h2>
				<p><?= e($usuario['correo']) ?></p>
				<p>Miembro desde <?= e(date('d/m/Y', strtotime($usuario['fecha_registro']))) ?></p>
				<p>Cursos sugeridos: <?= $cursos->num_rows ?></p>
			</div>
		</section>

		<section class="profile-options-section">
			<div class="section-divider-title">Opciones de Perfil</div>
			<div class="profile-options-grid">
				<a href="crear_curso.php" class="profile-option-card"><div class="option-left"><div class="option-info"><h3>Sugerir curso</h3><p>Comparte un curso nuevo</p></div></div><div class="option-arrow">&gt;</div></a>
				<a href="logout.php" class="profile-option-card"><div class="option-left"><div class="option-info"><h3>Cerrar sesión</h3><p>Salir de tu cuenta</p></div></div><div class="option-arrow">&gt;</div></a>
				<?php if ($usuario['rol'] === 'admin'): ?>
				<a href="admin_curso.php" class="profile-option-card"><div class="option-left"><div class="option-info"><h3>Administración</h3><p>Cursos y certificaciones</p></div></div><div class="option-arrow">&gt;</div></a>
				<?php else: ?>
				<a href="#certificaciones" class="profile-option-card"><div class="option-left"></div><div class="option-info"><h3>Certificaciones</h3><p><?= $certificaciones->num_rows ?> enviadas</p></div></div><div class="option-arrow">&gt;</div></a>
				<?php endif; ?>
			</div>
		</section>

		<section class="courses-progress-section">
			<div class="section-title-action"><h2>Tus cursos sugeridos</h2><a href="catalogo.php">Ver catálogo →</a></div>
			<div class="progress-courses-grid">
				<?php if ($cursos->num_rows === 0): ?>
					<p>Aún no has sugerido cursos.</p>
				<?php else: ?>
					<?php while ($curso = $cursos->fetch_assoc()): ?>
						<article class="progress-course-card">
							<img src="<?= e($curso['imagen'] ? '../uploads/cursos/' . $curso['imagen'] : '../assets/IMG/inicio.jpg') ?>" alt="<?= e($curso['titulo']) ?>">
							<div class="progress-course-info"><h3><?= e($curso['titulo']) ?></h3><div class="progress-sub"><?= e($curso['institucion']) ?> · <?= e(ucfirst($curso['estado'])) ?></div><a class="btn-continue" href="curso_detalle.php?id=<?= (int) $curso['id'] ?>">Ver curso →</a></div>
						</article>
					<?php endwhile; ?>
				<?php endif; ?>
			</div>
		</section>

		<section id="certificaciones" class="profile-options-section">
			<div class="section-divider-title">Mis certificaciones (<?= $certificaciones->num_rows ?>)</div>
			<div class="profile-list">
				<?php if ($certificaciones->num_rows === 0): ?><p>No tienes certificaciones enviadas.</p><?php else: ?>
					<?php while ($cert = $certificaciones->fetch_assoc()): ?><div class="profile-list-item"><strong><?= e($cert['titulo']) ?></strong><span class="status status-<?= e($cert['estado']) ?>"><?= e(ucfirst($cert['estado'])) ?></span></div><?php endwhile; ?>
				<?php endif; ?>
			</div>
		</section>

		<section id="servicios" class="availability-section"><div class="availability-info"><h2>Mis <span>acompañamientos</span></h2><p>Consulta el estado de los servicios que has ofrecido a la comunidad.</p><?php if ($servicios->num_rows === 0): ?><p>Aún no tienes servicios publicados.</p><?php else: ?><div class="profile-list profile-list-dark"><?php while ($servicio = $servicios->fetch_assoc()): ?><div class="profile-list-item"><strong><?= e($servicio['titulo']) ?></strong><span class="status"><?= $servicio['activo'] && $servicio['certificacion_estado'] === 'aprobado' ? 'Activo' : 'En revisión' ?></span></div><?php endwhile; ?></div><?php endif; ?></div></section>
	</main>
</body>
</html>
