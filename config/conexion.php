<?php
class Conexion {
public static function conectar() {

    $host = getenv('DB_HOST') ?: 'localhost';
    $db   = getenv('DB_NAME') ?: 'bdmei';
    $user = getenv('DB_USERNAME') ?: 'root'; // <- Cambiado a DB_USERNAME
    $pass = getenv('DB_PASSWORD') !== false ? getenv('DB_PASSWORD') : ''; // <- Cambiado a DB_PASSWORD
    $charset = 'utf8';

        try {
            $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            return new PDO($dsn, $user, $pass, $options);
        } catch (\PDOException $e) {
            die("Error crítico de conexión: " . $e->getMessage());
        }
    }
}