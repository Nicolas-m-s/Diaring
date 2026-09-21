<?php
// contratar.php
// Un cliente contrata un servicio de acompañamiento.
// Crea la fila en `contrataciones` y calcula la comisión de la plataforma.

session_start();
require_once __DIR__ . '/conexion.php'; // Debe definir $pdo (PDO con ERRMODE_EXCEPTION)

const COMISION_PORCENTAJE = 10; // AJUSTAR: % que se queda la plataforma

if (empty($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Método no permitido');
}

$clienteId  = (int) $_SESSION['usuario_id'];
$servicioId = filter_input(INPUT_POST, 'servicio_id', FILTER_VALIDATE_INT);

if (!$servicioId) {
    http_response_code(400);
    exit('Servicio inválido');
}

try {
    // 1. El servicio debe existir y estar aprobado.
    //    El precio SIEMPRE se lee de la BD, nunca del formulario.
    $stmt = $pdo->prepare(
        "SELECT id, usuario_id, precio
           FROM servicios_acompanamiento
          WHERE id = ? AND estado = 'aprobado'"
    );
    $stmt->execute([$servicioId]);
    $servicio = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$servicio) {
        exit('El servicio no existe o aún no está disponible.');
    }

    // 2. No puede contratar su propio servicio
    if ((int) $servicio['usuario_id'] === $clienteId) {
        exit('No puedes contratar tu propio servicio.');
    }

    // 3. Evitar contrataciones duplicadas activas
    $stmt = $pdo->prepare(
        "SELECT COUNT(*) FROM contrataciones
          WHERE servicio_id = ? AND cliente_id = ?
            AND estado IN ('pendiente_pago', 'en_curso')"
    );
    $stmt->execute([$servicioId, $clienteId]);
    if ($stmt->fetchColumn() > 0) {
        exit('Ya tienes una contratación activa de este servicio.');
    }

    // 4. Cálculo de la comisión
    $monto         = (float) $servicio['precio'];
    $comision      = round($monto * COMISION_PORCENTAJE / 100, 2);
    $montoProveedor = round($monto - $comision, 2);

    // 5. Crear la contratación
    $stmt = $pdo->prepare(
        "INSERT INTO contrataciones
            (servicio_id, cliente_id, proveedor_id, monto, comision, monto_proveedor, estado, created_at)
         VALUES (?, ?, ?, ?, ?, ?, 'pendiente_pago', NOW())"
    );
    $stmt->execute([
        $servicioId,
        $clienteId,
        $servicio['usuario_id'],
        $monto,
        $comision,
        $montoProveedor,
    ]);
    $contratacionId = (int) $pdo->lastInsertId();

    // 6. Siguiente paso: pasarela de pago (o confirmación)
    header('Location: pago.php?contratacion=' . $contratacionId);
    exit;

} catch (Throwable $e) {
    error_log($e->getMessage());
    http_response_code(500);
    exit('Error al procesar la contratación.');
}