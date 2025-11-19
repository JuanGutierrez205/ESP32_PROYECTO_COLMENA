<?php
// PROGRAMA PARA MOSTRAR DATOS DE UNA COLMENA ESPECÍFICA
include "conexion.php";
session_start();

if ($_SESSION["autenticado"] != "SIx3") {
    header('Location: index.php?mensaje=3');
    exit();
} else {
    $mysqli = new mysqli($host, $user, $pw, $db);
    if ($mysqli->connect_errno) {
        die("Error de conexión a la base de datos: " . $mysqli->connect_error);
    }

    // CONSULTA EL TIPO DE USUARIO CON ID=1 (APICULTOR)
    $sqlusu = "SELECT * FROM tipo_usuario WHERE id='1'";
    $resultusu = $mysqli->query($sqlusu);
    $rowusu = $resultusu ? $resultusu->fetch_array(MYSQLI_NUM) : null;
    $desc_tipo_usuario = ($rowusu && isset($rowusu[1])) ? $rowusu[1] : 'Apicultor';
    $nombre_usuario = isset($_SESSION["nombre_usuario"]) ? $_SESSION["nombre_usuario"] : "";

    if ($_SESSION["tipo_usuario"] != $desc_tipo_usuario) {
        $is_api = false;
        $desc_tipo_usuario = isset($_SESSION["tipo_usuario"]) ? $_SESSION["tipo_usuario"] : $desc_tipo_usuario;
    } else {
        $is_api = true;
    }

    if (!$is_api && isset($_SESSION["tipo_usuario"]) && is_numeric($_SESSION["tipo_usuario"]) && (int)$_SESSION["tipo_usuario"] === 1) {
        $is_api = true;
    }

    // Validar colmena seleccionada
    if (!isset($_GET['id_colmena']) || !is_numeric($_GET['id_colmena'])) {
        die("Colmena no válida");
    }
    $id_colmena = intval($_GET['id_colmena']);

    // Obtener info de la colmena
    $sql_colmena = "SELECT * FROM colmenas WHERE id = $id_colmena";
    $result_colmena = $mysqli->query($sql_colmena);
    if (!$result_colmena || $result_colmena->num_rows == 0) {
        die("Colmena no encontrada");
    }
    $colmena = $result_colmena->fetch_assoc();

    // Obtener datos medidos de la colmena seleccionada
    $id_tarjeta = $colmena['id_tarjeta'];
    $sql_datos = "SELECT * FROM datos_medidos WHERE ID_TARJ = $id_tarjeta ORDER BY id DESC LIMIT 10";
    $result_datos = $mysqli->query($sql_datos);

    // Valores por defecto para los inputs del historial
    $fecha_ini_val = isset($_POST['fecha_ini']) ? $_POST['fecha_ini'] : '';
    $fecha_fin_val = isset($_POST['fecha_fin']) ? $_POST['fecha_fin'] : '';
}
?>

