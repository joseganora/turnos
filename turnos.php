<?php
include('./connect.php');
session_start();
if($_GET['borrar']==1){
  session_destroy();
  header('location:index.php');
}
$id_capilla=$_GET['cap'];
$capilla=$db->get_row("SELECT * from capillas where id=$id_capilla");

 ?>
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
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-174104181-1"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', 'UA-174104181-1');

      function goAnotarme(hash){
        window.location="./anotarme.php?h="+hash;
      }
      $( document ).ready(function() {
        viewContent('content-1');
      });
    </script>
  </head>
  <body>
    <backGrey id="help_view" style="display:none">
      <div class="content_mail_send" style="text-align:center">
        <h4>Capilla <?php echo $capilla->nombre ?>.</h4>
        <img src="./img/help.png" style="height:50px">
        <p>Para anotarte debes seguir tres sencillos pasos.</p>
        <p>1. <b>Solicitar cupos</b> completando tu mail y la cantidad que necesitas. Solo podes pedir hasta 5 lugares. Una vez que envíes la solicitud el sistema te enviará un mail a la cuenta ingresada con un link para ingresar los datos.</p>
        <p>2. <b>Ingresar los datos</b> de todos los que asistirán haciendo click en el link que se envió a tu correo. Es muy importante que estos datos sean los correctos ya que la secretaría de cada capilla puede intentar contactarte para corroborar tu asistencia. </p>
        <p>Si ingresaste correctamente los datos el sistema te notificará por pantalla y puedes usar ese mismo link como comprobante de inscripción. Si tu inscripción se ve alterada por algún motivo, ya no figurarán tus datos en el link de inscripción. </p>
        <p>Para <b>dar de baja una reserva</b>, es decir liberar un cupo que reservaste, podes volver a ingresar al mismo link del paso dos y eliminar uno o varios de los inscriptos que figuren detallados. </p>

        <p>Si queres hacer contacto con alguno de los encargados a continuación te dejamos sus números.</p>
        <ul style="text-align: left">
          <?php
            $encargados=$db->get_results("SELECT u.* FROM usuarios u join capillas_usuarios cu on u.id=cu.id_usuario where cu.id_capilla=$id_capilla order by id desc");
            foreach ($encargados as $key => $value) {
              ?>
              <li><?php echo $value->nombre ?>.
                <br>
                <a href="mailto:<?php echo $value->mail ?>">
                  <img  style="height:30px" src="./img/gmail-icon.png" alt=""></a>
                <a target="_blank" href="https://wa.me/54<?php echo $value->telefono ?>">
                  <img style="height:30px" src="./img/WhatsApp-icon.png" alt=""></a>
                <a href="tel:<?php echo $value->telefono ?>">
                  <img style="height:30px" src="./img/icon-telefono-300x300.png" alt=""></a></li>
              <?php
            }
           ?>
        </ul>
        <a href="#" onclick="$('#help_view').hide(300);return false;" class="btn" style="background: #101010e0;">Cerrar</a>
      </div>
    </backGrey>
    <div class="container"
    <?php if(isset($capilla->imagen) && strlen($capilla->imagen)>2 && file_exists('./img/capillas/'.$capilla->imagen)){
      $file='./img/capillas/'.$capilla->imagen;
      ?>
      style="background-image:url(<?php echo $file ?>)"
      <?php


    } ?>
    onclick="viewContent('content-1')">
      <div id="content-1" class="transp">
        <br>
        <h3 style="margin-top: 10px;">Turnos de "<?php echo $capilla->nombre ?>"</h3>
        <br>
        <div class="scrollcontent">
          <?php
          $hoy=time();
          $turnos=$db->get_results("SELECT * from turnos where id_capilla='$id_capilla' and activo=1 and timestamp>$hoy");
           ?>
          <table>
            <tbody>
              <?php
              $dias=array("",'Lun','Mar','Mie','Jue','Vie','Sáb','Dom');
              foreach ($turnos as $key => $value) {
                $cupos=$db->get_var("SELECT (SELECT cupos from capillas where id=$id_capilla)-count(*) from turnos_fieles where id_turno='$value->id' and cupo_visible=1");
                ?>
                <tr>
                  <td><?php echo $dias[$value->dia].' '.str_replace('-','/',substr($value->fecha,0,5)); ?> <?php echo $value->hora ?>hs</td><td><?php if($cupos>0) echo $cupos; else echo '0'; ?> cupos</td>
                  <td>
                    <?php
                    $now_2horas=time()+3600;
                    if($now_2horas<$value->timestamp){
                      if($cupos<1){
                        echo "<i>No hay lugar</i>";
                      }else{
                        ?> <button type="button" name="button" onclick="goAnotarme('<?php echo $value->hash ?>')">¡Anotarme!</button> <?php }
                    }else{
                      echo "<i>Fin de inscripción</i>";
                    }
                     ?>
                    </td>
                </tr>
                <?php
                if(strlen($value->note)>0){
                  ?>
                  <tr>
                    <td colspan="3" style="text-align: left;font-size: 16px;font-style: italic;padding-left: 15px;padding-top: 0px;">
                      <div class="" style="display: inline-block;padding: 9px;background: #ffffffed;color: #000;border-radius: 5px;text-shadow: none;font-size: 13px;">
                        <?php echo $value->note ?>
                      </div>
                    </td>  
                  </tr>
                  <?php
                }
              }
               ?>
            </tbody>
          </table>
        </div>

        <div class="preguntas foot">
          <a href="#" onclick="$('#help_view').show(300);return false;" class="btn">Ayuda</a>
          <a href="./index.php" class="btn">Volver</a>
        </div>

      </div>

    </div>
  </body>
</html>
