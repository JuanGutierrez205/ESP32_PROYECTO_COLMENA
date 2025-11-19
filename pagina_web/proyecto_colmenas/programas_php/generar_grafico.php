<?php
// ==========================
// Mostrar errores (solo desarrollo)
// ==========================
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include "conexion.php";
session_start();

// ==========================
// Validar sesión
// ==========================
if (!isset($_SESSION["autenticado"]) || $_SESSION["autenticado"] != "SIx3") {
    header('Location: index.php?mensaje=3');
    exit();
}

// Datos de sesión
$tipo_sesion = isset($_SESSION["tipo_usuario"]) ? $_SESSION["tipo_usuario"] : "";
$nombre_usuario = isset($_SESSION["nombre_usuario"]) ? $_SESSION["nombre_usuario"] : "";
$is_api = (strtolower($tipo_sesion) == "apicultor");

// ==========================
// Conexión BD
// ==========================
$mysqli = new mysqli($host, $user, $pw, $db);
if ($mysqli->connect_errno) {
    die("Error al conectar a la base de datos: " . $mysqli->connect_error);
}
$mysqli->set_charset("utf8mb4");

// ==========================
// Obtener descripción del tipo de usuario
// ==========================
$stmt = $mysqli->prepare("SELECT descripcion_tipo FROM tipo_usuario WHERE LOWER(descripcion_tipo) = LOWER(?) LIMIT 1");
$stmt->bind_param('s', $tipo_sesion);
$stmt->execute();
$resultusu = $stmt->get_result();
if ($resultusu->num_rows == 0) {
    header('Location: index.php?mensaje=4');
    exit();
}
$rowusu = $resultusu->fetch_assoc();
$desc_tipo_usuario = $rowusu['descripcion_tipo'];
$stmt->close();

