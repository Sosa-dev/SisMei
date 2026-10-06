-- Ejecutar en la base de datos escolar seleccionada en config/conexion.php.
-- La tabla usuarios/roles conserva el esquema y las contrasenas actuales.

INSERT INTO roles (id_rol, nombre, descripcion)
VALUES (4, 'Docente', 'Toma y consulta la asistencia de la Escuela Bíblica')
ON DUPLICATE KEY UPDATE nombre = VALUES(nombre), descripcion = VALUES(descripcion);

CREATE TABLE IF NOT EXISTS secciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    grado VARCHAR(50) NOT NULL,
    seccion VARCHAR(10) NOT NULL,
    anio_lectivo INT NOT NULL,
    UNIQUE KEY uq_seccion_anio (grado, seccion, anio_lectivo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS estudiantes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nie VARCHAR(20) NOT NULL UNIQUE,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    id_seccion INT NOT NULL,
    estado ENUM('activo', 'inactivo') NOT NULL DEFAULT 'activo',
    CONSTRAINT fk_estudiantes_seccion
        FOREIGN KEY (id_seccion) REFERENCES secciones(id)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS asistencias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_estudiante INT NOT NULL,
    id_usuario INT NOT NULL,
    fecha DATE NOT NULL,
    estado ENUM('presente', 'ausente', 'tardanza', 'justificado') NOT NULL,
    observacion VARCHAR(255) DEFAULT NULL,
    UNIQUE KEY uq_asistencia_dia (id_estudiante, fecha),
    CONSTRAINT fk_asistencia_estudiante
        FOREIGN KEY (id_estudiante) REFERENCES estudiantes(id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_asistencia_usuario
        FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS categorias_inventario (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS articulos_inventario (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(50) DEFAULT NULL UNIQUE,
    nombre VARCHAR(150) NOT NULL,
    id_categoria INT NOT NULL,
    cantidad INT NOT NULL DEFAULT 0,
    ubicacion VARCHAR(100) NOT NULL,
    estado_conservacion ENUM('Bueno', 'Regular', 'Malo') NOT NULL DEFAULT 'Bueno',
    CONSTRAINT fk_articulos_categoria
        FOREIGN KEY (id_categoria) REFERENCES categorias_inventario(id)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS movimientos_inventario (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_articulo INT NOT NULL,
    id_usuario INT NOT NULL,
    tipo ENUM('entrada', 'salida', 'baja') NOT NULL,
    cantidad INT NOT NULL,
    fecha TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    observacion VARCHAR(255) DEFAULT NULL,
    CONSTRAINT fk_movimiento_articulo
        FOREIGN KEY (id_articulo) REFERENCES articulos_inventario(id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_movimiento_usuario
        FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
