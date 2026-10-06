<?php
use apps\escuela\controladores\EscuelaController;
$escuelaController = new EscuelaController();
$resumenEscuela = $escuelaController->obtenerResumen((int)date('Y'));
$escape = function ($valor) {
    return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8');
};
?>
<div class="container-fluid">
    <div class="jumbotron bg-white shadow-sm border-left-primary">
        <h1 class="h2 text-gray-800">Escuela Bíblica MEI</h1>
        <p class="lead mb-0">Ministerios Emanuel Internacional · Niños y Jóvenes con Propósito (NJCP)</p>
        <p class="text-muted mt-2 mb-0">Bienvenido, <?= $escape($_SESSION['usuario_nombre'] ?? '') ?>.</p>
    </div>
    <div class="row">
        <div class="col-md-6 col-xl-3 mb-4">
            <div class="card border-left-primary shadow h-100 py-2"><div class="card-body">
                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Estudiantes activos</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= (int)$resumenEscuela['estudiantes'] ?></div>
            </div></div>
        </div>
        <div class="col-md-6 col-xl-3 mb-4">
            <div class="card border-left-info shadow h-100 py-2"><div class="card-body">
                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Secciones · <?= (int)date('Y') ?></div>
                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= (int)$resumenEscuela['secciones'] ?></div>
            </div></div>
        </div>
        <div class="col-md-6 col-xl-3 mb-4">
            <div class="card border-left-success shadow h-100 py-2"><div class="card-body">
                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Presentes hoy</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= (int)$resumenEscuela['presentes'] ?></div>
            </div></div>
        </div>
        <div class="col-md-6 col-xl-3 mb-4">
            <div class="card border-left-danger shadow h-100 py-2"><div class="card-body">
                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Ausentes hoy</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= (int)$resumenEscuela['ausentes'] ?></div>
            </div></div>
        </div>
    </div>
    <div class="card shadow mb-4">
        <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Accesos rápidos</h6></div>
        <div class="card-body">
            <a class="btn btn-primary mr-2 mb-2" href="index.php?modulo=escuela&accion=asistencia"><i class="fas fa-clipboard-check mr-1"></i> Tomar asistencia</a>
            <?php if ((int)$_SESSION['usuario_rol'] === 1): ?>
                <a class="btn btn-outline-primary mr-2 mb-2" href="index.php?modulo=escuela&accion=estudiantes"><i class="fas fa-user-graduate mr-1"></i> Gestionar estudiantes</a>
                <a class="btn btn-outline-primary mr-2 mb-2" href="index.php?modulo=escuela&accion=secciones"><i class="fas fa-chalkboard mr-1"></i> Gestionar secciones</a>
                <a class="btn btn-outline-primary mr-2 mb-2" href="index.php?modulo=escuela&accion=inventario"><i class="fas fa-boxes mr-1"></i> Inventario escolar</a>
            <?php endif; ?>
        </div>
    </div>
</div>
