<?php
$host = "localhost";
$usuario = "root";
$password = "";
$basedatos = "diaring";
$puerto = 3307;

$conexion = new mysqli($host, $usuario, $password, $basedatos, $puerto);

if ($conexion->connect_error) {
    die("Error: " . $conexion->connect_error);
}
?>