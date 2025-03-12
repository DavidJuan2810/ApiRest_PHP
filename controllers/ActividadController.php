<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Actividad.php';

class ActividadController {
    private $db;
    private $actividad;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->actividad = new Actividad($this->db);
    }

    public function getAll() {
        $stmt = $this->actividad->getAll();
        $actividad = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(["status" => "200", "data" => $actividad]);
    }

    public function create() {
        $data = json_decode(file_get_contents("php://input"), true);
        if (!isset($data['descripcion']) || !isset($data['nombre_actividad'])) {
            echo json_encode(["status" => "Error", "message" => "Datos incompletos"]);
            return;
        }

        $this->actividad->descripcion = $data['descripcion'];
        $this->actividad->nombre_actividad = $data['nombre_actividad'];

        if ($this->actividad->create()) {
            echo json_encode(["status" => "201", "message" => "Actividad creada"]);
        } else {
            echo json_encode(["status" => "Error", "message" => "Error al crear"]);
        }
    }

    public function update($id) {
        $data = json_decode(file_get_contents("php://input"), true);
    
        if (!isset($data['descripcion'], $data['nombre_actividad'])) {
            echo json_encode(["status" => "Error", "message" => "Datos incompletos"]);
            return;
        }
    
        $this->actividad->id_actividad = $id;
        $this->actividad->descripcion = $data['descripcion'];
        $this->actividad->nombre_actividad = $data['nombre_actividad'];
    
        if ($this->actividad->update()) {
            echo json_encode(["status" => "200", "message" => "Actividad actualizada"]);
        } else {
            echo json_encode(["status" => "Error", "message" => "Error al actualizar"]);
        }
    }    

    public function delete($id) {
        $query = "DELETE FROM actividad WHERE id_actividad = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
    
        if ($stmt->execute()) {
            echo json_encode(["status" => "200", "message" => "Actividad eliminada correctamente"]);
        } else {
            echo json_encode(["status" => "Error", "message" => "No se pudo eliminar la Actividad"]);
        }
    }

}
?>