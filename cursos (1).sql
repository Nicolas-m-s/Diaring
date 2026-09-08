-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3307
-- Tiempo de generación: 08-09-2026 a las 06:43:48
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `cursos`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `curso`
--

CREATE TABLE `curso` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `link` varchar(200) NOT NULL,
  `contrasena` varchar(255) NOT NULL,
  `descripcion` varchar(255) NOT NULL,
  `imagen` varchar(255) NOT NULL,
  `duracion` int(11) NOT NULL,
  `Subtema1` varchar(100) NOT NULL,
  `Subtema2` varchar(100) NOT NULL,
  `Subtema3` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `curso`
--

INSERT INTO `curso` (`id`, `nombre`, `link`, `contrasena`, `descripcion`, `imagen`, `duracion`, `Subtema1`, `Subtema2`, `Subtema3`) VALUES
(1, 'sociales', 'https://www.youtube.com/watch?v=B6cT4JtrBAI', '$2y$10$kyyalquhU5dhpqNBh5xCRe2z.7H5kGa1k1Rg/HRcog.F2jUDwgmge', 'sociales es muy bueno', 'img/imagenes_usuarios/1787631649_sociales.jpg', 1, 'historia', 'geografia', 'economia'),
(4, 'Programacion', 'https://www.youtube.com/watch?v=B6cT4JtrBAy', '$2y$10$ZWrDaUefi5I6t2nY2cm2Kecn5yL0OT45tPzRMWp7PAdtSui.wz6pa', 'Un cursito de python', 'img/imagenes_usuarios/1787634859_pithon.jpg', 2, 'ffff', 'ffff', 'ffff'),
(5, 'Idiomas', 'https://www.youtube.com/watch?v=zUB19hoSFQk', '$2y$10$Cw017mkMxjd91eUt0csiT.WF5I2OnKs2YrmzznBvV4yb5UeimON8i', 'sffgghhjhkkkkkkk', 'img/imagenes_usuarios/1787699542_idiomas.jpg', 2, 'ff', 'ff', 'ff'),
(6, 'fisica', 'https://www.youtube.com/', '$2y$10$jQxCwJ4AZn8./SwUvMLd0OZujH0mVnCWOwz7Mfrelpd93aoRecdA.', 'un libro de fisica', 'img/imagenes_usuarios/1787701050_fisica.jpg', 2, 'ciencias', 'quimica', 'biologia'),
(7, 'ingles', 'https://chatgpt.com/c/6a972ac3-a1b0-83e9-a7cf-d14a8787b480', '$2y$10$AQ7mimQutnJgg0uNe56SWu3eJVFwXKSjMDUbbUsvSRauszFYFC/Km', 'curso de ingles muy bueno', 'img/imagenes_usuarios/1788292640_minifondo.jpg', 42, 'fonetica', 'gramatica', 'listening'),
(8, 'mecnica', 'https://www.peopleperhour.com/', '$2y$10$oDZauDTuS8nfOwQN/Z7PluBwQLhcIU7NClsCDr1nKuHshFMh6MSKS', 'curso de mecaniuca moto', 'img/imagenes_usuarios/1788294360_arte.jpg', 5, 'motores', 'aeronautica', 'carros');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `id` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(80) NOT NULL,
  `correo` varchar(150) NOT NULL,
  `contrasena` varchar(255) NOT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id`, `nombre`, `correo`, `contrasena`, `fecha_registro`) VALUES
(5, 'ni', 'pvptakeo@gmail.com', '$2y$10$fU8LT3FC2.OJa.siuEVjvusHtCPO0EdY4ud4zE8wmWJC2dRYrpc4q', '2026-09-08 04:02:30'),
(6, 'login1788840558', 'login1788840558@example.com', '$2y$10$oJ7ekemrF2pG6GAksaKRtedI5UZM1btWVRRXcTdRofHtCWfS7IrkS', '2026-09-08 04:09:18'),
(7, 'evans', 'nero@gmail.com', '$2y$10$YyB89Liy4NSdZOCdNxVY5uNrKbjL5ED7vKTgLdE9EWz5wkxyYW3L.', '2026-09-08 04:11:03'),
(8, 'registro1788840897', 'registro1788840897@example.com', '$2y$10$yutO6bvF.3sZHTAdPYRMTOS70tTGOsX4BtXoqaV6tycwexn.xCkGm', '2026-09-08 04:14:57'),
(9, 'tia paola', 'tiapaola@gmail.com', '$2y$10$e31PurorXlvpQz3IeBLB6ex9ONL43ApP1hQ6gIgEIJDGHRpyrHYGu', '2026-09-08 04:15:49'),
(10, 'yanfri', 'yanfri@gmail.com', '$2y$10$tfGbKMDydIxfgBdWI0Q9NuI72i4RN3/InWr9DWVuL0IwcQA8NB8ki', '2026-09-08 04:39:41');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario_curso`
--

CREATE TABLE `usuario_curso` (
  `id` int(10) UNSIGNED NOT NULL,
  `usuario_id` int(10) UNSIGNED NOT NULL,
  `curso_id` int(11) NOT NULL,
  `accesos` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `horas_acumuladas` int(11) NOT NULL DEFAULT 0,
  `ultimo_acceso` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuario_curso`
--

INSERT INTO `usuario_curso` (`id`, `usuario_id`, `curso_id`, `accesos`, `horas_acumuladas`, `ultimo_acceso`) VALUES
(1, 6, 1, 1, 1, '2026-09-08 04:26:33'),
(2, 9, 6, 2, 4, '2026-09-08 04:38:58'),
(3, 9, 7, 2, 84, '2026-09-08 04:34:55'),
(6, 10, 8, 1, 5, '2026-09-08 04:40:29');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `curso`
--
ALTER TABLE `curso`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `correo` (`correo`),
  ADD UNIQUE KEY `contrasena` (`contrasena`);

--
-- Indices de la tabla `usuario_curso`
--
ALTER TABLE `usuario_curso`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuario_curso_unico` (`usuario_id`,`curso_id`),
  ADD KEY `usuario_curso_curso_fk` (`curso_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `curso`
--
ALTER TABLE `curso`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `usuario_curso`
--
ALTER TABLE `usuario_curso`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `usuario_curso`
--
ALTER TABLE `usuario_curso`
  ADD CONSTRAINT `usuario_curso_curso_fk` FOREIGN KEY (`curso_id`) REFERENCES `curso` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `usuario_curso_usuario_fk` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
