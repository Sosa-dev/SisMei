<?php
namespace apps\escuela\controladores;

use apps\escuela\modelos\EscuelaModel;

class EscuelaController {
    private function modelo() {
        return new EscuelaModel();
    }

    private function validarCsrf() {
        $token = $_POST['csrf_token'] ?? null;
        if (!isset($_SESSION['csrf_token']) || !is_string($token)
            || !hash_equals($_SESSION['csrf_token'], $token)) {
            $_SESSION['error_mensaje'] = 'La sesión de seguridad expiró. Intente nuevamente.';
            return false;
        }
        return true;
    }

    private function redirigir($accion) {
        header('Location: index.php?modulo=escuela&accion=' . $accion);
        exit();
    }

    private function reportarError($mensaje, $accion) {
        $_SESSION['error_mensaje'] = $mensaje;
        $this->redirigir($accion);
    }

    private function textoPost($nombre) {
        $valor = $_POST[$nombre] ?? '';
        return is_string($valor) ? trim($valor) : '';
    }

    private function enteroPost($nombre) {
        $valor = $_POST[$nombre] ?? null;
        return is_string($valor) || is_int($valor)
            ? filter_var($valor, FILTER_VALIDATE_INT)
            : false;
    }

    public function obtenerSecciones() {
        return $this->modelo()->obtenerSecciones();
    }

    public function obtenerEstudiantes() {
        return $this->modelo()->obtenerEstudiantes();
    }

    public function obtenerEstudiante($id) {
        return $this->modelo()->obtenerEstudiante($id);
    }

    public function obtenerAsistencia($idSeccion, $fecha) {
        if (!$this->fechaValida($fecha)) {
            return [];
        }
        return $this->modelo()->obtenerEstudiantesAsistencia($idSeccion, $fecha);
    }

    public function obtenerResumen($anio) {
        return $this->modelo()->obtenerResumen($anio);
    }

    public function obtenerCategoriasInventario() {
        return $this->modelo()->obtenerCategoriasInventario();
    }

    public function obtenerInventario() {
        return $this->modelo()->obtenerInventario();
    }

    public function obtenerMovimientosInventario() {
        return $this->modelo()->obtenerMovimientosInventario();
    }

    public function guardarCategoriaInventario() {
        if (!$this->validarCsrf()) {
            $this->redirigir('inventario');
        }
        $nombre = $this->textoPost('nombre_categoria');
        if ($nombre === '' || strlen($nombre) > 100) {
            $this->reportarError('Indique un nombre de categoría válido.', 'inventario');
        }
        try {
            $this->modelo()->guardarCategoriaInventario($nombre);
            $_SESSION['success_mensaje'] = 'Categoría de inventario registrada.';
        } catch (\PDOException $e) {
            error_log('Error al registrar categoría de inventario: ' . $e->getMessage());
            $_SESSION['error_mensaje'] = $e->getCode() === '23000'
                ? 'Ya existe una categoría con ese nombre.'
                : 'No se pudo registrar la categoría.';
        }
        $this->redirigir('inventario');
    }

    public function guardarArticulo() {
        if (!$this->validarCsrf()) {
            $this->redirigir('inventario');
        }
        $codigo = $this->textoPost('codigo');
        $nombre = $this->textoPost('nombre_articulo');
        $idCategoria = $this->enteroPost('id_categoria');
        $cantidad = $this->enteroPost('cantidad');
        $ubicacion = $this->textoPost('ubicacion');
        $estado = $this->textoPost('estado_conservacion');
        if ($nombre === '' || strlen($nombre) > 150 || !$idCategoria
            || $cantidad === false || $cantidad < 0 || $ubicacion === ''
            || strlen($ubicacion) > 100 || !in_array($estado, ['Bueno', 'Regular', 'Malo'], true)
            || strlen($codigo) > 50) {
            $this->reportarError('Complete los datos del artículo con valores válidos.', 'inventario');
        }
        try {
            $this->modelo()->guardarArticulo(
                $codigo === '' ? null : $codigo,
                $nombre,
                $idCategoria,
                $cantidad,
                $ubicacion,
                $estado,
                $_SESSION['usuario_id']
            );
            $_SESSION['success_mensaje'] = 'Artículo de inventario registrado.';
        } catch (\PDOException $e) {
            error_log('Error al registrar artículo de inventario: ' . $e->getMessage());
            $_SESSION['error_mensaje'] = $e->getCode() === '23000'
                ? 'El código ya existe o la categoría seleccionada no es válida.'
                : 'No se pudo registrar el artículo.';
        }
        $this->redirigir('inventario');
    }

