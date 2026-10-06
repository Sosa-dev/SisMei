<?php
namespace apps\usuarios\modelos;

// Traemos la conexión de la carpeta global
require_once __DIR__ . '/../../../config/conexion.php';

class usuarioModel {
    private $db;

    public function __construct() {
        $this->db = \Conexion::conectar();
    }

    public function guardar($nombre_completo, $id_rol, $correo, $contrasena) {
        $sql = "INSERT INTO usuarios (nombre_completo, id_rol, correo, contrasena) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$nombre_completo, $id_rol, $correo, $contrasena]);
    }

    public function listar(){
        $sql = "SELECT u.*, r.nombre AS role_nombre FROM usuarios u INNER JOIN roles r ON u.id_rol = r.id_rol";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function eliminar($id_usuario){
        $sql = "DELETE FROM usuarios WHERE id_usuario = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id_usuario]);
    }

    public function un_registro($id_usuario){
        $sql = "SELECT * FROM usuarios WHERE id_usuario = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id_usuario]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function editar($id_usuario, $nombre_completo, $id_rol, $correo, $estado){
    // Mapeamos el estado: si es "1" o 1, será true (b'1'). Si es "0" o vacío, será false (b'0').
    $estadoBinario = ($estado == 1) ? true : false;

    $sql = "UPDATE usuarios SET nombre_completo = ?, id_rol = ?, correo = ?, estado = ? WHERE id_usuario = ?";
    $stmt = $this->db->prepare($sql);
    
    // Pasamos $estadoBinario en lugar de (int)$estado
    return $stmt->execute([$nombre_completo, $id_rol, $correo, $estadoBinario, $id_usuario]);
    }

    public function login($correo, $contrasena) {
    // 1. Usamos marcadores de posición (?) en lugar de concatenar las variables directamente
    $sql = "SELECT * FROM usuarios WHERE correo = ? AND estado = b'1' AND id_rol IN (1, 4) LIMIT 1";
    
    $stmt = $this->db->prepare($sql);
    
    // 2. Ejecutamos pasando únicamente el correo de forma segura
    $stmt->execute([$correo]);
    
    // 3. Traemos el registro como un arreglo asociativo
    $usuario = $stmt->fetch(\PDO::FETCH_ASSOC);

    // 4. Verificamos si el usuario existe y si la contraseña coincide con el hash encriptado
    if ($usuario && password_verify($contrasena, $usuario['contrasena'])) {
        return $usuario; // Login exitoso, retornamos los datos
    }

    return false; // Credenciales incorrectas

    // // Buscamos un registro donde coincidan exactamente el correo y la contraseña en texto plano
    // $sql = "SELECT id_usuario, nombre_completo, id_rol FROM usuarios WHERE correo = ? AND contrasena = ? AND estado = b'1' LIMIT 1";
    
    // $stmt = $this->db->prepare($sql);
    
    // // Pasamos ambos parámetros ordenados al execute
    // $stmt->execute([$correo, $contrasena]);
    
    // // Retorna el arreglo con los datos del usuario si se encontró coincidencia, o false si no existió
    // return $stmt->fetch(\PDO::FETCH_ASSOC);
}
}