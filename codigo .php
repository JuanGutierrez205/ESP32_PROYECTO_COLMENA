// config_colmena.php

<?php
header('Content-Type: application/json');
$conn = new mysqli('localhost', 'usuario', 'password', 'proy_int');
$res = $conn->query("SELECT nombre_parametro, valor_parametro FROM config_colmena");
$config = array();
while($row = $res->fetch_assoc()) {
    $config[$row['nombre_parametro']] = $row['valor_parametro'];
}
echo json_encode($config);
?>
