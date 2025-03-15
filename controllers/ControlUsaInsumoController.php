<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/ControlUsaInsumo.php';

class ControlUsaInsumoController {
    private $db;
    private $control_usa_insumo;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->control_usa_insumo = new ControlUsaInsumo($this->db); // Inicializar correctamente
    }

    public function getAll() {
        $stmt = $this->control_usa_insumo->getAll();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(["status" => "200", "data" => $data]);
    }
    
    public function getById($id) {
        if (!$id) {
            echo json_encode(["status" => "Error", "message" => "ID no proporcionado"]);
            return;
        }

        $stmt = $this->control_usa_insumo->getById($id);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            echo json_encode(["status" => "200", "data" => $result]);
        } else {
            echo json_encode(["status" => "404", "message" => "No se encontró el registro"]);
        }
    }

    public function create() {
        $data = json_decode(file_get_contents("php://input"), true);
        if (!isset($data['fk_id_insumo'], $data['fk_id_control_fitosanitario'], $data['cantidad'])) {
            echo json_encode(["status" => "Error", "message" => "Datos incompletos"]);
            return;
        }

        $this->control_usa_insumo->fk_id_insumo = $data['fk_id_insumo'];
        $this->control_usa_insumo->fk_id_control_fitosanitario = $data['fk_id_control_fitosanitario'];
        $this->control_usa_insumo->cantidad = $data['cantidad'];

        if ($this->control_usa_insumo->create()) {
            echo json_encode(["status" => "201", "message" => "Registro creado"]);
        } else {
            echo json_encode(["status" => "Error", "message" => "Error al crear"]);
        }
    }

    public function update($id) {
        $data = json_decode(file_get_contents("php://input"), true);
        if (!isset($data['fk_id_insumo'], $data['fk_id_control_fitosanitario'], $data['cantidad'])) {
            echo json_encode(["status" => "Error", "message" => "Datos incompletos"]);
            return;
        }

        $this->control_usa_insumo->id_control_usa_insumo = $id;
        $this->control_usa_insumo->fk_id_insumo = $data['fk_id_insumo'];
        $this->control_usa_insumo->fk_id_control_fitosanitario = $data['fk_id_control_fitosanitario'];
        $this->control_usa_insumo->cantidad = $data['cantidad'];

        if ($this->control_usa_insumo->update()) {
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

        if ($this->control_usa_insumo->patch($id, $data)) {
            echo json_encode(["status" => "200", "message" => "Registro actualizado parcialmente"]);
        } else {
            echo json_encode(["status" => "Error", "message" => "Error al actualizar"]);
        }
    }

    public function delete($id) {
        if ($this->control_usa_insumo->delete($id)) {
            echo json_encode(["status" => "200", "message" => "Registro eliminado"]);
        } else {
            echo json_encode(["status" => "Error", "message" => "Error al eliminar"]);
        }
    }
}
?>
