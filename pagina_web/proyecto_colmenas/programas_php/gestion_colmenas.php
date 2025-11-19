<?php
include "conexion.php";

session_start();
if (!isset($_SESSION["autenticado"]) || $_SESSION["autenticado"] != "SIx3") {
    header('Location: index.php?mensaje=3');
    exit();
}

$mysqli = new mysqli($host, $user, $pw, $db);
if ($mysqli->connect_errno) {
    die("Error conexión BD: " . $mysqli->connect_error);
}

// Recuperamos el tipo de usuario desde la sesión
$tipo_sesion = isset($_SESSION["tipo_usuario"]) ? $_SESSION["tipo_usuario"] : "";

// Verificamos que sea apicultor
$stmt = $mysqli->prepare("SELECT id, descripcion_tipo FROM tipo_usuario WHERE LOWER(descripcion_tipo) = LOWER(?) LIMIT 1");
$stmt->bind_param('s', $tipo_sesion);
$stmt->execute();
$resultusu = $stmt->get_result();
if ($resultusu->num_rows == 0) {
    header('Location: index.php?mensaje=4');
    exit();
}
$rowusu = $resultusu->fetch_assoc();
$desc_tipo_usuario = $rowusu['descripcion_tipo'];

if (strtolower($desc_tipo_usuario) != "apicultor") {
    header('Location: index.php?mensaje=5'); // acceso denegado
    exit();
}

$nombre_usuario = isset($_SESSION["nombre_usuario"]) ? $_SESSION["nombre_usuario"] : "";
$id_usuario = isset($_SESSION["id_usuario"]) ? $_SESSION["id_usuario"] : 0;

$stmt->close();
?>
<!DOCTYPE HTML>
<html>
<head>
    <title>Gestión de colmenas</title>
    <meta charset="utf-8">
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

<!-- Menú solo apicultores -->
<?php include "menu_api.php"; ?>

<table width="100%" align=center cellpadding=5 border=0 bgcolor="#FFFFFF">
    <tr valign="top">
        <td height="20%" align="left" bgcolor="#FFFFFF" class="_espacio_celdas" style="color: #000000; font-weight: bold">
            <font FACE="arial" SIZE=2 color="#e65100"><b><h1>Gestión de colmenas</h1></b></font>
        </td>
        <td height="20%" align="right" bgcolor="#FFFFFF" class="_espacio_celdas">
            <img src="img/Apiarios.jpg" border=0 width=115 height=115>
        </td>
    </tr>
</table>

<!-- Agregar colmena -->
<table width="100%" align=center cellpadding=5 border=0 bgcolor="#FFFFFF">
    <tr>
        <td align="center" bgcolor="#FFF3E0" style="padding:10px;">
            <font FACE="arial" SIZE=2 color="#e65100">
                <b>Para agregar una nueva colmena, <a href="agregar_colmena.php" style="color:#e65100; text-decoration:underline;">haz clic aquí</a></b>
            </font>
        </td>
    </tr>
</table>

<!-- Tabla de colmenas -->
<table width="100%" align=center cellpadding=5 border=0 bgcolor="#FFFFFF">
    <tr>
        <td colspan=2 height="20%" align="left" bgcolor="#FFFFFF" class="_espacio_celdas">
            <table width=90% border=1 align=center>
                <tr>
                    <td bgcolor="#ff9800" align=center><font FACE="arial" SIZE=2 color="#FFFFFF"><b>Estado</b></font></td>
                    <td bgcolor="#ff9800" align=center><font FACE="arial" SIZE=2 color="#FFFFFF"><b>Nombre Colmena</b></font></td>
                    <td bgcolor="#ff9800" align=center><font FACE="arial" SIZE=2 color="#FFFFFF"><b>ID Tarjeta</b></font></td>
                    <td bgcolor="#ff9800" align=center><font FACE="arial" SIZE=2 color="#FFFFFF"><b>Ubicación</b></font></td>
                    <td bgcolor="#ff9800" align=center><font FACE="arial" SIZE=2 color="#FFFFFF"><b>Latitud</b></font></td>
                    <td bgcolor="#ff9800" align=center><font FACE="arial" SIZE=2 color="#FFFFFF"><b>Longitud</b></font></td>
                    <td bgcolor="#ff9800" align=center><font FACE="arial" SIZE=2 color="#FFFFFF"><b>Responsable</b></font></td>
                    <td bgcolor="#ff9800" align=center><font FACE="arial" SIZE=2 color="#FFFFFF"><b>Modificar</b></font></td>
                    <td bgcolor="#ff9800" align=center><font FACE="arial" SIZE=2 color="#FFFFFF"><b>Mostrar</b></font></td>
                    <td bgcolor="#ff9800" align=center><font FACE="arial" SIZE=2 color="#FFFFFF"><b>Mapa</b></font></td>
                </tr>

                <?php
                $sql1 = "SELECT * FROM colmenas WHERE id_apicultor = $id_usuario ORDER BY id_tarjeta";
                $result1 = $mysqli->query($sql1);
                if ($result1) {
                    if ($result1->num_rows == 0) {
                        echo "<tr><td colspan='10' align='center'>No hay colmenas registradas para este usuario.</td></tr>";
                    } else {
                        while ($row1 = $result1->fetch_assoc()) {
                            ?>
                            <tr>
                                <td bgcolor="#FFF3E0" align=center><?php echo htmlspecialchars($row1['estado_colmena']); ?></td>
                                <td bgcolor="#FFF3E0" align=center><?php echo htmlspecialchars($row1['nombre_colmena']); ?></td>
                                <td bgcolor="#FFF3E0" align=center><?php echo htmlspecialchars($row1['id_tarjeta']); ?></td>
                                <td bgcolor="#FFF3E0" align=center><?php echo htmlspecialchars($row1['ubicacion']); ?></td>
                                <td bgcolor="#FFF3E0" align=center><?php echo htmlspecialchars($row1['latitud']); ?></td>
                                <td bgcolor="#FFF3E0" align=center><?php echo htmlspecialchars($row1['longitud']); ?></td>
                                <td bgcolor="#FFF3E0" align=center><?php echo htmlspecialchars($nombre_usuario); ?></td>
                                <td bgcolor="#FFF3E0" align=center>
                                    <a href="modificar_colmena.php?id_colmena=<?php echo urlencode($row1['id']); ?>">
                                        <img src="img/icono_editar.jpg" border=0 width=40 height=30>
                                    </a>
                                </td>
                                <td bgcolor="#FFF3E0" align=center>
                                    <a href="mostrar_colmena.php?id_colmena=<?php echo urlencode($row1['id']); ?>">
                                        <img src="img/Mostrar.png" border=0 width=40 height=30>
                                    </a>
                                </td>
                                <td bgcolor="#FFF3E0" align=center>
                                    <a href="mapa_consultar.php?latitud=<?php echo urlencode($row1['latitud']); ?>&longitud=<?php echo urlencode($row1['longitud']); ?>&estado=<?php echo urlencode($row1['estado_colmena']); ?>&nombre=<?php echo urlencode($row1['nombre_colmena']); ?>">
                                        <img src="img/mapa_100x100.png" border=0 width=40 height=30 title="Ver mapa">
                                    </a>
                                </td>
                            </tr>
                            <?php
                        }
                    }
                    $result1->free();
                } else {
                    echo "<tr><td colspan='10' align='center'>Error al consultar las colmenas.</td></tr>";
                }
                ?>
            </table>
        </td>
    </tr>
</table>

</body>
</html>
