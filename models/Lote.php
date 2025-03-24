<?php
class Lote {
    private $conn;
    private $table_name = "lote";

    public $id_lote;
    public $dimension;
    public $nombre_lote;
    public $fk_id_ubicacion;
    public $estado;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function getById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id_lote = :id_lote";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_lote", $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " (dimension, nombre_lote, fk_id_ubicacion, estado) 
                  VALUES (:dimension, :nombre_lote, :fk_id_ubicacion, :estado)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":dimension", $this->dimension);
        $stmt->bindParam(":nombre_lote", $this->nombre_lote);
        $stmt->bindParam(":fk_id_ubicacion", $this->fk_id_ubicacion);
        $stmt->bindParam(":estado", $this->estado);

        return $stmt->execute();
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET dimension = :dimension, nombre_lote = :nombre_lote, 
                      fk_id_ubicacion = :fk_id_ubicacion, estado = :estado 
                  WHERE id_lote = :id_lote";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":id_lote", $this->id_lote);
        $stmt->bindParam(":dimension", $this->dimension);
        $stmt->bindParam(":nombre_lote", $this->nombre_lote);
        $stmt->bindParam(":fk_id_ubicacion", $this->fk_id_ubicacion);
        $stmt->bindParam(":estado", $this->estado);

        return $stmt->execute();
    }

    public function patch($id, $data) {
        $fields = [];
        foreach ($data as $key => $value) {
            $fields[] = "$key = :$key";
        }

        if (empty($fields)) {
            return false;
        }

        $query = "UPDATE " . $this->table_name . " SET " . implode(", ", $fields) . " WHERE id_lote = :id_lote";
        $stmt = $this->conn->prepare($query);
        
        foreach ($data as $key => &$value) {
            $stmt->bindParam(":$key", $value);
        }
        $stmt->bindParam(":id_lote", $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    // Nuevo método para verificar dependencias
    public function hasDependencies($id) {
        $query = "SELECT COUNT(*) FROM produccion WHERE fk_id_lote = :id_lote";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_lote", $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    public function delete($id) {
        // Verificar si hay dependencias
        if ($this->hasDependencies($id)) {
            return false; // No eliminar si hay registros dependientes
        }

        $query = "DELETE FROM " . $this->table_name . " WHERE id_lote = :id_lote";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_lote", $id, PDO::PARAM_INT);

        return $stmt->execute();
    }
}
?>