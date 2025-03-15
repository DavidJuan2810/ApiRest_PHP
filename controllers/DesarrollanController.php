<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Desarrollan.php';

class DesarrollanController {
    private $db;
    private $desarrollan;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->desarrollan = new Desarrollan($this->db);
    }

    public function getAll() {
        $stmt = $this->desarrollan->getAll();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(["status" => "200", "data" => $data]);
    }
    public function getById($id) {
        if (!$id) {
            echo json_encode(["status" => "Error", "message" => "ID no proporcionado"]);
            return;
        }

        $stmt = $this->desarrollan->getById($id);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            echo json_encode(["status" => "200", "data" => $result]);
        } else {
            echo json_encode(["status" => "404", "message" => "No se encontró el registro"]);
        }
    }

    public function create() {
        $data = json_decode(file_get_contents("php://input"), true);
        
        if (!isset($data['fk_id_cultivo'], $data['fk_id_pea'])) {
            echo json_encode(["status" => "Error", "message" => "Datos incompletos"]);
            return;
        }

        $this->desarrollan->fk_id_cultivo = $data['fk_id_cultivo'];
        $this->desarrollan->fk_id_pea = $data['fk_id_pea'];

        if ($this->desarrollan->create()) {
            echo json_encode(["status" => "201", "message" => "Registro creado"]);
        } else {
            echo json_encode(["status" => "Error", "message" => "Error al crear"]);
        }
    }

    public function update($id) {
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['fk_id_cultivo'], $data['fk_id_pea'])) {
            echo json_encode(["status" => "Error", "message" => "Datos incompletos"]);
            return;
        }

        $this->desarrollan->id_desarrollan = $id;
        $this->desarrollan->fk_id_cultivo = $data['fk_id_cultivo'];
        $this->desarrollan->fk_id_pea = $data['fk_id_pea'];

        if ($this->desarrollan->update()) {
            echo json_encode(["status" => "200", "message" => "Registro actualizado"]);
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

        if ($this->desarrollan->patch($id, $data)) {
            echo json_encode(["status" => "200", "message" => "Registro actualizado parcialmente"]);
        } else {
            echo json_encode(["status" => "Error", "message" => "Error al actualizar"]);
        }
    }

    public function delete($id) {
        $this->desarrollan->id_desarrollan = $id;

        if ($this->desarrollan->delete()) {
            echo json_encode(["status" => "200", "message" => "Registro eliminado"]);
        } else {
            echo json_encode(["status" => "Error", "message" => "No se pudo eliminar"]);
        }
    }
}
?>
