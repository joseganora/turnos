<?php
include('./connect.php');
$now=time();
$now_24hs=$now-(84600);
$cantidad=$db->get_var("SELECT count(*) from enlaces where timestamp>$now_24hs");


 ?>
<!DOCTYPE html>
<html lang="es" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title></title>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);

      <?php
      $menos1mes=time()-(86400*30);
      $cantidad_por_hora=$db->get_results("SELECT from_unixtime(timestamp, '%d-%m-%Y') fecha, min(timestamp) times, count(*) cantidad from enlaces where timestamp>$menos1mes group by fecha order by times");
      //from_unixtime(timestamp, '%H') horas
       ?>

      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          ['Horas', 'Cantidad'],
          <?php
          foreach ($cantidad_por_hora as $key => $value) {
            ?>
            ['<?php echo $value->fecha ?>',  <?php echo $value->cantidad ?>],
            <?php
          }
           ?>
        ]);

        var options = {
          title: 'Cantidad de mails por día ultimos 30 dias',
          legend: { position: 'bottom' }
        };

        var chart = new google.visualization.LineChart(document.getElementById('curve_chart'));

        chart.draw(data, options);
      }
    </script>
    <script type="text/javascript">
      google.charts.setOnLoadCallback(drawChart);

      <?php
      $db->query("SET SESSION time_zone = '-3:00'");
      $cantidad_por_hora=$db->get_results("SELECT from_unixtime(timestamp, '%H') hora, min(timestamp) times, count(*) cantidad from enlaces where timestamp>$now_24hs group by hora order by times");
      //from_unixtime(timestamp, '%H') horas
       ?>

      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          ['Horas', 'Cantidad'],
          <?php
          foreach ($cantidad_por_hora as $key => $value) {
            ?>
            ['<?php echo $value->hora ?>',  <?php echo $value->cantidad ?>],
            <?php
          }
           ?>
        ]);

        var options = {
          title: 'Cantidad de mails por hora',
          legend: { position: 'bottom' }
        };

        var chart = new google.visualization.LineChart(document.getElementById('curve_chart_horas'));

        chart.draw(data, options);
      }
    </script>
  </head>
  <body>
    <p>Mails en las últimas 24hs: <?php echo $cantidad; ?></p>

    <p>Promedio de consumo por horas</p>

    <div id="curve_chart" style="width: 100%; height: 500px"></div>
    <div id="curve_chart_horas" style="width: 100%; height: 500px"></div>
  </body>
</html>
