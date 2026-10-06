<?php
$escape = function ($valor) {
    return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8');
};
?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800">Secciones y grupos</h1>
            <p class="mb-0 text-muted">Organización de la Escuela Bíblica NJCP</p>
        </div>
    </div>
    <?php if (isset($_SESSION['success_mensaje'])): ?>
        <div class="alert alert-success"><?= $escape($_SESSION['success_mensaje']); unset($_SESSION['success_mensaje']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error_mensaje'])): ?>
        <div class="alert alert-danger"><?= $escape($_SESSION['error_mensaje']); unset($_SESSION['error_mensaje']); ?></div>
    <?php endif; ?>

    <div class="row">
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Nueva sección</h6></div>
                <div class="card-body">
                    <form method="POST" action="index.php?modulo=escuela&accion=guardarSeccion">
                        <input type="hidden" name="csrf_token" value="<?= $escape($_SESSION['csrf_token']) ?>">
                        <div class="form-group">
                            <label for="grado">Grado o grupo</label>
                            <input class="form-control" type="text" id="grado" name="grado" maxlength="50" placeholder="Ej. Grupo de niños" required>
                        </div>
                        <div class="form-group">
                            <label for="seccion">Sección</label>
                            <input class="form-control" type="text" id="seccion" name="seccion" maxlength="10" placeholder="Ej. A" required>
                        </div>
                        <div class="form-group">
                            <label for="anio_lectivo">Año lectivo</label>
                            <input class="form-control" type="number" id="anio_lectivo" name="anio_lectivo" min="2000" max="2200" value="<?= (int)date('Y') ?>" required>
                        </div>
                        <button class="btn btn-primary btn-block" type="submit">Registrar sección</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Secciones registradas</h6></div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="thead-light">
                                <tr><th>Grado / Grupo</th><th>Sección</th><th>Año lectivo</th><th>Estudiantes activos</th></tr>
                            </thead>
                            <tbody>
                                <?php if (!$secciones): ?>
                                    <tr><td colspan="4" class="text-center text-muted">Aún no hay secciones.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($secciones as $seccion): ?>
                                        <tr>
                                            <td><?= $escape($seccion['grado']) ?></td>
                                            <td><?= $escape($seccion['seccion']) ?></td>
                                            <td><?= (int)$seccion['anio_lectivo'] ?></td>
                                            <td><?= (int)$seccion['total_estudiantes'] ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
