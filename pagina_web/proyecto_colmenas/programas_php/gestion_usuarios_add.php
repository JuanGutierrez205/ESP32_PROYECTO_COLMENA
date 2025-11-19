<?php
// PROGRAMA DE MENU APICULTOR
include "conexion.php";

session_start();
if ($_SESSION["autenticado"] != "SIx3") {
    header('Location: index.php?mensaje=3');
    exit;
} else {      
    $mysqli = new mysqli($host, $user, $pw, $db);
    $sqlusu = "SELECT * from tipo_usuario where id='1'"; // APICULTOR
    $resultusu = $mysqli->query($sqlusu);
    $rowusu = $resultusu->fetch_array(MYSQLI_NUM);
    $desc_tipo_usuario = $rowusu[1];
    if ($_SESSION["tipo_usuario"] != $desc_tipo_usuario) {
        header('Location: index.php?mensaje=4');
        exit;
    }
}
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <link rel="stylesheet" href="css/estilos_virtual.css" type="text/css">
    <title>Gestión Usuarios Adicionar</title>
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
            <b><u><?php echo "Nombre Usuario</u>: ".$_SESSION["nombre_usuario"];?></b>
        </font><br>
        <font FACE="arial" SIZE=2 color="#000000"> 
            <b><u><?php echo "Tipo Usuario</u>: ".$desc_tipo_usuario;?></b>
        </font><br>  
        <font FACE="arial" SIZE=2 color="#00FFFF"> 
            <b><u><a href="cerrar_sesion.php">Cerrar Sesion</a></u></b>
        </font>  
    </td>
</tr>

<?php
include "menu_api.php";

if (isset($_POST["enviado"])) {
    $nombre_usuario = str_replace(["�","�"], ["n","N"], $_POST["nombre_usuario"]);
    $num_id = $_POST["num_id"];
    $tipo_usuario = $_POST["tipo_usuario"];
    $direccion = $_POST["direccion"];
    $login = $_POST["login"];
    $activo = $_POST["activo"];
    $password = $_POST["password"];
    $password_enc = md5($password);

    $mysqli = new mysqli($host, $user, $pw, $db);
    $sqlcon = "SELECT * FROM usuarios WHERE identificacion='$num_id'";
    $resultcon = $mysqli->query($sqlcon);
    if ($resultcon->num_rows > 0) {
        header('Location: gestion_usuarios.php?mensaje=5');
        exit;
    } else {
        $sql = "INSERT INTO usuarios(tipo_usuario, nombre_completo, identificacion, passwd, direccion, login, activo) 
                VALUES ('$tipo_usuario','$nombre_usuario','$num_id','$password_enc','$direccion','$login','$activo')";
        $result1 = $mysqli->query($sql);
        if ($result1) {
            header('Location: gestion_usuarios.php?mensaje=3');
            exit;
        } else {
            header('Location: gestion_usuarios.php?mensaje=4');
            exit;
        }
    }
} else {
?>

<tr valign="top">
    <td width="50%" height="20%" align="left" bgcolor="#FFFFFF">
        <font FACE="arial" SIZE=2 color="#000044"> 
            <b><h1>Gestión Usuarios</h1> Adición Usuario</b>
        </font>  
    </td>
    <td width="50%" height="20%" align="right" bgcolor="#FFFFFF">
        <img src="img/gestion_usuarios.jpg" border=0 width=115 height=115>    
    </td>
</tr>

<tr>
<td colspan=2 width="25%" height="20%" align="left" bgcolor="#FFFFFF">
<form method="POST" action="gestion_usuarios_add.php">
<table width=50% border=1 align=center>
<tr>  
    <td bgcolor="#FFF3E0" align=center>Nombre Usuario</td>
    <td bgcolor="#FFFFFF" align=center>
        <input type="text" name="nombre_usuario" value="" required>
    </td>  
</tr>
<tr>
    <td bgcolor="#FFF3E0" align=center>Número Id</td>
    <td bgcolor="#FFFFFF" align=center>
        <input type="number" name="num_id" value="" required>
    </td>  
</tr>
<tr>
    <td bgcolor="#FFF3E0" align=center>Tipo Usuario</td>
    <td bgcolor="#FFFFFF" align=center>
        <select name="tipo_usuario" required>
            <?php
            $sql6 = "SELECT * from tipo_usuario order by id DESC";
            $result6 = $mysqli->query($sql6);
            while($row6 = $result6->fetch_array(MYSQLI_NUM)) {
                echo "<option value='".$row6[0]."'>".$row6[1]."</option>";
            }
            ?>
        </select>
    </td>  
</tr>
<tr>
    <td bgcolor="#FFF3E0" align=center>Usuario</td>
    <td bgcolor="#FFFFFF" align=center>
        <input type="text" name="login" value="" required>
    </td>  
</tr>
<tr>
    <td bgcolor="#FFF3E0" align=center>Clave</td>
    <td bgcolor="#FFFFFF" align=center>
        <input type="password" name="password" value="" required>
    </td>  
</tr>
<tr>
    <td bgcolor="#FFF3E0" align=center>Dirección</td>
    <td bgcolor="#FFFFFF" align=center>
        <input type="text" name="direccion" value="" required>
    </td>  
</tr>
<tr>
    <td bgcolor="#FFF3E0" align=center>Teléfono</td>
    <td bgcolor="#FFFFFF" align=center>
        <input type="number" name="telefono" value="">
    </td>  
</tr>
<tr>
    <td bgcolor="#FFF3E0" align=center>Activo (S/N)</td>
    <td bgcolor="#FFFFFF" align=center>
        <select name="activo" required>
            <option value="1">S (Activo)</option>  
            <option value="0">N (Inactivo)</option>  
        </select>
    </td>  
</tr>
</table>
<br>
<input type="hidden" value="S" name="enviado">
<table width=50% align=center border=0>
<tr>  
    <td width=50%></td>                                                                       
    <td align=center>
        <input style="background-color: #DBA926" type="submit" value="Grabar" name="Modificar">
    </td>  
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

