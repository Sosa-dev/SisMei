<?php
$escape = function ($valor) {
    return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8');
};
$editando = isset($estudianteEditar) && $estudianteEditar;
?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800">Estudiantes</h1>
            <p class="mb-0 text-muted">Registro de alumnos de la Escuela Bíblica NJCP</p>
        </div>
        <?php if ($editando): ?>
            <a class="btn btn-secondary" href="index.php?modulo=escuela&accion=estudiantes">Cancelar edición</a>
        <?php endif; ?>
    </div>
    <?php if (isset($_SESSION['success_mensaje'])): ?>
        <div class="alert alert-success"><?= $escape($_SESSION['success_mensaje']); unset($_SESSION['success_mensaje']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error_mensaje'])): ?>
        <div class="alert alert-danger"><?= $escape($_SESSION['error_mensaje']); unset($_SESSION['error_mensaje']); ?></div>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary"><?= $editando ? 'Editar estudiante' : 'Registrar estudiante' ?></h6></div>
        <div class="card-body">
            <?php if (!$secciones): ?>
                <div class="alert alert-warning mb-0">Primero debe <a href="index.php?modulo=escuela&accion=secciones">registrar una sección</a>.</div>
            <?php elseif ($editando && !$estudianteEditar): ?>
                <div class="alert alert-warning mb-0">No se encontró el estudiante solicitado.</div>
            <?php else: ?>
                <form method="POST" action="index.php?modulo=escuela&accion=<?= $editando ? 'actualizarEstudiante' : 'guardarEstudiante' ?>" class="form-row align-items-end">
                    <input type="hidden" name="csrf_token" value="<?= $escape($_SESSION['csrf_token']) ?>">
                    <?php if ($editando): ?>
                        <input type="hidden" name="id" value="<?= (int)$estudianteEditar['id'] ?>">
                    <?php endif; ?>
                    <div class="form-group col-md-2">
                        <label for="nie">Código / NIE</label>
                        <input class="form-control" id="nie" name="nie" maxlength="20" value="<?= $editando ? $escape($estudianteEditar['nie']) : '' ?>" required>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="nombre">Nombre</label>
                        <input class="form-control" id="nombre" name="nombre" maxlength="100" value="<?= $editando ? $escape($estudianteEditar['nombre']) : '' ?>" required>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="apellido">Apellido</label>
                        <input class="form-control" id="apellido" name="apellido" maxlength="100" value="<?= $editando ? $escape($estudianteEditar['apellido']) : '' ?>" required>
                    </div>
                    <div class="form-group col-md-2">
                        <label for="id_seccion">Sección</label>
                        <select class="form-control" id="id_seccion" name="id_seccion" required>
                            <option value="">Seleccione</option>
                            <?php foreach ($secciones as $seccion): ?>
                                <option value="<?= (int)$seccion['id'] ?>" <?= $editando && (int)$estudianteEditar['id_seccion'] === (int)$seccion['id'] ? 'selected' : '' ?>>
                                    <?= $escape($seccion['grado'] . ' ' . $seccion['seccion'] . ' · ' . $seccion['anio_lectivo']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php if ($editando): ?>
                        <div class="form-group col-md-1">
                            <label for="estado">Estado</label>
                            <select class="form-control" id="estado" name="estado">
                                <option value="activo" <?= $estudianteEditar['estado'] === 'activo' ? 'selected' : '' ?>>Activo</option>
                                <option value="inactivo" <?= $estudianteEditar['estado'] === 'inactivo' ? 'selected' : '' ?>>Inactivo</option>
                            </select>
                        </div>
                    <?php endif; ?>
                    <div class="form-group col-md-<?= $editando ? '1' : '2' ?>">
                        <button class="btn btn-primary btn-block" type="submit"><?= $editando ? 'Guardar' : 'Agregar' ?></button>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Listado de estudiantes</h6></div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="thead-light">
                        <tr><th>Código / NIE</th><th>Estudiante</th><th>Grado / Sección</th><th>Año</th><th>Estado</th><th>Acción</th></tr>
                    </thead>
                    <tbody>
                        <?php if (!$estudiantes): ?>
                            <tr><td colspan="6" class="text-center text-muted">Aún no hay estudiantes registrados.</td></tr>
                        <?php else: ?>
                            <?php foreach ($estudiantes as $estudiante): ?>
                                <tr>
                                    <td><?= $escape($estudiante['nie']) ?></td>
                                    <td><?= $escape($estudiante['apellido'] . ', ' . $estudiante['nombre']) ?></td>
                                    <td><?= $escape($estudiante['grado'] . ' ' . $estudiante['seccion']) ?></td>
                                    <td><?= (int)$estudiante['anio_lectivo'] ?></td>
                                    <td><span class="badge badge-<?= $estudiante['estado'] === 'activo' ? 'success' : 'secondary' ?>"><?= $escape(ucfirst($estudiante['estado'])) ?></span></td>
                                    <td><a class="btn btn-sm btn-outline-primary" href="index.php?modulo=escuela&accion=editarEstudiante&id=<?= (int)$estudiante['id'] ?>">Editar</a></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
