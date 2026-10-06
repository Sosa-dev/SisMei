<?php
$escape = function ($valor) {
    return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8');
};
?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800">Inventario escolar</h1>
            <p class="mb-0 text-muted">Control de materiales, mobiliario y recursos de NJCP</p>
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
                <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Nueva categoría</h6></div>
                <div class="card-body">
                    <form method="POST" action="index.php?modulo=escuela&accion=guardarCategoriaInventario">
                        <input type="hidden" name="csrf_token" value="<?= $escape($_SESSION['csrf_token']) ?>">
                        <div class="form-group">
                            <label for="nombre_categoria">Nombre</label>
                            <input class="form-control" id="nombre_categoria" name="nombre_categoria" maxlength="100" placeholder="Ej. Material didáctico" required>
                        </div>
                        <button class="btn btn-outline-primary" type="submit">Agregar categoría</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Registrar activo o material</h6></div>
                <div class="card-body">
                    <?php if (!$categoriasInventario): ?>
                        <p class="mb-0">Registre una categoría antes de agregar artículos.</p>
                    <?php else: ?>
                        <form method="POST" action="index.php?modulo=escuela&accion=guardarArticulo" class="form-row">
                            <input type="hidden" name="csrf_token" value="<?= $escape($_SESSION['csrf_token']) ?>">
                            <div class="form-group col-md-4">
                                <label for="nombre_articulo">Nombre</label>
                                <input class="form-control" id="nombre_articulo" name="nombre_articulo" maxlength="150" required>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="id_categoria">Categoría</label>
                                <select class="form-control" id="id_categoria" name="id_categoria" required>
                                    <?php foreach ($categoriasInventario as $categoria): ?>
                                        <option value="<?= (int)$categoria['id'] ?>"><?= $escape($categoria['nombre']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group col-md-2">
                                <label for="codigo">Código (opcional)</label>
                                <input class="form-control" id="codigo" name="codigo" maxlength="50">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="ubicacion">Ubicación</label>
                                <input class="form-control" id="ubicacion" name="ubicacion" maxlength="100" placeholder="Aula, bodega..." required>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="cantidad">Existencia inicial</label>
                                <input class="form-control" type="number" id="cantidad" name="cantidad" min="0" value="0" required>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="estado_conservacion">Conservación</label>
                                <select class="form-control" id="estado_conservacion" name="estado_conservacion" required>
                                    <option value="Bueno">Bueno</option>
                                    <option value="Regular">Regular</option>
                                    <option value="Malo">Malo</option>
                                </select>
                            </div>
                            <div class="form-group col-md-3 d-flex align-items-end">
                                <button class="btn btn-primary btn-block" type="submit">Registrar artículo</button>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Existencias</h6></div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="thead-light">
                        <tr><th>Código</th><th>Artículo</th><th>Categoría</th><th>Ubicación</th><th>Cantidad</th><th>Conservación</th></tr>
                    </thead>
                    <tbody>
                        <?php if (!$articulosInventario): ?>
                            <tr><td colspan="6" class="text-center text-muted">Aún no hay artículos en el inventario.</td></tr>
                        <?php else: ?>
                            <?php foreach ($articulosInventario as $articulo): ?>
                                <tr>
                                    <td><?= $escape($articulo['codigo'] ?? '—') ?></td>
                                    <td><?= $escape($articulo['nombre']) ?></td>
                                    <td><?= $escape($articulo['categoria']) ?></td>
                                    <td><?= $escape($articulo['ubicacion']) ?></td>
                                    <td><?= (int)$articulo['cantidad'] ?></td>
                                    <td><?= $escape($articulo['estado_conservacion']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php if ($articulosInventario): ?>
        <div class="row">
            <div class="col-lg-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Registrar movimiento</h6></div>
                    <div class="card-body">
                        <form method="POST" action="index.php?modulo=escuela&accion=registrarMovimientoInventario">
                            <input type="hidden" name="csrf_token" value="<?= $escape($_SESSION['csrf_token']) ?>">
                            <div class="form-group">
                                <label for="id_articulo">Artículo</label>
                                <select class="form-control" id="id_articulo" name="id_articulo" required>
                                    <?php foreach ($articulosInventario as $articulo): ?>
                                        <option value="<?= (int)$articulo['id'] ?>"><?= $escape($articulo['nombre'] . ' · Existencia: ' . $articulo['cantidad']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="tipo_movimiento">Tipo</label>
                                <select class="form-control" id="tipo_movimiento" name="tipo_movimiento" required>
                                    <option value="entrada">Entrada</option>
                                    <option value="salida">Salida / préstamo</option>
                                    <option value="baja">Baja</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="cantidad_movimiento">Cantidad</label>
                                <input class="form-control" type="number" id="cantidad_movimiento" name="cantidad_movimiento" min="1" required>
                            </div>
                            <div class="form-group">
                                <label for="observacion_movimiento">Observación</label>
                                <input class="form-control" id="observacion_movimiento" name="observacion_movimiento" maxlength="255">
                            </div>
                            <button class="btn btn-success btn-block" type="submit">Guardar movimiento</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="card shadow mb-4">
                    <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Últimos movimientos</h6></div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <thead class="thead-light">
                                    <tr><th>Fecha</th><th>Artículo</th><th>Movimiento</th><th>Cantidad</th><th>Registró</th><th>Observación</th></tr>
                                </thead>
                                <tbody>
                                    <?php if (!$movimientosInventario): ?>
                                        <tr><td colspan="6" class="text-center text-muted">Aún no hay movimientos.</td></tr>
                                    <?php else: ?>
                                        <?php foreach ($movimientosInventario as $movimiento): ?>
                                            <tr>
                                                <td><?= $escape(date('d/m/Y H:i', strtotime($movimiento['fecha']))) ?></td>
                                                <td><?= $escape($movimiento['articulo']) ?></td>
                                                <td><?= $escape(ucfirst($movimiento['tipo'])) ?></td>
                                                <td><?= (int)$movimiento['cantidad'] ?></td>
                                                <td><?= $escape($movimiento['usuario']) ?></td>
                                                <td><?= $escape($movimiento['observacion'] ?? '') ?></td>
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
    <?php endif; ?>
</div>
