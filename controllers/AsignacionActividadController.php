<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/asignacion_actividad.php';

class AsignacionActividadController {
    private $db;
    private $asignacion_actividad;
   

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->asignacion_actividad = new AsignacionActividad($this->db);
    }

    public function getAll() {
        $stmt = $this->asignacion_actividad->getAll();
        $asignacion_actividad = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(["status" => "200", "data" => $asignacion_actividad]);
    }
    public function getById($id) {
        $stmt = $this->asignacion_actividad->getById($id);
        $asignacion = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($asignacion) {
            echo json_encode(["status" => "200", "data" => $asignacion]);
        } else {
            echo json_encode(["status" => "Error", "message" => "Asignación no encontrada"]);
            http_response_code(404);
        }
    }

    public function create() {
        $data = json_decode(file_get_contents("php://input"), true);
        if (!isset($data['fecha'], $data['fk_identificacion'], $data['fk_id_actividad'])) {
            echo json_encode(["status" => "Error", "message" => "Datos incompletos"]);
            return;
        }

        $this->asignacion_actividad->fecha = $data['fecha'];
        $this->asignacion_actividad->fk_identificacion = $data['fk_identificacion'];
        $this->asignacion_actividad->fk_id_actividad = $data['fk_id_actividad'];

        if ($this->asignacion_actividad->create()) {
            echo json_encode(["status" => "200", "message" => "Actividad asignada correctamente"]);
        } else {
            echo json_encode(["status" => "Error", "message" => "Error al asignar actividad"]);
        }
    }

    
    public function update($id) {
        $data = json_decode(file_get_contents("php://input"), true);
    
        if (!isset($data['fecha'], $data['fk_identificacion'], $data['fk_id_actividad'])) {
            echo json_encode(["status" => "Error", "message" => "Datos incompletos"]);
            return;
        }
    
        $this->asignacion_actividad->id_asignacion_actividad = $id; 
        $this->asignacion_actividad->fecha = $data['fecha'];
        $this->asignacion_actividad->fk_identificacion = $data['fk_identificacion'];
        $this->asignacion_actividad->fk_id_actividad = $data['fk_id_actividad'];
    
        if ($this->asignacion_actividad->update()) {
            echo json_encode(["status" => "200", "message" => "Actividad Asignada actualizada"]);
        } else {
            echo json_encode(["status" => "Error", "message" => "Error al actualizar"]);
        }
    }
    public function patch($id) {
        $data = json_decode(file_get_contents("php://input"), true);

        if (empty($data)) {
            echo json_encode(["status" => "Error", "message" => "No hay datos para actualizar"]);
            return;
        }

        if ($this->asignacion_actividad->patch($id, $data)) {
            echo json_encode(["status" => "200", "message" => "Asignación actualizada parcialmente"]);
        } else {
            echo json_encode(["status" => "Error", "message" => "Error al actualizar"]);
        }
    }

    
    public function delete($id) {
        $query = "DELETE FROM asignacion_actividad WHERE id_asignacion_actividad = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
    
        if ($stmt->execute()) {
            echo json_encode(["status" => "200", "message" => "Asignacion de actividad eliminada correctamente"]);
        } else {
            echo json_encode(["status" => "Error", "message" => "No se pudo eliminar la Asignacion de actividad"]);
        }
    }
}