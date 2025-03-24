<?php
class Produccion {
    private $connect;
    private $table = "produccion";

    public $id_produccion;
    public $fk_id_cultivo;
    public $cantidad_producida;
    public $fecha_produccion;
    public $fk_id_lote;
    public $descripcion_produccion;
    public $estado;
    public $fecha_cosecha;

    public function __construct($db) {
        $this->connect = $db;
    }

    public function getAll() {
        $query = "SELECT * FROM " . $this->table;
        $stmt = $this->connect->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function getById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id_produccion = :id_produccion";
        $stmt = $this->connect->prepare($query);
        $stmt->bindParam(":id_produccion", $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create() {
        $query = "INSERT INTO " . $this->table . " (fk_id_cultivo, cantidad_producida, fecha_produccion, fk_id_lote, descripcion_produccion, estado, fecha_cosecha) 
                  VALUES (:fk_id_cultivo, :cantidad_producida, :fecha_produccion, :fk_id_lote, :descripcion_produccion, :estado, :fecha_cosecha)";
        
        $stmt = $this->connect->prepare($query);
        $stmt->bindParam(":fk_id_cultivo", $this->fk_id_cultivo, PDO::PARAM_INT);
        $stmt->bindParam(":cantidad_producida", $this->cantidad_producida, PDO::PARAM_INT);
        $stmt->bindParam(":fecha_produccion", $this->fecha_produccion);
        $stmt->bindParam(":fk_id_lote", $this->fk_id_lote, PDO::PARAM_INT);
        $stmt->bindParam(":descripcion_produccion", $this->descripcion_produccion);
        $stmt->bindParam(":estado", $this->estado);
        $stmt->bindParam(":fecha_cosecha", $this->fecha_cosecha);

        return $stmt->execute();
    }

    public function update() {
        $query = "UPDATE " . $this->table . " 
                  SET fk_id_cultivo = :fk_id_cultivo, cantidad_producida = :cantidad_producida, fecha_produccion = :fecha_produccion, 
                      fk_id_lote = :fk_id_lote, descripcion_produccion = :descripcion_produccion, estado = :estado, fecha_cosecha = :fecha_cosecha 
                  WHERE id_produccion = :id_produccion";

        $stmt = $this->connect->prepare($query);
        $stmt->bindParam(":id_produccion", $this->id_produccion, PDO::PARAM_INT);
        $stmt->bindParam(":fk_id_cultivo", $this->fk_id_cultivo, PDO::PARAM_INT);
        $stmt->bindParam(":cantidad_producida", $this->cantidad_producida, PDO::PARAM_INT);
        $stmt->bindParam(":fecha_produccion", $this->fecha_produccion);
        $stmt->bindParam(":fk_id_lote", $this->fk_id_lote, PDO::PARAM_INT);
        $stmt->bindParam(":descripcion_produccion", $this->descripcion_produccion);
        $stmt->bindParam(":estado", $this->estado);
        $stmt->bindParam(":fecha_cosecha", $this->fecha_cosecha);

        return $stmt->execute();
    }

    public function patch($id, $data) {
        // Definir los campos válidos que se pueden actualizar en la tabla produccion
        $validFields = [
            'fk_id_cultivo',
            'cantidad_producida',
            'fecha_produccion',
            'fk_id_lote',
            'descripcion_produccion',
            'estado',
            'fecha_cosecha'
        ];
        $fields = [];

        // Filtrar solo los campos válidos de $data
        foreach ($data as $key => $value) {
            if (in_array($key, $validFields)) {
                $fields[] = "$key = :$key";
            }
        }

        if (empty($fields)) {
            return false; // No hay campos válidos para actualizar
        }

        $query = "UPDATE " . $this->table . " SET " . implode(", ", $fields) . " WHERE id_produccion = :id_produccion";
        $stmt = $this->connect->prepare($query);

        // Vincular los parámetros de los campos válidos
        foreach ($data as $key => &$value) {
            if (in_array($key, $validFields)) {
                if ($key === 'fk_id_cultivo' || $key === 'fk_id_lote' || $key === 'cantidad_producida') {
                    $stmt->bindParam(":$key", $value, PDO::PARAM_INT);
                } else {
                    $stmt->bindParam(":$key", $value);
                }
            }
        }
        $stmt->bindParam(":id_produccion", $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function delete() {
        $query = "DELETE FROM " . $this->table . " WHERE id_produccion = :id_produccion";
        $stmt = $this->connect->prepare($query);
        $stmt->bindParam(":id_produccion", $this->id_produccion, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>