    public function registrarMovimientoInventario() {
        if (!$this->validarCsrf()) {
            $this->redirigir('inventario');
        }
        $idArticulo = $this->enteroPost('id_articulo');
        $cantidad = $this->enteroPost('cantidad_movimiento');
        $tipo = $this->textoPost('tipo_movimiento');
        $observacion = $this->textoPost('observacion_movimiento');
        if (!$idArticulo || !$cantidad || $cantidad < 1
            || !in_array($tipo, ['entrada', 'salida', 'baja'], true)
            || strlen($observacion) > 255) {
            $this->reportarError('Indique un tipo, artículo y cantidad de movimiento válidos.', 'inventario');
        }
        try {
            $this->modelo()->registrarMovimientoInventario(
                $idArticulo,
                $_SESSION['usuario_id'],
                $tipo,
                $cantidad,
                $observacion === '' ? null : $observacion
            );
            $_SESSION['success_mensaje'] = 'Movimiento registrado y existencias actualizadas.';
        } catch (\InvalidArgumentException $e) {
            $_SESSION['error_mensaje'] = $e->getMessage();
        } catch (\PDOException $e) {
            error_log('Error al registrar movimiento de inventario: ' . $e->getMessage());
            $_SESSION['error_mensaje'] = 'No se pudo registrar el movimiento.';
        }
        $this->redirigir('inventario');
    }

    public function guardarSeccion() {
        if (!$this->validarCsrf()) {
            $this->redirigir('secciones');
        }

        $grado = $this->textoPost('grado');
        $seccion = $this->textoPost('seccion');
        $anio = $this->enteroPost('anio_lectivo');
        if ($grado === '' || $seccion === '' || strlen($grado) > 50
            || strlen($seccion) > 10 || !$anio || $anio < 2000 || $anio > 2200) {
            $this->reportarError('Complete los datos de la sección con valores válidos.', 'secciones');
        }

        try {
            $this->modelo()->guardarSeccion($grado, $seccion, $anio);
            $_SESSION['success_mensaje'] = 'Sección registrada correctamente.';
        } catch (\PDOException $e) {
            error_log('Error al registrar sección: ' . $e->getMessage());
            $_SESSION['error_mensaje'] = $e->getCode() === '23000'
                ? 'Ya existe esa sección para el año lectivo indicado.'
                : 'No se pudo registrar la sección.';
        }
        $this->redirigir('secciones');
    }

    public function guardarEstudiante() {
        if (!$this->validarCsrf()) {
            $this->redirigir('estudiantes');
        }

        $datos = $this->datosEstudiante();
        if (!$datos) {
            $this->reportarError('Complete todos los datos del estudiante correctamente.', 'estudiantes');
        }

        try {
            $this->modelo()->guardarEstudiante(
                $datos['nie'],
                $datos['nombre'],
                $datos['apellido'],
                $datos['id_seccion']
            );
            $_SESSION['success_mensaje'] = 'Estudiante registrado correctamente.';
        } catch (\PDOException $e) {
            error_log('Error al registrar estudiante: ' . $e->getMessage());
            $_SESSION['error_mensaje'] = $e->getCode() === '23000'
                ? 'El NIE ya está registrado o la sección seleccionada no existe.'
                : 'No se pudo registrar el estudiante.';
        }
        $this->redirigir('estudiantes');
    }

