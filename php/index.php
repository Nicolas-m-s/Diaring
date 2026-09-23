<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Diaring - Inicio</title>
    <link rel="stylesheet" href="../CSS/styles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com"></link>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@400;600;800&display=swap" rel="stylesheet">
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
        <!-- Hero Section -->
        <section class="hero-section">
            <video autoplay muted loop>
                <source src="../assets/IMG/videoini.mp4" type="video/mp4">
        
            </video>
            <div class="hero-badge">
                ¡Quiero crear mi curso! <span>Conozca más.</span>
            </div>
            <div class="hero-content">
                <h2>Aprende con quienes ya lo hicieron</h2>
                <p>Descubra cursos gratis y certificaciones de instituciones reconocidas, con el acompañamiento que necesitas para terminar.</p>
                <div class="hero-buttons">
                    <a href="catalogo.php" class="btn-primary">Explorar catalogo</a>
                    <a href="ensena.php" class="btn-secondary">Contratar ayuda</a>
                </div>
            </div>
        </section>
        <section class="companions-section">
            <div class="companions-text">
                <h2>Conozca a nuestros <span>acompañantes.</span></h2>
                <p>Personas que ya se certificaron te ayudaran a lograr tu orientación, con experiencia real en el curso que estés tomando.</p>
                
                <div class="feature-list">
                    <div class="feature-item">
                        <div class="feature-dot"></div>
                        <div>
                            <h4>Acompañantes <span>verificados.</span></h4>
                            <p>Cada acompañante certifica sus credenciales con nosotros antes de ofrecer ayuda, garantizando que realmente completó el curso que dice dominar.</p>
                        </div>
                    </div>
                    <div class="feature-item">
                        <div class="feature-dot"></div>
                        <div>
                            <h4>A tu <span>ritmo.</span></h4>
                            <p>Elige entre acompañamiento idóneo para aprender con guía, o complétalo rápido si solo necesitas el certificado a tiempo.</p>
                        </div>
                    </div>
                    <div class="feature-item">
                        <div class="feature-dot"></div>
                        <div>
                            <h4>Certificate sin <span>abandonar.</span></h4>
                            <p>Te acompañamos desde el primer módulo hasta el certificado final, sin que el curso se ajuste a medias por falta de tiempo o disciplina.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="companions-image">
                <img src="../assets/IMG/inicio.jpg" alt="Graduación y título">
            </div>
        </section>
        <section class="guarantee-section">
            <div class="guarantee-info">
                <h2>Cursos reales, acompañantes <span>verificados.</span></h2>
                <p>Solo trabajamos con instituciones serias y personas que demuestran su certificación.</p>
            </div>
            <div class="guarantee-card">
                <h4>Como lo garantizamos</h4>
                <ul class="guarantee-checks">
                    <li>Instituciones valiosas como Santander, Google, AWS y Coursera.</li>
                    <li>Acompañantes con credenciales verificadas antes de ofrecer ayuda.</li>
                </ul>
                <a href="#" class="btn-primary" style="width: 100%; text-align: center;">Contáctanos</a>
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