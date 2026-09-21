<?php
// crear_servicio.php
// Sube un certificado y ofrece un servicio de acompañamiento.
// Ambos registros quedan en estado 'pendiente' hasta que el admin los valide.

session_start();
require_once __DIR__ . '/conexion.php'; // Debe definir $pdo (PDO con ERRMODE_EXCEPTION)

if (empty($_SESSION['usuario_id'])) {
    header('Location: login.html');
    exit;
}
$usuarioId = (int) $_SESSION['usuario_id'];

const MAX_BYTES   = 5 * 1024 * 1024; // 5 MB
const CARPETA     = __DIR__ . '/uploads/certificados/';
const MIMES_OK    = [
    'application/pdf' => 'pdf',
    'image/jpeg'      => 'jpg',
    'image/png'       => 'png',
];

$errores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Datos del formulario
    $tituloCert  = trim($_POST['titulo_certificado'] ?? '');
    $entidad     = trim($_POST['entidad_emisora'] ?? '');
    $tituloServ  = trim($_POST['titulo_servicio'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $precio      = filter_var($_POST['precio'] ?? '', FILTER_VALIDATE_FLOAT);

    if ($tituloCert === '' || mb_strlen($tituloCert) > 150)  $errores[] = 'Título del certificado inválido.';
    if ($entidad === '')                                     $errores[] = 'Indica la entidad emisora.';
    if ($tituloServ === '' || mb_strlen($tituloServ) > 150)  $errores[] = 'Título del servicio inválido.';
    if ($descripcion === '')                                 $errores[] = 'Describe tu servicio.';
    if ($precio === false || $precio <= 0)                   $errores[] = 'Precio inválido.';

    // 2. Archivo
    $archivo = $_FILES['certificado'] ?? null;
    if (!$archivo || $archivo['error'] !== UPLOAD_ERR_OK) {
        $errores[] = 'Debes subir tu certificado.';
    } elseif ($archivo['size'] > MAX_BYTES) {
        $errores[] = 'El archivo supera 5 MB.';
    } else {
        // Se valida el tipo real del archivo, no la extensión que envía el usuario
        $mime = (new finfo(FILEINFO_MIME_TYPE))->file($archivo['tmp_name']);
        if (!isset(MIMES_OK[$mime])) {
            $errores[] = 'Solo se permiten PDF, JPG o PNG.';
        }
    }

    // 3. Guardar
    if (!$errores) {
        if (!is_dir(CARPETA)) mkdir(CARPETA, 0755, true);
        $nombreArchivo = bin2hex(random_bytes(16)) . '.' . MIMES_OK[$mime];
        $destino       = CARPETA . $nombreArchivo;

        if (!move_uploaded_file($archivo['tmp_name'], $destino)) {
            $errores[] = 'No se pudo guardar el archivo.';
        } else {
            try {
                // Transacción: o se guardan las dos filas o ninguna
                $pdo->beginTransaction();

                $stmt = $pdo->prepare(
                    "INSERT INTO certificaciones
                        (usuario_id, titulo, entidad_emisora, archivo, estado, created_at)
                     VALUES (?, ?, ?, ?, 'pendiente', NOW())"
                );
                $stmt->execute([$usuarioId, $tituloCert, $entidad, $nombreArchivo]);
                $certificacionId = (int) $pdo->lastInsertId();

                $stmt = $pdo->prepare(
                    "INSERT INTO servicios_acompanamiento
                        (usuario_id, certificacion_id, titulo, descripcion, precio, estado, created_at)
                     VALUES (?, ?, ?, ?, ?, 'pendiente', NOW())"
                );
                $stmt->execute([$usuarioId, $certificacionId, $tituloServ, $descripcion, $precio]);

                $pdo->commit();
                header('Location: perfil.php?msg=servicio_enviado');
                exit;
            } catch (Throwable $e) {
                if ($pdo->inTransaction()) $pdo->rollBack();
                @unlink($destino); // no dejar archivos huérfanos
                error_log($e->getMessage());
                $errores[] = 'Error al guardar. Intenta de nuevo.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ofrecer acompañamiento</title>
</head>
<body>
    <h1>Ofrece tu acompañamiento</h1>

    <?php foreach ($errores as $e): ?>
        <p style="color:red"><?= htmlspecialchars($e) ?></p>
    <?php endforeach; ?>

    <form method="post" enctype="multipart/form-data">
        <h2>Tu certificado</h2>
        <input name="titulo_certificado" placeholder="Título del certificado" required>
        <input name="entidad_emisora" placeholder="Entidad emisora" required>
        <input type="file" name="certificado" accept=".pdf,.jpg,.jpeg,.png" required>

        <h2>Tu servicio</h2>
        <input name="titulo_servicio" placeholder="Título del servicio" required>
        <textarea name="descripcion" placeholder="¿Qué incluye tu acompañamiento?" required></textarea>
        <input type="number" name="precio" step="0.01" min="1" placeholder="Precio" required>

        <button type="submit">Enviar para revisión</button>
    </form>
</body>
</html>