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

$foto = '../assets/IMG/ini.jpg';
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

	<main class="profile-container">
		<div class="profile-banner"></div>
		<section class="profile-header-row">
			<div class="profile-user-info">
				<img class="profile-avatar" src="<?= e($foto) ?>" alt="Foto de <?= e($usuario['nombre']) ?>">
				<div>
					<h2><?= e($usuario['nombre']) ?></h2>
					<p><?= e($usuario['correo']) ?></p>
				</div>
			</div>
			<a href="crear_curso.php" class="btn-primary">Sugerir curso</a>
		</section>

		<ul class="profile-tabs">
			<li><a href="#actividad" class="active">Mi actividad</a></li>
			<li><a href="#servicios">Acompañamientos</a></li>
			<?php if ($usuario['rol'] === 'admin'): ?>
				<li><a href="admin_curso.php">Administración</a></li>
			<?php endif; ?>
		</ul>

		<section id="actividad" class="profile-layout">
			<aside class="profile-sidebar">
				<p><strong>Miembro desde</strong><br><?= e(date('d/m/Y', strtotime($usuario['fecha_registro']))) ?></p>
				<p><strong>Certificaciones</strong><br><?= $certificaciones->num_rows ?></p>
				<p><strong>Cursos sugeridos</strong><br><?= $cursos->num_rows ?></p>
			</aside>
			<div class="profile-main-content">
				<h2 class="catalog-title">Cursos <span>sugeridos</span></h2>
				<div class="content-grid">
					<?php if ($cursos->num_rows === 0): ?>
						<p>Aún no has sugerido cursos.</p>
					<?php else: ?>
						<?php while ($curso = $cursos->fetch_assoc()): ?>
							<article class="card">
								<div class="card-body">
									<h4><?= e($curso['titulo']) ?></h4>
									<span><?= e($curso['institucion']) ?></span>
									<p>Estado: <strong><?= e(ucfirst($curso['estado'])) ?></strong></p>
								</div>
							</article>
						<?php endwhile; ?>
					<?php endif; ?>
				</div>

				<h2 id="certificaciones" class="catalog-title profile-section-title">Mis <span>certificaciones</span></h2>
				<div class="profile-list">
					<?php if ($certificaciones->num_rows === 0): ?>
						<p>No tienes certificaciones enviadas.</p>
					<?php else: ?>
						<?php while ($cert = $certificaciones->fetch_assoc()): ?>
							<div class="profile-list-item">
								<div><strong><?= e($cert['titulo']) ?></strong><br><small><?= e($cert['archivo_certificado']) ?></small></div>
								<span class="status status-<?= e($cert['estado']) ?>"><?= e(ucfirst($cert['estado'])) ?></span>
							</div>
						<?php endwhile; ?>
					<?php endif; ?>
				</div>
			</div>
		</section>

		<section id="servicios" class="availability-section">
			<div class="availability-info">
				<h2>Mis <span>acompañamientos</span></h2>
				<p>Consulta el estado de los servicios que has ofrecido a la comunidad.</p>
				<?php if ($servicios->num_rows === 0): ?>
					<p>Aún no tienes servicios publicados.</p>
				<?php else: ?>
					<div class="profile-list profile-list-dark">
						<?php while ($servicio = $servicios->fetch_assoc()): ?>
							<div class="profile-list-item">
								<div><strong><?= e($servicio['titulo']) ?></strong><br><?= e(ucfirst($servicio['modalidad'])) ?> · $<?= number_format((float) $servicio['precio'], 2) ?></div>
								<span class="status"><?= $servicio['activo'] && $servicio['certificacion_estado'] === 'aprobado' ? 'Activo' : 'En revisión' ?></span>
							</div>
						<?php endwhile; ?>
					</div>
				<?php endif; ?>
			</div>
		</section>
	</main>
</body>
</html>
