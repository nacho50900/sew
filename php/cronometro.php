<?php
// INCLUIR la clase en lugar de definirla aquí
require_once 'cronometroClass.php';

// Manejo de las peticiones POST de los botones
session_start();

if (!isset($_SESSION['cronometro'])) {
    $_SESSION['cronometro'] = serialize(new Cronometro());
}

$cronometro = unserialize($_SESSION['cronometro']);
$tiempoMostrado = "00:00.0";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['arrancar'])) {
        $cronometro->arrancar();
        $tiempoMostrado = "00:00.0";
    } elseif (isset($_POST['parar'])) {
        $cronometro->parar();
    } elseif (isset($_POST['mostrar'])) {
        $tiempoMostrado = $cronometro->mostrar();
    }
    
    $_SESSION['cronometro'] = serialize($cronometro);
}
?>
<!DOCTYPE HTML>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>MotoGP-Cronómetro</title>
    
    <!-- Tarea 6: Enlaces a hojas de estilo estilo.css y layout.css -->
    <link rel="stylesheet" type="text/css" href="../estilo/estilo.css" />
    <link rel="stylesheet" type="text/css" href="../estilo/layout.css" />
    <link rel="icon" type="image/x-icon" href="../multimedia/favicon.ico" />
    <link rel="stylesheet" type="text/css" href="../estilo/menu-movil.css" />
    
    <meta name="author" content="Ignacio - UO300737" />
    <meta name="description" content="Cronómetro PHP del proyecto MotoGP-Desktop" />
    <meta name="keywords" content="MotoGP, cronómetro, juegos, PHP" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
      
    <script src="../js/menu-movil.js"></script>
</head>

<body>
    <!-- Tarea 6: Estructura general con header, h1 y nav -->
    <h1>
        <a href="../index.html" title="Página principal">MotoGPDesktop</a>
    </h1>
    
    <header>
        <nav>
            <a href="../index.html" title="Página principal">Inicio</a>
            <a href="../piloto.html" title="Información del piloto">Piloto</a>
            <a href="../circuito.html" title="Información del circuito">Circuito</a>
            <a href="../meteorologia.html" title="Información de la meteorología">Meteorologia</a>
            <a href="../clasificaciones.php" title="Información de las clasificaciones">Clasificaciones</a>
            <a href="../juegos.html" title="Información de los juegos" class="active">Juegos</a>
            <a href="../ayuda.html" title="Información de ayuda">Ayuda</a>
        </nav>
        
        <!-- Tarea 6: Migas de navegación -->
        <p>Estás en: <a href="../index.html" title="Página principal">Inicio</a> >> <a href="../juegos.html" title="Información de los juegos">Juegos</a> >> <strong>Cronómetro PHP</strong></p>
    </header>
    
    <main>
        <h2>Cronómetro</h2>
        
        <section>
            <h3>Control del Cronómetro</h3>
            
            <!-- Tarea 6: Interfaz con tres botones -->
            <form method="post" action="cronometro.php">
                <button type="submit" name="arrancar">Arrancar</button>
                <button type="submit" name="parar">Parar</button>
                <button type="submit" name="mostrar">Mostrar Tiempo</button>
            </form>
            
            <!-- Mostrar el tiempo transcurrido -->
            <?php 
                echo "<p>Tiempo transcurrido: <strong>" . $tiempoMostrado . "</strong></p>";
            ?>
        </section>
    </main>
</body>
</html>