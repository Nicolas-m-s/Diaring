<?php
$host = "localhost";
$usuario = "root";
$password = "";
$basedatos = "cursos";
$puerto = "3307";

$conexion = mysqli_connect($host, $usuario, $password, $basedatos, $puerto);

if ($conexion->connect_error) {
    die("Error". $conexion->connect_error);
}
?>