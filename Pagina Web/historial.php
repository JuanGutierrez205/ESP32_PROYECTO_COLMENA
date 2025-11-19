<?php
include "conexion.php";  
session_start();

if ($_SESSION["autenticado"] != "SIx3") {
    header('Location: index.php?mensaje=3');
    exit();
} else {      
    $mysqli = new mysqli($host, $user, $pw, $db);
    $nombre_usuario = isset($_SESSION["nombre_usuario"]) ? $_SESSION["nombre_usuario"] : "";

    // Traer descripción del tipo de usuario desde la sesión
    $desc_tipo_usuario = $_SESSION["tipo_usuario"];

    // Verificamos si el usuario en sesión es Apicultor
    $is_api = (strtolower($desc_tipo_usuario) == "apicultor");
}
?>
<!DOCTYPE HTML>
<html>
<head>
    <title>Datos medidos del Apiario por rango de fechas</title>
    <style>
    /* Estilo igual al de la página de colmena */
        .tabla-datos {
            border-collapse: collapse;
            width: 70%;
            margin: auto;
            background-color: #FFFFFF;
        }
        .tabla-datos th,
        .tabla-datos td {
            border: 1px solid #f39c12;
            padding: 8px;
            text-align: center;
        }
        .tabla-datos th {
            background-color: #f8c471;
            color: #ffffff;
        }
        h1, h2, h3 { color: #e65100; }

        /* Botón y etiquetas de formulario en el mismo estilo */
        .btn {
            background-color: #e65100;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 7px;
            cursor: pointer;
            font-family: Arial, sans-serif;
            font-weight: 600;
        }
        .btn:hover { opacity:.9; }
        .form-label {
            color: #e65100;
            font-weight: bold;
            font-size: 16px;
        }
        .inline-form { display:inline-block; margin-left:12px; }
    </style>
</head>
<body>
<table width="100%" align=center cellpadding=5 border=0 bgcolor="#FFFFFF">
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

<?php
if ((isset($_POST["enviado"]))) {
    $enviado = $_POST["enviado"];
    if ($enviado == "S1") {
        $fecha_ini = $_POST["fecha_ini"];
        $fecha_fin = $_POST["fecha_fin"];
        $id_colmena = isset($_POST["id_colmena"]) ? intval($_POST["id_colmena"]) : 0;

        if ($is_api) {
            // Apicultor: ve todo
            $sql1 = "SELECT dm.* 
                     FROM datos_medidos dm
                     INNER JOIN colmenas c ON dm.ID_TARJ = c.id_tarjeta
                     WHERE c.id = $id_colmena 
                     AND dm.fecha >= '$fecha_ini' AND dm.fecha <= '$fecha_fin'
                     ORDER BY dm.fecha, dm.hora";
        } else {
            // Consulta: solo ve temp y humedad
            $sql1 = "SELECT dm.id, dm.ID_TARJ, dm.temperatura, dm.humedad, dm.fecha, dm.hora 
                     FROM datos_medidos dm
                     INNER JOIN colmenas c ON dm.ID_TARJ = c.id_tarjeta
                     WHERE c.id = $id_colmena 
                     AND dm.fecha >= '$fecha_ini' AND dm.fecha <= '$fecha_fin'
                     ORDER BY dm.fecha, dm.hora";
        }

        $result1 = $mysqli->query($sql1);
        ?>
        <table class="tabla-datos">
        <tr>
            <th colspan=<?php echo $is_api ? 9 : 6; ?>>
                Rango de fechas consultado: desde <?php echo htmlspecialchars($fecha_ini); ?> hasta <?php echo htmlspecialchars($fecha_fin); ?>
                <!-- Botón Ver gráficas (alineado en el mismo renglón) -->
                <br><br>
                <form method="POST" action="ver_graficas_rango.php" class="inline-form">
                    <input type="hidden" name="fecha_ini" value="<?php echo htmlspecialchars($fecha_ini); ?>">
                    <input type="hidden" name="fecha_fin" value="<?php echo htmlspecialchars($fecha_fin); ?>">
                    <!-- Enviamos id_colmena por si luego lo usas en ver_graficas_rango.php -->
                    <input type="hidden" name="id_colmena" value="<?php echo intval($id_colmena); ?>">
                    <button type="submit" class="btn"> Ver gráficas</button>
                </form>
            </th>
        </tr>
        <tr>
            <th>#</th>
            <th>Id de la Tarjeta</th>
            <th>Fecha</th>
            <th>Hora</th>
            <th>Temperatura</th>
            <th>Humedad</th>
            <?php if ($is_api) { ?>
                <th>Peso Colmena</th>
                <th>No. Abejas</th>
                <th>No. Inicial abejas</th>
            <?php } ?>
        </tr>
        <?php
        $contador = 0;
        while($row1 = $result1->fetch_array(MYSQLI_ASSOC)) {
            $contador++;
            ?>
            <tr>
                <td><?php echo $contador; ?></td>
                <td><?php echo htmlspecialchars($row1["ID_TARJ"]); ?></td>
                <td><?php echo htmlspecialchars($row1["fecha"]); ?></td>
                <td><?php echo htmlspecialchars($row1["hora"]); ?></td>
                <td><?php echo htmlspecialchars($row1["temperatura"])." °C"; ?></td>
                <td><?php echo htmlspecialchars($row1["humedad"])." %"; ?></td>
                <?php if ($is_api) { ?>
                    <td><?php echo htmlspecialchars($row1["peso_colmena"])." g"; ?></td>
                    <td><?php echo htmlspecialchars($row1["conteo_abejas"]); ?></td>
                    <td><?php echo htmlspecialchars($row1["total_abejas"]); ?></td>
                <?php } ?>
            </tr>
            <?php
        }
        ?>
        <tr>
            <td align="center" colspan=<?php echo $is_api ? 9 : 6; ?>>
                <form method="GET" action="mostrar_colmena.php" style="display:inline-block;">
                    <input type="hidden" name="id_colmena" value="<?php echo intval($id_colmena); ?>">
                    <button type="submit" class="btn">Volver</button>
                </form>
            </td>
        </tr>

        </table>
        <?php
    }
} else {
?>
<table class="tabla-datos">
<form method=POST action="historial.php">
<tr>
    <td align=center class="form-label">Fecha Inicial:</td>
    <td><input type="date" name="fecha_ini" required></td>
</tr>
<tr>
    <td align=center class="form-label">Fecha Final:</td>
    <td><input type="date" name="fecha_fin" required></td>
</tr>
<tr>
    <td align=center colspan=2 style="padding-top: 15px;">
        <input type="hidden" name="enviado" value="S1">
        <input type="hidden" name="id_colmena" value="<?php echo isset($_POST['id_colmena']) ? intval($_POST['id_colmena']) : 0; ?>">
        <input type="submit" class="btn" value="Consultar" name="Consultar">
    </td>
</tr>
</form>
</table>
<?php
}
?>
<hr><br><br>
</body>
</html>
