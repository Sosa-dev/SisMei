<?php
session_start();

spl_autoload_register(function($clase) {
    $archivo = __DIR__ . '/../' . str_replace('\\', '/', $clase) . '.php';
    if (file_exists($archivo)) {
        require_once $archivo;
    }
});

function is_auth_role($roles_permitidos) {
    if (!isset($_SESSION['usuario_rol'])) {
        $_SESSION['error_mensaje'] = 'Debes iniciar sesión para acceder.';
        header('Location: index.php?modulo=usuarios&accion=login');
        exit();
    }

    if (!is_array($roles_permitidos)) {
        $roles_permitidos = [$roles_permitidos];
    }

    if (!in_array((int)$_SESSION['usuario_rol'], $roles_permitidos, true)) {
        $_SESSION['error_mensaje'] = 'No tienes permiso para realizar esta acción.';
        header('Location: index.php?modulo=dashboard&accion=home');
        exit();
    }
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$modulo = $_GET['modulo'] ?? 'usuarios';
$accion = $_GET['accion'] ?? 'login';
$method = $_SERVER['REQUEST_METHOD'];

if ($modulo === 'usuarios') {
    switch ($method) {
        case 'GET':
            if ($accion === 'login') {
                require_once __DIR__ . '/../apps/usuarios/vistas/login.php';
            } elseif ($accion === 'logout') {
                $_SESSION = [];
                if (ini_get('session.use_cookies')) {
                    $params = session_get_cookie_params();
                    setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'],
                        $params['secure'], $params['httponly']);
                }
                session_destroy();
                header('Location: index.php?modulo=usuarios&accion=login');
                exit();
            } elseif (in_array($accion, ['listar', 'editar', 'registro'], true)) {
                is_auth_role(1);
                require_once __DIR__ . '/../layouts/header.php';
                if ($accion === 'listar') {
                    require_once __DIR__ . '/../apps/usuarios/vistas/listar.php';
                } elseif ($accion === 'editar') {
                    require_once __DIR__ . '/../apps/usuarios/vistas/editar.php';
                } else {
                    require_once __DIR__ . '/../apps/usuarios/vistas/registro.php';
                }
                require_once __DIR__ . '/../layouts/footer.php';
            }
            break;
        case 'POST':
            $controller = new \apps\usuarios\controladores\UsuarioController();
            if ($accion === 'login') {
                $controller->procesarLogin();
            } elseif ($accion === 'registro') {
                is_auth_role(1);
                $controller->guardar();
            } elseif ($accion === 'eliminar') {
                is_auth_role(1);
                $controller->eliminarUsuarios($_POST['usuario_id'] ?? null);
            } elseif ($accion === 'procesar') {
                is_auth_role(1);
                $controller->procesarUpdate();
            } elseif ($accion === 'editar') {
                is_auth_role(1);
                $controller->editar();
            }
            break;
    }
} elseif ($modulo === 'dashboard' && $accion === 'home' && $method === 'GET') {
    is_auth_role([1, 4]);
    require_once __DIR__ . '/../layouts/header.php';
    require_once __DIR__ . '/../layouts/homeEscuela.php';
    require_once __DIR__ . '/../layouts/footer.php';
} elseif ($modulo === 'escuela') {
    $controller = new \apps\escuela\controladores\EscuelaController();
    switch ($method) {
        case 'GET':
            if ($accion === 'asistencia') {
                is_auth_role([1, 4]);
                require_once __DIR__ . '/../layouts/header.php';
                $secciones = $controller->obtenerSecciones();
                $id_seccion = filter_input(INPUT_GET, 'id_seccion', FILTER_VALIDATE_INT) ?: null;
                $fecha = $_GET['fecha'] ?? date('Y-m-d');
                $fechaParseada = is_string($fecha)
                    ? \DateTime::createFromFormat('!Y-m-d', $fecha)
                    : false;
                if (!$fechaParseada || $fechaParseada->format('Y-m-d') !== $fecha) {
                    $fecha = date('Y-m-d');
                }
                $estudiantes = $id_seccion ? $controller->obtenerAsistencia($id_seccion, $fecha) : [];
                require_once __DIR__ . '/../apps/escuela/vistas/asistencia.php';
                require_once __DIR__ . '/../layouts/footer.php';
            } elseif ($accion === 'estudiantes') {
                is_auth_role(1);
                require_once __DIR__ . '/../layouts/header.php';
                $secciones = $controller->obtenerSecciones();
                $estudiantes = $controller->obtenerEstudiantes();
                require_once __DIR__ . '/../apps/escuela/vistas/estudiantes.php';
                require_once __DIR__ . '/../layouts/footer.php';
            } elseif ($accion === 'editarEstudiante') {
                is_auth_role(1);
                $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
                if (!$id) {
                    header('Location: index.php?modulo=escuela&accion=estudiantes');
                    exit();
                }
                require_once __DIR__ . '/../layouts/header.php';
                $secciones = $controller->obtenerSecciones();
                $estudiantes = $controller->obtenerEstudiantes();
                $estudianteEditar = $controller->obtenerEstudiante($id);
                require_once __DIR__ . '/../apps/escuela/vistas/estudiantes.php';
                require_once __DIR__ . '/../layouts/footer.php';
            } elseif ($accion === 'secciones') {
                is_auth_role(1);
                require_once __DIR__ . '/../layouts/header.php';
                $secciones = $controller->obtenerSecciones();
                require_once __DIR__ . '/../apps/escuela/vistas/secciones.php';
                require_once __DIR__ . '/../layouts/footer.php';
            } elseif ($accion === 'inventario') {
                is_auth_role(1);
                require_once __DIR__ . '/../layouts/header.php';
                $categoriasInventario = $controller->obtenerCategoriasInventario();
                $articulosInventario = $controller->obtenerInventario();
                $movimientosInventario = $controller->obtenerMovimientosInventario();
                require_once __DIR__ . '/../apps/escuela/vistas/inventario.php';
                require_once __DIR__ . '/../layouts/footer.php';
            }
            break;
        case 'POST':
            if ($accion === 'guardarAsistencia') {
                is_auth_role([1, 4]);
                $controller->guardarAsistencia();
            } elseif ($accion === 'guardarEstudiante') {
                is_auth_role(1);
                $controller->guardarEstudiante();
            } elseif ($accion === 'actualizarEstudiante') {
                is_auth_role(1);
                $controller->actualizarEstudiante();
            } elseif ($accion === 'guardarSeccion') {
                is_auth_role(1);
                $controller->guardarSeccion();
            } elseif ($accion === 'guardarCategoriaInventario') {
                is_auth_role(1);
                $controller->guardarCategoriaInventario();
            } elseif ($accion === 'guardarArticulo') {
                is_auth_role(1);
                $controller->guardarArticulo();
            } elseif ($accion === 'registrarMovimientoInventario') {
                is_auth_role(1);
                $controller->registrarMovimientoInventario();
            }
            break;
    }
} else {
    require_once __DIR__ . '/../layouts/header.php';
    require_once __DIR__ . '/../layouts/404.php';
    require_once __DIR__ . '/../layouts/footer.php';
}
