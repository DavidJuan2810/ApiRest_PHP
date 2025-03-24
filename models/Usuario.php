<?php
class Usuario {
    private $connect;
    private $table = "usuarios";

    public $identificacion;
    public $nombre;
    public $contrasena;
    public $email;
    public $fk_id_rol;

    public function __construct($db) {
        $this->connect = $db;
    }

    public function getAll() {
        $query = "SELECT usuarios.*, Rol.nombre_rol FROM " . $this->table . " INNER JOIN Rol ON usuarios.fk_id_rol = Rol.id_rol";
        $stmt = $this->connect->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function getById($id) {
        $query = "SELECT usuarios.*, Rol.nombre_rol FROM " . $this->table . " INNER JOIN Rol ON usuarios.fk_id_rol = Rol.id_rol WHERE usuarios.identificacion = :id";
        $stmt = $this->connect->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table . " (identificacion, nombre, contrasena, email, fk_id_rol) VALUES (:identificacion, :nombre, :contrasena, :email, :fk_id_rol)";
        $stmt = $this->connect->prepare($query);
        $this->contrasena = password_hash($this->contrasena, PASSWORD_BCRYPT); 
        $stmt->bindParam(":identificacion", $this->identificacion);
        $stmt->bindParam(":nombre", $this->nombre);
        $stmt->bindParam(":contrasena", $this->contrasena);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":fk_id_rol", $this->fk_id_rol);
        return $stmt->execute();
    }

    public function update() {
        $query = "UPDATE usuarios SET nombre = :nombre, email = :email, fk_id_rol = :fk_id_rol " .
                 (!empty($this->contrasena) ? ", contrasena = :contrasena " : "") .
                 "WHERE identificacion = :identificacion";
        
        $stmt = $this->connect->prepare($query);
        
        $stmt->bindParam(":identificacion", $this->identificacion);
        $stmt->bindParam(":nombre", $this->nombre);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":fk_id_rol", $this->fk_id_rol);
        
        if (!empty($this->contrasena)) {
            $this->contrasena = password_hash($this->contrasena, PASSWORD_BCRYPT);
            $stmt->bindParam(":contrasena", $this->contrasena);
        }
    
        return $stmt->execute();
    }
    
    public function delete($id) {
        $query = "DELETE FROM usuarios WHERE identificacion = :id";
        $stmt = $this->connect->prepare($query);
        $stmt->bindParam(":id", $id); // Corrección: el parámetro debe coincidir con el nombre en la consulta
        return $stmt->execute();
    }

    public function login($email, $contrasena) {
        $query = "SELECT identificacion, nombre, contrasena, email, fk_id_rol FROM " . $this->table . " WHERE LOWER(email) = LOWER(:email) LIMIT 1";
        $stmt = $this->connect->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
    
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (password_verify($contrasena, $row['contrasena'])) {
                $this->identificacion = $row['identificacion'];
                $this->nombre = $row['nombre'];
                $this->email = $row['email'];
                $this->fk_id_rol = $row['fk_id_rol'];
                return true;
            }
        }
        return false;
    }
}
?>