<?php

require_once 'conexion.php';



class Sesion {

    private $db;

    private $table = 'sesiones';



    public $id;

    public $usuario_id;

    public $token;

    public $fecha_expiracion;



    public function __construct() {

        $this->db = (new Database())->connect();

    }



    // Crear sesión

    public function crear($usuario_id) {

        $this->usuario_id = $usuario_id;

        $this->token = bin2hex(random_bytes(64));

        $this->fecha_expiracion = date('Y-m-d H:i:s', strtotime('+1 week'));

        

        $query = 'INSERT INTO ' . $this->table . ' 

                  (usuario_id, token, fecha_expiracion)

                  VALUES (:usuario_id, :token, :fecha_expiracion)';

        

        $stmt = $this->db->prepare($query);

        

        $stmt->bindParam(':usuario_id', $this->usuario_id);

        $stmt->bindParam(':token', $this->token);

        $stmt->bindParam(':fecha_expiracion', $this->fecha_expiracion);

        

        if($stmt->execute()) {

            return $this->token;

        }

        

        return false;

    }



    // Validar sesión

    public function validar($token) {

        $query = 'SELECT * FROM ' . $this->table . ' 

                  WHERE token = :token

                  AND fecha_expiracion > NOW() 

                  LIMIT 1';

        

        $stmt = $this->db->prepare($query);

        $stmt->bindParam(':token', $token);

        $stmt->execute();

        

        return $stmt->fetch(PDO::FETCH_ASSOC);

    }



    // Eliminar sesión

    public function eliminar($token) {

        $query = 'DELETE FROM ' . $this->table . ' WHERE token = :token';

        $stmt = $this->db->prepare($query);

        $stmt->bindParam(':token', $token);

        return $stmt->execute();

    }

    

    // Nuevo: Obtener usuario desde token

    public function obtenerUsuario($token) {

        $query = 'SELECT u.* 

                  FROM ' . $this->table . ' s

                  JOIN usuarios u ON s.usuario_id = u.id

                  WHERE s.token = :token

                  AND s.fecha_expiracion > NOW()

                  LIMIT 1';

        

        $stmt = $this->db->prepare($query);

        $stmt->bindParam(':token', $token);

        $stmt->execute();

        

        return $stmt->fetch(PDO::FETCH_ASSOC);

    }

}