<?php
require "conexion.php";
$sql = "SELECT * FROM curso ORDER BY id DESC";
$resultado = mysqli_query($conexion, $sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diaring | Catalogo completo</title>
    <link rel="stylesheet" href="/Diaring/CSS/styles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fira+Sans:ital,wght@0,100;0,200;0,300;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Libre+Franklin:ital,wght@0,10０..9００;1,1００..9００&family=Montserrat:ital,wght@０,1００..9００;1,1００..9００&family=Raleway:ital,wght@０,1００..9００;1,1００..9００&display=swap" rel="stylesheet">
</head>
<body>
    <header class = header>
        <a href="#" class="brand">
            <img src="img/diaring.logo.png" alt="Logo de Diaring">
            <div class="brand-texto">
                <span class="brand-nombre">Diaring</span>
                <small class="eslogan">Entiende más, memoriza menos</small>
            </div>
        </a>
            <nav class="navbar">
              <ul>
                  <li><a href="/Index.html">INICIO</a></li>
                  <li><a href="/Tutoriales.html">TUTORIALES</a></li>
                  <li><a href="/catalogo.html">CATÁLOGO</a></li>
                  <li><a href="/cuenta.html">MI CUENTA</a></li>
                  <li><a href="/comunidad.html">COMUNIDAD</a></li>
              </ul>
            </nav>
        <div class="iconos">
            <form class="buscador" role="search">
              <input type="search" placeholder="Search" aria-label="Buscar">
              <button type="submit" aria-label="Buscar">🔍</button>
            </form>
            <button class="notificaciones" aria-label="Notificaciones">🔔</button>
            <img src="img/User.png" alt="Imagen de Usuario" class="User">
        </div>
    </header>
<main class = "catalogocom">

<main class="catalogocom">

<div class="cursoscom">

<?php while ($curso = mysqli_fetch_assoc($resultado)) { ?>

    <div class="cursocom">
        <img src="<?= htmlspecialchars($curso['imagen']) ?>" alt="">

        <div class="info">
            <h3><?= htmlspecialchars($curso['nombre']) ?></h3>
            <p class="subtemas">
                <?= htmlspecialchars($curso['Subtema1']) ?>, 
                <?= htmlspecialchars($curso['Subtema2']) ?>, 
                <?= htmlspecialchars($curso['Subtema3']) ?>
            </p>
              <p class="descripcion"><?= htmlspecialchars($curso['descripcion']) ?></p>
        </div>

        <div class="extra">
            <p class="duracion"><?= htmlspecialchars($curso['duracion']) ?> horas</p>
            <a href="<?= htmlspecialchars($curso['link']) ?>">
                <button class="botontin">Saber más</button>
            </a>
        </div>
    </div>

<?php } ?>

</div>

</main>



</main>




    <footer>
        <div class="footer-contenido">

            <div class="footer-brand">
                <a href="#" class="brand">
                    <img src="img/diaring.logo.png" alt="Logo de Diaring">
                    <div class="brand-texto">
                        <span class="brand-nombre">Diaring</span>
                        <small class="eslogan">Entiende más, memoriza menos.</small>
                    </div>
                </a>
            </div>

            <nav class="footer-links" aria-label="Enlaces del footer">
                <div class="footer-columna">
                    <h3>Compañía</h3>
                    <ul>  
                        <li><a href="#">Sobre nosotros</a></li>
                        <li><a href="#">Contacto</a></li>
                        <li><a href="#">Carreras</a></li>
                    </ul>
                </div>

                <div class="footer-columna">
                    <h3>Recursos</h3>
                    <ul>
                        <li><a href="#">Documentación</a></li>
                        <li><a href="#">Ayuda</a></li>
                        <li><a href="#">Blog</a></li>
                    </ul>
                </div>

                <div class="footer-columna">
                    <h3>Legal</h3>
                    <ul>
                        <li><a href="#">Términos de uso</a></li>
                        <li><a href="#">Privacidad</a></li>
                    </ul>
                </div>
            </nav>

            <div class="footer-social">
                <a href="https://www.instagram.com/diaringaprendizaje/" aria-label="Instagram">
                    <img src="img/instagram.png" alt="Instagram">
                </a>
                <a href="https://x.com/diaring01" aria-label="X">
                    <img src="img/x.png" alt="X">
                </a>
                <a href="https://www.tiktok.com/@diaringaprendizaje?lang=es-419" aria-label="TikTok">
                    <img src="img/tiktok.png" alt="TikTok">
                </a>
                <a href="https://mail.google.com/mail/u/0/?tab=rm&ogbl#inbox?compose=DmwnWstzWGRnDRmKDVTdqMGsQCkHXjjPCJXqZCvrpDWGbqLQBcZzLKQjTSrmgFwXswCwBWkFqxcV" aria-label="Gmail">
                    <img src="img/gmail.png" alt="Gmail">
                </a>
            </div>

        </div>

        <div class="footer-copy">
            <p>© 2026 Diaring. Todos los derechos reservados.</p>
        </div>
    </footer>
    
</body>
</html>