<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h2>Probando registro</h2>";
include "conexion.php";
include "registro_usuario.php"; // o el archivo donde falla (login.php, validar.php, etc)
include "validar.php";
?>
