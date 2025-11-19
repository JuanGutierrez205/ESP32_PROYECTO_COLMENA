<?php
// PROGRAMA DE MENU APICULTORES
include "conexion.php";

session_start();
if ($_SESSION["autenticado"] != "SIx3") {
    header('Location: index.php?mensaje=3');
    exit;
} else {
    $mysqli = new mysqli($host, $user, $pw, $db);
    $sqlusu = "SELECT * from tipo_usuario where id='1'"; // APICULTORES
    $resultusu = $mysqli->query($sqlusu);
    $rowusu = $resultusu->fetch_array(MYSQLI_NUM);
    $desc_tipo_usuario = $rowusu[1];
    if ($_SESSION["tipo_usuario"] != $desc_tipo_usuario) {
        header('Location: index.php?mensaje=4');
        exit;
    }
}
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
    <link rel="stylesheet" href="css/estilos_virtual.css" type="text/css">
    <title>Gestión Usuarios Modif - MERCURY - Puntos de Venta Application</title>
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
            <b><u><?php  echo "Nombre Usuario</u>: ".$_SESSION["nombre_usuario"];?></b>
        </font><br>
        <font FACE="arial" SIZE=2 color="#000000"> 
            <b><u><?php  echo "Tipo Usuario</u>: ".$_SESSION["tipo_usuario"];?></b>
        </font><br>
        <font FACE="arial" SIZE=2 color="#00FFFF"> 
            <b><u><a href="cerrar_sesion.php">Cerrar Sesion</a></u></b>
        </font>
    </td>
</tr>

<?php include "menu_api.php"; ?>

