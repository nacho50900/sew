<?php
// Incluir la clase BaseDatos para operaciones con la BD
include 'BaseDatos.class.php';

class Configuracion {
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
    }
    
    private function conectar() {
        $this->conexion = new mysqli($this->servidor, $this->usuario, $this->password, $this->baseDatos);
        
        if ($this->conexion->connect_error) {
            die("Error de conexión: " . $this->conexion->connect_error);
        }
        
        $this->conexion->set_charset("utf8mb4");
        return $this->conexion;
    }
    
    private function cerrarConexion() {
        if ($this->conexion) {
            $this->conexion->close();
        }
    }
    
    public function reiniciarBaseDatos() {
        $this->conectar();
        
        $resultado = "";
        
        // Deshabilitar comprobaciones de claves foráneas temporalmente
        if ($this->conexion->query("SET FOREIGN_KEY_CHECKS = 0")) {
            $resultado .= "Comprobaciones de claves foráneas deshabilitadas.<br>";
        }
        
        // Orden correcto: primero tablas dependientes, luego las principales
        $tablas = [
            'OBSERVACIONES_FACILITADOR', 
            'RESPUESTAS_TEST',
            'RESULTADOS_TEST', 
            'USUARIOS', 
            'DISPOSITIVOS', 
            'GENEROS', 
            'PROFESIONES'
        ];
        
        foreach ($tablas as $tabla) {
            $query = "TRUNCATE TABLE " . $tabla;
            if ($this->conexion->query($query)) {
                $resultado .= "Tabla $tabla vaciada correctamente.<br>";
            } else {
                $resultado .= "Error al vaciar tabla $tabla: " . $this->conexion->error . "<br>";
            }
        }
        
        // Rehabilitar comprobaciones de claves foráneas
        if ($this->conexion->query("SET FOREIGN_KEY_CHECKS = 1")) {
            $resultado .= "Comprobaciones de claves foráneas rehabilitadas.<br>";
        }
        
        // Reinsertar datos iniciales
        $this->insertarDatosIniciales();
        $resultado .= "Datos iniciales insertados correctamente.<br>";
        
        $this->cerrarConexion();
        return $resultado;
    }
    
    private function insertarDatosIniciales() {
        // Insertar géneros
        $generos = ['Masculino', 'Femenino', 'Otro', 'Prefiero no decirlo'];
        foreach ($generos as $genero) {
            $stmt = $this->conexion->prepare("INSERT INTO GENEROS (descripcion_genero) VALUES (?)");
            $stmt->bind_param("s", $genero);
            $stmt->execute();
            $stmt->close();
        }
        
        // Insertar dispositivos
        $dispositivos = ['Ordenador', 'Tableta', 'Teléfono'];
        foreach ($dispositivos as $dispositivo) {
            $stmt = $this->conexion->prepare("INSERT INTO DISPOSITIVOS (nombre_dispositivo) VALUES (?)");
            $stmt->bind_param("s", $dispositivo);
            $stmt->execute();
            $stmt->close();
        }
        
        // Insertar profesiones
        $profesiones = [
            'Estudiante de Ingeniería Informática',
            'Ingeniero Informático',
            'Estudiante',
            'Profesor',
            'Médico',
            'Abogado',
            'Administrativo',
            'Comerciante',
            'Jubilado',
            'Desempleado',
            'Otra'
        ];
        foreach ($profesiones as $profesion) {
            $stmt = $this->conexion->prepare("INSERT INTO PROFESIONES (nombre_profesion) VALUES (?)");
            $stmt->bind_param("s", $profesion);
            $stmt->execute();
            $stmt->close();
        }
    }
    
    public function eliminarBaseDatos() {
        $conexionSinDB = new mysqli($this->servidor, $this->usuario, $this->password);
        
        if ($conexionSinDB->connect_error) {
            return "Error de conexión: " . $conexionSinDB->connect_error;
        }
        
        $query = "DROP DATABASE IF EXISTS " . $this->baseDatos;
        
        if ($conexionSinDB->query($query)) {
            $resultado = "Base de datos eliminada correctamente.";
        } else {
            $resultado = "Error al eliminar la base de datos: " . $conexionSinDB->error;
        }
        
        $conexionSinDB->close();
        return $resultado;
    }
    
    public function exportarDatosCSV() {
        $this->conectar();
        
        // Query que une todas las tablas relevantes INCLUYENDO las respuestas
        $query = "
            SELECT 
                u.id_usuario AS 'ID Usuario',
                p.nombre_profesion AS 'Profesión',
                u.edad AS 'Edad',
                g.descripcion_genero AS 'Género',
                u.pericia_informatica AS 'Pericia Informática',
                d.nombre_dispositivo AS 'Dispositivo',
                r.tiempo_completado AS 'Tiempo (segundos)',
                r.completado AS 'Completado',
                r.valoracion AS 'Valoración',
                resp.pregunta_1 AS 'Respuesta 1',
                resp.pregunta_2 AS 'Respuesta 2',
                resp.pregunta_3 AS 'Respuesta 3',
                resp.pregunta_4 AS 'Respuesta 4',
                resp.pregunta_5 AS 'Respuesta 5',
                resp.pregunta_6 AS 'Respuesta 6',
                resp.pregunta_7 AS 'Respuesta 7',
                resp.pregunta_8 AS 'Respuesta 8',
                resp.pregunta_9 AS 'Respuesta 9',
                resp.pregunta_10 AS 'Respuesta 10',
                r.comentarios_usuario AS 'Comentarios Usuario',
                r.propuestas_mejora AS 'Propuestas Mejora',
                r.fecha_realizacion AS 'Fecha Realización',
                o.comentarios_facilitador AS 'Observaciones Facilitador',
                o.fecha_observacion AS 'Fecha Observación'
            FROM USUARIOS u
            LEFT JOIN PROFESIONES p ON u.id_profesion = p.id_profesion
            LEFT JOIN GENEROS g ON u.id_genero = g.id_genero
            LEFT JOIN RESULTADOS_TEST r ON u.id_usuario = r.id_usuario
            LEFT JOIN DISPOSITIVOS d ON r.id_dispositivo = d.id_dispositivo
            LEFT JOIN RESPUESTAS_TEST resp ON u.id_usuario = resp.id_usuario
            LEFT JOIN OBSERVACIONES_FACILITADOR o ON u.id_usuario = o.id_usuario
            ORDER BY r.fecha_realizacion DESC
        ";
        
        $resultado = $this->conexion->query($query);
        
        if ($resultado) {
            $nombreCSV = "test_usabilidad_completo_" . date('Y-m-d_H-i-s') . ".csv";
            
            // Headers para descargar el archivo
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="' . $nombreCSV . '"');
            header('Cache-Control: no-cache, must-revalidate');
            header('Pragma: no-cache');
            
            $output = fopen('php://output', 'w');
            
            // BOM UTF-8 para que Excel reconozca los caracteres especiales (ñ, tildes, etc.)
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Obtener nombres de columnas
            $campos = $resultado->fetch_fields();
            $nombresColumnas = array_map(function($campo) { 
                return $campo->name; 
            }, $campos);
            
            // Escribir encabezados
            fputcsv($output, $nombresColumnas, ';'); // Usar ; como separador para Excel en español
            
            // Escribir datos
            while ($fila = $resultado->fetch_assoc()) {
                fputcsv($output, $fila, ';');
            }
            
            fclose($output);
            $this->cerrarConexion();
            exit();
        }
        
        $this->cerrarConexion();
        return "Error al generar el archivo de exportación";
    }
    
    public function crearBaseDatos() {
        // Conectar sin seleccionar base de datos
        $conexionSinDB = new mysqli($this->servidor, $this->usuario, $this->password);
        
        if ($conexionSinDB->connect_error) {
            return "Error de conexión: " . $conexionSinDB->connect_error;
        }
        
        $conexionSinDB->set_charset("utf8mb4");
        
        // Verificar si el archivo SQL existe
        $archivoSQL = 'uo300737_db.sql';
        if (!file_exists($archivoSQL)) {
            $conexionSinDB->close();
            return "Error: No se encuentra el archivo $archivoSQL";
        }
        
        // Leer el archivo SQL
        $scriptSQL = file_get_contents($archivoSQL);
        
        if ($scriptSQL === false) {
            $conexionSinDB->close();
            return "Error: No se pudo leer el archivo SQL";
        }
        
        $resultado = "";
        
        // Ejecutar el script completo
        if ($conexionSinDB->multi_query($scriptSQL)) {
            $resultado .= "Ejecutando script SQL...<br>";
            
            // Procesar todos los resultados
            do {
                // Almacenar el resultado si existe
                if ($res = $conexionSinDB->store_result()) {
                    $res->free();
                }
                
                // Verificar si hay errores
                if ($conexionSinDB->errno) {
                    $resultado .= "Error en consulta: " . $conexionSinDB->error . "<br>";
                }
                
                // Continuar con el siguiente resultado
            } while ($conexionSinDB->more_results() && $conexionSinDB->next_result());
            
            $resultado .= "Base de datos creada correctamente con todos los datos iniciales.<br>";
        } else {
            $resultado = "Error al ejecutar el script: " . $conexionSinDB->error;
        }
        
        $conexionSinDB->close();
        return $resultado;
    }
}

