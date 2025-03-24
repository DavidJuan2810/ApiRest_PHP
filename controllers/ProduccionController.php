<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Produccion.php';

class ProduccionController {
    private $db;
    private $produccion;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->produccion = new Produccion($this->db);
    }

    public function getAll() {
        $stmt = $this->produccion->getAll();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(["status" => "200", "data" => $data]);
    }

    public function getById($id) {
        $produccion = $this->produccion->getById($id);
        if ($produccion) {
            echo json_encode(["status" => "200", "data" => $produccion]);
        } else {
            echo json_encode(["status" => "Error", "message" => "Producción no encontrada"]);
            http_response_code(404);
        }
    }

    public function create() {
        $data = json_decode(file_get_contents("php://input"), true);
        
        if (!isset($data['fk_id_cultivo'], $data['cantidad_producida'], $data['fecha_produccion'], $data['fk_id_lote'], $data['estado'])) {
            echo json_encode(["status" => "Error", "message" => "Datos incompletos"]);
            return;
        }

        $this->produccion->fk_id_cultivo = $data['fk_id_cultivo'];
        $this->produccion->cantidad_producida = $data['cantidad_producida'];
        $this->produccion->fecha_produccion = $data['fecha_produccion'];
        $this->produccion->fk_id_lote = $data['fk_id_lote'];
        $this->produccion->descripcion_produccion = $data['descripcion_produccion'] ?? null;
        $this->produccion->estado = $data['estado'];
        $this->produccion->fecha_cosecha = $data['fecha_cosecha'] ?? null;

        if ($this->produccion->create()) {
            echo json_encode(["status" => "201", "message" => "Producción registrada"]);
        } else {
            echo json_encode(["status" => "Error", "message" => "Error al registrar producción"]);
        }
    }

    public function update($id) {
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['fk_id_cultivo'], $data['cantidad_producida'], $data['fecha_produccion'], $data['fk_id_lote'], $data['estado'])) {
            echo json_encode(["status" => "Error", "message" => "Datos incompletos"]);
            return;
        }

        $this->produccion->id_produccion = $id;
        $this->produccion->fk_id_cultivo = $data['fk_id_cultivo'];
        $this->produccion->cantidad_producida = $data['cantidad_producida'];
        $this->produccion->fecha_produccion = $data['fecha_produccion'];
        $this->produccion->fk_id_lote = $data['fk_id_lote'];
        $this->produccion->descripcion_produccion = $data['descripcion_produccion'] ?? null;
        $this->produccion->estado = $data['estado'];
        $this->produccion->fecha_cosecha = $data['fecha_cosecha'] ?? null;

        if ($this->produccion->update()) {
            echo json_encode(["status" => "200", "message" => "Producción actualizada"]);
        } else {
            echo json_encode(["status" => "Error", "message" => "Error al actualizar producción"]);
        }
    }

    public function patch($id) {
        $data = json_decode(file_get_contents("php://input"), true);

        if (empty($data)) {
            echo json_encode(["status" => "Error", "message" => "No hay datos para actualizar"]);
            http_response_code(400);
            return;
        }

        $produccionExistente = $this->produccion->getById($id);
        if (!$produccionExistente) {
            echo json_encode(["status" => "Error", "message" => "Producción no encontrada"]);
            http_response_code(404);
            return;
        }

        if ($this->produccion->patch($id, $data)) {
            echo json_encode(["status" => "200", "message" => "Producción actualizada parcialmente"]);
        } else {
            // Verificar si no se proporcionaron campos válidos
            $validFields = ['fk_id_cultivo', 'cantidad_producida', 'fecha_produccion', 'fk_id_lote', 'descripcion_produccion', 'estado', 'fecha_cosecha'];
            $hasValidFields = false;
            foreach ($data as $key => $value) {
                if (in_array($key, $validFields)) {
                    $hasValidFields = true;
                    break;
                }
            }

            if (!$hasValidFields) {
                echo json_encode(["status" => "Error", "message" => "No se proporcionaron campos válidos para actualizar"]);
                http_response_code(400);
            } else {
                echo json_encode(["status" => "Error", "message" => "Error al actualizar la producción"]);
                http_response_code(500);
            }
        }
    }

    public function delete($id) {
        $this->produccion->id_produccion = $id;

        if ($this->produccion->delete()) {
            echo json_encode(["status" => "200", "message" => "Producción eliminada"]);
        } else {
            echo json_encode(["status" => "Error", "message" => "No se pudo eliminar la producción"]);
        }
    }
}
?>