if (isset($_GET['ajax']) && $_GET['ajax'] == '1') {
    header('Content-Type: application/json; charset=utf-8');

    $rango = isset($_GET['rango']) ? $_GET['rango'] : 'dia';

    switch ($rango) {
        case 'semana':
            $groupBy = "YEAR(fecha), WEEK(fecha)";
            $dateSelect = "STR_TO_DATE(CONCAT(YEAR(fecha), WEEK(fecha), '1'), '%X%V%w') AS fecha_ref";
            break;
        case 'mes':
            $groupBy = "YEAR(fecha), MONTH(fecha)";
            $dateSelect = "STR_TO_DATE(CONCAT(YEAR(fecha), '-', MONTH(fecha), '-01'), '%Y-%m-%d') AS fecha_ref";
            break;
        default:
            $groupBy = "fecha";
            $dateSelect = "fecha AS fecha_ref";
    }

    $sql = "
        SELECT $dateSelect,
               AVG(temperatura) AS temp_avg,
               AVG(humedad) AS hum_avg
        FROM datos_medidos
        GROUP BY $groupBy
        ORDER BY fecha_ref
    ";
    $res = $mysqli->query($sql);
    $temp = [];
    $hum = [];
    while ($r = $res->fetch_assoc()) {
        $ts = strtotime($r['fecha_ref']) * 1000;
        $temp[] = [$ts, round($r['temp_avg'], 2)];
        $hum[]  = [$ts, round($r['hum_avg'], 2)];
    }

    $peso = [];
    $conteo = [];
    if ($is_api) {
        $sql2 = "
            SELECT $dateSelect, MAX(id) AS ultimo_id
            FROM datos_medidos
            GROUP BY $groupBy
        ";
        $res2 = $mysqli->query($sql2);
        $ids = [];
        while ($r = $res2->fetch_assoc()) $ids[] = $r['ultimo_id'];
        if ($ids) {
            $sql3 = "SELECT id, fecha, peso_colmena, conteo_abejas FROM datos_medidos WHERE id IN (" . implode(',', $ids) . ")";
            $res3 = $mysqli->query($sql3);
            while ($r = $res3->fetch_assoc()) {
                $ts = strtotime($r['fecha']) * 1000;
                $peso[]   = [$ts, round($r['peso_colmena'], 2)];
                $conteo[] = [$ts, round($r['conteo_abejas'], 2)];
            }
        }
    }
    echo json_encode([
        'temperatura' => $temp,
        'humedad' => $hum,
        'peso' => $peso,
        'conteo' => $conteo
    ], JSON_NUMERIC_CHECK);
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Gráficas de datos</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <style>
        /* Mínimos ajustes para mantener exactamente el diseño original
           pero evitando que esté apretado. No se cambian colores/fuentes. */
        .top-table { margin-bottom: 14px; } /* separa el header del menú */
        .top-td-right { padding-right: 18px; vertical-align: middle; } /* evita que el texto esté pegado */
        .logo-td { padding-left: 10px; vertical-align: middle; } /* aleja logo del borde izquierdo */
    </style>
</head>
<body>
<table width="100%" align=center cellpadding=5 border=0 bgcolor="#FFFFFF" class="top-table">
    <tr>
        <td valign="top" align=left width=70%>
            <table width="100%" align=center border=0>
                <tr>
                    <td valign="top" align=center width=30% class="logo-td">
                        <img src="img/panal.jpeg" border=0 width=350 height=80>
                    </td>
                    <td valign="top" align=center width=60%>
                        <h1><font color="#e65100">Sistema de Monitoreo de Apiario</font></h1>
                    </td>
                </tr>
            </table>
        </td>
        <td valign="top" align=right class="top-td-right">
            <font FACE="arial" SIZE=2 color="#000000">
                <b><u><?php echo "Nombre Usuario</u>: " . htmlspecialchars($nombre_usuario); ?></b>
            </font><br>
            <font FACE="arial" SIZE=2 color="#000000">
                <b><u><?php echo "Tipo Usuario</u>: " . htmlspecialchars($desc_tipo_usuario); ?></b>
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

<table width="100%" align=center cellpadding=5 border=0 bgcolor="#FFFFFF">
    <tr valign="top">
        <td height="20%" align="left" bgcolor="#FFFFFF" class="_espacio_celdas" style="color: #000000; font-weight: bold">
            <font FACE="arial" SIZE=2 color="#e65100"><b><h1>Gráficas de datos</h1></b></font>
        </td>
        <td height="20%" align="right" bgcolor="#FFFFFF" class="_espacio_celdas">
            <img src="img/Apiarios.jpg" border=0 width=115 height=115>
        </td>
    </tr>
</table>

<div class="container">
    <br/>
    <h2 class="text-center"><font color="#ff9800">Registro de datos</font></h2>

    <div class="text-center">
        <label><b>Ver datos por:</b></label>
        <select id="rango" class="form-control" style="display:inline-block; width:200px;">
            <option value="dia" selected>Día</option>
            <option value="semana">Semana</option>
            <option value="mes">Mes</option>
        </select>
    </div>

    <br>

    <div id="graf_temp" style="height: 350px; margin-bottom: 60px;"></div>
    <div id="graf_hum" style="height: 350px; margin-bottom: 60px;"></div>

    <?php if ($is_api) { ?>
        <div id="graf_peso" style="height: 350px; margin-bottom: 60px;"></div>
        <div id="graf_conteo" style="height: 350px; margin-bottom: 60px;"></div>
    <?php } ?>
</div>

<script>
const esApicultor = <?php echo $is_api ? 'true' : 'false'; ?>;

function cargarDatos(rango = 'dia') {
    $.ajax({
        url: window.location.href,
        method: 'GET',
        data: { ajax: 1, rango: rango },
        dataType: 'json',
        success: function(data) {
            crearGrafico('graf_temp', 'Temperatura promedio por ' + rango, 'Temperatura (°C)', data.temperatura, '#ff9800', '°C');
            crearGrafico('graf_hum', 'Humedad promedio por ' + rango, 'Humedad (%)', data.humedad, '#ff9800', '%');
            if (esApicultor) {
                crearGrafico('graf_peso', 'Peso por ' + rango, 'Peso (kg)', data.peso, '#ff9800', 'kg');
                crearGrafico('graf_conteo', 'Conteo de abejas por ' + rango, 'Conteo', data.conteo, '#ff9800', 'abejas');
            }
        },
        error: function(xhr, status, error) {
            console.error("Error al cargar datos:", error);
        }
    });
}

function crearGrafico(container, titulo, nombreSerie, datos, color, unidad) {
    Highcharts.chart(container, {
        chart: { type: 'line', zoomType: 'x' },
        title: { text: titulo },
        xAxis: { type: 'datetime', title: { text: 'Fecha' } },
        yAxis: { title: { text: unidad } },
        tooltip: {
            shared: true,
            crosshairs: true,
            xDateFormat: '%Y-%m-%d',
            valueSuffix: ' ' + unidad
        },
        plotOptions: {
            line: {
                dataLabels: { enabled: true },
                marker: { enabled: true, radius: 5, symbol: 'circle', fillColor: color },
                lineWidth: 2
            }
        },
        series: [{ name: nombreSerie, data: datos, color: color }],
        credits: { enabled: false }
    });
}

$('#rango').on('change', function() {
    cargarDatos($(this).val());
});

$(document).ready(() => cargarDatos('dia'));
</script>

</body>
</html>
