<?php

// PROGRAMA DE MENU APICULTOR
include "conexion.php";

session_start();
if ($_SESSION["autenticado"] != "SIx3")
{
    header('Location: index.php?mensaje=3');
    exit();
}
else
{      
    $mysqli = new mysqli($host, $user, $pw, $db);
    $sqlusu = "SELECT * from tipo_usuario where id='1'"; //CONSULTA EL TIPO DE USUARIO CON ID=1, Apicultor
    $resultusu = $mysqli->query($sqlusu);
    $rowusu = $resultusu->fetch_array(MYSQLI_NUM);
    $desc_tipo_usuario = $rowusu[1];
    if ($_SESSION["tipo_usuario"] != $desc_tipo_usuario)
        header('Location: index.php?mensaje=4');
}

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <title>Gestion De Usuarios</title>
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
          <font FACE="arial" SIZE=2 color="#000000"> <b><u><?php  echo "Nombre Usuario</u>:   ".$_SESSION["nombre_usuario"];?> </b></font><br>
          <font FACE="arial" SIZE=2 color="#000000"> <b><u><?php  echo "Tipo Usuario</u>:   ".$desc_tipo_usuario;?> </b></font><br>  
          <font FACE="arial" SIZE=2 color="#ff9800"> <b><u> <a href="cerrar_sesion.php" style="color:#e65100;"> Cerrar Sesion </a></u></b></font>  
       </td>
   </tr>
</table>

<table width="100%" align=center cellpadding=5 border=0 bgcolor="#FFFFFF">
<?php include "menu_api.php"; ?>
<tr valign="top">
   <td height="20%" align="left" bgcolor="#FFFFFF">
       <font FACE="arial" SIZE=2 color="#e65100"> <b><h1>Gesti&oacute;n de usuarios</h1></b></font>
   </td>
   <td height="20%" align="right" bgcolor="#FFFFFF">
       <img src="img/Apiarios.jpg" border=0 width=115 height=115>    
   </td>
</tr>
</table>

<table width="100%" align=center cellpadding=5 border=0 bgcolor="#FFFFFF">
  <tr>
   <td align=left width=50%>
    <form action="gestion_usuarios.php" method="POST">
     <table border=0 width=100%>   
      <tr>
       <td align=left>
         <font FACE="arial" SIZE=2 color="#000000">Consul. por Identif.: <input type="number" name=id_con value=""></font>
       </td>
       <td align=right>
         <font FACE="arial" SIZE=2 color="#000000">Consul. por Nombre: <input type="text" name=nombre_con value=""></font>
       </td>
      </tr>
     </table>
    </td>
   <td align=left width=50%>
     <table border=0 width=100%>   
      <tr>
       <td align=right width=50%>
         <font FACE="arial" SIZE=2 color="#000000">Estado Usuario: 
         <select name=estado>
         <?php
         if (isset($_POST["estado"]))
         {
             $estado = $_POST["estado"];
             if ($_POST["estado"]!="")
             {  
                 if ($estado == "2")
                 {
                     echo "<option value=".$estado."> Todos los Usuarios</option>";
                     echo "<option value=1> Usuarios solo Activos</option>";
                     echo "<option value=0> Usuarios solo Inactivos</option>";
                 }
                 else if ($estado == "1")
                 {
                     echo "<option value=".$estado."> Usuarios solo Activos</option>";
                     echo "<option value=2> Todos los Usuarios</option>";
                     echo "<option value=0> Usuarios solo Inactivos</option>";
                 }
                 else if ($estado == "0")
                 { 
                     echo "<option value=".$estado."> Usuarios solo Inactivos</option>";
                     echo "<option value=2> Todos los Usuarios</option>";
                     echo "<option value=1> Usuarios solo Activos</option>";
                 }
             }  
         }
         else
         {
         ?>
             <option value=2> Todos los Usuarios</option>
             <option value=1> Usuarios solo Activos </option>
             <option value=0> Usuarios solo Inactivos </option>
         <?php
         }
         ?>  
         </select>
       </td>
       <td align=center width=50%>
         <font FACE="arial" SIZE=2 color="#000000"><input type="submit" name=Consultar value="Consultar"></font>
       </td>
      </tr>
     </table>
      <input type="hidden" value="1" name="enviado">
     </form>
    </td>
  </tr>

  <tr>
   <td>&nbsp;&nbsp;&nbsp;</td>
   <td align=center>
    <a href="gestion_usuarios_add.php" style="color:#e65100;"> <b>Agregar Nuevo Usuario </b></a>    
   </td>
  </tr>

