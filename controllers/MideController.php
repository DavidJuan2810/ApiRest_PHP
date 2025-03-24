<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Mide.php';

class MideController {
    private $db;
    private $mide;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->mide = new Mide($this->db);
    }

    public function getAll() {
        $stmt = $this->mide->getAll();
        $mideData = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(["status" => "200", "data" => $mideData]);
    }

    public function getById($id) {
        $mide = $this->mide->getById($id);
        if ($mide) {
            echo json_encode(["status" => "200", "data" => $mide]);
        } else {
            echo json_encode(["status" => "Error", "message" => "Registro en mide no encontrado"]);
            http_response_code(404);
        }
    }

    public function create() {
        $data = json_decode(file_get_contents("php://input"), true);
        if (!isset($data['fk_id_sensor'], $data['fk_id_era'])) {
            echo json_encode(["status" => "Error", "message" => "Datos incompletos"]);
            return;
        }

        $this->mide->fk_id_sensor = $data['fk_id_sensor'];
        $this->mide->fk_id_era = $data['fk_id_era'];

        if ($this->mide->create()) {
            echo json_encode(["status" => "200", "message" => "Registro en mide creado"]);
        } else {
            echo json_encode(["status" => "Error", "message" => "Error al crear"]);
        }
    }

    public function update($id) {
        $data = json_decode(file_get_contents("php://input"), true);
        if (!isset($data['fk_id_sensor'], $data['fk_id_era'])) {
            echo json_encode(["status" => "Error", "message" => "Datos incompletos"]);
            return;
        }

        $this->mide->id_mide = $id;
        $this->mide->fk_id_sensor = $data['fk_id_sensor'];
        $this->mide->fk_id_era = $data['fk_id_era'];

        if ($this->mide->update()) {
            echo json_encode(["status" => "200", "message" => "Registro en mide actualizado"]);
        } else {
            echo json_encode(["status" => "Error", "message" => "Error al actualizar"]);
        }
    }

    public function patch($id) {
        $data = json_decode(file_get_contents("php://input"), true);

        if (empty($data)) {
            echo json_encode(["status" => "Error", "message" => "No hay datos para actualizar"]);
            http_response_code(400);
            return;
        }

        $mideExistente = $this->mide->getById($id);
        if (!$mideExistente) {
            echo json_encode(["status" => "Error", "message" => "Registro en mide no encontrado"]);
            http_response_code(404);
            return;
        }

        if ($this->mide->patch($id, $data)) {
            echo json_encode(["status" => "200", "message" => "Registro en mide actualizado parcialmente"]);
        } else {
            echo json_encode(["status" => "Error", "message" => "Error al actualizar el registro"]);
            http_response_code(500);
        }
    }

    public function delete($id) {
        if ($this->mide->delete($id)) {
            echo json_encode(["status" => "200", "message" => "Registro en mide eliminado"]);
        } else {
            echo json_encode(["status" => "Error", "message" => "Error al eliminar"]);
        }
    }
}
?>