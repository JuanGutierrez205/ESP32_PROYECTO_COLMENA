<?php
// PROGRAMA DE AGREGAR COLMENA
include "conexion.php";
session_start();

// Verificar si está autenticado
if ($_SESSION["autenticado"] != "SIx3") {
    header('Location: index.php?mensaje=3');
    exit;
} else {
    $mysqli = new mysqli($host, $user, $pw, $db);
    $sqlusu = "SELECT * FROM tipo_usuario WHERE id='1'";
    $resultusu = $mysqli->query($sqlusu);
    $rowusu = $resultusu->fetch_array(MYSQLI_NUM);
    $tipo_api = $rowusu[1];

    if ($_SESSION["tipo_usuario"] != $tipo_api) {
        header('Location: index.php?mensaje=4');
        exit;
    }
}

// Variables de sesión
$nombre_usuario = $_SESSION["nombre_usuario"];
$tipo_usuario = $_SESSION["tipo_usuario"];
$id_usuario = $_SESSION["id_usuario"];

$mensaje_agregado = "";
$agregado = false;

if (isset($_POST["enviado"])) {
    $nombre_colmena = $_POST["nombre_colmena"];
    $id_tarjeta = $_POST["id_tarjeta"];
    $latitud = $_POST["latitud"];
    $longitud = $_POST["longitud"];
    $ubicacion = $_POST["ubicacion"];
    $sql = "INSERT INTO colmenas (nombre_colmena, id_tarjeta, latitud, longitud, ubicacion, id_apicultor) 
            VALUES ('$nombre_colmena', '$id_tarjeta', '$latitud', '$longitud', '$ubicacion', '$id_usuario')";
    $result = $mysqli->query($sql);

    if ($result) {
        $mensaje_agregado = '<div style="background-color:#d4edda;color:#155724;padding:10px;border-radius:5px;text-align:center;">
                                 Colmena agregada correctamente.
                            </div><br>';
        $agregado = true;
    } else {
        $mensaje_agregado = '<div style="background-color:#f8d7da;color:#721c24;padding:10px;border-radius:5px;text-align:center;">
                                 Error al agregar la colmena. Intente nuevamente.
                            </div><br>';
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Agregar Colmena - Sistema de Colmenas</title>
    <link rel="stylesheet" href="css/estilos_virtual.css" type="text/css">
    <script>
    window.onload = function() {
        if(localStorage.getItem("colmena_latitud") && localStorage.getItem("colmena_longitud")) {
            document.getElementById("latitud").value = localStorage.getItem("colmena_latitud");
            document.getElementById("longitud").value = localStorage.getItem("colmena_longitud");
            localStorage.removeItem("colmena_latitud");
            localStorage.removeItem("colmena_longitud");
        }
    }
    function agregarUbicacion() {
        window.location.href = "mapa_ubicacion.php";
    }
    </script>
</head>
<body>
<table width="100%" align="center" cellpadding="5" border="0" bgcolor="#FFFFFF">
<tr>
    <td valign="top" align="left">
        <img src="img/panal.jpeg" border="0" width="350" height="80">
    </td>
    <td valign="top" align="right">
        <font FACE="arial" SIZE=2 color="#000000">
            <b><u><?php echo "Nombre Usuario</u>: ".$nombre_usuario;?></b></font><br>
        <font FACE="arial" SIZE=2 color="#000000">
            <b><u><?php echo "Tipo Usuario</u>: ".$tipo_usuario;?></b></font><br>
        <font FACE="arial" SIZE=2 color="#ffbb00ff">
            <b><u><a href="cerrar_sesion.php">Cerrar Sesión</a></u></b></font>
    </td>
</tr>
<?php include "menu_api.php"; ?>
<tr valign="top">
    <td colspan="2" align="center">
        <h1>Agregar Colmena</h1>

        <?php
        if ($mensaje_agregado != "") {
            echo $mensaje_agregado;
        }
        ?>

        <?php if (!$agregado): ?>
        <form method="POST" action="agregar_colmena.php">
            <table width="50%" border="1" align="center">
                <tr>
                    <td bgcolor="#FFF3E0" align="center"><b>Nombre Colmena</b></td>
                    <td bgcolor="#EEEEEE" align="center">
                        <input type="text" name="nombre_colmena" required>
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#FFF3E0" align="center"><b>ID Tarjeta</b></td>
                    <td bgcolor="#EEEEEE" align="center">
                        <input type="number" name="id_tarjeta" min="1" max="9" required>
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#FFF3E0" align="center"><b>Latitud</b></td>
                    <td bgcolor="#EEEEEE" align="center">
                        <input type="text" id="latitud" name="latitud" required readonly>
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#FFF3E0" align="center"><b>Longitud</b></td>
                    <td bgcolor="#EEEEEE" align="center">
                        <input type="text" id="longitud" name="longitud" required readonly>
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#FFF3E0" align="center"><b>Ubicación en el mapa</b></td>
                    <td bgcolor="#EEEEEE" align="center">
                        <button type="button" onclick="window.location.href='mapa_ubicacion.php?origen=agregar'">
                        Seleccionar ubicación
                        </button>
                        <small>Haz clic para elegir la latitud y longitud en el mapa</small>
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#FFF3E0" align="center"><b>Ubicación</b></td>
                    <td bgcolor="#EEEEEE" align="center">
                        <input type="text" name="ubicacion">
                    </td>
                </tr>
            </table>
            <br>
            <input type="hidden" name="enviado" value="S">
            <input type="submit" style="background-color: #DBA926; color:#FFFFFF; padding:8px 15px; border:none; border-radius:5px;" value="Agregar">
            <a href="gestion_colmenas.php"><input type="button" style="background-color: #EEEEEE; padding:8px 15px; border:none; border-radius:5px;" value="Volver"></a>
        </form>
        <?php else: ?>
            <a href="gestion_colmenas.php"><input type="button" style="background-color: #EEEEEE; padding:8px 15px; border:none; border-radius:5px;" value="Volver"></a>
        <?php endif; ?>
    </td>
</tr>
</table>
</body>
</html>
