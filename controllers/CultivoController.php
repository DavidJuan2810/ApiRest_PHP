<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Cultivo.php';

class CultivoController {
    private $db;
    private $cultivo;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->cultivo = new Cultivo($this->db);
    }

    public function getAll() {
        $stmt = $this->cultivo->getAll();
        $cultivos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(["status" => "200", "data" => $cultivos]);
    }
    public function getById($id) {
        if (!$id) {
            echo json_encode(["status" => "Error", "message" => "ID no proporcionado"]);
            return;
        }

        $stmt = $this->cultivo->getById($id);
        $cultivo = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($cultivo) {
            echo json_encode(["status" => "200", "data" => $cultivo]);
        } else {
            echo json_encode(["status" => "404", "message" => "Cultivo no encontrado"]);
        }
    }

    public function create() {
        $data = json_decode(file_get_contents("php://input"), true);
        
        if (!isset($data['fecha_plantacion'], $data['nombre_cultivo'], $data['descripcion'], $data['fk_id_especie'], $data['fk_id_semillero'])) {
            echo json_encode(["status" => "Error", "message" => "Datos incompletos"]);
            return;
        }

        $this->cultivo->fecha_plantacion = $data['fecha_plantacion'];
        $this->cultivo->nombre_cultivo = $data['nombre_cultivo'];
        $this->cultivo->descripcion = $data['descripcion'];
        $this->cultivo->fk_id_especie = $data['fk_id_especie'];
        $this->cultivo->fk_id_semillero = $data['fk_id_semillero'];

        if ($this->cultivo->create()) {
            echo json_encode(["status" => "201", "message" => "Cultivo creado"]);
        } else {
            echo json_encode(["status" => "Error", "message" => "Error al crear el cultivo"]);
        }
    }

    public function update($id) {
        $data = json_decode(file_get_contents("php://input"), true);
        
        if (!isset($data['fecha_plantacion'], $data['nombre_cultivo'], $data['descripcion'], $data['fk_id_especie'], $data['fk_id_semillero'])) {
            echo json_encode(["status" => "Error", "message" => "Datos incompletos"]);
            return;
        }

        $this->cultivo->id_cultivo = $id;
        $this->cultivo->fecha_plantacion = $data['fecha_plantacion'];
        $this->cultivo->nombre_cultivo = $data['nombre_cultivo'];
        $this->cultivo->descripcion = $data['descripcion'];
        $this->cultivo->fk_id_especie = $data['fk_id_especie'];
        $this->cultivo->fk_id_semillero = $data['fk_id_semillero'];

        if ($this->cultivo->update()) {
            echo json_encode(["status" => "200", "message" => "Cultivo actualizado"]);
        } else {
            echo json_encode(["status" => "Error", "message" => "Error al actualizar el cultivo"]);
        }
    }
    public function patch($id) {
        $data = json_decode(file_get_contents("php://input"), true);
    
        if (empty($data)) {
            echo json_encode(["status" => "Error", "message" => "No hay datos para actualizar"]);
            return;
        }
    
        if ($this->cultivo->patch($id, $data)) {
            echo json_encode(["status" => "200", "message" => "Cultivo actualizado parcialmente"]);
        } else {
            echo json_encode(["status" => "Error", "message" => "Error al actualizar"]);
        }
    }

    public function delete($id) {
        $query = "DELETE FROM cultivo WHERE id_cultivo = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo json_encode(["status" => "200", "message" => "Cultivo eliminado correctamente"]);
        } else {
            echo json_encode(["status" => "Error", "message" => "No se pudo eliminar el cultivo"]);
        }
    }
}
?>