<?php
if (isset($_GET["mensaje"]))
{
    $mensaje = $_GET["mensaje"];
    if ($mensaje != "")
    {
?>
     <tr>
       <td> </td>
       <td height="20%" align="left">
            <table width=60% border=1>
             <tr>
              <?php 
                 if ($mensaje == 1)
                     echo "<td bgcolor=#FFECB3 class=_espacio_celdas_p style=color: #000000; font-weight: bold >Usuario actualizado correctamente.";
                 if ($mensaje == 2)
                     echo "<td bgcolor=#FFE0B2 class=_espacio_celdas_p style=color: #000000; font-weight: bold >Usuario no fue actualizado correctamente.";
                 if ($mensaje == 3)
                     echo "<td bgcolor=#FFECB3 class=_espacio_celdas_p style=color: #000000; font-weight: bold >Usuario creado correctamente.";
                 if ($mensaje == 4)
                     echo "<td bgcolor=#FFE0B2 class=_espacio_celdas_p style=color: #000000; font-weight: bold >Usuario no fue creado. Se presentó un inconveniente";
                 if ($mensaje == 5)
                     echo "<td bgcolor=#FFE0B2 class=_espacio_celdas_p style=color: #000000; font-weight: bold >Usuario no fue creado. Ya existe usuario con la misma cédula.";
              ?>
            </td>
           </tr>
          </table>
      </td>
     </tr>
<?php
    }
}   
?>                         

<tr>
  <td colspan=2 height="20%" align="left" bgcolor="#FFFFFF">

<table width=80% border=1 align=center>
 <tr>    
    <td bgcolor="#ff9800" align=center> 
      <font FACE="arial" SIZE=2 color="#FFFFFF"> <b>Nombre Usuario</b></font>  
    </td>    
    <td bgcolor="#ff9800" align=center> 
      <font FACE="arial" SIZE=2 color="#FFFFFF"> <b>N&uacute;mero Id</b></font>  
    </td>     
    <td bgcolor="#ff9800" align=center> 
      <font FACE="arial" SIZE=2 color="#FFFFFF"> <b>Direcci&oacute;n</b></font>  
    </td>     
    <td bgcolor="#ff9800" align=center> 
      <font FACE="arial" SIZE=2 color="#FFFFFF"> <b>Usuario</b></font>  
    </td>
    <td bgcolor="#ff9800" align=center> 
      <font FACE="arial" SIZE=2 color="#FFFFFF"> <b>Tipo Usuario</b></font>  
    </td>
    <td bgcolor="#ff9800" align=center> 
      <font FACE="arial" SIZE=2 color="#FFFFFF"> <b>Activo (S/N)</b></font>  
    </td>
    <td bgcolor="#ff9800" align=center> 
      <font FACE="arial" SIZE=2 color="#FFFFFF"> <b>Modificar</b></font>  
    </td>
</tr>

<?php
$mysqli = new mysqli($host, $user, $pw, $db);
if (isset($_POST["enviado"]))
{
    $id_con = $_POST["id_con"];
    $nombre_con = $_POST["nombre_con"];
    $estado = $_POST["estado"];
    $sql1 = "SELECT * from usuarios order by nombre_completo";
    if (($id_con == "") and ($nombre_con == ""))
    {
        if ($estado != "2")
            $sql1 = "SELECT * from usuarios where activo='$estado' order by nombre_completo";
    }
    if (($id_con != "") and ($nombre_con == ""))
        $sql1 = "SELECT * from usuarios where identificacion='$id_con' order by nombre_completo";
    if (($id_con == "") and ($nombre_con != ""))
        $sql1 = "SELECT * from usuarios where nombre_completo like '%$nombre_con%' order by nombre_completo";
    if (($id_con != "") and ($nombre_con != ""))
        $sql1 = "SELECT * from usuarios where identificacion='$id_con' and nombre_completo like '%$nombre_con%' order by nombre_completo";
}
else
{
    $sql1 = "SELECT * from usuarios order by nombre_completo";  
}

$result1 = $mysqli->query($sql1);

while ($row1 = $result1->fetch_array(MYSQLI_ASSOC))
{
    $id1 = $row1["id"];
    $nombre = $row1["nombre_completo"];
    $identificacion = $row1["identificacion"];
    $direccion = $row1["direccion"];
    $login = $row1["login"];
    $tipo_usuario = $row1["tipo_usuario"];
    $activo = $row1["activo"];

    // CONSULTAR NOMBRE TIPO USUARIO
    $sql_tipo = "SELECT * from tipo_usuario where id='$tipo_usuario'";
    $result_tipo = $mysqli->query($sql_tipo);
    $row_tipo = $result_tipo->fetch_array(MYSQLI_ASSOC);
?>

<tr>
 <td bgcolor="#FFF3E0" align=center><font FACE="arial" SIZE=2 color="#000000"><b><?php echo $nombre;?></b></font></td>
 <td bgcolor="#FFF3E0" align=center><font FACE="arial" SIZE=2 color="#000000"><b><?php echo $identificacion;?></b></font></td>
 <td bgcolor="#FFF3E0" align=center><font FACE="arial" SIZE=2 color="#000000"><b><?php echo $direccion;?></b></font></td>
 <td bgcolor="#FFF3E0" align=center><font FACE="arial" SIZE=2 color="#000000"><b><?php echo $login;?></b></font></td>
 <td bgcolor="#FFF3E0" align=center><font FACE="arial" SIZE=2 color="#000000"><b><?php echo $row_tipo['descripcion_tipo'];?></b></font></td>
 <td bgcolor="#FFF3E0" align=center><font FACE="arial" SIZE=2 color="#000000"><b><?php if($activo==1) echo "S"; else echo "N";?></b></font></td>
 <td bgcolor="#FFF3E0" align=center>
     <a href="gestion_usuarios_mod.php?id=<?php echo $id1; ?>">
        <img src="img/icono_editar.jpg" border="0" width="40" height="30" alt="Modificar">
    </a>
 </td>
</tr>

<?php
}
?>

