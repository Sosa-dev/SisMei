-- PRECAUCION: este script elimina permanentemente datos y tablas del sistema comercial.
-- Haga primero un respaldo completo de la base de datos escuela_biblica.
-- Conserva usuarios, roles, estudiantes, secciones, asistencias e inventario escolar.

USE escuela_biblica;

-- Asegurar que exista el rol escolar de docente.
INSERT INTO roles (id_rol, nombre, descripcion)
VALUES (4, 'Docente', 'Acceso a la Escuela Bíblica y toma de asistencia')
ON DUPLICATE KEY UPDATE
    nombre = VALUES(nombre),
    descripcion = VALUES(descripcion);

-- Conservar las cuentas asociadas a los roles antiguos, pero deshabilitarlos
-- y renombrarlos para que no aparezcan como funciones comerciales.
UPDATE roles
SET nombre = CASE id_rol
        WHEN 2 THEN 'Deshabilitado legado 2'
        WHEN 3 THEN 'Deshabilitado legado 3'
        ELSE nombre
    END,
    descripcion = CASE id_rol
        WHEN 2 THEN 'Rol legado deshabilitado; reasignar la cuenta a Administrador o Docente'
        WHEN 3 THEN 'Rol legado deshabilitado; reasignar la cuenta a Administrador o Docente'
        ELSE descripcion
    END
WHERE id_rol IN (2, 3);

-- Eliminar tablas comerciales; se conserva categorias_inventario.
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS detalle_ventas;
DROP TABLE IF EXISTS ventas;
DROP TABLE IF EXISTS productos;
DROP TABLE IF EXISTS categorias;
DROP TABLE IF EXISTS clientes;
SET FOREIGN_KEY_CHECKS = 1;
