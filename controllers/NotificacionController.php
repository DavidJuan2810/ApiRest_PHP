<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Notificacion.php';

class NotificacionController {
    private $db;
    private $notificacion;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->notificacion = new Notificacion($this->db);
    }

    public function getAll() {
        $stmt = $this->notificacion->getAll();
        $notificaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(["status" => "200", "data" => $notificaciones]);
    }

    public function create() {
        $data = json_decode(file_get_contents("php://input"), true);
        if (!isset($data['fk_id_programacion'], $data['mensaje'], $data['titulo'])) {
            echo json_encode(["status" => "Error", "message" => "Datos incompletos"]);
            return;
        }

        $this->notificacion->fk_id_programacion = $data['fk_id_programacion'];
        $this->notificacion->mensaje = $data['mensaje'];
        $this->notificacion->titulo = $data['titulo'];

        if ($this->notificacion->create()) {
            echo json_encode(["status" => "201", "message" => "Notificación creada correctamente"]);
        } else {
            echo json_encode(["status" => "Error", "message" => "Error al crear la notificación"]);
        }
    }

    public function update($id) {
        $data = json_decode(file_get_contents("php://input"), true);
    
        if (!isset($data['fk_id_programacion'], $data['mensaje'], $data['titulo'])) {
            echo json_encode(["status" => "Error", "message" => "Datos incompletos"]);
            return;
        }
    
        $this->notificacion->id_notificacion = $id;
        $this->notificacion->fk_id_programacion = $data['fk_id_programacion'];
        $this->notificacion->mensaje = $data['mensaje'];
        $this->notificacion->titulo = $data['titulo'];
    
        if ($this->notificacion->update()) {
            echo json_encode(["status" => "200", "message" => "Notificación actualizada correctamente"]);
        } else {
            echo json_encode(["status" => "Error", "message" => "Error al actualizar la notificación"]);
        }
    }

    public function delete($id) {
        if ($this->notificacion->delete($id)) {
            echo json_encode(["status" => "200", "message" => "Notificación eliminada correctamente"]);
        } else {
            echo json_encode(["status" => "Error", "message" => "No se pudo eliminar la notificación"]);
        }
    }
}
?>
