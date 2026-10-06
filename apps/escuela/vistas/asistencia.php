<?php
$fecha = preg_match('/^\d{4}-\d{2}-\d{2}$/', (string)$fecha) ? $fecha : date('Y-m-d');
$idSeccionSeleccionada = isset($id_seccion) ? (int)$id_seccion : 0;
$escape = function ($valor) {
    return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8');
};
?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800">Control de asistencia</h1>
            <p class="mb-0 text-muted">NJCP · Escuela Bíblica MEI</p>
        </div>
    </div>

    <?php if (isset($_SESSION['success_mensaje'])): ?>
        <div class="alert alert-success"><?= $escape($_SESSION['success_mensaje']); unset($_SESSION['success_mensaje']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error_mensaje'])): ?>
        <div class="alert alert-danger"><?= $escape($_SESSION['error_mensaje']); unset($_SESSION['error_mensaje']); ?></div>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Seleccionar grupo y fecha</h6></div>
        <div class="card-body">
            <?php if (!$secciones): ?>
                <p class="mb-0">Todavía no hay secciones. Solicite al administrador que registre una sección y sus estudiantes.</p>
            <?php else: ?>
                <form method="GET" action="index.php" class="form-row align-items-end">
                    <input type="hidden" name="modulo" value="escuela">
                    <input type="hidden" name="accion" value="asistencia">
                    <div class="form-group col-md-6">
                        <label for="id_seccion">Grado y sección</label>
                        <select class="form-control" id="id_seccion" name="id_seccion" required>
                            <option value="">Seleccione una sección</option>
                            <?php foreach ($secciones as $seccion): ?>
                                <option value="<?= (int)$seccion['id'] ?>" <?= $idSeccionSeleccionada === (int)$seccion['id'] ? 'selected' : '' ?>>
                                    <?= $escape($seccion['grado'] . ' ' . $seccion['seccion'] . ' · ' . $seccion['anio_lectivo']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="fecha">Fecha de clase</label>
                        <input class="form-control" type="date" id="fecha" name="fecha" value="<?= $escape($fecha) ?>" required>
                    </div>
                    <div class="form-group col-md-3">
                        <button class="btn btn-primary btn-block" type="submit"><i class="fas fa-search mr-1"></i> Cargar estudiantes</button>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($idSeccionSeleccionada && $secciones): ?>
        <?php
        $seccionEncontrada = false;
        foreach ($secciones as $seccion) {
            if ((int)$seccion['id'] === $idSeccionSeleccionada) {
                $seccionEncontrada = true;
                $tituloSeccion = $seccion['grado'] . ' ' . $seccion['seccion'];
                break;
            }
        }
        ?>
        <?php if (!$seccionEncontrada): ?>
            <div class="alert alert-warning">La sección seleccionada no existe.</div>
        <?php elseif (!$estudiantes): ?>
            <div class="alert alert-info">No hay estudiantes activos en esta sección.</div>
        <?php else: ?>
            <form method="POST" action="index.php?modulo=escuela&accion=guardarAsistencia">
                <input type="hidden" name="csrf_token" value="<?= $escape($_SESSION['csrf_token']) ?>">
                <input type="hidden" name="id_seccion" value="<?= $idSeccionSeleccionada ?>">
                <input type="hidden" name="fecha" value="<?= $escape($fecha) ?>">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary"><?= $escape($tituloSeccion) ?> · <?= $escape(date('d/m/Y', strtotime($fecha))) ?></h6>
                        <span class="badge badge-info"><?= count($estudiantes) ?> estudiantes</span>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="thead-light">
                                    <tr>
                                        <th>NIE</th>
                                        <th>Estudiante</th>
                                        <th>Estado de asistencia</th>
                                        <th>Observación</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($estudiantes as $estudiante): ?>
                                        <?php $estadoActual = $estudiante['asistencia_estado'] ?: 'presente'; ?>
                                        <tr>
                                            <td><?= $escape($estudiante['nie']) ?></td>
                                            <td><?= $escape($estudiante['apellido'] . ', ' . $estudiante['nombre']) ?></td>
                                            <td>
                                                <?php foreach (['presente' => 'Presente', 'ausente' => 'Ausente', 'tardanza' => 'Tardanza', 'justificado' => 'Justificado'] as $valor => $etiqueta): ?>
                                                    <div class="custom-control custom-radio custom-control-inline">
                                                        <input class="custom-control-input" type="radio"
                                                               id="estado_<?= (int)$estudiante['id'] ?>_<?= $valor ?>"
                                                               name="asistencias[<?= (int)$estudiante['id'] ?>][estado]"
                                                               value="<?= $valor ?>" <?= $estadoActual === $valor ? 'checked' : '' ?> required>
                                                        <label class="custom-control-label" for="estado_<?= (int)$estudiante['id'] ?>_<?= $valor ?>"><?= $etiqueta ?></label>
                                                    </div>
                                                <?php endforeach; ?>
                                            </td>
                                            <td>
                                                <input class="form-control" type="text"
                                                       name="asistencias[<?= (int)$estudiante['id'] ?>][observacion]"
                                                       maxlength="255" value="<?= $escape($estudiante['observacion'] ?? '') ?>"
                                                       placeholder="Opcional">
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <button class="btn btn-success" type="submit"><i class="fas fa-save mr-1"></i> Guardar asistencia</button>
                    </div>
                </div>
            </form>
        <?php endif; ?>
    <?php endif; ?>
</div>
