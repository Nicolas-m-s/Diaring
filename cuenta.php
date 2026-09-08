<?php
session_start();
require "conexion.php";

if (!isset($_SESSION['usuario_id'])) {
    header('Location: iniciar_sesion.html');
    exit;
}

$stmt = mysqli_prepare($conexion, 'SELECT nombre, correo, fecha_registro FROM usuario WHERE id = ? LIMIT 1');
mysqli_stmt_bind_param($stmt, 'i', $_SESSION['usuario_id']);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
$usuario = mysqli_fetch_assoc($resultado);

if (!$usuario) {
    session_destroy();
    header('Location: iniciar_sesion.html');
    exit;
}

$nombre = htmlspecialchars($usuario['nombre'], ENT_QUOTES, 'UTF-8');
$correo = htmlspecialchars($usuario['correo'], ENT_QUOTES, 'UTF-8');
$fechaRegistro = date('d/m/Y', strtotime($usuario['fecha_registro']));

$progreso = mysqli_prepare($conexion, 'SELECT c.nombre, c.imagen, c.duracion, uc.horas_acumuladas FROM usuario_curso uc INNER JOIN curso c ON c.id = uc.curso_id WHERE uc.usuario_id = ? ORDER BY uc.ultimo_acceso DESC');
mysqli_stmt_bind_param($progreso, 'i', $_SESSION['usuario_id']);
mysqli_stmt_execute($progreso);
$resultadoCursos = mysqli_stmt_get_result($progreso);
$cursos = mysqli_fetch_all($resultadoCursos, MYSQLI_ASSOC);
mysqli_stmt_close($progreso);

$cursosRealizados = count($cursos);
$horasAprendidas = 0;
$tarjetasCursos = '';

foreach ($cursos as $curso) {
    $nombreCurso = htmlspecialchars($curso['nombre'], ENT_QUOTES, 'UTF-8');
    $imagenCurso = htmlspecialchars($curso['imagen'], ENT_QUOTES, 'UTF-8');
    $horasCurso = (int) $curso['horas_acumuladas'];
    $horasAprendidas += $horasCurso;
    $tarjetasCursos .= "\n    <div class=\"curso-card\">\n        <img src=\"$imagenCurso\" alt=\"$nombreCurso\">\n        <span class=\"estado\">En progreso</span>\n        <h3>$nombreCurso</h3>\n        <div class=\"estrellas\">★★★★★ <small>(5.0)</small></div>\n        <p>⏱ $horasCurso Horas</p>\n    </div>\n";
}

if ($tarjetasCursos === '') {
    $tarjetasCursos = '<p class="sin-cursos">Aún no has entrado a ningún curso.</p>';
}

ob_start();
include __DIR__ . '/cuenta.html';
$pagina = ob_get_clean();

$pagina = str_replace(
    ['<p id="nombreusuario">marin</p>', '<p id="subnombre">@usuario</p>', '<p>marin@gmail.com</p>', 'Miembro desde septiembre 2026', 'Cursos guardados: 0', '<strong id="horas-aprendidas">0 H</strong>', '<!-- CURSOS_USUARIO -->'],
    ["<p id=\"nombreusuario\">$nombre</p>", "<p id=\"subnombre\">@$nombre</p>", "<p>$correo</p>", "Miembro desde $fechaRegistro", "Cursos guardados: $cursosRealizados", "<strong id=\"horas-aprendidas\">$horasAprendidas H</strong>", $tarjetasCursos],
    $pagina
);

echo $pagina;
?>
