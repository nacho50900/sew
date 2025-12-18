<?php

class Clasificacion {

    protected $documento;
    protected $xml;

    public function __construct() {
        $this->documento = "../xml/circuitoEsquema.xml";
    }

    public function consultar() {
        if (file_exists($this->documento)) {
            $this->xml = simplexml_load_file($this->documento);
        } else {
            $this->xml = null;
        }
    }

    public function obtenerGanador() {
        if ($this->xml) {
            $datos = $this->xml->children('http://www.uniovi.es');
            $vencedor = $datos->vencedor;
            
            return [
                'nombre' => (string)$vencedor->nombre,
                'tiempo' => (string)$vencedor->tiempo
            ];
        }
        return null;
    }

    public function obtenerClasificacion() {
        if ($this->xml) {
            $datos = $this->xml->children('http://www.uniovi.es');
            $clasificacion = $datos->clasificacion;
            
            $pilotos = [];
            foreach ($clasificacion->piloto as $piloto) {
                $pilotos[] = (string)$piloto;
            }
            
            return $pilotos;
        }
        return [];
    }

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

    public function mostrarClasificacion() {
        if ($this->xml) {
            $datos = $this->xml->children('http://www.uniovi.es');
            $clasificacion = $datos->clasificacion;

            echo "<section>
                    <h3>Clasificación del Mundial tras la carrera</h3>
                    <ol>";
                        
            foreach ($clasificacion->piloto as $piloto) {
                $nombre = htmlspecialchars((string)$piloto);
                echo "<li>" . $nombre . "</li>";
            }

            echo "  </ol>
                  </section>";
        }
    }
}
?>