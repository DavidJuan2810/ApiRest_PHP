<?php
class Plantacion {
    private $connect;
    private $table = "plantacion";

    public $id_plantacion;
    public $fk_id_cultivo;
    public $fk_id_era;

    public function __construct($db) {
        $this->connect = $db;
    }

    public function getAll() {
        $query = "SELECT plantacion.*, cultivo.nombre_cultivo, eras.descripcion 
                  FROM " . $this->table . " 
                  INNER JOIN cultivo ON plantacion.fk_id_cultivo = cultivo.id_cultivo 
                  INNER JOIN eras ON plantacion.fk_id_era = eras.id_eras";
        $stmt = $this->connect->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function getById($id) {
        $query = "SELECT plantacion.*, cultivo.nombre_cultivo, eras.descripcion 
                  FROM " . $this->table . " 
                  INNER JOIN cultivo ON plantacion.fk_id_cultivo = cultivo.id_cultivo 
                  INNER JOIN eras ON plantacion.fk_id_era = eras.id_eras 
                  WHERE plantacion.id_plantacion = :id_plantacion";
        $stmt = $this->connect->prepare($query);
        $stmt->bindParam(":id_plantacion", $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create() {
        $query = "INSERT INTO " . $this->table . " (fk_id_cultivo, fk_id_era) 
                  VALUES (:fk_id_cultivo, :fk_id_era)";
        
        $stmt = $this->connect->prepare($query);
        $stmt->bindParam(":fk_id_cultivo", $this->fk_id_cultivo);
        $stmt->bindParam(":fk_id_era", $this->fk_id_era);

        return $stmt->execute();
    }

    public function update() {
        $query = "UPDATE " . $this->table . " 
                  SET fk_id_cultivo = :fk_id_cultivo, 
                      fk_id_era = :fk_id_era
                  WHERE id_plantacion = :id_plantacion";

        $stmt = $this->connect->prepare($query);
        $stmt->bindParam(":id_plantacion", $this->id_plantacion);
        $stmt->bindParam(":fk_id_cultivo", $this->fk_id_cultivo);
        $stmt->bindParam(":fk_id_era", $this->fk_id_era);

        return $stmt->execute();
    }

    public function patch($id, $data) {
        // Definir los campos válidos que se pueden actualizar en la tabla plantacion
        $validFields = ['fk_id_cultivo', 'fk_id_era'];
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

        $query = "UPDATE " . $this->table . " SET " . implode(", ", $fields) . " WHERE id_plantacion = :id_plantacion";
        $stmt = $this->connect->prepare($query);

        // Vincular los parámetros de los campos válidos
        foreach ($data as $key => &$value) {
            if (in_array($key, $validFields)) {
                $stmt->bindParam(":$key", $value);
            }
        }
        $stmt->bindParam(":id_plantacion", $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function delete() {
        $query = "DELETE FROM " . $this->table . " WHERE id_plantacion = :id_plantacion";
        $stmt = $this->connect->prepare($query);
        $stmt->bindParam(":id_plantacion", $this->id_plantacion);
        return $stmt->execute();
    }
}
?>