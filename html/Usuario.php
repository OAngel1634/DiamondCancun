<?php

require_once 'conexion.php';



class Usuario {

    private $db;

    private $table = 'usuarios';

    private $table_preferencias = 'preferencias';



    public $id;

    public $google_id;

    public $nombre;

    public $email;

    public $password;

    public $fecha_nacimiento;

    public $telefono;

    public $contacto_emergencia;

    public $direccion;

    public $foto_perfil;

    public $verificado;

    public $preferencias = [];



    public function __construct() {

        $this->db = (new Database())->connect();

    }



    // Buscar usuario por ID con sus preferencias

    public function buscarPorId($id) {

        $query = 'SELECT u.*, p.notificaciones, p.idioma, p.preferencias_gastronomicas, 

                         p.experiencia_favorita, p.genero, p.facilidades_acceso 

                  FROM ' . $this->table . ' u 

                  LEFT JOIN ' . $this->table_preferencias . ' p ON u.id = p.usuario_id 

                  WHERE u.id = :id LIMIT 1';

        

        $stmt = $this->db->prepare($query);

        $stmt->bindParam(':id', $id);

        $stmt->execute();

        

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        

        if($row) {

            // Asignar propiedades del usuario

            $this->id = $row['id'];

            $this->google_id = $row['google_id'];

            $this->nombre = $row['nombre'];

            $this->email = $row['email'];

            $this->password = $row['password'];

            $this->fecha_nacimiento = $row['fecha_nacimiento'];

            $this->telefono = $row['telefono'];

            $this->contacto_emergencia = $row['contacto_emergencia'];

            $this->direccion = $row['direccion'];

            $this->foto_perfil = $row['foto_perfil'];

            $this->verificado = $row['verificado'];

            

            // Asignar preferencias

            $this->preferencias = [

                'notificaciones' => $row['notificaciones'],

                'idioma' => $row['idioma'],

                'preferencias_gastronomicas' => $row['preferencias_gastronomicas'],

                'experiencia_favorita' => $row['experiencia_favorita'],

                'genero' => $row['genero'],

                'facilidades_acceso' => $row['facilidades_acceso']

            ];

            

            return true;

        }

        

        return false;

    }



    // Actualizar información básica

    public function actualizarBasica($data) {

        $query = 'UPDATE ' . $this->table . ' SET 

                  nombre = :nombre,

                  fecha_nacimiento = :fecha_nacimiento

                  WHERE id = :id';

        

        $stmt = $this->db->prepare($query);

        $stmt->bindParam(':nombre', $data['nombre']);

        $stmt->bindParam(':fecha_nacimiento', $data['fecha_nacimiento']);

        $stmt->bindParam(':id', $this->id);

        

        $result = $stmt->execute();

        

        // Actualizar preferencias (género y facilidades)

        if ($result) {

            $this->actualizarGeneroYFacilidades($data);

        }

        

        return $result;

    }



    // Actualizar género y facilidades (parte de la tabla preferencias)

    private function actualizarGeneroYFacilidades($data) {

        $query = 'SELECT usuario_id FROM ' . $this->table_preferencias . ' 

                  WHERE usuario_id = :usuario_id';

        $stmt = $this->db->prepare($query);

        $stmt->bindParam(':usuario_id', $this->id);

        $stmt->execute();

        

        if ($stmt->rowCount() > 0) {

            // Actualizar

            $query = 'UPDATE ' . $this->table_preferencias . ' SET 

                      genero = :genero,

                      facilidades_acceso = :facilidades_acceso

                      WHERE usuario_id = :usuario_id';

        } else {

            // Insertar

            $query = 'INSERT INTO ' . $this->table_preferencias . ' 

                      (usuario_id, genero, facilidades_acceso) 

                      VALUES 

                      (:usuario_id, :genero, :facilidades_acceso)';

        }

        

        $stmt = $this->db->prepare($query);

        $stmt->bindParam(':usuario_id', $this->id);

        $stmt->bindParam(':genero', $data['genero']);

        $stmt->bindParam(':facilidades_acceso', $data['facilidades']);

        

        return $stmt->execute();

    }



    // Actualizar información de contacto

    public function actualizarContacto($data) {

        $query = 'UPDATE ' . $this->table . ' SET 

                  telefono = :telefono,

                  contacto_emergencia = :contacto_emergencia,

                  direccion = :direccion,

                  email = :email

                  WHERE id = :id';

        

        $stmt = $this->db->prepare($query);

        $stmt->bindParam(':telefono', $data['telefono']);

        $stmt->bindParam(':contacto_emergencia', $data['contacto_emergencia']);

        $stmt->bindParam(':direccion', $data['direccion']);

        $stmt->bindParam(':email', $data['email']);

        $stmt->bindParam(':id', $this->id);

        

        return $stmt->execute();

    }

    



    // Actualizar preferencias

