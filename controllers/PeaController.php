<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Pea.php';

class PeaController {
    private $db;
    private $pea;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->pea = new Pea($this->db);
    }

    public function getAll() {
        $stmt = $this->pea->getAll();
        $peas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(["status" => "200", "data" => $peas]);
    }

    public function getById($id) {
        $pea = $this->pea->getById($id);
        if ($pea) {
            echo json_encode(["status" => "200", "data" => $pea]);
        } else {
            echo json_encode(["status" => "Error", "message" => "PEA no encontrado"]);
            http_response_code(404);
        }
    }

    public function create() {
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['nombre'], $data['descripcion'])) {
            echo json_encode(["status" => "Error", "message" => "Datos incompletos"]);
            return;
        }

        $this->pea->nombre = $data['nombre'];
        $this->pea->descripcion = $data['descripcion'];

        if ($this->pea->create()) {
            echo json_encode(["status" => "200", "message" => "PEA creado"]);
        } else {
            echo json_encode(["status" => "Error", "message" => "Error al crear"]);
        }
    }

    public function update($id) {
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['nombre'], $data['descripcion'])) {
            echo json_encode(["status" => "Error", "message" => "Datos incompletos"]);
            return;
        }

        $this->pea->id_pea = $id;
        $this->pea->nombre = $data['nombre'];
        $this->pea->descripcion = $data['descripcion'];

        if ($this->pea->update()) {
            echo json_encode(["status" => "200", "message" => "PEA actualizado"]);
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

        $peaExistente = $this->pea->getById($id);
        if (!$peaExistente) {
            echo json_encode(["status" => "Error", "message" => "PEA no encontrado"]);
            http_response_code(404);
            return;
        }

        if ($this->pea->patch($id, $data)) {
            echo json_encode(["status" => "200", "message" => "PEA actualizado parcialmente"]);
        } else {
            echo json_encode(["status" => "Error", "message" => "Error al actualizar el PEA"]);
            http_response_code(500);
        }
    }

    public function delete($id) {
        $query = "DELETE FROM PEA WHERE id_pea = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo json_encode(["status" => "200", "message" => "PEA eliminado correctamente"]);
        } else {
            echo json_encode(["status" => "Error", "message" => "No se pudo eliminar el PEA"]);
        }
    }
}
?>