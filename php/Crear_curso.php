<?php
session_start();
require 'conexion.php';

if (!isset($_SESSION['user_id']) && !isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit;
}

$id_usuario_sesion = (int) ($_SESSION['user_id'] ?? $_SESSION['id_usuario']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $nombre_institucion = trim($_POST['institucion'] ?? '');
    $duracion = filter_input(INPUT_POST, 'duracion_horas', FILTER_VALIDATE_INT);
    $area = trim($_POST['area'] ?? '');
    $link = trim($_POST['link_original'] ?? '');
    $certificado_gratis = isset($_POST['certificado_gratis']) ? 1 : 0;
    $id_usuario = $id_usuario_sesion;

    if ($titulo === '' || $descripcion === '' || $nombre_institucion === '' || !$duracion || $duracion < 1 || $area === '' || !filter_var($link, FILTER_VALIDATE_URL)) {
        header("Location: Crear_curso.php?error=datos_invalidos");
        exit;
    }

    $imagen = '';
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($_FILES['imagen']['error'] !== UPLOAD_ERR_OK) {
            header("Location: Crear_curso.php?error=imagen");
            exit;
        }
        $extension = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, ['jpg', 'jpeg', 'png', 'webp'], true)) {
            header("Location: Crear_curso.php?error=imagen");
            exit;
        }
        $imagen = bin2hex(random_bytes(12)) . '.' . $extension;
        if (!move_uploaded_file($_FILES['imagen']['tmp_name'], __DIR__ . '/../uploads/cursos/' . $imagen)) {
            header("Location: Crear_curso.php?error=imagen");
            exit;
        }
    }

    $conexion->begin_transaction();
    try {
        $buscar = $conexion->prepare("SELECT id FROM instituciones WHERE LOWER(TRIM(nombre)) = LOWER(?) LIMIT 1");
        $buscar->bind_param('s', $nombre_institucion);
        $buscar->execute();
        $institucion = $buscar->get_result()->fetch_assoc();
        $buscar->close();

        if ($institucion) {
            $id_institucion = (int) $institucion['id'];
        } else {
            $insertar_institucion = $conexion->prepare("INSERT INTO instituciones (nombre) VALUES (?)");
            $insertar_institucion->bind_param('s', $nombre_institucion);
            $insertar_institucion->execute();
            $id_institucion = $insertar_institucion->insert_id;
            $insertar_institucion->close();
        }

        $insertar_curso = $conexion->prepare("INSERT INTO cursos
            (titulo, descripcion, id_institucion, duracion_horas, area, link_original, certificado_gratis, imagen, id_usuario_creador, estado)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pendiente')");
        $insertar_curso->bind_param('ssiissisi', $titulo, $descripcion, $id_institucion, $duracion, $area, $link, $certificado_gratis, $imagen, $id_usuario);
        $insertar_curso->execute();
        $id_curso_nuevo = $insertar_curso->insert_id;
        $insertar_curso->close();

        if (isset($_POST['ofrecer_acompanamiento'])) {
            $modalidad = $_POST['modalidad'] ?? '';
            $precio = filter_input(INPUT_POST, 'precio', FILTER_VALIDATE_FLOAT);
            $requiere_video = isset($_POST['requiere_videollamada']) ? 1 : 0;
            $desc_servicio = trim($_POST['descripcion_servicio'] ?? '');
            if (!in_array($modalidad, ['basica', 'completado'], true) || $precio === false || $precio < 0 || $desc_servicio === '') {
                throw new RuntimeException('Datos de acompañamiento inválidos.');
            }

            $archivo_certificado = '';
            if (isset($_FILES['certificado']) && $_FILES['certificado']['error'] !== UPLOAD_ERR_NO_FILE) {
                if ($_FILES['certificado']['error'] !== UPLOAD_ERR_OK) {
                    throw new RuntimeException('Certificado inválido.');
                }
                $extension = strtolower(pathinfo($_FILES['certificado']['name'], PATHINFO_EXTENSION));
                if (!in_array($extension, ['pdf', 'jpg', 'jpeg', 'png'], true)) {
                    throw new RuntimeException('Certificado inválido.');
                }
                $archivo_certificado = bin2hex(random_bytes(12)) . '.' . $extension;
                if (!move_uploaded_file($_FILES['certificado']['tmp_name'], __DIR__ . '/../uploads/certificados/' . $archivo_certificado)) {
                    throw new RuntimeException('No se pudo guardar el certificado.');
                }
            }

            $insertar_certificacion = $conexion->prepare("INSERT INTO certificaciones (id_usuario, id_curso, archivo_certificado, estado) VALUES (?, ?, ?, 'pendiente')");
            $insertar_certificacion->bind_param('iis', $id_usuario, $id_curso_nuevo, $archivo_certificado);
            $insertar_certificacion->execute();
            $id_certificacion = $insertar_certificacion->insert_id;
            $insertar_certificacion->close();

            $insertar_servicio = $conexion->prepare("INSERT INTO servicios_acompanamiento
                (id_usuario, id_curso, id_certificacion, modalidad, precio, requiere_videollamada, descripcion, activo)
                VALUES (?, ?, ?, ?, ?, ?, ?, 0)");
            $insertar_servicio->bind_param('iiisdis', $id_usuario, $id_curso_nuevo, $id_certificacion, $modalidad, $precio, $requiere_video, $desc_servicio);
            $insertar_servicio->execute();
            $insertar_servicio->close();
        }

        $conexion->commit();
    } catch (Throwable $error) {
        $conexion->rollback();
        header("Location: Crear_curso.php?error=guardar");
        exit;
    }

    header("Location: catalogo.php?mensaje=curso_enviado");
    exit;
}

$existentes = $conexion->query("SELECT nombre FROM instituciones ORDER BY nombre ASC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Diaring - Sugerir curso</title>
    <link rel="stylesheet" href="CSS/styles.css">
</head>
<body>
    <header>
        <div class="logo-area">
            <h1>Diaring</h1>
        </div>
        <nav>
            <ul>
                <li><a href="index.php">Inicio</a></li>
                <li><a href="catalogo.php">Catalogo</a></li>
                <li><a href="recursos.php">Recursos</a></li>
                <li><a href="ensena.php">Enseña en diaring</a></li>
                <li><a href="perfil.php">Perfil</a></li>
                <li><a href="login.php" class="nav-login">Iniciar Sesión</a></li>
                <li><a href="registro.php" class="nav-register">Registrarse</a></li>
            </ul>
        </nav>
    </header>

    <main class="auth-section">
        <div class="auth-container">
            <div class="auth-form-wrapper">
                <h2>Sugiere un <span>curso nuevo</span></h2>
                <p class="auth-subtitle">Comparte un curso que valga la pena certificar</p>

                <form method="POST" enctype="multipart/form-data">

                    <div class="form-group">
                        <label>Nombre del curso</label>
                        <input type="text" name="titulo" required>
                    </div>

                    <div class="form-group">
                        <label>Nombre Institución</label>
                        <input type="text" name="institucion" list="lista-instituciones" required>
                        <datalist id="lista-instituciones">
                            <?php while ($fila = $existentes->fetch_assoc()): ?>
                                <option value="<?= htmlspecialchars($fila['nombre']) ?>">
                            <?php endwhile; ?>
                        </datalist>
                    </div>

                    <div class="form-group">
                        <label>Descripción</label>
                        <textarea name="descripcion" required></textarea>
                    </div>

                    <div class="form-group">
                        <label>Duración (horas)</label>
                        <input type="number" name="duracion_horas" required>
                    </div>

                    <div class="form-group">
                        <label>Área/Tema</label>
                        <input type="text" name="area" required>
                    </div>

                    <div class="form-group">
                        <label>Link del curso original</label>
                        <input type="url" name="link_original" required>
                    </div>

                    <div class="form-group">
                        <label><input type="checkbox" name="certificado_gratis" checked> Certificación gratis</label>
                    </div>

                    <div class="form-group">
                        <label>Imagen del curso</label>
                        <input type="file" name="imagen">
                    </div>

                    <hr>

                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="ofrecer_acompanamiento" id="check-acompanamiento">
                            ¿Quieres ofrecer tú el acompañamiento de este curso?
                        </label>
                    </div>

                    <div id="campos-acompanamiento" style="display:none;">

                        <div class="form-group">
                            <label>Modalidad</label>
                            <select name="modalidad">
                                <option value="basica">Básica</option>
                                <option value="completado">Completado</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Precio</label>
                            <input type="number" name="precio" step="1000">
                        </div>

                        <div class="form-group">
                            <label><input type="checkbox" name="requiere_videollamada"> Este curso requiere videollamadas</label>
                        </div>

                        <div class="form-group">
                            <label>Describe tu servicio</label>
                            <textarea name="descripcion_servicio"></textarea>
                        </div>

                        <div class="form-group">
                            <label>Sube tu certificado</label>
                            <input type="file" name="certificado">
                        </div>

                    </div>

                    <button type="submit" class="btn-google">Enviar</button>
                </form>
            </div>
        </div>
    </main>

    <footer>
        <div class="footer-container">
            <div class="footer-col">
                <h3>Diaring</h3>
                <p>Creado por estudiantes, para estudiantes.</p>
            </div>
            <div class="footer-col">
                <h4>Síguenos</h4>
                <p>📷 🎵 ✖</p>
            </div>
            <div class="footer-col">
                <h4>Acerca de Diaring</h4>
                <p>Lo que hay detrás de los cursos</p>
            </div>
            <div class="footer-col">
                <h4>Contáctanos</h4>
                <p>Si necesitas ayuda, comunícate con nosotros y te apoyaremos.</p>
            </div>
        </div>
        <div class="footer-bottom">
            <span>© 2026 Diaring. Todos los derechos reservados.</span>
        </div>
    </footer>

    <script>
    document.getElementById('check-acompanamiento').addEventListener('change', function() {
        document.getElementById('campos-acompanamiento').style.display = this.checked ? 'block' : 'none';
    });
    </script>
</body>
</html>
<!-- no coje el github -->

