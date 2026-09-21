-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3307
-- Tiempo de generación: 20-09-2026 a las 18:56:21
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE DATABASE IF NOT EXISTS `diaring` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `diaring`;


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `diaring`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cursos`
--

CREATE TABLE `cursos` (
  `id` int(11) NOT NULL,
  `titulo` varchar(150) NOT NULL,
  `descripcion` text NOT NULL,
  `id_institucion` int(11) NOT NULL,
  `duracion_horas` int(11) NOT NULL,
  `area` varchar(100) NOT NULL,
  `link_original` varchar(255) NOT NULL,
  `certificado_gratis` tinyint(1) NOT NULL DEFAULT 1,
  `imagen` varchar(255) NOT NULL,
  `id_usuario_creador` int(11) DEFAULT NULL,
  `estado` enum('pendiente','aprobado','rechazado') NOT NULL DEFAULT 'aprobado',
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `instituciones`
--

CREATE TABLE `instituciones` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `sitio_web` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `correo` varchar(150) NOT NULL,
  `contrasena` varchar(255) NOT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `rol` enum('usuario','admin') NOT NULL DEFAULT 'usuario',
  `fecha_registro` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

CREATE TABLE `certificaciones` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_curso` int(11) NOT NULL,
  `archivo_certificado` varchar(255) NOT NULL,
  `estado` enum('pendiente','aprobado','rechazado') NOT NULL DEFAULT 'pendiente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

CREATE TABLE `servicios_acompanamiento` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_curso` int(11) NOT NULL,
  `id_certificacion` int(11) NOT NULL,
  `modalidad` enum('basica','completado') NOT NULL,
  `precio` decimal(10,2) NOT NULL DEFAULT 0.00,
  `requiere_videollamada` tinyint(1) NOT NULL DEFAULT 0,
  `descripcion` text NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contrataciones`
--

CREATE TABLE `contrataciones` (
  `id` int(11) NOT NULL,
  `id_servicio` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `monto_total` decimal(10,2) NOT NULL,
  `comision` decimal(10,2) NOT NULL,
  `estado` enum('activo','completado','cancelado') NOT NULL,
  `fecha` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `valoraciones`
--

CREATE TABLE `valoraciones` (
  `id` int(11) NOT NULL,
  `id_contratacion` int(11) NOT NULL,
  `puntaje` tinyint(5) NOT NULL,
  `comentario` text NOT NULL,
  `fecha` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Indices de la tabla `cursos`
--
ALTER TABLE `cursos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_tabla_institucion` (`id_institucion`),
  ADD KEY `fk_tabla_usuario_creador` (`id_usuario_creador`);

--
-- Indices de la tabla `instituciones`
--
ALTER TABLE `instituciones`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_usuarios_correo` (`correo`);

ALTER TABLE `certificaciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_cert_usuario` (`id_usuario`),
  ADD KEY `fk_cert_curso` (`id_curso`);

ALTER TABLE `servicios_acompanamiento`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_servicio_usuario` (`id_usuario`),
  ADD KEY `fk_servicio_curso` (`id_curso`),
  ADD KEY `fk_servicio_certificacion` (`id_certificacion`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `cursos`
--
ALTER TABLE `cursos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `instituciones`
--
ALTER TABLE `instituciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `certificaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `servicios_acompanamiento`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `cursos`
--
ALTER TABLE `cursos`
  ADD CONSTRAINT `fk_tabla_institucion` FOREIGN KEY (`id_institucion`) REFERENCES `instituciones` (`id`),
  ADD CONSTRAINT `fk_tabla_usuario_creador` FOREIGN KEY (`id_usuario_creador`) REFERENCES `usuarios` (`id`);

ALTER TABLE `certificaciones`
  ADD CONSTRAINT `fk_cert_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `fk_cert_curso` FOREIGN KEY (`id_curso`) REFERENCES `cursos` (`id`);

ALTER TABLE `servicios_acompanamiento`
  ADD CONSTRAINT `fk_servicio_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `fk_servicio_curso` FOREIGN KEY (`id_curso`) REFERENCES `cursos` (`id`),
  ADD CONSTRAINT `fk_servicio_certificacion` FOREIGN KEY (`id_certificacion`) REFERENCES `certificaciones` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