    public function actualizarPreferencias($data) {

        $query = 'SELECT usuario_id FROM ' . $this->table_preferencias . ' 

                  WHERE usuario_id = :usuario_id';

        $stmt = $this->db->prepare($query);

        $stmt->bindParam(':usuario_id', $this->id);

        $stmt->execute();

        

        if ($stmt->rowCount() > 0) {

            // Actualizar

            $query = 'UPDATE ' . $this->table_preferencias . ' SET 

                      notificaciones = :notificaciones,

                      idioma = :idioma,

                      preferencias_gastronomicas = :preferencias_gastronomicas,

                      experiencia_favorita = :experiencia_favorita

                      WHERE usuario_id = :usuario_id';

        } else {

            // Insertar

            $query = 'INSERT INTO ' . $this->table_preferencias . ' 

                      (usuario_id, notificaciones, idioma, 

                       preferencias_gastronomicas, experiencia_favorita) 

                      VALUES 

                      (:usuario_id, :notificaciones, :idioma, 

                       :preferencias_gastronomicas, :experiencia_favorita)';

        }

        

        $stmt = $this->db->prepare($query);

        $stmt->bindParam(':usuario_id', $this->id);

        $stmt->bindParam(':notificaciones', $data['notificaciones']);

        $stmt->bindParam(':idioma', $data['idioma']);

        $stmt->bindParam(':preferencias_gastronomicas', $data['preferencias_gastronomicas']);

        $stmt->bindParam(':experiencia_favorita', $data['experiencia_favorita']);

        

        return $stmt->execute();

    }



    // Crear usuario con Google

    public function crearConGoogle($google_user) {

        $query = 'INSERT INTO ' . $this->table . ' 

                  (google_id, nombre, email, foto_perfil) 

                  VALUES (:google_id, :nombre, :email, :foto_perfil)';

        

        $stmt = $this->db->prepare($query);

        

        // Limpiar datos

        $this->google_id = htmlspecialchars(strip_tags($google_user['id']));

        $this->nombre = htmlspecialchars(strip_tags($google_user['givenName'] . ' ' . $google_user['familyName']));

        $this->email = htmlspecialchars(strip_tags($google_user['email']));

        $this->foto_perfil = htmlspecialchars(strip_tags($google_user['picture']));

        

        // Vincular valores

        $stmt->bindParam(':google_id', $this->google_id);

        $stmt->bindParam(':nombre', $this->nombre);

        $stmt->bindParam(':email', $this->email);

        $stmt->bindParam(':foto_perfil', $this->foto_perfil);

        

        // Ejecutar consulta

        if($stmt->execute()) {

            return $this->db->lastInsertId();

        }

        

        return false;

    }



    // Buscar usuario por Google ID

    public function buscarPorGoogleId($google_id) {

        $query = 'SELECT * FROM ' . $this->table . ' WHERE google_id = :google_id LIMIT 1';

        $stmt = $this->db->prepare($query);

        $stmt->bindParam(':google_id', $google_id);

        $stmt->execute();

        

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        

        if($row) {

            // Cargar todas las propiedades básicas

            $this->id = $row['id'];

            $this->google_id = $row['google_id'];

            $this->nombre = $row['nombre'];

            $this->email = $row['email'];

            $this->password = $row['password'];

            $this->fecha_nacimiento = $row['fecha_nacimiento'];

            $this->telefono = $row['telefono'];

            $this->contacto_emergencia = $row['contacto_emergencia'];

            $this->direccion = $row['direccion'];

            $this->foto_perfil = $row['foto_perfil'];

            $this->verificado = $row['verificado'];

            return true;

        }

        

        return false;

    }



    public function buscarPorEmail($email) {

        $query = 'SELECT * FROM ' . $this->table . ' WHERE email = :email LIMIT 1';

        $stmt = $this->db->prepare($query);

        $stmt->bindParam(':email', $email);

        $stmt->execute();

        

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        

        if($row) {

            // Cargar todas las propiedades básicas

            $this->id = $row['id'];

            $this->google_id = $row['google_id'];

            $this->nombre = $row['nombre'];

            $this->email = $row['email'];

            $this->password = $row['password'];

            $this->fecha_nacimiento = $row['fecha_nacimiento'];

            $this->telefono = $row['telefono'];

            $this->contacto_emergencia = $row['contacto_emergencia'];

            $this->direccion = $row['direccion'];

            $this->foto_perfil = $row['foto_perfil'];

            $this->verificado = $row['verificado'];

            return true;

        }

        

        return false;

    }
      // Agrega este método a la clase Usuario
public function crear($data) {
    $query = 'INSERT INTO ' . $this->table . ' 
              (nombre, email, password) 
              VALUES (:nombre, :email, :password)';
    
    $stmt = $this->db->prepare($query);
    
    // Hashear la contraseña
    $hashed_password = password_hash($data['password'], PASSWORD_BCRYPT);
    
    $stmt->bindParam(':nombre', $data['nombre']);
    $stmt->bindParam(':email', $data['email']);
    $stmt->bindParam(':password', $hashed_password);
    
    if($stmt->execute()) {
        return $this->db->lastInsertId();
    }
    
    return false;
}


}

