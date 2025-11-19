<?php
include "conexion.php";

// Crear una sola conexión global
$mysqli = new mysqli($host, $user, $pw, $db);
if ($mysqli->connect_error) {
    die("Error de conexión: " . $mysqli->connect_error);
}

// Procesar el formulario solo si se envió
if (isset($_POST["enviado"])) {
    $nombre_usuario = str_replace(["�", "�"], ["n", "N"], $_POST["nombre_usuario"]);
    $tipo_usuario = $_POST["tipo_usuario"];
    $direccion = $_POST["direccion"];
    $login = $_POST["login"];
    $password = $_POST["password"];
    $password_enc = md5($password);

    // Verificar si ya existe el usuario
    $sqlcon = "SELECT * FROM usuarios WHERE login = '$login'";
    $resultcon = $mysqli->query($sqlcon);

    if ($resultcon && $resultcon->num_rows > 0) {
        header('Location: registro_usuario.php?mensaje=usuario_existente');
        exit;
    }

    // Si selecciona "apicultor", se guarda como "consulta" temporalmente
    $tipo_final = ($tipo_usuario == 1) ? 2 : $tipo_usuario;

    // Insertar nuevo usuario
    $sql = "INSERT INTO usuarios (nombre_completo, identificacion, direccion, fecha_nacimiento, login, passwd, tipo_usuario, activo)
            VALUES ('$nombre_usuario', '', '$direccion', '0000-00-00', '$login', '$password_enc', '$tipo_final', 1)";
    $result1 = $mysqli->query($sql);

    if ($result1) {
        $id_usuario = $mysqli->insert_id;

        // Si pidió ser apicultor, registrar solicitud
        if ($tipo_usuario == 1) {
            $sqlsol = "INSERT INTO solicitudes_apicultor (id_usuario) VALUES ('$id_usuario')";
            $result2 = $mysqli->query($sqlsol);

            if (!$result2) {
                echo "⚠️ Error al registrar solicitud de apicultor: " . $mysqli->error;
                exit;
            }
        }

        // Redirigir a la página principal con mensaje de éxito
        header('Location: index.php?');
        exit;
    } else {
        echo " Error al registrar usuario: " . $mysqli->error;
        exit;
    }
}
?>

<!DOCTYPE HTML>
<html>
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="css/estilos_virtual.css" type="text/css">
    <title>Registro de Usuario</title>
</head>
<body>
<table width="100%" align="center" cellpadding="5" border="0" bgcolor="#FFFFFF">
<tr>
    <td valign="top" align="center">
        <img src="img/panal.jpeg" border="0" width="350" height="80"><br>
        <h1><font color="#e65100">Registro de Usuario - Sistema de Monitoreo de Apiario</font></h1>
    </td>
</tr>

<tr valign="top">
<td colspan="2" width="25%" height="20%" align="left" bgcolor="#FFFFFF">
<form method="POST" action="registro_usuario.php">
<table width="50%" border="1" align="center">
<tr>  
    <td bgcolor="#FFF3E0" align="center">Nombre Usuario</td>
    <td bgcolor="#FFFFFF" align="center">
        <input type="text" name="nombre_usuario" required>
    </td>  
</tr>
<tr>
    <td bgcolor="#FFF3E0" align="center">Tipo Usuario</td>
    <td bgcolor="#FFFFFF" align="center">
        <select name="tipo_usuario" required onchange="mostrarAviso(this)">
            <?php
            $sql6 = "SELECT * FROM tipo_usuario ORDER BY id DESC";
            $result6 = $mysqli->query($sql6);
            while ($row6 = $result6->fetch_array(MYSQLI_NUM)) {
                echo "<option value='".$row6[0]."'>".$row6[1]."</option>";
            }
            ?>
        </select>
        <div id="aviso_apicultor" style="display:none; color:red; font-size:12px;">
            Si seleccionas "apicultor", tu solicitud será revisada antes de ser aprobada.
        </div>
    </td>  
</tr>
<tr>
    <td bgcolor="#FFF3E0" align="center">Usuario (login)</td>
    <td bgcolor="#FFFFFF" align="center">
        <input type="text" name="login" required>
    </td>  
</tr>
<tr>
    <td bgcolor="#FFF3E0" align="center">Clave</td>
    <td bgcolor="#FFFFFF" align="center">
        <input type="password" name="password" required>
    </td>  
</tr>
<tr>
    <td bgcolor="#FFF3E0" align="center">Dirección</td>
    <td bgcolor="#FFFFFF" align="center">
        <input type="text" name="direccion" required>
    </td>  
</tr>
<tr>
    <td bgcolor="#FFF3E0" align="center">Teléfono</td>
    <td bgcolor="#FFFFFF" align="center">
        <input type="number" name="telefono">
    </td>  
</tr>
</table>

<br>
<input type="hidden" value="S" name="enviado">

<table width="50%" align="center" border="0">
<tr>  
    <td width="50%"></td>                                                                       
    <td align="center">
        <input style="background-color: #DBA926" type="submit" value="Registrar" name="Registrar">
    </td>  
    <td align="left">
        <a href="index.php"><input style="background-color: #EEEEEE" type="button" value="Volver al Inicio"></a>
    </td>  
</tr>
</table>
</form> 
<br><br><hr>
</td>
</tr>  
</table>

<script>
function mostrarAviso(select) {
    var aviso = document.getElementById('aviso_apicultor');
    if (select.options[select.selectedIndex].text.toLowerCase() === "apicultor") {
        aviso.style.display = 'block';
    } else {
        aviso.style.display = 'none';
    }
}
</script>

</body>
</html>
