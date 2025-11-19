<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Página de Inicio - Sistema Monitoreo de Apiarios</title>
  <meta http-equiv="refresh" content="15" />
  <style>
    body {
      font-family: Arial, sans-serif;
      background: linear-gradient(135deg, #fff7e6, #ffe0b2);
      margin: 0;
      padding: 0;
    }

    .container {
      width: 90%;
      margin: 20px auto;
      background: #ffffff;
      border-radius: 12px;
      box-shadow: 0px 4px 10px rgba(0,0,0,0.2);
      overflow: hidden;
    }

    .header {
      position: relative;
      color: white;
      padding: 60px 20px; 
      background: url("img/panal.jpeg") no-repeat center center;
      background-size: cover; 
      display: flex;
      justify-content: flex-end; 
      align-items: center;
    }

    .login-box {
      background: rgba(255, 255, 255, 0.88);
      padding: 20px;
      border-radius: 10px;
      text-align: center;
      color: #333;
      max-width: 300px;
    }

    .login-box h2 {
      margin-bottom: 15px;
      color: #e65100;
    }

    .login-box input {
      padding: 8px;
      margin: 8px 0;
      width: 95%;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-family: Arial, sans-serif;
      font-size: 14px;
      box-sizing: border-box;
    }

    /* 🔸 Estilo unificado para ambos botones */
    .login-box input[type="submit"],
    .register-btn {
      background: #ff9800;
      color: white;
      font-weight: bold;
      border: none;
      cursor: pointer;
      transition: 0.3s;
      border-radius: 6px;
      padding: 10px;
      width: 95%;
      font-family: Arial, sans-serif;
      font-size: 14px;
      display: block;
      margin: 10px auto 0 auto;
      text-decoration: none;
      text-align: center;
      box-sizing: border-box;
    }

    .login-box input[type="submit"]:hover,
    .register-btn:hover {
      background: #e68900;
    }

    .title-bar {
      text-align: center;
      padding: 15px;
      background: #ffb74d;
      color: white;
    }

    .content {
      padding: 20px;
    }

    .content h2 {
      color: #e65100;
      border-bottom: 2px solid #ffcc80;
      display: inline-block;
      margin-bottom: 10px;
    }

    .content p {
      color: #555;
      line-height: 1.6;
      text-align: justify;
    }

    .content ul {
      color: #555;
      line-height: 1.6;
    }

    .content ul li {
      margin-bottom: 10px;
    }

    .error {
      background: #ffdddd;
      color: #d32f2f;
      padding: 10px;
      margin: 10px 0;
      border-left: 5px solid #d32f2f;
      border-radius: 6px;
    }
  </style>
</head>
<body>
  <div class="container">
    <!-- Encabezado con imagen de fondo y login -->
    <div class="header">
      <div class="login-box">
        <h2>Ingreso de Usuarios</h2>
        <form method="POST" action="validar.php">
          <input type="text" name="login1" placeholder="Usuario" required><br>
          <input type="password" name="passwd1" placeholder="Password" required><br>
          <input type="submit" value="Iniciar Sesión" name="Enviar">
        </form>

        <!-- 🔸 Botón de registro con mismo estilo -->
        <a href="registro_usuario.php" class="register-btn">Registrarse</a>

        <?php
        if (isset($_GET["mensaje"])) {
          $mensaje = $_GET["mensaje"];
          if ($mensaje != "") {
            echo '<div class="error"><strong>Datos Incorrectos:</strong><br>';
            if ($mensaje == 1)
              echo "El password del usuario no coincide.";
            if ($mensaje == 2)
              echo "No hay usuarios con el login ingresado o está inactivo.";
            if ($mensaje == 3)
              echo "No se ha logueado en el Sistema. Por favor ingrese los datos.";
            if ($mensaje == 4)
              echo "Su tipo de usuario no tiene credenciales suficientes.";
            echo '</div>';
          }
        }
        ?>
      </div>
    </div>

    <!-- Título principal -->
    <div class="title-bar">
      <h1>Sistema Monitoreo de Apiarios</h1>
    </div>

    <!-- Contenido principal -->
    <div class="content">
      <h2>Descripción del Sistema</h2>
      <p>
        Este sistema permite visualizar datos en colmenas a 
        partir de mediciones asociadas a temperatura, conteo 
        de abejas, número de colmenas, peso y humedad realizadas
        en los apiarios en la Facultad de Ciencias Agrarias y en 
        la granja experimental la Sultana.
      </p>

      <h2>Servicios</h2>
      <ul>
        <li><strong>Gestión de colmenas:</strong> Permite a los usuarios consultar los datos de una colmena específica a partir del ID de tarjeta asociado.</li>
        <li><strong>Gestión de usuarios:</strong> Dirigida a los apicultores registrados, permite visualizar la información correspondiente al usuario, como nombre, identificador, actividad, entre otros.</li>
        <li><strong>Gestión de informes:</strong> Facilita la visualización de los datos de temperatura y humedad promedio de todas las colmenas durante el día, presentados en forma de gráfico.</li>
      </ul>
    </div>
  </div>
</body>
</html>
