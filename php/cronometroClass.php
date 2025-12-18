<?php

class Cronometro {
    private $tiempo;
    private $inicio;
    

    public function __construct() {
        $this->tiempo = 0;
    }
    

    public function arrancar() {
        $this->inicio = microtime(true);
    }
    

    public function parar() {
        $fin = microtime(true);
        $this->tiempo = $fin - $this->inicio;
        return $this->tiempo;
    }
    

    public function getTiempo() {
        return $this->tiempo;
    }
    

    public function mostrar() {
        $minutos = floor($this->tiempo / 60);
        $segundos = floor($this->tiempo % 60);
        $decimas = floor(($this->tiempo - floor($this->tiempo)) * 10);
        
        return sprintf("%02d:%02d.%d", $minutos, $segundos, $decimas);
    }
}
?>