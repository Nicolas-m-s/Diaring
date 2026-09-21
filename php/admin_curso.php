<?php
session_start();
require_once __DIR__ . '/conexion.php';

if (empty($_SESSION['user_id'])) {
	http_response_code(403);
	exit('No tienes permisos para acceder a esta sección.');
}

$acceso = $conexion->prepare('SELECT rol FROM usuarios WHERE id = ? LIMIT 1');
$acceso->bind_param('i', $_SESSION['user_id']);
$acceso->execute();
$rolActual = $acceso->get_result()->fetch_assoc()['rol'] ?? null;
$acceso->close();

if ($rolActual !== 'admin') {
	http_response_code(403);
	exit('No tienes permisos para acceder a esta sección.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$cursoId = filter_input(INPUT_POST, 'curso_id', FILTER_VALIDATE_INT);
	$estado = $_POST['estado'] ?? '';
	if ($cursoId && in_array($estado, ['aprobado', 'rechazado'], true)) {
		$stmt = $conexion->prepare('UPDATE cursos SET estado = ? WHERE id = ? AND estado = "pendiente"');
		$stmt->bind_param('si', $estado, $cursoId);
		$stmt->execute();
		$stmt->close();
	}
	header('Location: admin_curso.php');
	exit;
}

$cursos = $conexion->query('SELECT c.id, c.titulo, c.descripcion, c.duracion_horas, c.area, c.link_original,
	c.imagen, c.fecha_creacion, i.nombre AS institucion, u.nombre AS creador
	FROM cursos c
	JOIN instituciones i ON i.id = c.id_institucion
	LEFT JOIN usuarios u ON u.id = c.id_usuario_creador
	WHERE c.estado = "pendiente"
	ORDER BY c.fecha_creacion ASC');

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
	<title>Revisión de cursos | Diaring</title>
	<link rel="stylesheet" href="../CSS/styles.css">
</head>
<body>
	<header>
		<div class="logo-area"><h1>Diaring</h1></div>
		<nav><ul>
			<li><a href="index.php">Inicio</a></li>
			<li><a href="catalogo.php">Catálogo</a></li>
			<li><a href="perfil.php">Perfil</a></li>
			<li><a href="admin_certificados.php">Certificaciones</a></li>
			<li><a href="logout.php" class="nav-login">Cerrar sesión</a></li>
		</ul></nav>
	</header>
	<main class="admin-container">
		<div class="admin-heading"><div><p class="eyebrow">Panel de administración</p><h2>Revisar <span>cursos</span></h2></div><a href="perfil.php" class="btn-secondary">Volver al perfil</a></div>
		<p class="admin-intro">Aprueba únicamente cursos de instituciones reconocidas antes de publicarlos en el catálogo.</p>
		<section class="admin-table-wrap">
			<?php if ($cursos->num_rows === 0): ?>
				<div class="empty-state"><h3>No hay cursos pendientes</h3><p>Todo está al día por ahora.</p></div>
			<?php else: ?>
				<div class="admin-table-scroll"><table class="admin-table"><thead><tr><th>Curso</th><th>Institución</th><th>Propuesto por</th><th>Detalles</th><th>Acciones</th></tr></thead><tbody>
				<?php while ($curso = $cursos->fetch_assoc()): ?>
					<tr><td><strong><?= e($curso['titulo']) ?></strong><small><?= e($curso['area']) ?> · <?= (int) $curso['duracion_horas'] ?> horas</small></td><td><?= e($curso['institucion']) ?></td><td><?= e($curso['creador'] ?? 'Equipo Diaring') ?></td><td><a href="<?= e($curso['link_original']) ?>" target="_blank" rel="noopener noreferrer">Ver curso</a><small><?= e($curso['descripcion']) ?></small></td><td><div class="admin-actions"><form method="post"><input type="hidden" name="curso_id" value="<?= (int) $curso['id'] ?>"><button class="btn-primary" name="estado" value="aprobado">Aprobar</button><button class="btn-danger" name="estado" value="rechazado">Rechazar</button></form></div></td></tr>
				<?php endwhile; ?>
				</tbody></table></div>
			<?php endif; ?>
		</section>
	</main>
</body>
</html>
