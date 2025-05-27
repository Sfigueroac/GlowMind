-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 26-05-2025 a las 09:22:00
-- Versión del servidor: 10.4.27-MariaDB
-- Versión de PHP: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `glowmind`
--
CREATE DATABASE IF NOT EXISTS `glowmind` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `glowmind`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol` enum('usuario','psicologo','admin') DEFAULT 'usuario'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `email`, `password`, `rol`) VALUES
(1, 'Marta Cardona', 'martacardona@gmail.com', '$2y$10$J8z7eX9Y5vZ3Q2W1K6M8.uX9fR4bT2N0P7Q5L3M9K2J1H6G4F8I2', 'usuario'), -- Contraseña: usuario123
(2, 'Santiago Dulcey', 'santiaguito@gmail.com', '$2y$10$J8z7eX9Y5vZ3Q2W1K6M8.uX9fR4bT2N0P7Q5L3M9K2J1H6G4F8I2', 'usuario'), -- Contraseña: usuario123
(3, 'Valentin', 'valentin12@gmail.com', '$2y$10$J8z7eX9Y5vZ3Q2W1K6M8.uX9fR4bT2N0P7Q5L3M9K2J1H6G4F8I2', 'usuario'), -- Contraseña: usuario123
(4, 'Dr. Laura Gómez', 'laura.psicologo@glowmind.com', '$2y$10$J8z7eX9Y5vZ3Q2W1K6M8.uX9fR4bT2N0P7Q5L3M9K2J1H6G4F8I2', 'psicologo'), -- Contraseña: psicologo123
(5, 'Dr. Carlos Méndez', 'carlos.psicologo@glowmind.com', '$2y$10$J8z7eX9Y5vZ3Q2W1K6M8.uX9fR4bT2N0P7Q5L3M9K2J1H6G4F8I2', 'psicologo'); -- Contraseña: psicologo123

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sesiones`
--

CREATE TABLE `sesiones` (
  `id_sesion` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_psicologo` int(11) NOT NULL,
  `fecha_inicio` timestamp NOT NULL DEFAULT current_timestamp(),
  `estado` enum('activa','finalizada') DEFAULT 'activa',
  `notas` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `sesiones`
--

INSERT INTO `sesiones` (`id_sesion`, `id_usuario`, `id_psicologo`, `fecha_inicio`, `estado`, `notas`) VALUES
(1, 1, 4, '2025-06-01 10:00:00', 'activa', 'Primera sesión, evaluación inicial'),
(2, 2, 4, '2025-06-02 14:00:00', 'activa', 'Discusión sobre ansiedad'),
(3, 3, 5, '2025-06-03 16:00:00', 'finalizada', 'Seguimiento de objetivos');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indices de la tabla `sesiones`
--
ALTER TABLE `sesiones`
  ADD PRIMARY KEY (`id_sesion`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_psicologo` (`id_psicologo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `sesiones`
--
ALTER TABLE `sesiones`
  MODIFY `id_sesion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `sesiones`
--
ALTER TABLE `sesiones`
  ADD CONSTRAINT `sesiones_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sesiones_ibfk_2` FOREIGN KEY (`id_psicologo`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;