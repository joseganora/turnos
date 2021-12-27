<?php
include('./connect.php');
session_start();
$_SESSION['marca']=getCode();
if($_GET['borrar']==1){
  session_destroy();
  header('location:index.php');
}
 ?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Turnos de misa</title>
    <link rel="image_src" href="./img/img.jpg"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#1d1815">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <link rel="stylesheet" href="./styles.css" charset="utf-8">
    <script type="text/javascript" src="./functions.js?3"></script>
    <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Raleway" />
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-174104181-1"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', 'UA-174104181-1');

      function goCapilla(id){
        window.location="./turnos.php?cap="+id;
      }
      $( document ).ready(function() {

        viewContent('content-1');
      });
      function select_provincia(value){
        loading();
        $('#ciudad').load("cargarDatos.php",
        {action:'get_ciudades',provincia:value},
        function(){
          dismiss_load();
          $('#ciudad').show();
          $('#btn-rucas').hide();
          $('#capillas').html('');
        });
      }
      function select_ciudad(value){
        loading();
        $('#capillas').load("cargarDatos.php",
        {action:'get_capillas',ciudad:value},
        function(){
          dismiss_load();
          $('#capillas').show();
        });
      }
    </script>
  </head>
  <body>
    <div class="container" onclick="viewContent('content-1')">
      <div id="content-1" class="transp">
        <h3>Turnos</h3>
        <select id="provincia" class="select_location" onchange="select_provincia(this.value)" name="">
          <option value="">Seleccione provincia</option>
          <?php
          $prov=$db->get_results("SELECT provincia from capillas where activo=1 group by provincia order by provincia ");
          foreach ($prov as $key => $value) {
            ?>
            <option <?php if((isset($_SESSION['provincia']) && $_SESSION['provincia']==$value->provincia)||(isset($_GET['provincia']) && $_GET['provincia']==$value->provincia)){ echo 'selected';$_SESSION['provincia']=$value->provincia; } ?> value="<?php echo $value->provincia ?>"><?php echo $value->provincia ?></option>
            <?php
          }
           ?>

        </select>
        <select id="ciudad" class="select_location" onchange="select_ciudad(this.value)" <?php if(!isset($_SESSION['provincia'])) echo 'style="display:none"'; ?>>
          <option value="">Seleccione ciudad</option>
          <?php
          if(isset($_SESSION['provincia'])){
            $provincia=$_SESSION['provincia'];
            $ciudades=$db->get_results("SELECT ciudad from capillas where provincia='$provincia' and activo=1 group by ciudad order by ciudad ");
            foreach ($ciudades as $key => $value) {
              ?>
              <option <?php if(isset($_SESSION['ciudad']) && $_SESSION['ciudad']==$value->ciudad){ echo 'selected'; } ?> value="<?php echo $provincia.'_'.$value->ciudad ?>"><?php echo $value->ciudad ?></option>
              <?php
            }
          }
           ?>
        </select>
        <h3 style="margin-top: 10px;">Capillas disponibles</h3>
        <br>
        <div class="scrollcontent">
          <table cellspacing="0" id="capillas" style="-webkit-user-select: none;-moz-user-select: none;-khtml-user-select: none;-ms-user-select:none;">
            <tbody>
            <?php
            if(isset($_SESSION['provincia'])&&isset($_SESSION['ciudad'])){
              $ciudad=$_SESSION['ciudad'];
              $now=time();
              $capillas=$db->get_results("SELECT c.*, (SELECT count(*) from turnos where id_capilla=c.id and timestamp>$now and activo=1) turnos from capillas c where c.provincia='$provincia' and c.ciudad='$ciudad' and c.activo=1 order by c.cupos desc ");
              foreach ($capillas as $key => $value) {
                ?>
                  <tr class="tr-capillas" onclick="goCapilla(<?php echo $value->id ?>)" style="cursor:pointer">
                    <td><?php echo $value->nombre ?></td><td><?php echo $value->turnos ?> turno<?php if($value->turnos>1) echo 's'; ?></td><td><?php echo $value->cupos ?> personas</td>
                  </tr>
                <?php
              }
            }
             ?>
           </tbody>
          </table>
        </div>

        <?php
        if(isset($ciudad)&&isset($provincia)){
          $rucas=$db->get_results("SELECT * from rucas where ciudad='$ciudad' and provincia='$provincia'");
        }
        ?>
        <div class="preguntas foot">
          <a id="btn-rucas" <?php if(!isset($rucas) || count($rucas)<1){ ?>style="display:none"<?php } ?> href="rucas.php?provincia=<?php echo $provincia ?>&ciudad=<?php echo $ciudad ?>" class="btn">Rucas</a>
        </div>
        <?php
         ?>

  <!--
          <div class="preguntas foot">
            <a href="agregar.php" class="btn" style="color: #f5d768;background: #f1edda59;border: solid 1px #f5d768;">¡Quiero agregar mi capilla!</a>
          </div>
        -->


      </div>

    </div>
  </body>
</html>
