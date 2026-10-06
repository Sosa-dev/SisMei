<?php
// layout/header.php

// 1. Asegurar que la sesión esté iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// 2. Verificar si NO existe la variable de sesión que identifica al usuario
if (!isset($_SESSION['usuario_id'])) {
    //$_SESSION['usuario_rol']
    // Opcional: Mensaje para notificar al usuario por qué fue expulsado
    $_SESSION['error_mensaje'] = "Acceso denegado. Por favor, inicie sesión primero.";
    
    // 3. Redireccionar inmediatamente a la pantalla de login
    header("Location: index.php?modulo=usuarios&accion=login");
    exit(); // Detiene por completo la carga del resto de la página
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Escuela Bíblica MEI · NJCP</title>

    <link href="/SisInventarioVentas/public/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="/SisInventarioVentas/public/css/reporte.css" rel="stylesheet" type="text/css">

    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <link href="/SisInventarioVentas/public/css/sb-admin-2.min.css" rel="stylesheet">
</head>

<body id="page-top">

    <div id="wrapper">

        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php?modulo=dashboard&accion=home">
                <div class="sidebar-brand-icon rotate-n-15">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-pointer-2">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M14.185 13.14l5.644 -2.202c1.625 -.634 1.538 -2.962 -.13 -3.473l-14.319 -4.382c-1.41 -.431 -2.73 .888 -2.298 2.298l4.382 14.318c.51 1.668 2.84 1.755 3.473 .13l2.202 -5.644a1.84 1.84 0 0 1 1.045 -1.045" />
                    </svg>
                </div>
                <div class="sidebar-brand-text mx-3">MEI · NJCP</div>
            </a>

            <hr class="sidebar-divider my-0">
            <?php if($_SESSION['usuario_rol']==1||$_SESSION['usuario_rol']==4):?>
                <li class="nav-item">
                    <a class="nav-link" href="index.php?modulo=dashboard&accion=home">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-home-2">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M5 12l-2 0l9 -9l9 9l-2 0" />
                            <path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7" />
                            <path d="M10 12h4v4h-4l0 -4" />
                        </svg>
                        <span>Home</span>
                    </a>
                </li>


                <hr class="sidebar-divider">
            <?php endif;?>

            <?php if($_SESSION['usuario_rol']==1 || $_SESSION['usuario_rol']==4):?>
                <li class="nav-item">
                    <a class="nav-link" href="index.php?modulo=escuela&accion=asistencia">
                        <i class="fas fa-fw fa-clipboard-check"></i>
                        <span>Asistencia</span>
                    </a>
                </li>
                <?php if($_SESSION['usuario_rol']==1):?>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?modulo=escuela&accion=estudiantes">
                            <i class="fas fa-fw fa-user-graduate"></i>
                            <span>Estudiantes</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?modulo=escuela&accion=secciones">
                            <i class="fas fa-fw fa-chalkboard"></i>
                            <span>Secciones</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?modulo=escuela&accion=inventario">
                            <i class="fas fa-fw fa-boxes"></i>
                            <span>Inventario escolar</span>
                        </a>
                    </li>
                <?php endif;?>
                <hr class="sidebar-divider">
            <?php endif;?>

            <?php if($_SESSION['usuario_rol']==1):?>


                <li class="nav-item">
                    <a class="nav-link" href="index.php?modulo=usuarios&accion=listar">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-user-search">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                            <path d="M6 21v-2a4 4 0 0 1 4 -4h1.5" />
                            <path d="M15 18a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" />
                            <path d="M20.2 20.2l1.8 1.8" />
                        </svg>
                        <span>Usuarios</span>
                    </a>
                </li>
            <?php endif;?>

            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>
        </ul>
        <div id="content-wrapper" class="d-flex flex-column">

            <div id="content">

                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- <form class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
                        <div class="input-group">
                            <input type="text" class="form-control bg-light border-0 small" placeholder="Search for..." aria-label="Search" aria-describedby="basic-addon2">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="button">
                                    <i class="fas fa-search fa-sm"></i>
                                </button>
                            </div>
                        </div>
                    </form> -->

                    <ul class="navbar-nav ml-auto">
                        <div class="topbar-divider d-none d-sm-block"></div>

                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?></span>
                                <img class="img-profile rounded-circle" src="/SisInventarioVentas/public/img/undraw_profile.svg">
                            </a>
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i> Profile
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="index.php?modulo=usuarios&accion=logout" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i> Logout
                                </a>
                            </div>
                        </li>
                    </ul>
                </nav>
                <div class="container-fluid">