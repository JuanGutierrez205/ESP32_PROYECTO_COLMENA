<?php
// PROGRAMA DE MODIFICAR COLMENA
include "conexion.php";
session_start();

// Verificar si está autenticado
if (!isset($_SESSION["autenticado"]) || $_SESSION["autenticado"] != "SIx3") {
    header('Location: index.php?mensaje=3');
    exit();
}

$mysqli = new mysqli($host, $user, $pw, $db);
if ($mysqli->connect_errno) {
    die("Error conexión BD: " . $mysqli->connect_error);
}

// Traer el tipo de usuario con ID=1 (Apicultor)
$sqlusu = "SELECT * FROM tipo_usuario WHERE id='1'";
$resultusu = $mysqli->query($sqlusu);
$rowusu = $resultusu->fetch_array(MYSQLI_NUM);
$tipo_api = $rowusu[1];

// Verificamos si el usuario actual es apicultor
if ($_SESSION["tipo_usuario"] != $tipo_api) {
    header('Location: index.php?mensaje=4');
    exit();
}

// Variables de sesión
$nombre_usuario = $_SESSION["nombre_usuario"];
$tipo_usuario = $_SESSION["tipo_usuario"];
$id_usuario = $_SESSION["id_usuario"];

$mensaje_modificado = "";

// Si se envió el formulario, actualizamos los datos
if (isset($_POST["enviado"])) {
    $id_colmena_enc = $_POST["id_colmena"];
    $nombre_colmena = $_POST["nombre_colmena"];
    $id_tarjeta = $_POST["id_tarjeta"];
    $latitud = $_POST["latitud"];
    $longitud = $_POST["longitud"];
    $ubicacion = $_POST["ubicacion"];

    $sql = "UPDATE colmenas SET 
            nombre_colmena='$nombre_colmena',
            id_tarjeta='$id_tarjeta',
            latitud='$latitud',
            longitud='$longitud',
            ubicacion='$ubicacion',
            id_apicultor='$id_usuario'
            WHERE id='$id_colmena_enc'";
    $result = $mysqli->query($sql);

    if ($result) {
        header("Location: consulta_por_colmena.php?mensaje=modificado");
        exit();
    } else {
        $mensaje_modificado = '<div style="background-color:#f8d7da;color:#721c24;padding:10px;border-radius:5px;text-align:center;">
                                  Error al modificar la colmena. Intente nuevamente.
                              </div><br>';
    }
} else {
    // Traemos los datos de la colmena si no se envió el formulario
    $id_colmena_enc = $_GET["id_colmena"];
    $sql = "SELECT * FROM colmenas WHERE id='$id_colmena_enc'";
    $result = $mysqli->query($sql);
    $row = $result->fetch_array(MYSQLI_ASSOC);

    $nombre_colmena = $row['nombre_colmena'];
    $id_tarjeta = $row['id_tarjeta'];
    $latitud = $row['latitud'];
    $longitud = $row['longitud'];
    $ubicacion = $row['ubicacion'];
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Modificar Colmena - Sistema de Colmenas</title>
    <link rel="stylesheet" href="css/estilos_virtual.css" type="text/css">
    <script>
    window.onload = function() {
        // Si vienen lat/lng desde mapa_ubicacion.php, rellenar los campos
        if(localStorage.getItem("colmena_latitud") && localStorage.getItem("colmena_longitud")) {
            document.getElementById("latitud").value = localStorage.getItem("colmena_latitud");
            document.getElementById("longitud").value = localStorage.getItem("colmena_longitud");
            localStorage.removeItem("colmena_latitud");
            localStorage.removeItem("colmena_longitud");
        }
    }

    function seleccionarUbicacion() {
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
        <font FACE="arial" SIZE=2 color="#e65100">
            <b><u><a href="cerrar_sesion.php" style="color:#e65100;">Cerrar Sesión</a></u></b></font>
    </td>
</tr>

<?php include "menu_api.php"; ?>

<tr valign="top">
    <td colspan="2" align="center">
        <h1>Modificar Colmena</h1>

        <?php
        if (!empty($mensaje_modificado)) {
            echo $mensaje_modificado;
        }
        ?>

        <form method="POST" action="modificar_colmena.php">
            <table width="50%" border="1" align="center">
                <tr>
                    <td bgcolor="#FFF3E0" align="center"><b>Nombre Colmena</b></td>
                    <td bgcolor="#FFFFFF" align="center">
                        <input type="text" name="nombre_colmena" value="<?php echo $nombre_colmena; ?>" required>
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#FFF3E0" align="center"><b>ID Tarjeta</b></td>
                    <td bgcolor="#FFFFFF" align="center">
                        <input type="number" name="id_tarjeta" min="1" max="9" value="<?php echo $id_tarjeta; ?>" required>
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#FFF3E0" align="center"><b>Latitud</b></td>
                    <td bgcolor="#FFFFFF" align="center">
                        <input type="text" id="latitud" name="latitud" value="<?php echo $latitud; ?>" required readonly>
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#FFF3E0" align="center"><b>Longitud</b></td>
                    <td bgcolor="#FFFFFF" align="center">
                        <input type="text" id="longitud" name="longitud" value="<?php echo $longitud; ?>" required readonly>
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#FFF3E0" align="center"><b>Ubicación en el mapa</b></td>
                    <td bgcolor="#FFFFFF" align="center">
                        <button type="button" onclick="window.location.href='mapa_ubicacion.php?origen=modificar&id_colmena=<?php echo $id_colmena_enc; ?>'">
                        Seleccionar ubicación
                        </button>
                        <small>Haz clic para elegir la latitud y longitud en el mapa</small>
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#FFF3E0" align="center"><b>Ubicación</b></td>
                    <td bgcolor="#FFFFFF" align="center">
                        <input type="text" name="ubicacion" value="<?php echo $ubicacion; ?>">
                    </td>
                </tr>
            </table>
            <br>
            <input type="hidden" name="enviado" value="S">
            <input type="hidden" name="id_colmena" value="<?php echo $id_colmena_enc; ?>">

            <input type="submit" style="background-color: #DBA926; color: #FFFFFF; padding: 8px 15px; border:none; border-radius:5px;" value="Modificar">
            <input type="button" style="background-color: #EEEEEE; padding: 8px 15px; border:none; border-radius:5px;" value="Volver" onclick="window.location.href='gestion_colmenas.php';">
        </form>
    </td>
</tr>
</table>
</body>
</html>
