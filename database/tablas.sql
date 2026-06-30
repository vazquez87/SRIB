CREATE DATABASE IF NOT EXISTS srib_db;
USE srib_db;

CREATE TABLE IF NOT EXISTS usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    correo VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol ENUM('usuario','administrador') DEFAULT 'usuario',
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS incidencias (
    id_incidencia INT AUTO_INCREMENT PRIMARY KEY,
    folio VARCHAR(20) UNIQUE,
    id_usuario INT NOT NULL,
    descripcion TEXT NOT NULL,
    categoria VARCHAR(100) NOT NULL,
    ubicacion VARCHAR(150) NOT NULL,
    prioridad ENUM('Baja','Media','Alta') DEFAULT 'Media',
    estado ENUM('Pendiente','En proceso','Resuelta') DEFAULT 'Pendiente',
    fecha_reporte TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
);
