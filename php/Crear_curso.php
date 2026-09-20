<?php
session_start();
require 'conexion.php';

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $titulo = $conexion->real_escape_string($_POST['titulo']);
    $descripcion = $conexion->real_escape_string($_POST['descripcion']);
    $nombre_institucion = trim($conexion->real_escape_string($_POST['institucion']));
    $duracion = intval($_POST['duracion_horas']);
    $area = $conexion->real_escape_string($_POST['area']);
    $link = $conexion->real_escape_string($_POST['link_original']);
    $certificado_gratis = isset($_POST['certificado_gratis']) ? 1 : 0;
    $id_usuario = $_SESSION['id_usuario'];

    $imagen = '';
    if (isset($_FILES['imagen']) && $_FILES['imagen']['name'] != '') {
        $imagen = time() . "_" . $_FILES['imagen']['name'];
        move_uploaded_file($_FILES['imagen']['tmp_name'], "uploads/cursos/" . $imagen);
    }

    $buscar = $conexion->query("SELECT id FROM instituciones WHERE LOWER(TRIM(nombre)) = LOWER('$nombre_institucion')");
    if ($buscar->num_rows > 0) {
        $id_institucion = $buscar->fetch_assoc()['id'];
    } else {
        $conexion->query("INSERT INTO instituciones (nombre) VALUES ('$nombre_institucion')");
        $id_institucion = $conexion->insert_id;
    }

    $conexion->query("INSERT INTO cursos 
        (titulo, descripcion, id_institucion, duracion_horas, area, link_original, certificado_gratis, imagen, id_usuario_creador, estado)
        VALUES 
        ('$titulo', '$descripcion', $id_institucion, $duracion, '$area', '$link', $certificado_gratis, '$imagen', $id_usuario, 'pendiente')");

    $id_curso_nuevo = $conexion->insert_id;

    if (isset($_POST['ofrecer_acompanamiento'])) {
        $modalidad = $_POST['modalidad'];
        $precio = floatval($_POST['precio']);
        $requiere_video = isset($_POST['requiere_videollamada']) ? 1 : 0;
        $desc_servicio = $conexion->real_escape_string($_POST['descripcion_servicio']);

        $archivo_certificado = '';
        if (isset($_FILES['certificado']) && $_FILES['certificado']['name'] != '') {
            $archivo_certificado = time() . "_" . $_FILES['certificado']['name'];
            move_uploaded_file($_FILES['certificado']['tmp_name'], "uploads/certificados/" . $archivo_certificado);
        }

        $conexion->query("INSERT INTO certificaciones (id_usuario, id_curso, archivo_certificado, estado)
                           VALUES ($id_usuario, $id_curso_nuevo, '$archivo_certificado', 'pendiente')");
        $id_certificacion = $conexion->insert_id;

        $conexion->query("INSERT INTO servicios_acompanamiento 
            (id_usuario, id_curso, id_certificacion, modalidad, precio, requiere_videollamada, descripcion, activo)
            VALUES 
            ($id_usuario, $id_curso_nuevo, $id_certificacion, '$modalidad', $precio, $requiere_video, '$desc_servicio', 0)");
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