// Procesar peticiones
$config = new Configuracion();
$mensaje = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['reiniciar'])) {
        $mensaje = $config->reiniciarBaseDatos();
    } elseif (isset($_POST['eliminar'])) {
        $mensaje = $config->eliminarBaseDatos();
    } elseif (isset($_POST['exportar'])) {
        $config->exportarDatosCSV();
    } elseif (isset($_POST['crear'])) {
        $mensaje = $config->crearBaseDatos();
    }
}
?>
<!DOCTYPE HTML>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>MotoGP-Configuración Test</title>
    
    <link rel="stylesheet" type="text/css" href="../estilo/estilo.css" />
    <link rel="stylesheet" type="text/css" href="../estilo/layout.css" />
    <link rel="stylesheet" type="text/css" href="../estilo/menu-movil.css" />
    <link rel="icon" type="image/x-icon" href="../multimedia/favicon.ico" />
    
    <meta name="author" content="Ignacio - UO300737" />
    <meta name="description" content="Configuración de la base de datos de pruebas de usabilidad" />
    <meta name="keywords" content="MotoGP, configuración, base de datos, test usabilidad" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    
    <script src="../js/menu-movil.js"></script>
</head>

<body>
    <h1>
        <a href="../index.html" title="Página principal">MotoGPDesktop</a>
    </h1>
    
    <header>
        <nav>
            <a href="../index.html" title="Página principal">Inicio</a>
            <a href="../piloto.html" title="Información del piloto">Piloto</a>
            <a href="../circuito.html" title="Información del circuito">Circuito</a>
            <a href="../meteorologia.html" title="Información del la meteorología">Meteorologia</a>
            <a href="clasificaciones.php" title="Información de las clasificaciones">Clasificaciones</a>
            <a href="../juegos.html" title="Información de los juegos" class="active">Juegos</a>
            <a href="../ayuda.html" title="Información de ayuda">Ayuda</a>
        </nav>

        <p>Estás en: <a href="../index.html" title="Página principal">Inicio</a> >> <a href="../juegos.html" title="Información de Juegos">Juegos</a> >> <strong>Configuración del Test de Usabilidad</strong></p>
    </header>

    <main>
        <section>
            <h2>Configuración del Test de Usabilidad</h2>

            <h3>Operaciones de Base de Datos</h3>
            
            <?php if ($mensaje): ?>
                <p><?php echo $mensaje; ?></p>
            <?php endif; ?>
            
            <form method="post" action="Configuracion.php">
                <button type="submit" name="crear">Crear Base de Datos</button>
                <button type="submit" name="reiniciar">Reiniciar Base de Datos</button>
                <button type="submit" name="eliminar" onclick="return confirm('¿Está seguro de eliminar la base de datos?')">Eliminar Base de Datos</button>
                <button type="submit" name="exportar">Exportar Datos (CSV)</button>
            </form>
        </section>
    </main>
</body>
</html>