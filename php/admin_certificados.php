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
	$certificacionId = filter_input(INPUT_POST, 'certificacion_id', FILTER_VALIDATE_INT);
	$estado = $_POST['estado'] ?? '';
	if ($certificacionId && in_array($estado, ['aprobado', 'rechazado'], true)) {
		$conexion->begin_transaction();
		try {
			$stmt = $conexion->prepare('UPDATE certificaciones SET estado = ? WHERE id = ? AND estado = "pendiente"');
			$stmt->bind_param('si', $estado, $certificacionId);
			$stmt->execute();
			$stmt->close();
			if ($estado === 'aprobado') {
				$stmt = $conexion->prepare('UPDATE servicios_acompanamiento SET activo = 1 WHERE id_certificacion = ?');
				$stmt->bind_param('i', $certificacionId);
				$stmt->execute();
				$stmt->close();
			}
			$conexion->commit();
		} catch (Throwable $error) {
			$conexion->rollback();
		}
	}
	header('Location: admin_certificados.php');
	exit;
}

$certificaciones = $conexion->query('SELECT cert.id, cert.archivo_certificado, cert.estado, c.titulo,
	u.nombre AS usuario, u.correo, s.modalidad, s.precio, s.id AS servicio_id
	FROM certificaciones cert
	JOIN usuarios u ON u.id = cert.id_usuario
	JOIN cursos c ON c.id = cert.id_curso
	LEFT JOIN servicios_acompanamiento s ON s.id_certificacion = cert.id
	WHERE cert.estado = "pendiente"
	ORDER BY cert.id ASC');

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
	<title>Revisión de certificaciones | Diaring</title>
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
	<main class="admin-container">
		<div class="admin-heading"><div><p class="eyebrow">Panel de administración</p><h2>Validar <span>certificaciones</span></h2></div><a href="perfil.php" class="btn-secondary">Volver al perfil</a></div>
		<p class="admin-intro">Verifica que el certificado corresponda al curso antes de activar el servicio de acompañamiento.</p>
		<section class="admin-table-wrap">
			<?php if ($certificaciones->num_rows === 0): ?>
				<div class="empty-state"><h3>No hay certificaciones pendientes</h3><p>Todo está al día por ahora.</p></div>
			<?php else: ?>
				<div class="admin-table-scroll"><table class="admin-table"><thead><tr><th>Usuario</th><th>Curso</th><th>Certificado</th><th>Servicio</th><th>Acciones</th></tr></thead><tbody>
				<?php while ($cert = $certificaciones->fetch_assoc()): ?>
					<tr><td><strong><?= e($cert['usuario']) ?></strong><small><?= e($cert['correo']) ?></small></td><td><?= e($cert['titulo']) ?></td><td><a href="../uploads/certificados/<?= e($cert['archivo_certificado']) ?>" target="_blank" rel="noopener noreferrer">Abrir archivo</a></td><td><?= $cert['servicio_id'] ? e(ucfirst($cert['modalidad'])) . ' · $' . number_format((float) $cert['precio'], 2) : 'Sin servicio' ?></td><td><form method="post" class="admin-actions"><input type="hidden" name="certificacion_id" value="<?= (int) $cert['id'] ?>"><button class="btn-primary" name="estado" value="aprobado">Aprobar</button><button class="btn-danger" name="estado" value="rechazado">Rechazar</button></form></td></tr>
				<?php endwhile; ?>
				</tbody></table></div>
			<?php endif; ?>
		</section>
	</main>
</body>
</html>
