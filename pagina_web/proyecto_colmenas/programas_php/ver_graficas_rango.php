<?php
// ==========================
// ver_graficas_rango.php
// ==========================
include "conexion.php";
session_start();

// ==========================
// Validar sesión
// ==========================
if (!isset($_SESSION["autenticado"]) || $_SESSION["autenticado"] != "SIx3") {
    header('Location: index.php?mensaje=3');
    exit();
}

// ==========================
// Conexión a BD
// ==========================
$mysqli = new mysqli($host, $user, $pw, $db);
if ($mysqli->connect_errno) {
    die("Error de conexión: " . $mysqli->connect_error);
}

$nombre_usuario    = isset($_SESSION["nombre_usuario"]) ? $_SESSION["nombre_usuario"] : "";
$desc_tipo_usuario = isset($_SESSION["tipo_usuario"]) ? $_SESSION["tipo_usuario"] : "";
$is_api            = (strtolower($desc_tipo_usuario) == "apicultor");

// ==========================
// 1) Parámetros desde historial
// ==========================
$fecha_ini  = isset($_POST["fecha_ini"])  ? $_POST["fecha_ini"]  : null;
$fecha_fin  = isset($_POST["fecha_fin"])  ? $_POST["fecha_fin"]  : null;
$id_colmena = isset($_POST["id_colmena"]) ? intval($_POST["id_colmena"]) : 0;

if (!$fecha_ini || !$fecha_fin || !$id_colmena) {
    header("Location: historial.php");
    exit();
}

// ==========================
// 2) Detectar columnas disponibles
// ==========================
$columnas = [];
$res_cols = $mysqli->query("SHOW COLUMNS FROM datos_medidos");
while ($col = $res_cols->fetch_assoc()) {
    $columnas[] = $col['Field'];
}
$has_conteo = in_array("conteo_abejas", $columnas);
$has_peso   = in_array("peso_colmena", $columnas);

// ==========================
// 3) Consulta por rango y colmena
// ==========================
$campos = "AVG(dm.temperatura) AS temperatura, AVG(dm.humedad) AS humedad";
if ($is_api && $has_conteo) $campos .= ", AVG(dm.conteo_abejas) AS conteo_abejas";
if ($is_api && $has_peso)   $campos .= ", AVG(dm.peso_colmena) AS peso_colmena";

$sql = "SELECT dm.fecha, HOUR(dm.hora) AS hora_h, $campos
        FROM datos_medidos dm
        INNER JOIN colmenas c ON dm.ID_TARJ = c.id_tarjeta
        WHERE c.id = ? AND dm.fecha BETWEEN ? AND ?
        GROUP BY dm.fecha, HOUR(dm.hora)
        ORDER BY dm.fecha, hora_h";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("iss", $id_colmena, $fecha_ini, $fecha_fin);
$stmt->execute();
$res = $stmt->get_result();

$labels = []; $temp = []; $hum = []; $conteo = []; $peso = [];
while ($row = $res->fetch_assoc()) {
    $labels[] = $row["fecha"]." ".str_pad($row["hora_h"],2,"0",STR_PAD_LEFT).":00";
    $temp[]   = isset($row["temperatura"]) ? round($row["temperatura"], 2) : null;
    $hum[]    = isset($row["humedad"])     ? round($row["humedad"], 2)     : null;
    if ($is_api && $has_conteo && isset($row["conteo_abejas"])) $conteo[] = round($row["conteo_abejas"], 2);
    if ($is_api && $has_peso   && isset($row["peso_colmena"]))  $peso[]   = round($row["peso_colmena"], 2);
}
$stmt->close();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Gráficas por Rango de Fechas</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #fff;
            margin: 0;
        }

        h1 {
            color: #e65100;
            text-align: center;
            margin: 20px 0 6px;
            font-size: 42px;
        }

        /* Contenedor superior con texto + botón */
        .info-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 14px;
            margin-top: 25px;
            margin-bottom: 20px;
            text-align: center;
        }

        .info-text {
            color: #e65100;
            font-weight: bold;
            font-size: 18px;
            line-height: 1.5;
        }

        .btn {
            background: #e65100;
            color: #fff;
            border: 0;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 15px;
            transition: background 0.2s ease;
        }

        .btn:hover {
            background: #cc5200;
        }

        .grafica {
            width: 80%;
            margin: 30px auto;
        }

        p.no-data {
            text-align: center;
            color: #c62828;
            font-weight: bold;
            margin-top: 30px;
            font-size: 18px;
        }
    </style>
</head>
<body>

<div class="info-container">
    <div class="info-text">
        Colmena #<?php echo htmlspecialchars((string)$id_colmena); ?><br>
        Rango: <?php echo htmlspecialchars($fecha_ini); ?> → <?php echo htmlspecialchars($fecha_fin); ?>
    </div>

    <form method="POST" action="historial.php">
        <input type="hidden" name="enviado" value="S1">
        <input type="hidden" name="fecha_ini" value="<?php echo htmlspecialchars($fecha_ini); ?>">
        <input type="hidden" name="fecha_fin" value="<?php echo htmlspecialchars($fecha_fin); ?>">
        <input type="hidden" name="id_colmena" value="<?php echo (int)$id_colmena; ?>">
        <button type="submit" class="btn">Volver al historial</button>
    </form>
</div>

<?php if (count($labels) === 0): ?>
    <p class="no-data">No hay datos en el rango seleccionado.</p>
<?php else: ?>
    <div class="grafica"><canvas id="tempChart"></canvas></div>
    <div class="grafica"><canvas id="humChart"></canvas></div>

    <?php if ($is_api && $has_conteo): ?>
        <div class="grafica"><canvas id="conteoChart"></canvas></div>
    <?php endif; ?>
    <?php if ($is_api && $has_peso): ?>
        <div class="grafica"><canvas id="pesoChart"></canvas></div>
    <?php endif; ?>
<?php endif; ?>

<script>
const labels = <?php echo json_encode($labels); ?>;
const commonOptions = {
    type: 'line',
    options: {
        tension: 0,
        responsive: true,
        scales: { y: { beginAtZero: false } }
    }
};

<?php if (count($labels) > 0): ?>
/* Temperatura */
new Chart(document.getElementById('tempChart'), {
    ...commonOptions,
    data: {
        labels,
        datasets: [{
            label: 'Temperatura (°C)',
            data: <?php echo json_encode($temp); ?>,
            borderColor: 'red',
            fill: false
        }]
    }
});

/* Humedad */
new Chart(document.getElementById('humChart'), {
    ...commonOptions,
    data: {
        labels,
        datasets: [{
            label: 'Humedad (%)',
            data: <?php echo json_encode($hum); ?>,
            borderColor: 'blue',
            fill: false
        }]
    }
});

<?php if ($is_api && $has_conteo): ?>
/* Conteo de abejas */
new Chart(document.getElementById('conteoChart'), {
    ...commonOptions,
    data: {
        labels,
        datasets: [{
            label: 'Conteo de abejas',
            data: <?php echo json_encode($conteo); ?>,
            borderColor: 'purple',
            fill: false
        }]
    }
});
<?php endif; ?>

<?php if ($is_api && $has_peso): ?>
/* Peso colmena */
new Chart(document.getElementById('pesoChart'), {
    ...commonOptions,
    data: {
        labels,
        datasets: [{
            label: 'Peso colmena (g)',
            data: <?php echo json_encode($peso); ?>,
            borderColor: 'green',
            fill: false
        }]
    }
});
<?php endif; ?>
<?php endif; ?>
</script>

</body>
</html>
