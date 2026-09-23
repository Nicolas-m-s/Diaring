<?php
require "conexion.php";

$busqueda = trim($_GET['q'] ?? '');
$categoria = trim($_GET['categoria'] ?? '');
$tipo = $_GET['tipo'] ?? '';
$condiciones = ["c.estado = 'aprobado'"];
$parametros = [];
$tipos = '';

if ($busqueda !== '') {
    $condiciones[] = '(c.titulo LIKE ? OR c.descripcion LIKE ? OR c.area LIKE ? OR i.nombre LIKE ?)';
    $texto = '%' . $busqueda . '%';
    array_push($parametros, $texto, $texto, $texto, $texto);
    $tipos .= 'ssss';
}

if ($categoria !== '') {
    $condiciones[] = 'c.area = ?';
    $parametros[] = $categoria;
    $tipos .= 's';
}

if ($tipo === 'gratis') {
    $condiciones[] = 'c.certificado_gratis = 1';
} elseif ($tipo === 'pago') {
    $condiciones[] = 'c.certificado_gratis = 0';
}

$sql = "SELECT c.*, i.nombre AS nombre_institucion
        FROM cursos c
        JOIN instituciones i ON c.id_institucion = i.id
        WHERE " . implode(' AND ', $condiciones) . "
        ORDER BY c.id DESC";
$stmt = $conexion->prepare($sql);
if ($parametros) {
    $stmt->bind_param($tipos, ...$parametros);
}
$stmt->execute();
$resultado = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Diaring - Catálogo</title>
    <link rel="stylesheet" href="../CSS/styles.css">
</head>
<body>
<header>
    <div class="logo-area">
        <a href="index.php" class="logo-link">
            <img src="../assets/IMG/Diaring_logo.png" alt="Logo de Diaring" class="logo">

        </a>
    </div>
        <nav class="main_nav">
            <ul>
                <li><a href="index.php" class="active">Inicio</a></li>
                <li><a href="catalogo.php">Catalogo</a></li>
                <li><a href="recursos.php">Recursos</a></li>
                <li><a href="ensena.php">Enseña en diaring</a></li>
                <li><a href="perfil.php">Perfil</a></li>
            </ul>
        </nav>

        <div class="auth-area">
            <a href="login.php" class="nav-login">Iniciar Sesión</a>
            <a href="registro.php" class="nav-register">Registrarse</a>
        </div>
    </header>
    <main class="main-container">
        <aside class="sidebar">
            <ul class="sidebar-menu">
                <li><a href="catalogo.php" class="active">Cursos nuevos</a></li>
                <li><a href="catalogo.php?tipo=gratis">Cursos gratis</a></li>
                <li><a href="catalogo.php?tipo=pago">Cursos de pago</a></li>
            </ul>

            <h3>Categoria</h3>
            <ul class="sidebar-categories">
                <?php foreach (['Liderazgo', 'Dibujo', 'Idiomas', 'Software', 'Finanzas', 'Marketing', 'Diseño', 'Arte'] as $nombreCategoria): ?>
                    <li><a href="catalogo.php?categoria=<?= rawurlencode($nombreCategoria) ?>" class="<?= $categoria === $nombreCategoria ? 'active' : '' ?>"><?= htmlspecialchars($nombreCategoria) ?></a></li>
                <?php endforeach; ?>
            </ul>
        </aside>

        <section class="catalog-content">
            <h2 class="catalog-title">Cursos <span>nuevos</span></h2>
            <form class="catalog-search" method="get" action="catalogo.php">
                <input type="search" name="q" value="<?= htmlspecialchars($busqueda, ENT_QUOTES, 'UTF-8') ?>" placeholder="Buscar cursos, áreas o instituciones..." aria-label="Buscar cursos">
                <?php if ($categoria !== ''): ?><input type="hidden" name="categoria" value="<?= htmlspecialchars($categoria, ENT_QUOTES, 'UTF-8') ?>"><?php endif; ?>
                <?php if ($tipo !== ''): ?><input type="hidden" name="tipo" value="<?= htmlspecialchars($tipo, ENT_QUOTES, 'UTF-8') ?>"><?php endif; ?>
                <button type="submit" class="btn-primary">Buscar</button>
                <?php if ($busqueda !== '' || $categoria !== '' || $tipo !== ''): ?><a class="catalog-clear" href="catalogo.php">Limpiar</a><?php endif; ?>
            </form>
            <?php if (($_GET['mensaje'] ?? '') === 'curso_enviado'): ?>
                <p role="status" style="color: #166534; margin: 12px 0;">Tu curso se guardó y está pendiente de aprobación. Aparecerá en el catálogo cuando un administrador lo apruebe.</p>
            <?php endif; ?>
            <p class="results-count"><?= $resultado->num_rows ?> resultados</p>

            <div class="content-grid">
                <?php while ($curso = mysqli_fetch_assoc($resultado)): ?>
                    <div class="card">
                        <?php
                        $imagenCurso = trim((string) $curso['imagen']);
                        $imagenUrl = $imagenCurso !== ''
                            ? '../uploads/cursos/' . rawurlencode($imagenCurso)
                            : '../assets/IMG/inicio.jpg';
                        ?>
                        <img src="<?= htmlspecialchars($imagenUrl, ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($curso['titulo'], ENT_QUOTES, 'UTF-8') ?>">
                        <div class="card-body">
                            <h4><?= htmlspecialchars($curso['titulo'], ENT_QUOTES, 'UTF-8') ?></h4>
                            <span><?= htmlspecialchars($curso['nombre_institucion'], ENT_QUOTES, 'UTF-8') ?></span>
                            <p><?= htmlspecialchars($curso['descripcion'], ENT_QUOTES, 'UTF-8') ?></p>
                            <div class="card-hours"><?= $curso['duracion_horas'] ?> horas</div>
                            <div class="card-tags">
                                <span><?= htmlspecialchars($curso['area'], ENT_QUOTES, 'UTF-8') ?></span>
                                <span><?= $curso['certificado_gratis'] ? 'Gratis' : 'Con costo' ?></span>
                            </div>
                            <a href="curso_detalle.php?id=<?= $curso['id'] ?>" class="btn-primary">Acceder</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </section>
    </main>

<footer>
    <div class="footer-container">
        <div class="footer-col">
            <h3>Diaring</h3>
            <p>Creado por estudiantes, para estudiantes.</p>
        </div>
        <div class="footer-col">
            <h4>Redes</h4>
            <div class="footer-social">
                <a href="https://www.instagram.com/diaringaprendizaje/" aria-label="Instagram">
                    <img src="../assets/IMG/insta.png" alt="Instagram">
                </a>
                <a href="https://x.com/diaring01" aria-label="X">
                    <img src="../assets/IMG/twi-removebg-preview.png" alt="X">
                </a>
                <a href="https://www.tiktok.com/@diaringaprendizaje?lang=es-419" aria-label="TikTok">
                    <img src="../assets/IMG/tiktok.png" alt="TikTok">
                </a>
                <a href="https://mail.google.com/mail/u/0/?tab=rm&ogbl#inbox?compose=..." aria-label="Gmail">
                    <img src="../assets/IMG/gm-removebg-preview.png" alt="Gmail">
                </a>
            </div>
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
</body>
</html>

