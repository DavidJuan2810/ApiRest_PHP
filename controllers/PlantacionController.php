<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Plantacion.php';

class PlantacionController {
    private $db;
    private $plantacion;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->plantacion = new Plantacion($this->db);
    }

    public function getAll() {
        $stmt = $this->plantacion->getAll();
        $plantaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(["status" => "200", "data" => $plantaciones]);
    }

    public function getById($id) {
        $plantacion = $this->plantacion->getById($id);
        if ($plantacion) {
            echo json_encode(["status" => "200", "data" => $plantacion]);
        } else {
            echo json_encode(["status" => "Error", "message" => "Plantación no encontrada"]);
            http_response_code(404);
        }
    }

    public function create() {
        $data = json_decode(file_get_contents("php://input"), true);
        
        if (!isset($data['fk_id_cultivo'], $data['fk_id_era'])) {
            echo json_encode(["status" => "Error", "message" => "Datos incompletos"]);
            return;
        }

        $this->plantacion->fk_id_cultivo = $data['fk_id_cultivo'];
        $this->plantacion->fk_id_era = $data['fk_id_era'];

        if ($this->plantacion->create()) {
            echo json_encode(["status" => "201", "message" => "Plantación creada"]);
        } else {
            echo json_encode(["status" => "Error", "message" => "Error al crear la plantación"]);
        }
    }

    public function update($id) {
        $data = json_decode(file_get_contents("php://input"), true);
        
        if (!isset($data['fk_id_cultivo'], $data['fk_id_era'])) {
            echo json_encode(["status" => "Error", "message" => "Datos incompletos"]);
            return;
        }

        $this->plantacion->id_plantacion = $id;
        $this->plantacion->fk_id_cultivo = $data['fk_id_cultivo'];
        $this->plantacion->fk_id_era = $data['fk_id_era'];

        if ($this->plantacion->update()) {
            echo json_encode(["status" => "200", "message" => "Plantación actualizada"]);
        } else {
            echo json_encode(["status" => "Error", "message" => "Error al actualizar la plantación"]);
        }
    }

    public function patch($id) {
        $data = json_decode(file_get_contents("php://input"), true);

        if (empty($data)) {
            echo json_encode(["status" => "Error", "message" => "No hay datos para actualizar"]);
            http_response_code(400);
            return;
        }

        $plantacionExistente = $this->plantacion->getById($id);
        if (!$plantacionExistente) {
            echo json_encode(["status" => "Error", "message" => "Plantación no encontrada"]);
            http_response_code(404);
            return;
        }

        // Verificar si hay campos válidos antes de intentar la actualización
        $validFields = ['fk_id_cultivo', 'fk_id_era'];
        $hasValidFields = false;
        foreach ($data as $key => $value) {
            if (in_array($key, $validFields)) {
                $hasValidFields = true;
                break;
            }
        }

        if (!$hasValidFields) {
            echo json_encode(["status" => "Error", "message" => "No se proporcionaron campos válidos para actualizar (solo fk_id_cultivo y fk_id_era son permitidos)"]);
            http_response_code(400);
            return;
        }

        if ($this->plantacion->patch($id, $data)) {
            echo json_encode(["status" => "200", "message" => "Plantación actualizada parcialmente"]);
        } else {
            echo json_encode(["status" => "Error", "message" => "Error al actualizar la plantación"]);
            http_response_code(500);
        }
    }

    public function delete($id) {
        $query = "DELETE FROM plantacion WHERE id_plantacion = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo json_encode(["status" => "200", "message" => "Plantación eliminada correctamente"]);
        } else {
            echo json_encode(["status" => "Error", "message" => "No se pudo eliminar la plantación"]);
        }
    }
}
?>