<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtMiddleware {
    public static function validate() {
        $headers = apache_request_headers();
        $secret_key = "your_secret_key_123"; // La misma clave secreta usada al generar el token

        if (isset($headers['Authorization'])) {
            $auth_header = $headers['Authorization'];
            $token = str_replace('Bearer ', '', $auth_header);

            try {
                // Decodificar el token
                $decoded = JWT::decode($token, new Key($secret_key, 'HS256'));
                return $decoded; // Devuelve los datos del token (como el ID del usuario)
            } catch (Exception $e) {
                // Token inválido o expirado
                http_response_code(401);
                echo json_encode(['status' => 'Error', 'message' => 'Token inválido o expirado']);
                exit();
            }
        } else {
            // No se proporcionó el token
            http_response_code(401);
            echo json_encode(['status' => 'Error', 'message' => 'Token requerido']);
            exit();
        }
    }
}
?>