<?php
class UsuarioController {
    private $db;
    private $usuario;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->usuario = new Usuario($this->db);
    }

    public function getAll() {
        $stmt = $this->usuario->getAll();
        $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
        http_response_code(200);
        echo json_encode(["status" => "200", "data" => $usuarios]);
    }

    public function getById($id) {
        // El modelo no tiene un método getById, así que usamos getAll con un filtro
        $this->usuario->identificacion = $id;
        $stmt = $this->usuario->getAll(); // Esto devuelve todos, pero podemos filtrar
        $usuario = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Filtrar manualmente por identificacion (mejoraría si el modelo tuviera un método getById)
        $usuario = array_filter($usuario, function($u) use ($id) {
            return $u['identificacion'] == $id;
        });

        if (!empty($usuario)) {
            http_response_code(200);
            echo json_encode(["status" => "200", "data" => array_values($usuario)[0]]);
        } else {
            http_response_code(404);
            echo json_encode(["status" => "Error", "message" => "Usuario no encontrado"]);
        }
    }

    public function create() {
        $data = json_decode(file_get_contents("php://input"), true);
        if (!isset($data['identificacion'], $data['nombre'], $data['contrasena'], $data['email'], $data['fk_id_rol'])) {
            http_response_code(400);
            echo json_encode(["status" => "Error", "message" => "Datos incompletos"]);
            return;
        }

        $this->usuario->identificacion = $data['identificacion'];
        $this->usuario->nombre = $data['nombre'];
        $this->usuario->contrasena = $data['contrasena']; // El modelo se encarga del hash
        $this->usuario->email = $data['email'];
        $this->usuario->fk_id_rol = $data['fk_id_rol'];

        if ($this->usuario->create()) {
            http_response_code(201);
            echo json_encode(["status" => "201", "message" => "Usuario creado"]);
        } else {
            http_response_code(500);
            echo json_encode(["status" => "Error", "message" => "Error al crear"]);
        }
    }

    public function update($id) {
        $data = json_decode(file_get_contents("php://input"), true);
        
        if (!isset($data['nombre'], $data['email'], $data['fk_id_rol'])) {
            http_response_code(400);
            echo json_encode(["status" => "Error", "message" => "Datos incompletos"]);
            return;
        }
        
        $this->usuario->identificacion = $id;
        $this->usuario->nombre = $data['nombre'];
        $this->usuario->email = $data['email'];
        $this->usuario->fk_id_rol = $data['fk_id_rol'];
        $this->usuario->contrasena = !empty($data['contrasena']) ? $data['contrasena'] : null; // El modelo se encarga del hash

        if ($this->usuario->update()) {
            http_response_code(200);
            echo json_encode(["status" => "200", "message" => "Usuario actualizado"]);
        } else {
            http_response_code(500);
            echo json_encode(["status" => "Error", "message" => "Error al actualizar"]);
        }
    }

    public function delete($id) {
        if ($this->usuario->delete($id)) {
            http_response_code(200);
            echo json_encode(["status" => "200", "message" => "Usuario eliminado correctamente"]);
        } else {
            http_response_code(500);
            echo json_encode(["status" => "Error", "message" => "No se pudo eliminar el usuario"]);
        }
    }

    public function patch($id) {
        // Método no implementado
        http_response_code(501);
        echo json_encode(["status" => "Error", "message" => "Método PATCH no implementado"]);
    }
}