<?php
namespace apps\usuarios\controladores;

use apps\usuarios\modelos\usuarioModel;

class UsuarioController {


    public function listarUsuarios(){
        $usuarioModel = new usuarioModel();
        return $usuarioModel->listar();
    }

    public function eliminarUsuarios($id_usuario){
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']){
            $_SESSION['error_mensaje'] = "Error de seguridad: Token CSRF inválido.";
            header("Location: index.php?modulo=usuarios&accion=listar");
            exit();
        }

        $id_usuario = trim($_POST['usuario_id']);
        if(empty($id_usuario) || !is_numeric($id_usuario)){
            $_SESSION['error_mensaje'] = "ID de usuario inválido.";
            header("Location: index.php?modulo=usuarios&accion=listar");
            exit();
        }

        $usuarioModel = new usuarioModel();
        $resultado = $usuarioModel->eliminar($id_usuario);
        if($resultado){
            $_SESSION['success_mensaje'] = "Usuario eliminado exitosamente.";
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
             header("Location: index.php?modulo=usuarios&accion=listar");
            exit();
        } else {
            $_SESSION['error_mensaje'] = "Error al eliminar el usuario.";
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
             header("Location: index.php?modulo=usuarios&accion=listar");
            exit();
        }
        
    }

    public function procesarUpdate(){
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']){
            $_SESSION['error_mensaje'] = "Error de seguridad: Token CSRF inválido.";
            header("Location: index.php?modulo=usuarios&accion=listar");
            exit();
        }

            $id_usuario = trim($_POST['usuario_id']);
            if(empty($id_usuario) || !is_numeric($id_usuario)){
                $_SESSION['error_mensaje'] = "ID de usuario inválido.";
                header("Location: index.php?modulo=usuarios&accion=listar");
                exit();
            }
            $usuarioModel = new usuarioModel();
            $resultado = $usuarioModel->un_registro($id_usuario);
            if($resultado){
                $_SESSION['usuario_editar'] = $resultado; // Guardamos los datos del usuario a editar en la sesión
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                 header("Location: index.php?modulo=usuarios&accion=editar");
                exit();
            } else {
                $_SESSION['error_mensaje'] = "Error al encontrar el usuario.";
                 header("Location: index.php?modulo=usuarios&accion=listar");
                exit();
            }
    }

    public function editar(){
//         echo "<pre>";
// print_r($_POST);
// echo "</pre>";
// echo $_SESSION['usuario_editar']['id_usuario'];
// exit();
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']){
                $_SESSION['error_mensaje'] = "Error de seguridad: Token CSRF inválido.";
                header("Location: index.php?modulo=usuarios&accion=listar");
                exit();
            }
            $id_usuario = trim($_SESSION['usuario_editar']['id_usuario']);
            $nombre_completo = trim($_POST['nombre_completo'] ?? '');
            $id_rol = trim($_POST['id_rol'] ?? '');
            $correo = trim($_POST['correo'] ?? '');
            $estado = trim($_POST['estado'] ?? '');
            if(empty($nombre_completo) || !in_array($id_rol, ['1', '4'], true) || empty($correo)){
                $_SESSION['error_mensaje'] = "Por favor, complete todos los campos.";
                header("Location: index.php?modulo=usuarios&accion=editar");
                exit();
            }

            if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                    $_SESSION['error_mensaje'] = "El formato del correo electrónico no es válido.";
                    header("Location: index.php?modulo=usuarios&accion=listar");
                    exit();
                }

            $usuarioModel = new usuarioModel();
            $resultado = $usuarioModel->editar($id_usuario, $nombre_completo, $id_rol, $correo, $estado);
            if($resultado){
                unset($_SESSION['usuario_editar']);
                $_SESSION['success_mensaje'] = "Usuario editado exitosamente.";
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                 header("Location: index.php?modulo=usuarios&accion=listar");
                exit();
            } else {
                $_SESSION['error_mensaje'] = "Error al editar el usuario.";
                 header("Location: index.php?modulo=usuarios&accion=listar");
                exit();
            }


    }
    public function guardar(){

        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']){
            $_SESSION['error_mensaje'] = "Error de seguridad: Token CSRF inválido.";
            header("Location: index.php?modulo=usuarios&accion=listar");
            exit();
        }

        $nombre_completo = trim($_POST['nombre_completo'] ?? '');
        $id_rol = trim($_POST['id_rol'] ?? '');
        $correo = trim($_POST['correo'] ?? '');
        $contra = trim($_POST['contrasena'] ?? '');

        if(empty($nombre_completo) || !in_array($id_rol, ['1', '4'], true) || empty($correo) || empty($contra)){
            $_SESSION['error_mensaje'] = "Por favor, complete todos los campos.";
            header("Location: index.php?modulo=usuarios&accion=listar");
            exit();
        }
        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error_mensaje'] = "El formato del correo electrónico no es válido.";
            header("Location: index.php?modulo=usuarios&accion=login");
            exit();
        }
        $contrasena = password_hash($contra, PASSWORD_DEFAULT);
        $usuarioModel = new usuarioModel();
        $resultado = $usuarioModel->guardar($nombre_completo, $id_rol, $correo, $contrasena);
        if($resultado){
            $_SESSION['success_mensaje'] = "Usuario registrado exitosamente.";
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
             header("Location: index.php?modulo=usuarios&accion=listar");
            exit();
        } else {
            $_SESSION['error_mensaje'] = "Error al registrar el usuario.";
             header("Location: index.php?modulo=usuarios&accion=listar");
            exit();
            }
    
    }
    


    public function procesarLogin() {
    // 1. Validación estricta del Token CSRF
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']){
        $_SESSION['error_mensaje'] = "Error de seguridad: Token CSRF inválido.";
        header("Location: index.php?modulo=usuarios&accion=login");
        exit();
    }

    // 2. Captura y limpieza de espacios en blanco
    $correo = trim($_POST['correo'] ?? '');
    $contrasena = trim($_POST['contrasena'] ?? '');

    // 3. Validación de campos vacíos
    if (empty($correo) || empty($contrasena)) {
        $_SESSION['error_mensaje'] = "Por favor, complete todos los campos.";
        header("Location: index.php?modulo=usuarios&accion=login");
        exit();
    }

    // 4. Validación del formato de correo
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error_mensaje'] = "El formato del correo electrónico no es válido.";
        header("Location: index.php?modulo=usuarios&accion=login");
        exit();
    }

    // 5. Instancia del Modelo (Con la 'E' mayúscula corregida)
    $usuarioModel = new usuarioModel();
    
    // Ejecutamos el método seguro que desinfecta el SQL y verifica el password encriptado
    $usuario = $usuarioModel->login($correo, $contrasena);

    // 6. Manejo de la sesión según el resultado del modelo
    if ($usuario) {
        // ¡Login exitoso! Guardamos los datos clave en la sesión
        $_SESSION['usuario_id'] = $usuario['id_usuario'];
        $_SESSION['usuario_nombre'] = $usuario['nombre_completo'];
        $_SESSION['usuario_rol'] = $usuario['id_rol']; // Útil si necesitas validar permisos más adelante

        // Regeneramos el token CSRF por seguridad al cambiar el estado de la sesión
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); 
        
        // Redirección al panel principal
        header("Location: index.php?modulo=dashboard&accion=home");
        exit();
    } else {
        // El modelo devolvió false (usuario no existe, está inactivo o contraseña incorrecta)
        $_SESSION['error_mensaje'] = "Correo o contraseña incorrectos.";
        header("Location: index.php?modulo=usuarios&accion=login");
        exit();
    }
}

}