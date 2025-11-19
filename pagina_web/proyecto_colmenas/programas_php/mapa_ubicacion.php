<!DOCTYPE html>
<html>
<head>
    <title>Seleccionar Ubicación en el Mapa</title>
    <meta charset="utf-8">
    <style>
        #map { width: 100%; height: 400px; }
        body { font-family: Arial, sans-serif; }
        .btn { background-color: #EEEEEE; color: black; padding:8px 15px; border:none; border-radius:5px; font-weight:bold; margin-top: 15px;}
    </style>
</head>
<body>
<h2>Selecciona la ubicación de la colmena haciendo clic en el mapa</h2>
<div id="map"></div>
<button class="btn" onclick="guardarUbicacion()">Guardar ubicación y volver</button>

<script>
let marker = null;
let selectedLat = null;
let selectedLng = null;

// Obtener parámetros de la URL
const urlParams = new URLSearchParams(window.location.search);
const origen = urlParams.get('origen'); // puede ser "agregar" o "modificar"
const idColmena = urlParams.get('id_colmena'); // solo si viene de modificar

function initMap() {
    const centro = {lat: 2.4400, lng: -76.6100}; // ajusta según tu región
    const map = new google.maps.Map(document.getElementById('map'), {
        zoom: 15,
        center: centro
    });

    map.addListener('click', function (event) {
        selectedLat = event.latLng.lat();
        selectedLng = event.latLng.lng();
        if(marker) marker.setMap(null);
        marker = new google.maps.Marker({
            position: {lat: selectedLat, lng: selectedLng},
            map: map,
            icon: "http://maps.google.com/mapfiles/ms/icons/green-dot.png"
        });
    });
}

function guardarUbicacion() {
    if(selectedLat && selectedLng){
        localStorage.setItem("colmena_latitud", selectedLat);
        localStorage.setItem("colmena_longitud", selectedLng);

        // Redirigir dependiendo de la acción anterior
        if(origen === "modificar" && idColmena){
            window.location.href = `modificar_colmena.php?id_colmena=${idColmena}`;
        } else {
            window.location.href = "agregar_colmena.php";
        }
    } else {
        alert("Selecciona una ubicación en el mapa primero.");
    }
}
</script>
<script async defer src="https://maps.googleapis.com/maps/api/js?key=&callback=initMap"></script>
</body>
</html>