<!DOCTYPE HTML>
<html>
<head>
    <title>Datos de la Colmena <?php echo htmlspecialchars($colmena['nombre_colmena'], ENT_QUOTES); ?></title>
    <meta http-equiv="refresh" content="15" />
    <style>
        .table-datos {
            border-collapse: collapse;
            width: 70%;
            margin: auto;
            background-color: #FFFFFF;
        }
        .table-datos th,
        .table-datos td {
            border: 1px solid #f39c12;
            padding: 8px;
            text-align: center;
        }
        .table-datos th {
            background-color: #f8c471;
            color: #ffffff;
        }
        h1, h2, h3 { color: #e65100; }
        .btn {
            background-color: #e65100;
            color: white;
            border: none;
            padding: 10px 25px;
            border-radius: 8px;
            cursor: pointer;
            font-family: Arial, sans-serif;
            font-size: 15px;
            text-decoration: none;
            text-align: center;
            transition: background-color 0.2s ease;
        }
        .btn:hover {
            background-color: #cc5200;
        }
        .btn-equal {
            width: 240px;
            display: inline-block;
        }
        .top-actions {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 20px;
            margin-bottom: 25px;
            flex-wrap: wrap;
        }
        .form-historial {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
            justify-content: center;
        }
        .form-historial label {
            font-family: Arial, sans-serif;
            font-size: 14px;
            color: #000;
        }
        .form-historial input[type="date"] {
            padding: 6px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
        }
    </style>
</head>
<body>

<table width="100%" align=center cellpadding=5 border=0 bgcolor="#FFFFFF" class="table-inicio">
    <tr>
        <td valign="top" align=left width=70%>
            <table width="100%" align=center border=0>
                <tr>
                    <td valign="top" align=center width=30%>
                        <img src="img/panal.jpeg" border=0 width=350 height=80>
                    </td>
                    <td valign="top" align=center width=60%>
                        <h1><font color="#e65100">Sistema de Monitoreo de Apiario</font></h1>
                    </td>
                </tr>
            </table>
        </td>
        <td valign="top" align=right>
            <font FACE="arial" SIZE=2 color="#000000">
                <b><u><?php echo "Nombre Usuario</u>:   " . htmlspecialchars($nombre_usuario); ?></b>
            </font><br>
            <font FACE="arial" SIZE=2 color="#000000">
                <b><u><?php echo "Tipo Usuario</u>:   " . htmlspecialchars($desc_tipo_usuario); ?></b>
            </font><br>
            <font FACE="arial" SIZE=2 color="#ff9800">
                <b><u><a href="cerrar_sesion.php" style="color:#e65100;">Cerrar Sesión</a></u></b>
            </font>
        </td>
    </tr>
</table>

<?php 
    if ($is_api) { 
        include "menu_api.php"; 
    } else { 
        include "menu_consul.php";
    }
?>

<h1 align="center">Datos de la Colmena: <?php echo htmlspecialchars($colmena['nombre_colmena'], ENT_QUOTES); ?></h1>
<h3 align="center">Ubicación: <?php echo htmlspecialchars($colmena['ubicacion'], ENT_QUOTES); ?> 
    (Lat: <?php echo $colmena['latitud']; ?>, Lng: <?php echo $colmena['longitud']; ?>)</h3>
<br>

<!-- BOTÓN VOLVER Y FORMULARIO DE HISTORIAL -->
<div class="top-actions">
    <a href="consulta_por_colmena.php" class="btn btn-equal">Volver a la lista de colmenas</a>

    <form method="POST" action="historial.php" class="form-historial">
        <input type="hidden" name="id_colmena" value="<?php echo $id_colmena; ?>">
        <input type="hidden" name="enviado" value="S1">

        <label for="fecha_ini"><b>Fecha inicial:</b></label>
        <input type="date" name="fecha_ini" required value="<?php echo htmlspecialchars($fecha_ini_val); ?>">
        <label for="fecha_fin"><b>Fecha final:</b></label>
        <input type="date" name="fecha_fin" required value="<?php echo htmlspecialchars($fecha_fin_val); ?>">
        <button type="submit" class="btn btn-equal">Consultar historial</button>
    </form>
</div>

<!-- TABLA DE LOS ÚLTIMOS 10 DATOS -->
<table class="table-datos">
   <tr>
       <th>#</th>
       <th>Fecha</th>
       <th>Hora</th>
       <th>Temperatura (°C)</th>
       <th>Humedad (%)</th>
       <?php if ($is_api) { ?>
           <th>Peso (g)</th>
           <th>No. Abejas</th>
           <th>No. Inicial abejas</th>
       <?php } ?>
   </tr>
<?php
$contador = 0;
while ($result_datos && $row = $result_datos->fetch_assoc()) {
    $contador++;
?>
   <tr>
       <td><?php echo $contador; ?></td>
       <td><?php echo htmlspecialchars($row['fecha'], ENT_QUOTES); ?></td>
       <td><?php echo htmlspecialchars($row['hora'], ENT_QUOTES); ?></td>
       <td><?php echo htmlspecialchars($row['temperatura'], ENT_QUOTES)." "; ?></td>
       <td><?php echo htmlspecialchars($row['humedad'], ENT_QUOTES)." "; ?></td>
       <?php if ($is_api) { ?>
           <td><?php echo htmlspecialchars($row['peso_colmena'], ENT_QUOTES)." "; ?></td>
           <td><?php echo htmlspecialchars($row['conteo_abejas'], ENT_QUOTES); ?></td>
           <td><?php echo htmlspecialchars($row['total_abejas'], ENT_QUOTES); ?></td>
       <?php } ?>
   </tr>
<?php } ?>
</table>

</body>
</html>
