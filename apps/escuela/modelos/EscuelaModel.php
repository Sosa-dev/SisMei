<?php
namespace apps\escuela\modelos;

require_once __DIR__ . '/../../../config/conexion.php';

class EscuelaModel {
    private $db;

    public function __construct() {
        $this->db = \Conexion::conectar();
    }

    public function obtenerSecciones() {
        $sql = "SELECT s.id, s.grado, s.seccion, s.anio_lectivo,
                       COUNT(e.id) AS total_estudiantes
                FROM secciones s
                LEFT JOIN estudiantes e ON e.id_seccion = s.id AND e.estado = 'activo'
                GROUP BY s.id, s.grado, s.seccion, s.anio_lectivo
                ORDER BY s.anio_lectivo DESC, s.grado, s.seccion";
        return $this->db->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function guardarSeccion($grado, $seccion, $anio) {
        $stmt = $this->db->prepare(
            "INSERT INTO secciones (grado, seccion, anio_lectivo) VALUES (?, ?, ?)"
        );
        return $stmt->execute([$grado, $seccion, $anio]);
    }

    public function obtenerEstudiantes() {
        $sql = "SELECT e.id, e.nie, e.nombre, e.apellido, e.estado, e.id_seccion,
                       s.grado, s.seccion, s.anio_lectivo
                FROM estudiantes e
                INNER JOIN secciones s ON s.id = e.id_seccion
                ORDER BY e.apellido, e.nombre";
        return $this->db->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function obtenerEstudiante($id) {
        $stmt = $this->db->prepare("SELECT * FROM estudiantes WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function guardarEstudiante($nie, $nombre, $apellido, $idSeccion) {
        $stmt = $this->db->prepare(
            "INSERT INTO estudiantes (nie, nombre, apellido, id_seccion)
             VALUES (?, ?, ?, ?)"
        );
        return $stmt->execute([$nie, $nombre, $apellido, $idSeccion]);
    }

    public function actualizarEstudiante($id, $nie, $nombre, $apellido, $idSeccion, $estado) {
        $stmt = $this->db->prepare(
            "UPDATE estudiantes
             SET nie = ?, nombre = ?, apellido = ?, id_seccion = ?, estado = ?
             WHERE id = ?"
        );
        return $stmt->execute([$nie, $nombre, $apellido, $idSeccion, $estado, $id]);
    }

    public function obtenerEstudiantesAsistencia($idSeccion, $fecha) {
        $stmt = $this->db->prepare(
            "SELECT e.id, e.nie, e.nombre, e.apellido,
                    a.estado AS asistencia_estado, a.observacion
             FROM estudiantes e
             LEFT JOIN asistencias a ON a.id_estudiante = e.id AND a.fecha = ?
             WHERE e.id_seccion = ? AND e.estado = 'activo'
             ORDER BY e.apellido, e.nombre"
        );
        $stmt->execute([$fecha, $idSeccion]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function guardarAsistencia($idSeccion, $fecha, $idUsuario, $asistencias) {
        $ids = array_keys($asistencias);
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $this->db->prepare(
            "SELECT id FROM estudiantes
             WHERE id_seccion = ? AND estado = 'activo' AND id IN ($placeholders)"
        );
        $stmt->execute(array_merge([$idSeccion], $ids));
        if (count($stmt->fetchAll(\PDO::FETCH_COLUMN)) !== count($ids)) {
            throw new \InvalidArgumentException('La lista de estudiantes ya no corresponde a esta sección.');
        }

        $this->db->beginTransaction();
        try {
            $sql = "INSERT INTO asistencias
                        (id_estudiante, id_usuario, fecha, estado, observacion)
                    VALUES (?, ?, ?, ?, ?)
                    ON DUPLICATE KEY UPDATE
                        id_usuario = VALUES(id_usuario),
                        estado = VALUES(estado),
                        observacion = VALUES(observacion)";
            $guardar = $this->db->prepare($sql);
            foreach ($asistencias as $idEstudiante => $datos) {
                $guardar->execute([
                    $idEstudiante,
                    $idUsuario,
                    $fecha,
                    $datos['estado'],
                    $datos['observacion']
                ]);
            }
            $this->db->commit();
        } catch (\PDOException $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        } catch (\Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }

    public function obtenerResumen($anio) {
        $stmt = $this->db->prepare(
            "SELECT
                (SELECT COUNT(*) FROM estudiantes WHERE estado = 'activo') AS estudiantes,
                (SELECT COUNT(*) FROM secciones WHERE anio_lectivo = ?) AS secciones,
                (SELECT COUNT(*) FROM asistencias WHERE fecha = CURDATE() AND estado = 'presente') AS presentes,
                (SELECT COUNT(*) FROM asistencias WHERE fecha = CURDATE() AND estado = 'ausente') AS ausentes"
        );
        $stmt->execute([$anio]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function obtenerCategoriasInventario() {
        return $this->db->query(
            "SELECT id, nombre FROM categorias_inventario ORDER BY nombre"
        )->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function obtenerInventario() {
        $sql = "SELECT a.id, a.codigo, a.nombre, a.cantidad, a.ubicacion,
                       a.estado_conservacion, c.nombre AS categoria
                FROM articulos_inventario a
                INNER JOIN categorias_inventario c ON c.id = a.id_categoria
                ORDER BY a.nombre";
        return $this->db->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function obtenerMovimientosInventario() {
        $sql = "SELECT m.tipo, m.cantidad, m.fecha, m.observacion,
                       a.nombre AS articulo, u.nombre_completo AS usuario
                FROM movimientos_inventario m
                INNER JOIN articulos_inventario a ON a.id = m.id_articulo
                INNER JOIN usuarios u ON u.id_usuario = m.id_usuario
                ORDER BY m.fecha DESC, m.id DESC
                LIMIT 20";
        return $this->db->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function guardarCategoriaInventario($nombre) {
        $stmt = $this->db->prepare("INSERT INTO categorias_inventario (nombre) VALUES (?)");
        return $stmt->execute([$nombre]);
    }

    public function guardarArticulo($codigo, $nombre, $idCategoria, $cantidad, $ubicacion, $estado, $idUsuario) {
        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO articulos_inventario
                    (codigo, nombre, id_categoria, cantidad, ubicacion, estado_conservacion)
                 VALUES (?, ?, ?, ?, ?, ?)"
            );
            $stmt->execute([$codigo, $nombre, $idCategoria, $cantidad, $ubicacion, $estado]);
            $idArticulo = $this->db->lastInsertId();
            if ($cantidad > 0) {
                $movimiento = $this->db->prepare(
                    "INSERT INTO movimientos_inventario
                        (id_articulo, id_usuario, tipo, cantidad, observacion)
                     VALUES (?, ?, 'entrada', ?, 'Existencia inicial')"
                );
                $movimiento->execute([$idArticulo, $idUsuario, $cantidad]);
            }
            $this->db->commit();
        } catch (\Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }

    public function registrarMovimientoInventario($idArticulo, $idUsuario, $tipo, $cantidad, $observacion) {
        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare(
                "SELECT cantidad FROM articulos_inventario WHERE id = ? FOR UPDATE"
            );
            $stmt->execute([$idArticulo]);
            $articulo = $stmt->fetch(\PDO::FETCH_ASSOC);
            if (!$articulo) {
                throw new \InvalidArgumentException('El artículo seleccionado no existe.');
            }
            if ($tipo !== 'entrada' && (int)$articulo['cantidad'] < $cantidad) {
                throw new \InvalidArgumentException('No hay suficientes unidades disponibles para esa salida.');
            }

            $nuevaCantidad = $tipo === 'entrada'
                ? (int)$articulo['cantidad'] + $cantidad
                : (int)$articulo['cantidad'] - $cantidad;
            $movimiento = $this->db->prepare(
                "INSERT INTO movimientos_inventario
                    (id_articulo, id_usuario, tipo, cantidad, observacion)
                 VALUES (?, ?, ?, ?, ?)"
            );
            $movimiento->execute([$idArticulo, $idUsuario, $tipo, $cantidad, $observacion]);
            $actualizar = $this->db->prepare(
                "UPDATE articulos_inventario SET cantidad = ? WHERE id = ?"
            );
            $actualizar->execute([$nuevaCantidad, $idArticulo]);
            $this->db->commit();
        } catch (\Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }
}
