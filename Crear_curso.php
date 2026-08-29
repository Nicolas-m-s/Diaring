<?php
require "conexion.php";
$nombre = $_POST['nombre'];
$link = $_POST['link'];
$contrasena = $_POST['contrasena'];
$descripcion = $_POST['descripcion'];
$duracion = $_POST['duracion'];
$Subtema1 = $_POST['Subtema1']??'';
$Subtema2 = $_POST['Subtema2']??'';
$Subtema3 = $_POST['Subtema3']??'';
$ContrasenaHash = password_hash($contrasena, PASSWORD_DEFAULT);
$nombreArchivo = $_FILES['imagen']['name'];
$tmpArchivo = $_FILES['imagen']['tmp_name'];
$nuevoNombre = time() . "_" . $nombreArchivo;
$rutaDestino = "img/imagenes_usuarios/" . $nuevoNombre;
move_uploaded_file($tmpArchivo, $rutaDestino);

$sql = "INSERT INTO curso (nombre, link, contrasena, descripcion, imagen, duracion, Subtema1, Subtema2, Subtema3) 
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = mysqli_prepare($conexion, $sql);
mysqli_stmt_bind_param($stmt, "sssssisss", $nombre, $link, $ContrasenaHash, $descripcion, $rutaDestino, $duracion, $Subtema1, $Subtema2, $Subtema3);

if (mysqli_stmt_execute($stmt)) {
    echo "¡Curso creado exitosamente!";
} else {
    echo "Error al guardar " . mysqli_error($conexion);
}

?>