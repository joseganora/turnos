<?php
include('./connect.php');
session_start();
if($_GET['borrar']==1){
  session_destroy();
  header('location:index.php');
}
$hash_ruca=$_GET['h'];
if(strlen($hash_ruca)!=10 || strpos($hash_ruca,' ')!==false){
  session_destroy();
  header('location:index.php');
}
$ruca=$db->get_row("SELECT * from rucas where hash_access='$hash_ruca'");
 ?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Ruca <?php echo $ruca->nombre ?></title>
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
        window.location="./anotarme_ruca.php?h="+hash;
      }
      $( document ).ready(function() {
        viewContent('content-1');
      });
    </script>
  </head>
  <body>
    <backGrey id="help_view" style="display:none">
      <div class="content_mail_send" style="text-align:center">
        <h4>Ruca <?php echo $ruca->nombre ?></h4>
        <img src="./img/help.png" style="height:50px">
        <p>Por consultas podés comunicarte con el encargado de tu sección o con el secretario correspondiente a tu ruca.</p>
        <p>Por cualquier consulta comunicarse a: </p>

        <ul style="text-align: left;">
          <li>Ruca Chapelco: Maximo Marciante 3513942428</li>
          <li>Ruca Carahue: Morena Mamondes 3513863757</li>
          <li>Ruca Champaqui: Santiago Ojeda 2944921870</li>
          <li>Fundación de Ruca en Villa Revol: Ignacio Catala 3515729818</li>
        </ul>
        <a href="#" onclick="$('#help_view').hide(300);return false;" class="btn" style="background: #101010e0;">Cerrar</a>
      </div>
    </backGrey>
    <div class="container"
    <?php if(isset($ruca->imagen) && strlen($ruca->imagen)>2 && file_exists('./img/rucas/'.$ruca->imagen)){
      $file='./img/rucas/'.$ruca->imagen;
      ?>
      style="background-image:url(<?php echo $file ?>)"
      <?php
    } ?>
    onclick="viewContent('content-1')">
      <div id="content-1" class="transp">
        <br>
        <h3 style="margin-top: 10px;">Secciones de <?php echo $ruca->nombre ?></h3>
        <br>
        <div class="scrollcontent">
          <?php
          $hoy=time();
          $secciones=$db->get_results("SELECT * from secciones where id_ruca='$ruca->id' and activo=1 order by orden");
           ?>
          <table>
            <tbody>
              <?php

              foreach ($secciones as $key => $value) {
                ?>
                <tr>
                  <td align="left" style="width: 90px;"><?php echo $value->nombre ?></td>
                  <td style="text-align: left;font-size: 16px;font-style: italic;padding-left: 15px;padding-top: 0px;">
                    <div class="" style="display: inline-block;padding: 9px;background: #ffffffed;color: #000;border-radius: 5px;text-shadow: none;font-size: 13px;">
                      <?php
                      $now=time();
                      $nota=$db->get_row("SELECT * from nota_secciones where timestamp_inicio<$now and timestamp_fin>$now and id_seccion=$value->id");
                      if(isset($nota)){
                        echo $nota->nota;
                      }else{
                        if(date('N')<6){
                          $next_sun=strtotime("next Saturday");
                        }else{
                          if(date('N')>6){
                            $next_sun=strtotime("last Saturday");
                          }else{
                            $next_sun=time();
                          }
                        }

                        $meses = array('','Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre');
                        ?>
                        Sábado <?php echo date('j',$next_sun).' de '.$meses[date('n',$next_sun)]; ?>
                        <?php
                      } ?>
                    </div>
                  </td>
                  <td>
                    <?php
                    if($nota && $nota->bloquear=='1'){
                      ?>
                      <i>No recibe inscripción</i>
                      <?php
                    }else{
                      ?>
                      <button type="button" name="button" onclick="goAnotarme('<?php echo $value->hash_access ?>')">Anotarme</button>
                      <?php
                    }
                     ?>
                  </td>
                </tr>
                <?php
              }
               ?>
            </tbody>
          </table>
        </div>

        <div class="preguntas foot">
          <a href="#" onclick="$('#help_view').show(300);return false;" class="btn">Ayuda</a>
          <a href="./rucas.php?provincia=<?php echo $ruca->provincia ?>&ciudad=<?php echo $ruca->ciudad ?>" class="btn">Volver</a>
        </div>

      </div>

    </div>
  </body>
</html>
