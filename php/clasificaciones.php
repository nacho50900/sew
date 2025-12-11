<?php
// Tarea 2: Creación de la clase Clasificacion
class Clasificacion {

    protected $documento;
    protected $xml;

    // Constructor que inicializa el atributo documento con la ruta al XML
    public function __construct() {
        $this->documento = "../xml/circuitoEsquema.xml";
    }

    // Tarea 3: Método consultar para leer el documento XML
    public function consultar() {
        if (file_exists($this->documento)) {
            $this->xml = simplexml_load_file($this->documento);
        } else {
            $this->xml = null;
        }
    }

    // Tarea 4: Mostrar el ganador de la carrera
    public function mostrarGanador() {
        if ($this->xml) {
            $datos = $this->xml->children('http://www.uniovi.es');
            $vencedor = $datos->vencedor;

            echo "<section>
                    <h3>Ganador de la Carrera</h3>
                    <p><strong>Piloto:</strong> " . htmlspecialchars($vencedor->nombre) . "</p>
                    <p><strong>Tiempo:</strong> " . htmlspecialchars($vencedor->tiempo) . "</p>
                  </section>";
        }
    }

    // Tarea 5: Mostrar la clasificación del mundial tras la carrera
    public function mostrarClasificacion() {
        if ($this->xml) {
            $datos = $this->xml->children('http://www.uniovi.es');
            $clasificacion = $datos->clasificacion;

            echo "<section>
                    <h3>Clasificación del Mundial tras la carrera</h3>
                    <ol>";
                        
            foreach ($clasificacion->piloto as $piloto) {
                // Ahora toma el texto directamente del elemento <piloto>
                $nombre = htmlspecialchars((string)$piloto);  // ✅ Convierte a string
                echo "<li>" . $nombre . "</li>";
            }

            echo "  </ol>
                  </section>";
        }
    }
}

// Crear instancia y consultar el XML
$clasificacion = new Clasificacion();
$clasificacion->consultar();
?>
<!DOCTYPE HTML>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>MotoGP-Clasificaciones</title>

    <link rel="stylesheet" type="text/css" href="../estilo/estilo.css" />
    <link rel="stylesheet" type="text/css" href="../estilo/layout.css" />
    <link rel="stylesheet" type="text/css" href="../estilo/menu-movil.css" />
    <link rel="icon" type="image/x-icon" href="../multimedia/favicon.ico" />

    <meta name="author" content="Ignacio - UO300737" />
    <meta name="description" content="Información de clasificaciones del proyecto MotoGP-Desktop" />
    <meta name="keywords" content="motogp, clasificaciones, pilotos, tiempos, carrera, circuito" />
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
            <a href="../meteorologia.html" title="Información de la meteorología">Meteorologia</a>
            <a href="clasificaciones.php" title="Información de las clasificaciones" class="active">Clasificaciones</a>
            <a href="../juegos.html" title="Información de los juegos">Juegos</a>
            <a href="../ayuda.html" title="Información de ayuda">Ayuda</a>
        </nav>

        <p>Estás en: <a href="../index.html" title="Página principal">Inicio</a> >> <strong>Clasificaciones</strong></p>
    </header>

    <h2>Clasificaciones</h2>

    <main>
        <?php 
            // Tarea 4 y 5: Mostrar ganador y clasificación
            $clasificacion->mostrarGanador(); 
            $clasificacion->mostrarClasificacion(); 
        ?>
    </main>
</body>
</html>