<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$host = getenv('DIARING_DB_HOST') ?: '127.0.0.1';
$usuario = getenv('DIARING_DB_USER') ?: 'root';
$password = getenv('DIARING_DB_PASSWORD') ?: '';
$baseDatos = getenv('DIARING_DB_NAME') ?: 'diaring';
$puertoConfigurado = (int) (getenv('DIARING_DB_PORT') ?: 0);
$puertos = $puertoConfigurado > 0 ? [$puertoConfigurado] : [3307, 3306];
$conexion = null;
$ultimoError = null;

foreach ($puertos as $puerto) {
    try {
        $conexion = new mysqli($host, $usuario, $password, $baseDatos, $puerto);
        $conexion->set_charset('utf8mb4');
        break;
    } catch (mysqli_sql_exception $error) {
        $ultimoError = $error;
    }
}

if (!$conexion) {
    http_response_code(500);
    exit('No se pudo conectar con la base de datos. Verifica que MySQL/MariaDB esté iniciado, que exista la base "diaring" y que el puerto sea 3306 o 3307.');
}