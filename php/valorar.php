<?php
// valorar.php
// El cliente califica un acompañamiento ya terminado.

session_start();
require_once __DIR__ . '/conexion.php'; // Debe definir $pdo (PDO con ERRMODE_EXCEPTION)

if (empty($_SESSION['usuario_id'])) {
    header('Location: login.html');
    exit;
}
$clienteId = (int) $_SESSION['usuario_id'];

$errores = [];

// El id llega por GET al mostrar el formulario y por POST al enviarlo
$contratacionId = filter_var(
    $_POST['contratacion_id'] ?? $_GET['contratacion'] ?? null,
    FILTER_VALIDATE_INT
);
if (!$contratacionId) {
    http_response_code(400);
    exit('Contratación inválida');
}

// 1. La contratación debe ser del cliente y estar finalizada
$stmt = $pdo->prepare(
    "SELECT id, servicio_id, proveedor_id
       FROM contrataciones
      WHERE id = ? AND cliente_id = ? AND estado = 'finalizada'"
);
$stmt->execute([$contratacionId, $clienteId]);
$contratacion = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$contratacion) {
    http_response_code(403);
    exit('No puedes valorar esta contratación.');
}

// 2. Una sola valoración por contratación
$stmt = $pdo->prepare("SELECT COUNT(*) FROM valoraciones WHERE contratacion_id = ?");
$stmt->execute([$contratacionId]);
if ($stmt->fetchColumn() > 0) {
    exit('Ya valoraste este acompañamiento.');
}

// 3. Guardar
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $puntuacion = filter_var($_POST['puntuacion'] ?? '', FILTER_VALIDATE_INT, [
        'options' => ['min_range' => 1, 'max_range' => 5],
    ]);
    $comentario = trim($_POST['comentario'] ?? '');

    if ($puntuacion === false) $errores[] = 'Elige una puntuación de 1 a 5.';
    if (mb_strlen($comentario) > 1000) $errores[] = 'El comentario es muy largo (máx. 1000).';

    if (!$errores) {
        try {
            $stmt = $pdo->prepare(
                "INSERT INTO valoraciones
                    (contratacion_id, servicio_id, cliente_id, proveedor_id, puntuacion, comentario, created_at)
                 VALUES (?, ?, ?, ?, ?, ?, NOW())"
            );
            $stmt->execute([
                $contratacionId,
                $contratacion['servicio_id'],
                $clienteId,
                $contratacion['proveedor_id'],
                $puntuacion,
                $comentario !== '' ? $comentario : null,
            ]);
            header('Location: perfil.php?msg=valoracion_enviada');
            exit;
        } catch (Throwable $e) {
            error_log($e->getMessage());
            $errores[] = 'Error al guardar tu valoración.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Valorar acompañamiento</title>
</head>
<body>
    <h1>¿Cómo fue tu acompañamiento?</h1>

    <?php foreach ($errores as $e): ?>
        <p style="color:red"><?= htmlspecialchars($e) ?></p>
    <?php endforeach; ?>

    <form method="post">
        <input type="hidden" name="contratacion_id" value="<?= $contratacionId ?>">

        <label>Puntuación:</label>
        <select name="puntuacion" required>
            <option value="">Elige...</option>
            <?php for ($i = 5; $i >= 1; $i--): ?>
                <option value="<?= $i ?>"><?= $i ?> ★</option>
            <?php endfor; ?>
        </select>

        <textarea name="comentario" maxlength="1000" placeholder="Cuéntanos tu experiencia (opcional)"></textarea>

        <button type="submit">Enviar valoración</button>
    </form>
</body>
</html>