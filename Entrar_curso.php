<?php
session_start();
require "conexion.php";

$cursoId = filter_input(INPUT_GET, 'curso_id', FILTER_VALIDATE_INT);

if (!$cursoId) {
    exit('Curso no válido.');
}

if (!isset($_SESSION['usuario_id'])) {
    header('Location: iniciar_sesion.html');
    exit;
}

$stmt = mysqli_prepare($conexion, 'SELECT id, link, duracion FROM curso WHERE id = ? LIMIT 1');
mysqli_stmt_bind_param($stmt, 'i', $cursoId);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
$curso = mysqli_fetch_assoc($resultado);
mysqli_stmt_close($stmt);

if (!$curso) {
    mysqli_close($conexion);
    exit('Curso no encontrado.');
}

$usuarioId = (int) $_SESSION['usuario_id'];
$duracion = (int) $curso['duracion'];

$sql = 'INSERT INTO usuario_curso (usuario_id, curso_id, accesos, horas_acumuladas, ultimo_acceso)
        VALUES (?, ?, 1, ?, NOW())
        ON DUPLICATE KEY UPDATE
        accesos = accesos + 1,
        horas_acumuladas = horas_acumuladas + VALUES(horas_acumuladas),
        ultimo_acceso = NOW()';

$registro = mysqli_prepare($conexion, $sql);
mysqli_stmt_bind_param($registro, 'iii', $usuarioId, $cursoId, $duracion);
mysqli_stmt_execute($registro);
mysqli_stmt_close($registro);
mysqli_close($conexion);

header('Location: ' . $curso['link']);
exit;
?>
