<?php
session_start();

// Incluir las clases necesarias
include 'BaseDatos.class.php';
include 'cronometroClass.php';

// ==================== CONTROLADOR ====================
class TestUsabilidadControlador {
    private $db;
    
    public function __construct() {
        $this->db = new BaseDatos();
    }
    
    public function procesarFormulario() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
        
        if (isset($_POST['iniciar_prueba'])) {
            $this->procesarInicioPrueba();
        } elseif (isset($_POST['terminar_prueba'])) {
            $this->procesarFinPrueba();
        } elseif (isset($_POST['finalizar'])) {
            $this->procesarFinalizacion();
        }
    }
    
    private function procesarInicioPrueba() {
        $_SESSION['id_usuario'] = intval($_POST['id_usuario']);
        $_SESSION['id_profesion'] = intval($_POST['id_profesion']);
        $_SESSION['edad'] = intval($_POST['edad']);
        $_SESSION['id_genero'] = intval($_POST['id_genero']);
        $_SESSION['pericia'] = intval($_POST['pericia']);
        $_SESSION['id_dispositivo'] = intval($_POST['id_dispositivo']);
        
        $this->db->insertarUsuario(
            $_SESSION['id_usuario'], 
            $_SESSION['id_profesion'], 
            $_SESSION['edad'], 
            $_SESSION['id_genero'], 
            $_SESSION['pericia']
        );
        
        // Iniciar cronómetro invisible
        $cronometro = new Cronometro();
        $cronometro->arrancar();
        $_SESSION['cronometro'] = serialize($cronometro);
        
        header("Location: test_usabilidad.php?paso=2");
        exit();
    }
    
    private function procesarFinPrueba() {
        // Detener cronómetro
        $cronometro = unserialize($_SESSION['cronometro']);
        $tiempo_total = $cronometro->parar();
        $_SESSION['tiempo_total'] = $tiempo_total;
        $_SESSION['respuestas'] = $_POST;
        
        header("Location: test_usabilidad.php?paso=3");
        exit();
    }
    
    private function procesarFinalizacion() {
        $completado = 1;
        $comentarios = $_POST['comentarios_usuario'];
        $propuestas = $_POST['propuestas_mejora'];
        $valoracion = intval($_POST['valoracion']);
        $comentarios_facilitador = $_POST['comentarios_facilitador'];
        $tiempo_segundos = round($_SESSION['tiempo_total']);
        
        $this->db->insertarResultado(
            $_SESSION['id_usuario'], 
            $_SESSION['id_dispositivo'], 
            $tiempo_segundos, 
            $completado, 
            $comentarios, 
            $propuestas, 
            $valoracion
        );
        
        $this->db->insertarObservacion($_SESSION['id_usuario'], $comentarios_facilitador);
        $this->db->insertarRespuestas($_SESSION['id_usuario'], $_SESSION['respuestas']);
        
        session_destroy();
        
        header("Location: test_usabilidad.php?paso=4");
        exit();
    }
    
    public function obtenerProfesiones() {
        return $this->db->obtenerProfesiones();
    }
    
    public function obtenerGeneros() {
        return $this->db->obtenerGeneros();
    }
    
    public function obtenerDispositivos() {
        return $this->db->obtenerDispositivos();
    }
}

// ==================== VISTA ====================
class TestUsabilidadVista {
    
    public function renderizarCabecera() {
        ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>MotoGP-Test de Usabilidad</title>
    
    <link rel="stylesheet" type="text/css" href="../estilo/estilo.css" />
    <link rel="stylesheet" type="text/css" href="../estilo/layout.css" />
    <link rel="stylesheet" type="text/css" href="../estilo/menu-movil.css" />
    <link rel="stylesheet" type="text/css" href="../estilo/test-usabilidad.css" />
    <link rel="icon" type="image/x-icon" href="../multimedia/favicon.ico" />
    
    <meta name="author" content="Ignacio - UO300737" />
    <meta name="description" content="Test de usabilidad del proyecto MotoGP-Desktop" />
    <meta name="keywords" content="MotoGP, test, usabilidad, prueba" />
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
            <a href="../juegos.html" title="Información de los juegos">Juegos</a>
            <a href="../ayuda.html" title="Información de ayuda">Ayuda</a>
        </nav>

        <p>Estás en: <a href="../index.html" title="Página principal">Inicio</a> >> <a href="../juegos.html" title="Información de los juegos">Juegos</a> >> <strong>Test de Usabilidad</strong></p>
    </header>
    
    <main>
        <h2>Test de Usabilidad</h2>
        <?php
    }
    
    public function renderizarPie() {
        ?>
    </main>
</body>
</html>
        <?php
    }
    