<?php
if (isset($_POST["enviado"])) {
    $id_usu_enc = $_POST["id_usu"];
    $nombre_usuario = $_POST["nombre_usuario"];
    $nombre_usuario = str_replace("�","n",$nombre_usuario);
    $nombre_usuario = str_replace("�","N",$nombre_usuario);
    $num_id = $_POST["num_id"];
    $tipo_usuario = $_POST["tipo_usuario"];
    $direccion = $_POST["direccion"];
    $activo = $_POST["activo"];
    $password = $_POST["password"];
    $login = $_POST["login"];

    $mysqli = new mysqli($host, $user, $pw, $db);
    $mysqli->query("UPDATE usuarios SET nombre_completo='$nombre_usuario', login='$login', identificacion='$num_id', tipo_usuario='$tipo_usuario', direccion='$direccion', activo='$activo' WHERE id='$id_usu_enc'");

    if ($password != "") {
        $password_enc = md5($password);
        $mysqli->query("UPDATE usuarios SET passwd='$password_enc' WHERE id='$id_usu_enc'");
    }

    header('Location: gestion_usuarios.php?mensaje=1');
    exit;

} else {
    $id_usu_enc = $_GET["id_usu"];
    $mysqli = new mysqli($host, $user, $pw, $db);
    $sqlenc = "SELECT * from usuarios";
    $resultenc = $mysqli->query($sqlenc);
    while($rowenc = $resultenc->fetch_array(MYSQLI_NUM)) {  
        $id_usu  = $rowenc[0];
        if (md5($id_usu) == $id_usu_enc)
            $id_usu_enc = $id_usu;
    }

    $sql1 = "SELECT * from usuarios where id='$id_usu_enc'";
    $result1 = $mysqli->query($sql1);
    $row1 = $result1->fetch_array(MYSQLI_NUM);

    $nombre_usuario  = $row1[1];
    $tipo_usuario  = $row1[7];
    $num_id = $row1[2];
    $direccion= $row1[3];
    $activo= $row1[8];
    $login= $row1[5];

    $desc_activo = ($activo == 1) ? "S (Activo)" : "N (Inactivo)";

    $sql3 = "SELECT * from tipo_usuario where id='$tipo_usuario'";
    $result3 = $mysqli->query($sql3);
    $row3 = $result3->fetch_array(MYSQLI_NUM);
    $desc_tipo_usuario = $row3[1];
?>

<tr valign="top">
    <td width="50%" height="20%" align="left" bgcolor="#FFFFFF">
        <font FACE="arial" SIZE=2 color="#000044"> 
            <b><h1>Gestión Usuarios</h1> Modificando Usuario: <u><?php echo $nombre_usuario; ?></u></b>
        </font>  
    </td>
    <td width="50%" height="20%" align="right" bgcolor="#FFFFFF">
        <img src="img/gestion_usuarios.jpg" border=0 width=115 height=115>    
    </td>
</tr>
<tr>
<td colspan=2 width="25%" height="20%" align="left" bgcolor="#FFFFFF">
<form method=POST action="gestion_usuarios_mod.php">
<table width=50% border=1 align=center>
<tr>
<td bgcolor="#FFF3E0" align=center>Nombre Usuario</td>
<td bgcolor="#FFFFFF" align=center>
<input type="text" name=nombre_usuario value="<?php echo $nombre_usuario; ?>" required>
</td>
</tr>
<tr>
<td bgcolor="#FFF3E0" align=center>Número Id</td>
<td bgcolor="#FFFFFF" align=center>
<input type="number" name=num_id value="<?php echo $num_id; ?>" required>
</td>
</tr>
<tr>
<td bgcolor="#FFF3E0" align=center>Tipo Usuario</td>
<td bgcolor="#FFFFFF" align=center>
<select name=tipo_usuario required>
    <option value="<?php echo $tipo_usuario; ?>"><?php echo $desc_tipo_usuario; ?></option>
    <?php
    $sql6 = "SELECT * from tipo_usuario";
    $result6 = $mysqli->query($sql6);
    while($row6 = $result6->fetch_array(MYSQLI_NUM)) {
        $tipo_usuario_con = $row6[0];
        $desc_tipo_usuario_con = $row6[1];
        if ($tipo_usuario_con != $tipo_usuario)
            echo "<option value='$tipo_usuario_con'>$desc_tipo_usuario_con</option>";
    }
    ?>
</select>
</td>
</tr>
<tr>
<td bgcolor="#FFF3E0" align=center>Usuario</td>
<td bgcolor="#FFFFFF" align=center>
<input type="text" name=login value="<?php echo $login; ?>" required>
</td>
</tr>
<tr>
<td bgcolor="#FFF3E0" align=center>Clave (dejar en blanco para no cambiar)</td>
<td bgcolor="#FFFFFF" align=center>
<input type="password" name=password value="">
</td>
</tr>
<tr>
<td bgcolor="#FFF3E0" align=center>Dirección</td>
<td bgcolor="#FFFFFF" align=center>
<input type="text" name=direccion value="<?php echo $direccion; ?>" required>
</td>
</tr>
<tr>
<td bgcolor="#FFF3E0" align=center>Activo (S/N)</td>
<td bgcolor="#FFFFFF" align=center>
<select name=activo required>
    <option value="<?php echo $activo; ?>"><?php echo $desc_activo; ?></option>
    <?php
    $activo_con = 1;
    if ($activo_con != $activo) {
        echo "<option value='$activo_con'>S (Activo)</option>";
    } else {
        echo "<option value='0'>N (Inactivo)</option>";
    }
    ?>
</select>
</td>
</tr>
</table>
<br>
<input type="hidden" value="S" name="enviado">
<input type="hidden" value="<?php echo $id_usu_enc; ?>" name="id_usu">
<table width=50% align=center border=0>
<tr>
<td width=50%></td>
<td align=center><input style="background-color: #DBA926" type=submit value="Modificar" name="Modificar"></td>
<td align=left>
<form method=POST action="gestion_usuarios.php">
<input style="background-color: #EEEEEE" type=submit value="Volver" name="Volver">
</form>
</td>
</tr>
</table>
</form>

<br><br><hr>
</td>
</tr>

<?php
} // FIN else mostrar formulario
?>
</table>
</body>
</html>
