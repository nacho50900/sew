<?php
/**
 * Clase Cronometro para medir tiempos
 * Archivo: Cronometro.php
 */
class Cronometro {
    private $tiempo;
    private $inicio;
    
    /**
     * Constructor que inicializa el atributo tiempo al valor cero
     */
    public function __construct() {
        $this->tiempo = 0;
    }
    
    /**
     * Método arrancar que inicia el cronómetro
     */
    public function arrancar() {
        $this->inicio = microtime(true);
    }
    
    /**
     * Método parar que detiene el cronómetro y devuelve el tiempo transcurrido
     * @return float Tiempo transcurrido en segundos
     */
    public function parar() {
        $fin = microtime(true);
        $this->tiempo = $fin - $this->inicio;
        return $this->tiempo;
    }
    
    /**
     * Obtiene el tiempo almacenado
     * @return float Tiempo en segundos
     */
    public function getTiempo() {
        return $this->tiempo;
    }
    
    /**
     * Método mostrar en formato mm:ss.d
     * @return string Tiempo formateado
     */
    public function mostrar() {
        $minutos = floor($this->tiempo / 60);
        $segundos = floor($this->tiempo % 60);
        $decimas = floor(($this->tiempo - floor($this->tiempo)) * 10);
        
        return sprintf("%02d:%02d.%d", $minutos, $segundos, $decimas);
    }
}
?>