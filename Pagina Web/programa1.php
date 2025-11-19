<?php
include "conexion.php";

$hum = $_GET["humedad"];
$temp = $_GET["temperatura"];
$ID_TARJ = $_GET["ID_TARJ"];
$peso= $_GET["peso_colmena"];
$conteo = $_GET["conteo_abejas"];
$totalabejas = $_GET["total_abejas"];
$estadocolmena = $_GET["estado_colmena"];

$mysqli = new mysqli($host, $user, $pw, $db);

$sql1 = "INSERT INTO datos_medidos 
(ID_TARJ, temperatura, humedad, fecha, hora, peso_colmena, conteo_abejas, total_abejas) 
VALUES ('$ID_TARJ', '$temp', '$hum', CURDATE(), CURTIME(), '$peso','$conteo','$totalabejas')";

$result1 = $mysqli->query($sql1);

$sql2 = "UPDATE colmenas SET estado_colmena='$estadocolmena' WHERE id_tarjeta='$ID_TARJ'";
$result2 = $mysqli->query($sql2);
?>
