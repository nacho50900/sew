<?php
// Clase para gestionar conexiones y operaciones con la base de datos
class BaseDatos {
    private $servidor;
    private $usuario;
    private $password;
    private $baseDatos;
    private $conexion;
    
    public function __construct() {
        $this->servidor = "localhost";
        $this->usuario = "DBUSER2025";
        $this->password = "DBPSWD2025";
        $this->baseDatos = "uo300737_db";
        $this->conectar();
    }
    
    private function conectar() {
        $this->conexion = new mysqli($this->servidor, $this->usuario, $this->password, $this->baseDatos);
        
        if ($this->conexion->connect_error) {
            die("Error de conexión: " . $this->conexion->connect_error);
        }
        
        $this->conexion->set_charset("utf8mb4");
    }
    
    public function obtenerProfesiones() {
        $resultado = $this->conexion->query("SELECT id_profesion, nombre_profesion FROM PROFESIONES ORDER BY nombre_profesion");
        $profesiones = [];
        while ($fila = $resultado->fetch_assoc()) {
            $profesiones[] = $fila;
        }
        return $profesiones;
    }
    
    public function obtenerGeneros() {
        $resultado = $this->conexion->query("SELECT id_genero, descripcion_genero FROM GENEROS ORDER BY id_genero");
        $generos = [];
        while ($fila = $resultado->fetch_assoc()) {
            $generos[] = $fila;
        }
        return $generos;
    }
    
    public function obtenerDispositivos() {
        $resultado = $this->conexion->query("SELECT id_dispositivo, nombre_dispositivo FROM DISPOSITIVOS ORDER BY id_dispositivo");
        $dispositivos = [];
        while ($fila = $resultado->fetch_assoc()) {
            $dispositivos[] = $fila;
        }
        return $dispositivos;
    }
    
    public function insertarUsuario($id_usuario, $id_profesion, $edad, $id_genero, $pericia) {
        $stmt = $this->conexion->prepare("INSERT INTO USUARIOS (id_usuario, id_profesion, edad, id_genero, pericia_informatica) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iiiii", $id_usuario, $id_profesion, $edad, $id_genero, $pericia);
        $resultado = $stmt->execute();
        $stmt->close();
        return $resultado;
    }
    
    public function insertarResultado($id_usuario, $id_dispositivo, $tiempo, $completado, $comentarios, $propuestas, $valoracion) {
        $stmt = $this->conexion->prepare("INSERT INTO RESULTADOS_TEST (id_usuario, id_dispositivo, tiempo_completado, completado, comentarios_usuario, propuestas_mejora, valoracion) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iiisssi", $id_usuario, $id_dispositivo, $tiempo, $completado, $comentarios, $propuestas, $valoracion);
        $resultado = $stmt->execute();
        $stmt->close();
        return $resultado;
    }
    
    public function insertarObservacion($id_usuario, $comentarios_facilitador) {
        $stmt = $this->conexion->prepare("INSERT INTO OBSERVACIONES_FACILITADOR (id_usuario, comentarios_facilitador) VALUES (?, ?)");
        $stmt->bind_param("is", $id_usuario, $comentarios_facilitador);
        $resultado = $stmt->execute();
        $stmt->close();
        return $resultado;
    }

    public function insertarRespuestas($id_usuario, $respuestas) {
        // Verificar que tenemos todas las respuestas
        $pregunta1 = isset($respuestas['pregunta1']) ? $respuestas['pregunta1'] : '';
        $pregunta2 = isset($respuestas['pregunta2']) ? $respuestas['pregunta2'] : '';
        $pregunta3 = isset($respuestas['pregunta3']) ? $respuestas['pregunta3'] : '';
        $pregunta4 = isset($respuestas['pregunta4']) ? $respuestas['pregunta4'] : '';
        $pregunta5 = isset($respuestas['pregunta5']) ? $respuestas['pregunta5'] : '';
        $pregunta6 = isset($respuestas['pregunta6']) ? $respuestas['pregunta6'] : '';
        $pregunta7 = isset($respuestas['pregunta7']) ? $respuestas['pregunta7'] : '';
        $pregunta8 = isset($respuestas['pregunta8']) ? $respuestas['pregunta8'] : '';
        $pregunta9 = isset($respuestas['pregunta9']) ? $respuestas['pregunta9'] : '';
        $pregunta10 = isset($respuestas['pregunta10']) ? $respuestas['pregunta10'] : '';
        
        $stmt = $this->conexion->prepare(
            "INSERT INTO RESPUESTAS_TEST 
            (id_usuario, pregunta_1, pregunta_2, pregunta_3, pregunta_4, pregunta_5, 
            pregunta_6, pregunta_7, pregunta_8, pregunta_9, pregunta_10) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        
        if ($stmt === false) {
            die("Error al preparar consulta de respuestas: " . $this->conexion->error);
        }
        
        $stmt->bind_param("issssssssss", 
            $id_usuario,
            $pregunta1,
            $pregunta2,
            $pregunta3,
            $pregunta4,
            $pregunta5,
            $pregunta6,
            $pregunta7,
            $pregunta8,
            $pregunta9,
            $pregunta10
        );
        
        if (!$stmt->execute()) {
            die("Error al ejecutar inserción de respuestas: " . $stmt->error);
        }
        
        $stmt->close();
        return true;
    }
}
?>