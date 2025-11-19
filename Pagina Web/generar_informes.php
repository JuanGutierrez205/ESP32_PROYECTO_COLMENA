<?php
// PROGRAMA DE MENU APICULTOR
include "conexion.php";

session_start();
if ($_SESSION["autenticado"] != "SIx3") {
    header('Location: index.php?mensaje=3');
} else {
    $mysqli = new mysqli($host, $user, $pw, $db);

    // Traer el tipo de usuario con ID=1 (Apicultor)
    $sqlusu = "SELECT * from tipo_usuario where id='1'"; 
    $resultusu = $mysqli->query($sqlusu);
    $rowusu = $resultusu->fetch_array(MYSQLI_NUM);
    $tipo_api = $rowusu[1];  

    // Guardamos el tipo de usuario de la sesión
    $desc_tipo_usuario = $_SESSION["tipo_usuario"];

    // Verificamos si es apicultor
    $is_api = ($_SESSION["tipo_usuario"] == $tipo_api);
}

$sql = "SELECT AVG(temperatura) as count FROM datos_medidos GROUP BY DAY(fecha) ORDER BY fecha";
$tempe = mysqli_query($mysqli,$sql);
$tempe = mysqli_fetch_all($tempe,MYSQLI_ASSOC);
$tempe = json_encode(array_column($tempe, 'count'),JSON_NUMERIC_CHECK);

$sql = "SELECT AVG(humedad) as count FROM datos_medidos GROUP BY DAY(fecha) ORDER BY fecha";
$hume = mysqli_query($mysqli,$sql);
$hume = mysqli_fetch_all($hume,MYSQLI_ASSOC);
$hume = json_encode(array_column($hume, 'count'),JSON_NUMERIC_CHECK);

$category[0] = "febrero 13 de 2019";
$category[1] = "febrero 14 de 2019";
$category[2] = "febrero 15 de 2019";
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Generar Informes</title>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.js"></script>
<script src="https://code.highcharts.com/highcharts.js"></script>

<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #fff8f0;
    }
    h1, h2 {
        color: #e65100;
    }
    a {
        color: #ff9800;
        text-decoration: none;
    }
    a:hover {
        color: #e65100;
    }
    .panel-heading {
        background-color: #ff9800;
        color: white;
        font-weight: bold;
        padding: 8px;
        border-radius: 5px 5px 0 0;
    }
    .panel-body {
        border: 1px solid #ff9800;
        padding: 10px;
        border-radius: 0 0 5px 5px;
    }
    #container {
        min-height: 400px;
    }
    hr {
        border: 1px solid #ff9800;
    }
</style>
</head>
<body>
<script type="text/javascript">
$(function () {
    var data_tempe = <?php echo $tempe; ?>;
    var data_hume = <?php echo $hume; ?>;

    $('#container').highcharts({
        chart: { type: 'line' },
        title: { text: 'Temperatura y Humedad promedio por día', style:{ color:'#e65100'} },
        xAxis: { categories: ['<?php echo $category[0]?>','<?php echo $category[1]?>','<?php echo $category[2]?>'] },
        yAxis: { title: { text: 'Valores promedio', style:{ color:'#ff9800'} } },
        series: [
            { name: 'Temperatura', data: data_tempe, color: '#0f00e6ff' },
            { name: 'Humedad', data: data_hume, color: '#ff9800' }
        ]
    });
});
</script>

<table width="100%" align=center cellpadding=5 border=0 bgcolor="#FFFFFF">
<tr>
<td valign="top" align=left width=70%>
    <table width="100%" align=center border=0>
        <tr>
            <td valign="top" align=center width=30%>
                <img src="img/panal.jpeg" border=0 width=350 height=80>
            </td>
            <td valign="top" align=center width=60%>
                <h1>Sistema de Invernadero Automatizado</h1>
            </td>
        </tr>
    </table>
</td>
<td valign="top" align=right>
    <font FACE="arial" SIZE=2 color="#000000">
        <b><u><?php echo "Nombre Usuario</u>: ".$_SESSION["nombre_usuario"];?></b></font><br>
    <font FACE="arial" SIZE=2 color="#000000">
        <b><u><?php echo "Tipo Usuario</u>: ".$desc_tipo_usuario;?></b></font><br>
    <font FACE="arial" SIZE=2>
        <b><u><a href="cerrar_sesion.php"> Cerrar Sesión </a></u></b></font>
</td>
</tr>
</table>

<table width="100%" align=center cellpadding=5 border=0 bgcolor="#FFFFFF">
<?php 
    if ($is_api) {
        include "menu_api.php"; 
    } else {
        include "menu_consul.php"; 
    }
?> 
<tr valign="top">
    <td height="20%" align="left" bgcolor="#FFFFFF" class="_espacio_celdas">
        <h1>Generar Informes</h1>
    </td>
    <td height="20%" align="right" bgcolor="#FFFFFF" class="_espacio_celdas">
        <img src="img/generar_informes.jpg" border=0 width=115 height=115>
    </td>
</tr>
</table>

<table width="100%" align=center cellpadding=5 border=0 bgcolor="#FFFFFF">
<tr>
<td align=left width=50%>
    <div class="container">
        <br>
        <h2 class="text-center">Ejemplo de gráfico de informe (Generado por Highcharts)</h2>
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default">
                    <div class="panel-heading">Panel</div>
                    <div class="panel-body">
                        <div id="container"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</td>
</tr>
</table>
<br><br><hr>
</body>
</html>
