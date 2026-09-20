<?php
require "conexion.php";

$sql = "SELECT c.*, i.nombre AS nombre_institucion 
        FROM cursos c
        JOIN instituciones i ON c.id_institucion = i.id
        WHERE c.estado = 'aprobado'
        ORDER BY c.id DESC";

$resultado = mysqli_query($conexion, $sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Diaring - Catálogo</title>
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
                <li><a href="catalogo.php" class="active">Catalogo</a></li>
                <li><a href="recursos.php">Recursos</a></li>
                <li><a href="ensena.php">Enseña en diaring</a></li>
                <li><a href="perfil.php">Perfil</a></li>
                <li><a href="login.php" class="nav-login">Iniciar Sesión</a></li>
                <li><a href="registro.php" class="nav-register">Registrarse</a></li>
            </ul>
        </nav>
    </header>

    <main class="main-container">
        <aside class="sidebar">
            <ul class="sidebar-menu">
                <li><a href="#" class="active">Cursos nuevos</a></li>
                <li><a href="#">Cursos promocion</a></li>
                <li><a href="#">Cursos gratis</a></li>
                <li><a href="#">Cursos de pago</a></li>
            </ul>

            <h3>Categoria</h3>
            <ul class="sidebar-categories">
                <li><a href="#">Liderazgo</a></li>
                <li><a href="#">Dibujo</a></li>
                <li><a href="#">Idiomas</a></li>
                <li><a href="#">Software</a></li>
                <li><a href="#">Finanzas</a></li>
                <li><a href="#">Marketing</a></li>
                <li><a href="#">Diseño</a></li>
                <li><a href="#">Arte</a></li>
            </ul>
        </aside>

        <section class="catalog-content">
            <div class="breadcrumb">🏠 &gt; Cursos &gt; Cursos nuevos</div>
            <h2 class="catalog-title">Cursos <span>nuevos</span></h2>
            <p class="results-count"><?= mysqli_num_rows($resultado) ?> resultados</p>

            <div class="content-grid">
                <?php while ($curso = mysqli_fetch_assoc($resultado)): ?>
                    <div class="card">
                        <img src="uploads/cursos/<?= htmlspecialchars($curso['imagen']) ?>" alt="<?= htmlspecialchars($curso['titulo']) ?>">
                        <div class="card-body">
                            <h4><?= htmlspecialchars($curso['titulo']) ?></h4>
                            <span><?= htmlspecialchars($curso['nombre_institucion']) ?></span>
                            <p><?= htmlspecialchars($curso['descripcion']) ?></p>
                            <div class="card-hours"><?= $curso['duracion_horas'] ?> horas</div>
                            <div class="card-tags">
                                <span><?= htmlspecialchars($curso['area']) ?></span>
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
</body>
</html>


