<?php
// PROGRAMA DE VALIDACION DE USUARIOS

$login = $_POST["login1"];
$passwd = $_POST["passwd1"];

$passwd_comp = md5($passwd);
session_start();

include ("conexion.php");

$mysqli = new mysqli($host, $user, $pw, $db);

$sql = "SELECT * FROM usuarios WHERE login = '$login' AND activo='1'";
$result1 = $mysqli->query($sql);
$row1 = $result1->fetch_array(MYSQLI_NUM);
$numero_filas = $result1->num_rows;

if ($numero_filas > 0) {
    $passwdc = $row1[6];

    if ($passwdc == $passwd_comp) {
        $_SESSION["autenticado"] = "SIx3";
        $tipo_usuario = $row1[7];           // ID del tipo de usuario (1=apicultor, 2=consulta)
        $nombre_usuario = $row1[1];

        // Traemos la descripción del tipo de usuario
        $sql2 = "SELECT * FROM tipo_usuario WHERE id='$tipo_usuario'";
        $result2 = $mysqli->query($sql2);
        $row2 = $result2->fetch_array(MYSQLI_NUM);
        $desc_tipo_usu = $row2[1];

        // Guardamos en la sesión
        $_SESSION["tipo_usuario"] = $desc_tipo_usu;
        $_SESSION["nombre_usuario"] = $nombre_usuario;  
        $_SESSION["id_usuario"] = $row1[0];  

        // Redirigir según el tipo de usuario
        if ($tipo_usuario == 1) {
            // Apicultor
            header("Location: gestion_colmenas.php");
        } else if ($tipo_usuario == 2) {
            // Usuario de consulta
            header("Location: consulta_por_colmena.php");
        }
    } else {
        header('Location: index.php?mensaje=1'); // Contraseña incorrecta
    }
} else {
    header('Location: index.php?mensaje=2'); // Usuario no encontrado
}
?>