</table>

<!-- ===================================== -->
<!-- SECCIÓN DE SOLICITUDES PENDIENTES     -->
<!-- ===================================== -->
<br><br>
<hr>
<h2 align="center" style="color:#e65100; font-family:arial;">Solicitudes pendientes de apicultor</h2>
<table width="80%" border="1" align="center">
  <tr>
    <td bgcolor="#ff9800" align="center">
      <font FACE="arial" SIZE=2 color="#FFFFFF"><b>Nombre Usuario</b></font>
    </td>
    <td bgcolor="#ff9800" align="center">
      <font FACE="arial" SIZE=2 color="#FFFFFF"><b>Número Id</b></font>
    </td>
    <td bgcolor="#ff9800" align="center">
      <font FACE="arial" SIZE=2 color="#FFFFFF"><b>Dirección</b></font>
    </td>
    <td bgcolor="#ff9800" align="center">
      <font FACE="arial" SIZE=2 color="#FFFFFF"><b>Usuario</b></font>
    </td>
    <td bgcolor="#ff9800" align="center">
      <font FACE="arial" SIZE=2 color="#FFFFFF"><b>Acciones</b></font>
    </td>
  </tr>

<?php
$sql_pendientes = "
SELECT s.id, u.id AS id_usuario, u.nombre_completo, u.identificacion, u.direccion, u.login 
FROM solicitudes_apicultor s 
INNER JOIN usuarios u ON s.id_usuario = u.id 
WHERE s.estado = 'pendiente'";

$result_pendientes = $mysqli->query($sql_pendientes);

if ($result_pendientes->num_rows > 0) {
    while($rowp = $result_pendientes->fetch_array(MYSQLI_ASSOC)) {
        $id_solicitud = $rowp["id"];
        $id_usuario = $rowp["id_usuario"];
        $nombre = $rowp["nombre_completo"];
        $identificacion = $rowp["identificacion"];
        $direccion = $rowp["direccion"];
        $usuario = $rowp["login"];
        ?>

        <tr>
            <td bgcolor="#FFF3E0" align="center"><font FACE="arial" SIZE=2 color="#000000"><b><?php echo $nombre; ?></b></font></td>
            <td bgcolor="#FFF3E0" align="center"><font FACE="arial" SIZE=2 color="#000000"><b><?php echo $identificacion; ?></b></font></td>
            <td bgcolor="#FFF3E0" align="center"><font FACE="arial" SIZE=2 color="#000000"><b><?php echo $direccion; ?></b></font></td>
            <td bgcolor="#FFF3E0" align="center"><font FACE="arial" SIZE=2 color="#000000"><b><?php echo $usuario; ?></b></font></td>
            <td bgcolor="#FFF3E0" align="center">
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="accion" value="aceptar">
                    <input type="hidden" name="id_solicitud" value="<?php echo $id_solicitud; ?>">
                    <input type="hidden" name="id_usuario" value="<?php echo $id_usuario; ?>">
                    <input type="submit" value="Aceptar" style="background-color:#ff9800; color:white; border:none; border-radius:4px; padding:5px 10px; font-family:arial; font-size:14px; cursor:pointer;">
                </form>

                <form method="POST" style="display:inline;">
                    <input type="hidden" name="accion" value="rechazar">
                    <input type="hidden" name="id_solicitud" value="<?php echo $id_solicitud; ?>">
                    <input type="submit" value="Rechazar" style="background-color:#ff9800; color:white; border:none; border-radius:4px; padding:5px 10px; font-family:arial; font-size:14px; cursor:pointer;">
                </form>
            </td>
        </tr>

        <?php
    }
} else {
    echo "<tr><td colspan='5' align='center' bgcolor='#FFF3E0'><font FACE='arial' SIZE=2 color='#000000'><b>No hay solicitudes pendientes.</b></font></td></tr>";
}
?>

</table>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["accion"])) {
    $accion = $_POST["accion"];
    $id_solicitud = $_POST["id_solicitud"];

    if ($accion == "aceptar") {
        $id_usuario = $_POST["id_usuario"];
        $mysqli->query("UPDATE usuarios SET tipo_usuario=1 WHERE id='$id_usuario'");
        $mysqli->query("UPDATE solicitudes_apicultor SET estado='aprobada' WHERE id='$id_solicitud'");
    }

    if ($accion == "rechazar") {
        $mysqli->query("DELETE FROM solicitudes_apicultor WHERE id='$id_solicitud'");
    }
}
?>

</body>
</html>
