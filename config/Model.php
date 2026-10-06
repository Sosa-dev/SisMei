<?php
class Model {
    protected $db;
    protected $table;

public function __construct(){
    $this->db = Conexion::conectar();
    // Corregido: Una sola 'R' en ATTR y agregada la barra a \PDO::ERRMODE_EXCEPTION
    $this->db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
}

    public function getAll(){
        $sql = "SELECT * FROM {$this->table}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getById($id_columna, $id){
        $sql="SELECT * FROM {$this->table} where {$id_columna} = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function insert($datos){
        $columnas= implode(', ', array_keys($datos));
        $marcadores= implode(', ', array_fill(0, count($datos), '?'));
        $sql = "INSERT INTO {$this->table} ({$columnas}) VALUES ({$marcadores})";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(array_values($datos));
    }

    public function update($datos, $id_columna, $id){
        $campos = [];
        foreach (array_keys($datos) as $llave){
            $campos[]= "{$llave} = ?";
        }

        $setStr= implode(', ', $campos);
        $sql= "UPDATE {$this->table} SET  {$setStr} where {$id_columna} = ?";
        $stmt = $this->db->prepare($sql);
        $valores = array_values($datos);
        $valores[]= $id;
        return  $stmt->execute($valores);
    }

    public function delete($id_columna, $id){
        $sql = "DELETE FROM {$this->table} where {$id_columna} = ?";
        $stmt= $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }
}