    public function actualizarEstudiante() {
        $id = $this->enteroPost('id');
        if (!$this->validarCsrf()) {
            $this->redirigir('estudiantes');
        }
        if (!$id) {
            $this->reportarError('El estudiante seleccionado no es válido.', 'estudiantes');
        }

        $datos = $this->datosEstudiante();
        $estado = $this->textoPost('estado') === 'inactivo' ? 'inactivo' : 'activo';
        if (!$datos) {
            $this->reportarError('Complete todos los datos del estudiante correctamente.', 'editarEstudiante&id=' . $id);
        }

        try {
            $this->modelo()->actualizarEstudiante(
                $id,
                $datos['nie'],
                $datos['nombre'],
                $datos['apellido'],
                $datos['id_seccion'],
                $estado
            );
            $_SESSION['success_mensaje'] = 'Estudiante actualizado correctamente.';
        } catch (\PDOException $e) {
            error_log('Error al actualizar estudiante: ' . $e->getMessage());
            $_SESSION['error_mensaje'] = $e->getCode() === '23000'
                ? 'El NIE ya está registrado o la sección seleccionada no existe.'
                : 'No se pudo actualizar el estudiante.';
        }
        $this->redirigir('estudiantes');
    }

    public function guardarAsistencia() {
        if (!$this->validarCsrf()) {
            $this->redirigir('asistencia');
        }

        $idSeccion = $this->enteroPost('id_seccion');
        $fecha = $_POST['fecha'] ?? '';
        $asistencias = $_POST['asistencias'] ?? null;
        $estadosValidos = ['presente', 'ausente', 'tardanza', 'justificado'];
        if (!$idSeccion || !$this->fechaValida($fecha) || !is_array($asistencias) || !$asistencias) {
            $this->reportarError('Seleccione una sección, fecha y al menos un estudiante.', 'asistencia');
        }

        $registros = [];
        foreach ($asistencias as $idEstudiante => $datos) {
            $idEstudiante = filter_var($idEstudiante, FILTER_VALIDATE_INT);
            if (!$idEstudiante || !is_array($datos) || !in_array($datos['estado'] ?? '', $estadosValidos, true)) {
                $this->reportarError('Los datos de asistencia enviados no son válidos.', 'asistencia');
            }
            $observacion = $datos['observacion'] ?? '';
            if (!is_string($observacion)) {
                $this->reportarError('Las observaciones enviadas no son válidas.', 'asistencia');
            }
            $observacion = trim($observacion);
            if (strlen($observacion) > 255) {
                $this->reportarError('Las observaciones no pueden exceder 255 caracteres.', 'asistencia');
            }
            $registros[$idEstudiante] = [
                'estado' => $datos['estado'],
                'observacion' => $observacion === '' ? null : $observacion
            ];
        }

        try {
            $this->modelo()->guardarAsistencia(
                $idSeccion,
                $fecha,
                $_SESSION['usuario_id'],
                $registros
            );
            $_SESSION['success_mensaje'] = 'La asistencia quedó guardada correctamente.';
        } catch (\InvalidArgumentException $e) {
            $_SESSION['error_mensaje'] = $e->getMessage();
        } catch (\PDOException $e) {
            error_log('Error al guardar asistencia: ' . $e->getMessage());
            $_SESSION['error_mensaje'] = 'No se pudo guardar la asistencia. Verifique la conexión y vuelva a intentarlo.';
        }
        header('Location: index.php?modulo=escuela&accion=asistencia&id_seccion='
            . $idSeccion . '&fecha=' . rawurlencode($fecha));
        exit();
    }

    private function datosEstudiante() {
        $nie = $this->textoPost('nie');
        $nombre = $this->textoPost('nombre');
        $apellido = $this->textoPost('apellido');
        $idSeccion = $this->enteroPost('id_seccion');

        if ($nie === '' || strlen($nie) > 20 || $nombre === '' || strlen($nombre) > 100
            || $apellido === '' || strlen($apellido) > 100 || !$idSeccion) {
            return false;
        }
        return [
            'nie' => $nie,
            'nombre' => $nombre,
            'apellido' => $apellido,
            'id_seccion' => $idSeccion
        ];
    }

    private function fechaValida($fecha) {
        if (!is_string($fecha)) {
            return false;
        }
        $date = \DateTime::createFromFormat('!Y-m-d', $fecha);
        return $date && $date->format('Y-m-d') === $fecha;
    }
}
