
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Turnos misas</title>
    <link rel="image_src" href="./img/img.jpg"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#1d1815">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <link rel="stylesheet" href="./styles.css" charset="utf-8">
    <script type="text/javascript" src="./functions.js?3"></script>
    <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Raleway" />

  </head>
  <body>

    <div class="container" onclick="viewContent('content-1')">
      <div id="content-1" class="transp" style="display:block!important">
      <p>

      <?php
      switch($_GET['type']){
        case 'nohash':
          echo "Hash no indicado";
        break;
        case 'hashincorrecto':
          echo "Hash incorrecto";
        break;
        case 'timeout':
          echo "Acceso fuera de tiempo.</p><p> Tenés 30 minutos para completar los datos luego de solicitar los cupos. Vuelve a solicitarlos.";
          ?>
          <a href="/turnos" style="text-decoration:none;color:#fff"><b>Ir al sitio.</b></a>
          <?php
        break;
        case 'linkusado':
          echo "Link utilizado.</p><p> No reservaste cupos con este link.";
        break;
        case 'timeout_turno':
          echo "Inscripción fuera de tiempo.</p><p> Solo podés inscibirte dos horas antes que inicie la misa.";
          ?>
          <a href="/turnos" style="text-decoration:none;color:#fff"><b>Ir al sitio.</b></a>
          <?php
          break;
      }
       ?>
       </p>
      </div>
      </div>
    </div>
  </body>
</html>
