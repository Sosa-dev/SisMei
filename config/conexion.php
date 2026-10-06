<?php
class Conexion {
    public static function conectar() {
        $host = 'localhost';
        $db   = 'escuela_biblica';
        $user = 'root';
        $pass = '';
        $charset = 'utf8'; // Si tu MySQL es moderno, puedes usar 'utf8mb4'

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