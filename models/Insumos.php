<?php

class Insumos {
    private $connect;
    private $table = "insumos";

    public $id_insumo;
    public $cantidad;
    public $nombre;
    public $precio_unidad;
    public $tipo;
    public $unidad_medida;

    public function __construct($db) {
        $this->connect = $db;
    }

    public function getAll() {
        $query = "SELECT * FROM " . $this->table;
        $stmt = $this->connect->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function getByID($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id_insumo = :id";
        $stmt = $this->connect->prepare($query);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create() {
        $query = "INSERT INTO " . $this->table . " (cantidad, nombre, precio_unidad, tipo, unidad_medida) VALUES (:cantidad, :nombre, :precio_unidad, :tipo, :unidad_medida)";
        $stmt = $this->connect->prepare($query);
        
        $stmt->bindParam(":cantidad", $this->cantidad);
        $stmt->bindParam(":nombre", $this->nombre);
        $stmt->bindParam(":precio_unidad", $this->precio_unidad);
        $stmt->bindParam(":tipo", $this->tipo);
        $stmt->bindParam(":unidad_medida", $this->unidad_medida);
        return $stmt->execute();
    }

    public function update() {
        $query = "UPDATE " . $this->table . " SET cantidad = :cantidad, nombre = :nombre, precio_unidad = :precio_unidad, tipo = :tipo, unidad_medida = :unidad_medida WHERE id_insumo = :id_insumo";
        $stmt = $this->connect->prepare($query);

        $stmt->bindParam(":id_insumo", $this->id_insumo, PDO::PARAM_INT);
        $stmt->bindParam(":cantidad", $this->cantidad);
        $stmt->bindParam(":nombre", $this->nombre);
        $stmt->bindParam(":precio_unidad", $this->precio_unidad);
        $stmt->bindParam(":tipo", $this->tipo);
        $stmt->bindParam(":unidad_medida", $this->unidad_medida);

        return $stmt->execute();
    }

    public function patch($id, $data) {
        $setClause = [];
        foreach ($data as $key => $value) {
            $setClause[] = "$key = :$key";
        }
        $query = "UPDATE " . $this->table . " SET " . implode(", ", $setClause) . " WHERE id_insumo = :id";
        $stmt = $this->connect->prepare($query);
        foreach ($data as $key => &$value) {
            $stmt->bindParam(":$key", $value);
        }
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id_insumo = :id";
        $stmt = $this->connect->prepare($query);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