    public function renderizarPaso1($profesiones, $generos, $dispositivos) {
        ?>
        <section>
            <h3>Datos del participante</h3>
            
            <form method="post" action="test_usabilidad.php">
                <fieldset>
                    <legend>Información personal</legend>
                    
                    <label for="id_usuario">
                        Código de usuario:
                        <input type="number" id="id_usuario" name="id_usuario" required />
                    </label>
                    
                    <label for="id_profesion">
                        Profesión:
                        <select id="id_profesion" name="id_profesion" required>
                            <?php foreach ($profesiones as $profesion): ?>
                                <option value="<?php echo $profesion['id_profesion']; ?>">
                                    <?php echo htmlspecialchars($profesion['nombre_profesion']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                    
                    <label for="edad">
                        Edad:
                        <input type="number" id="edad" name="edad" min="0" max="120" required />
                    </label>
                    
                    <label for="id_genero">
                        Género:
                        <select id="id_genero" name="id_genero" required>
                            <?php foreach ($generos as $genero): ?>
                                <option value="<?php echo $genero['id_genero']; ?>">
                                    <?php echo htmlspecialchars($genero['descripcion_genero']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                </fieldset>
                
                <fieldset>
                    <legend>Información técnica</legend>
                    
                    <label for="pericia">
                        Pericia informática (0-10):
                        <input type="number" id="pericia" name="pericia" min="0" max="10" required />
                    </label>
                    
                    <label for="id_dispositivo">
                        Dispositivo usado:
                        <select id="id_dispositivo" name="id_dispositivo" required>
                            <?php foreach ($dispositivos as $dispositivo): ?>
                                <option value="<?php echo $dispositivo['id_dispositivo']; ?>">
                                    <?php echo htmlspecialchars($dispositivo['nombre_dispositivo']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                </fieldset>
                
                <button type="submit" name="iniciar_prueba">Iniciar Prueba</button>
            </form>
        </section>
        <?php
    }
    
    public function renderizarPaso2() {
        $preguntas = [
            '¿Cuál es el nombre completo del piloto principal?',
            '¿En qué año nació el piloto?',
            '¿Cuál es el nombre del circuito principal?',
            '¿Cuántos metros mide el circuito?',
            '¿Cuántas curvas tiene el circuito?',
            '¿Qué información meteorológica se muestra en la página?',
            '¿Cuántos juegos hay disponibles en la sección de juegos?',
            '¿Qué tipo de clasificaciones se pueden consultar?',
            '¿Qué sección del menú está marcada como activa actualmente?',
            '¿Cómo se llama el proyecto según el título principal?'
        ];
        ?>
        <section>
            <h3>Preguntas sobre MotoGP-Desktop</h3>
            
            <p>Responde a las siguientes preguntas consultando el sitio web MotoGP-Desktop.</p>
            
            <form method="post" action="test_usabilidad.php">
                <?php foreach ($preguntas as $index => $pregunta): 
                    $numero = $index + 1;
                ?>
                    <label for="p<?php echo $numero; ?>">
                        <strong>Pregunta <?php echo $numero; ?>:</strong> <?php echo htmlspecialchars($pregunta); ?>
                        <input type="text" id="p<?php echo $numero; ?>" name="pregunta<?php echo $numero; ?>" required />
                    </label>
                <?php endforeach; ?>
                
                <button type="submit" name="terminar_prueba">Terminar Prueba</button>
            </form>
        </section>
        <?php
    }
    
    public function renderizarPaso3() {
        ?>
        <section>
            <h3>Valoración de la prueba</h3>
            
            <form method="post" action="test_usabilidad.php">
                <fieldset>
                    <legend>Comentarios del participante</legend>
                    
                    <label for="comentarios_usuario">
                        Comentarios sobre la experiencia:
                        <textarea id="comentarios_usuario" name="comentarios_usuario"></textarea>
                    </label>
                    
                    <label for="propuestas_mejora">
                        Propuestas de mejora:
                        <textarea id="propuestas_mejora" name="propuestas_mejora"></textarea>
                    </label>
                    
                    <label for="valoracion">
                        Valoración (0-10):
                        <input type="number" id="valoracion" name="valoracion" min="0" max="10" value="5" required />
                    </label>
                </fieldset>
                
                <fieldset>
                    <legend>Observaciones del facilitador</legend>
                    
                    <label for="comentarios_facilitador">
                        Comentarios del observador:
                        <textarea id="comentarios_facilitador" name="comentarios_facilitador" required></textarea>
                    </label>
                </fieldset>
                
                <button type="submit" name="finalizar">Finalizar y Guardar</button>
            </form>
        </section>
        <?php
    }
    
    public function renderizarPaso4() {
        ?>
        <section>
            <h3>¡Prueba completada con éxito!</h3>
            <p>Gracias por participar en el test de usabilidad. Tus respuestas han sido guardadas correctamente.</p>
            
            <nav>
                <a href="test_usabilidad.php">Realizar otra prueba</a>
                <a href="../juegos.html">Volver a Juegos</a>
            </nav>
        </section>
        <?php
    }
}

// ==================== EJECUCIÓN PRINCIPAL ====================
$controlador = new TestUsabilidadControlador();
$vista = new TestUsabilidadVista();

// Procesar formularios
$controlador->procesarFormulario();

// Determinar paso actual
$paso = isset($_GET['paso']) ? intval($_GET['paso']) : 1;

// Renderizar página
$vista->renderizarCabecera();

switch ($paso) {
    case 1:
        $profesiones = $controlador->obtenerProfesiones();
        $generos = $controlador->obtenerGeneros();
        $dispositivos = $controlador->obtenerDispositivos();
        $vista->renderizarPaso1($profesiones, $generos, $dispositivos);
        break;
    
    case 2:
        $vista->renderizarPaso2();
        break;
    
    case 3:
        $vista->renderizarPaso3();
        break;
    
    case 4:
        $vista->renderizarPaso4();
        break;
    
    default:
        header("Location: test_usabilidad.php?paso=1");
        exit();
}

$vista->renderizarPie();
?>