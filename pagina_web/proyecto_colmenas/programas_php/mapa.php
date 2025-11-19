<?php
include "conexion.php";
session_start();
if (!isset($_SESSION["autenticado"]) || $_SESSION["autenticado"] != "SIx3") {
    header("Location: index.php?mensaje=3");
    exit();
}
$nombreusuario = isset($_SESSION["nombreusuario"]) ? $_SESSION["nombreusuario"] : "";
$desctipousuario = $_SESSION["tipo_usuario"];

// Consulta de colmenas
$mysqli = new mysqli($host, $user, $pw, $db);
$sqlcolmena = "SELECT estado_colmena, latitud, longitud, nombre_colmena, ubicacion FROM colmenas ORDER BY id";
$resultcolmena = $mysqli->query($sqlcolmena);
$colmenas = [];
while($row = $resultcolmena->fetch_array(MYSQLI_ASSOC)) {
    $colmenas[] = [
        'estado' => $row['estado_colmena'],
        'lat' => floatval($row['latitud']),
        'lng' => floatval($row['longitud']),
        'nombre' => $row['nombre_colmena'],
        'ubicacion' => $row['ubicacion']
    ];
}
$json_colmenas = json_encode($colmenas);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mapa - Universidad del Cauca</title>
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
            margin-bottom: 10px;
            font-size: 18px;
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
    </style>
</head>
<body>
<div class="container">
    <div class="title-bar">
        <h1>Sistema Monitoreo de Apiarios</h1>
    </div>

    <div class="menu-bar">
        <a href="javascript:history.back()">Inicio</a> |
        <a href="mapa.php">Mapa</a>
    </div>

    <h2>Ubicación: Universidad del Cauca - Finca La Sultana</h2>

    <div class="mapa-contenedor">
        <div id="map"></div>
    </div>
</div>

<script>
var colmenas = <?php echo $json_colmenas; ?>;

function initMap() {
    var center = colmenas.length > 0 ? {lat: colmenas[0].lat, lng: colmenas[0].lng} : {lat: 4.5667, lng: -74.0833};
    var map = new google.maps.Map(document.getElementById('map'), {
        zoom: 12,
        center: center
    });

    for (var i = 0; i < colmenas.length; i++) {
        var iconUrl = "";
        if (colmenas[i].estado === "Error") {
            iconUrl = "http://maps.google.com/mapfiles/ms/icons/red-dot.png";
        } else if (colmenas[i].estado === "Anomalia") {
            iconUrl = "http://maps.google.com/mapfiles/ms/icons/blue-dot.png";
        } else if (colmenas[i].estado === "Riesgo") {
            iconUrl = "http://maps.google.com/mapfiles/ms/icons/yellow-dot.png";
        } else if (colmenas[i].estado === "Normal") {
            iconUrl = "http://maps.google.com/mapfiles/ms/icons/green-dot.png";
        }

        var marcador = new google.maps.Marker({
            position: {lat: colmenas[i].lat, lng: colmenas[i].lng},
            map: map,
            icon: iconUrl,
            title: colmenas[i].nombre
        });

        var info = new google.maps.InfoWindow({
            content: "<b>" + colmenas[i].nombre + "</b><br>" + colmenas[i].ubicacion
        });

        marcador.addListener('click', (function(info, marcador) {
            return function() { info.open(map, marcador); };
        })(info, marcador));
    }
}
</script>
<script async defer src="https://maps.googleapis.com/maps/api/js?key=&callback=initMap"></script>
</body>
</html>
