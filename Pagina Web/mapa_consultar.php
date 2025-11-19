<?php
$latitud = isset($_GET['latitud']) ? $_GET['latitud'] : '';
$longitud = isset($_GET['longitud']) ? $_GET['longitud'] : '';
$estado = isset($_GET['estado']) ? $_GET['estado'] : '';
$nombre = isset($_GET['nombre']) ? $_GET['nombre'] : 'Colmena';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mapa de <?php echo htmlspecialchars($nombre); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #fff7e6, #ffe0b2);
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            height: 100vh;
        }

        .container {
            width: 80%;
            max-width: 1000px;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0px 4px 10px rgba(0,0,0,0.2);
            overflow: hidden;
            margin-top: 20px;
            padding-bottom: 10px;
        }

        .title-bar {
            text-align: center;
            padding: 10px;
            background: #ffb74d;
            color: white;
        }

        .menu-bar {
            text-align: center;
            margin-top: 8px;
            margin-bottom: 8px;
            font-size: 16px;
        }

        .menu-bar a {
            color: #e65100;
            text-decoration: none;
            font-weight: bold;
            margin: 0 5px;
        }

        h2 {
            text-align: center;
            color: #e65100;
            margin-top: 10px;
            margin-bottom: 5px;
            font-size: 18px;
        }

        .coords {
            text-align: center;
            color: #555;
            font-size: 15px;
            margin-bottom: 10px;
        }

        .mapa-contenedor {
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 10px 0 20px 0;
        }

        #map {
            width: 720px;
            height: 460px;
            border: 4px solid #ffb74d;
            border-radius: 12px;
            box-shadow: 0px 4px 10px rgba(0,0,0,0.25);
            background: #fff;
        }

        @media (max-width: 820px) {
            .container {
                width: 95%;
            }
            #map {
                width: 95%;
                height: 340px !important;
            }
        }
    </style>

    <script src="https://maps.googleapis.com/maps/api/js?key=&callback=initMap"></script>
    <script>
        function initMap() {
            var estado = <?php echo json_encode($estado); ?>;
            var iconUrl = "";

            if (estado == "Error") {
                iconUrl = "http://maps.google.com/mapfiles/ms/icons/red-dot.png";
            } else if (estado == "Anomalia") {
                iconUrl = "http://maps.google.com/mapfiles/ms/icons/blue-dot.png";
            } else if (estado == "Riesgo") {
                iconUrl = "http://maps.google.com/mapfiles/ms/icons/yellow-dot.png";
            } else if (estado == "Normal") {
                iconUrl = "http://maps.google.com/mapfiles/ms/icons/green-dot.png";
            }

            var lat = <?php echo json_encode(floatval($latitud)); ?>;
            var lng = <?php echo json_encode(floatval($longitud)); ?>;
            var nombre = <?php echo json_encode($nombre); ?>;
            var myLatLng = {lat: lat, lng: lng};

            var map = new google.maps.Map(document.getElementById('map'), {
                zoom: 18,
                center: myLatLng
            });

            new google.maps.Marker({
                position: myLatLng,
                map: map,
                title: nombre,
                icon: iconUrl
            });
        }
    </script>
</head>

<body onload="initMap()">
<div class="container">
    <div class="title-bar">
        <h1>Sistema Monitoreo de Apiarios</h1>
    </div>

    <div class="menu-bar">
        <a href="consulta_por_colmena.php">Inicio</a> |
        <a href="mapa.php">Mapa</a>
    </div>

    <h2>Ubicación: <?php echo htmlspecialchars($nombre); ?></h2>

    <?php if ($latitud && $longitud): ?>
        <div class="coords">
            Latitud: <?php echo htmlspecialchars($latitud); ?> |
            Longitud: <?php echo htmlspecialchars($longitud); ?>
        </div>
        <div class="mapa-contenedor">
            <div id="map"></div>
        </div>
    <?php else: ?>
        <p style="text-align:center; color:#333;">No se proporcionó una ubicación válida.</p>
    <?php endif; ?>
</div>
</body>
</html>
