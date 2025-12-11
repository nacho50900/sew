-- Script para crear la base de datos de Pruebas de Usabilidad
-- Base de datos en Tercera Forma Normal (3FN)

-- Crear la base de datos
CREATE DATABASE IF NOT EXISTS uo300737_db 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

-- Usar la base de datos
USE uo300737_db;

-- Eliminar tablas si existen (en orden correcto)
DROP TABLE IF EXISTS OBSERVACIONES_FACILITADOR;
DROP TABLE IF EXISTS RESULTADOS_TEST;
DROP TABLE IF EXISTS USUARIOS;
DROP TABLE IF EXISTS DISPOSITIVOS;
DROP TABLE IF EXISTS GENEROS;
DROP TABLE IF EXISTS PROFESIONES;
DROP TABLE IF EXISTS RESPUESTAS_TEST;

-- Tabla de profesiones (para normalización)
CREATE TABLE PROFESIONES (
    id_profesion INT AUTO_INCREMENT PRIMARY KEY,
    nombre_profesion VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de géneros (para normalización)
CREATE TABLE GENEROS (
    id_genero INT AUTO_INCREMENT PRIMARY KEY,
    descripcion_genero VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de dispositivos (para normalización)
CREATE TABLE DISPOSITIVOS (
    id_dispositivo INT AUTO_INCREMENT PRIMARY KEY,
    nombre_dispositivo VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla principal de usuarios que realizan las pruebas
CREATE TABLE USUARIOS (
    id_usuario INT PRIMARY KEY,
    id_profesion INT NOT NULL,
    edad INT NOT NULL CHECK (edad >= 0 AND edad <= 120),
    id_genero INT NOT NULL,
    pericia_informatica INT NOT NULL CHECK (pericia_informatica >= 0 AND pericia_informatica <= 10),
    FOREIGN KEY (id_profesion) REFERENCES PROFESIONES(id_profesion) ON DELETE RESTRICT ON UPDATE CASCADE,
    FOREIGN KEY (id_genero) REFERENCES GENEROS(id_genero) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de resultados de los tests de usabilidad
CREATE TABLE RESULTADOS_TEST (
    id_resultado INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_dispositivo INT NOT NULL,
    tiempo_completado INT NOT NULL COMMENT 'Tiempo en segundos',
    completado BOOLEAN NOT NULL DEFAULT FALSE,
    comentarios_usuario TEXT,
    propuestas_mejora TEXT,
    valoracion INT CHECK (valoracion >= 0 AND valoracion <= 10),
    fecha_realizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES USUARIOS(id_usuario) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (id_dispositivo) REFERENCES DISPOSITIVOS(id_dispositivo) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Agregar después de la tabla RESULTADOS_TEST en uo300737_db.sql

-- Tabla de respuestas a las preguntas del test
CREATE TABLE RESPUESTAS_TEST (
    id_respuesta INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    pregunta_1 TEXT,
    pregunta_2 TEXT,
    pregunta_3 TEXT,
    pregunta_4 TEXT,
    pregunta_5 TEXT,
    pregunta_6 TEXT,
    pregunta_7 TEXT,
    pregunta_8 TEXT,
    pregunta_9 TEXT,
    pregunta_10 TEXT,
    fecha_respuesta TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES USUARIOS(id_usuario) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de observaciones del facilitador
CREATE TABLE OBSERVACIONES_FACILITADOR (
    id_observacion INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    comentarios_facilitador TEXT NOT NULL,
    fecha_observacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES USUARIOS(id_usuario) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertar datos iniciales en GENEROS
INSERT INTO GENEROS (descripcion_genero) VALUES 
('Masculino'),
('Femenino'),
('Otro'),
('Prefiero no decirlo');

-- Insertar datos iniciales en DISPOSITIVOS
INSERT INTO DISPOSITIVOS (nombre_dispositivo) VALUES 
('Ordenador'),
('Tableta'),
('Teléfono');

-- Insertar algunas profesiones comunes
INSERT INTO PROFESIONES (nombre_profesion) VALUES 
('Estudiante de Ingeniería Informática'),
('Ingeniero Informático'),
('Estudiante'),
('Profesor'),
('Médico'),
('Abogado'),
('Administrativo'),
('Comerciante'),
('Jubilado'),
('Desempleado'),
('Otra');

-- Crear índices para mejorar el rendimiento
CREATE INDEX idx_usuarios_pericia ON USUARIOS(pericia_informatica);
CREATE INDEX idx_usuarios_edad ON USUARIOS(edad);
CREATE INDEX idx_resultados_completado ON RESULTADOS_TEST(completado);
CREATE INDEX idx_resultados_fecha ON RESULTADOS_TEST(fecha_realizacion);

-- Conceder privilegios al usuario DBUSER2025
GRANT ALL PRIVILEGES ON uo300737_db.* TO 'DBUSER2025'@'localhost';
FLUSH PRIVILEGES;