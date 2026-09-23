<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Diaring - Enseña en Diaring</title>
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
    <main>
        <section class="teach-hero">
            <div class="top-alert-banner">
                ¡Destina los primeros acompañantes de <span>Diaring!</span>
            </div>
            <video class="videoapre" autoplay muted loop>
                <source src="../assets/IMG/videoapre.mp4" type="video/mp4">
            </video>
            <div class="teach-hero-content">
                <h2>Comparte tu logro y <span>cobra</span> por eso.</h2>
                <p>Únete como <span>acompañante</span> y convierte tu certificación en una oportunidad.</p>
                <a href="crear_curso.php" class="btn-primary" style="display: inline-block;">Quiero ser acompañante</a>
                
                <div class="teach-categories-tags">
                    <span>Edicion</span>
                    <span>Finanzas</span>
                    <span>Marketing</span>
                    <span>Idiomas</span>
                </div>
            </div>
        </section>
        <section class="why-companions-section">
            <h2>Porque ser <span>acompañante.</span></h2>
            <div class="steps-grid">
                <div class="step-card">
                    <h3>1</h3>
                    <h4>Ingresos flexibles</h4>
                    <p>Tú pones el precio de tu acompañamiento, según el curso y tu experiencia.</p>
                </div>
                <div class="step-card">
                    <h3>2</h3>
                    <h4>Aprovecha lo que lograste</h4>
                    <p>Convierte tu certificación en una fuente de ingresos ayudando a quien la está necesitando ahora.</p>
                </div>
                <div class="step-card">
                    <h3>3</h3>
                    <h4>Construye reputación</h4>
                    <p>Cada acompañamiento suma valoraciones que te dan más visibilidad dentro de Diaring.</p>
                </div>
                <div class="step-image">
                    <img src="../assets/IMG/ensena1pag.jpg" alt="Intercambio de dinero">
                </div>
            </div>
        </section>
        <section class="requirements-section">
            <h2>Requisitos para ser <span>acompañante.</span></h2>
            <p>Aquí los más importantes requisitos.</p>

            <div class="requirements-cards-grid">
                <div class="requirement-box">
                    <img src="../assets/IMG/ensena2.png" alt="Certificado curso">
                    <div class="req-content">
                        <span class="req-check">✔</span>
                        <span>Certificación válida del curso que quieres acompañar.</span>
                    </div>
                </div>
                <div class="requirement-box">
                    <img src="../assets/IMG/ensena3.png" alt="Validación soporte">
                    <div class="req-content">
                        <span class="req-check">✔</span>
                        <span>Validación de credenciales, por el equipo de Diaring.</span>
                    </div>
                </div>
                <div class="requirement-box">
                    <img src="https://images.unsplash.com/photo-1434030216411-0b793f4b4173" alt="Indicar módulos">
                    <div class="req-content">
                        <span class="req-check">✔</span>
                        <span>Indicar que módulos cursas (los modulables).</span>
                    </div>
                </div>
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
