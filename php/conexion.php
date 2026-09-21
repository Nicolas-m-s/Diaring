<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conexion = new mysqli("localhost", "root", "", "diaring", 3307);
    $conexion->set_charset("utf8mb4");
} catch (mysqli_sql_exception $error) {
    http_response_code(500);
    exit("No se pudo conectar con la base de datos.");
}