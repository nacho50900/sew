<?php

require_once 'clasificacion.php';

$clasificacion = new Clasificacion();
$clasificacion->consultar();

$ganador = $clasificacion->obtenerGanador();
$pilotos = $clasificacion->obtenerClasificacion();
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
        <?php if ($ganador): ?>
        <section>
            <h3>Ganador de la Carrera</h3>
            <p><strong>Piloto:</strong> <?php echo htmlspecialchars($ganador['nombre']); ?></p>
            <p><strong>Tiempo:</strong> <?php echo htmlspecialchars($ganador['tiempo']); ?></p>
        </section>
        <?php endif; ?>

        <?php if (!empty($pilotos)): ?>
        <section>
            <h3>Clasificación del Mundial tras la carrera</h3>
            <ol>
                <?php foreach ($pilotos as $piloto): ?>
                <li><?php echo htmlspecialchars($piloto); ?></li>
                <?php endforeach; ?>
            </ol>
        </section>
        <?php endif; ?>
    </main>
</body>
</html>