<?php


$hash=$_GET['hash'];
$band_mostrarInscripciones=false;
if(!isset($hash)){
  header('location: errorhash.php?type=nohash');
  die();
}
include('./connect.php');
session_start();
$_SESSION['marca']=getCode();
$enlace=$db->get_row("SELECT * from enlaces where hash='$hash'");
if(!isset($enlace)){
  header('location: errorhash.php?type=hashincorrecto');
  die();
}
$time_turno=$db->get_var("SELECT timestamp from turnos where id=$enlace->id_turno");
if($enlace->usado=='1'){
  $countInsc=$db->get_var("SELECT count(*) from turnos_fieles tf join fieles f on f.id=tf.id_fiel where tf.id_enlace=$enlace->id");
  if($countInsc>0){
    $band_mostrarInscripciones=true;
    if($_GET['eliminar']){
      $now=time();
      if($now<$time_turno){
        $fiel_id=$_GET['eliminar'];
        $inscripcion=$db->get_row("SELECT * from fieles where id=$fiel_id");
        if(isset($inscripcion)){
          $db->query("DELETE FROM turnos_fieles where id_fiel='$fiel_id' and id_turno='$enlace->id_turno'");
          $db->query("DELETE FROM fieles where id='$fiel_id'");
        }
      }else{
        ?>
        <script type="text/javascript">
          alert("La misa ya pasó. No podes darte de baja.");
        </script>
        <?php
      }
    }
  }else{
    header('location: errorhash.php?type=linkusado');
    die();
  }

}else{
  $now_2horas=time()+3600;
  if($now_2horas>$time_turno){
    header('location: errorhash.php?type=timeout_turno');
    die();
  }
}
if(!$band_mostrarInscripciones){
  $now30=time()-1800;
  if($enlace->timestamp<$now30){
    header('location: errorhash.php?type=timeout');
    die();
  }
}


$turno=$db->get_row("SELECT * from turnos where id=$enlace->id_turno");
$capilla=$db->get_row("SELECT * from capillas where id=$turno->id_capilla");
$dias=array("",'Lunes','Martes','Miercoles','Jueves','Viernes','Sábado','Domingo');
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
    <script async src="html2canvas.min.js"></script>
    <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', 'UA-174104181-1');

    <?php
    if($band_mostrarInscripciones){
      ?>
      $(document).ready(function(){
        $('#content-1').html('');
        viewContent('content-1');
        getInscripcion();
      });
      <?php
    }else{
      ?>
      function remove(val){
        if(confirm("¿Seguro que deseas resignar este lugar solicitado?")){
          $('.cupo'+val).remove();
        }
      }
        function validateEmail(email) {
          const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
          return re.test(String(email).toLowerCase());
        }
        function goAnotarme(){
          loading();
          if(validar()){
            var nombres = $('body').find('table .nombre').map(function(i, el) {
                return el.value;
            }).get();
            var emails = $('body').find('table .email').map(function(i, el) {
                return el.value;
            }).get();
            var telefonos = $('body').find('table .telefono').map(function(i, el) {
                return el.value;
            }).get();
            $.post('./reservar_cupos.php',{
              hash:'<?php echo $hash ?>',
              nombres:nombres,
              emails:emails,
              telefonos:telefonos
            },function(data){
              obj=JSON.parse(data);
              dismiss_load();
              if(obj.estado=='0'){
                alert(obj.text);
                if(obj.estado=='130'){
                  //pocos cupos
                }else{
                  //no hay mas cupos
                  setTimeout(function () {
                    location.reload();
                  }, 1000);
                }
              }else{
                getInscripcion();
              }

            });
          }else{
            dismiss_load();
          }
        }

        function validar(){
          error = '';
          $('.valid').each(function(){
            if(!$(this).val()){

              error += ' '+$(this).attr('name');
              $(this).addClass('required');
              }else{
                if(this.name=='email'){
                  if(!validateEmail(this.value)){
                    error += ' formato de email incorrecto';
                    $(this).addClass('required');
                  }else{
                    $(this).removeClass('required');
                  }
                }else{
                  $(this).removeClass('required');
                }

                }
            });
            if(error){
              alert('Por favor complete los siguientes campos: '+error);
              $('.required').focus();
              return false;
              }else{
                return true;
                }

        }
        $( document ).ready(function() {
          viewContent('content-1');
        });
      <?php
    }
     ?>
     function confirmar(message){
       if (confirm("¿SEGURO DESEA BORRAR LA RESERVA?")){
         location.href=message;
         return " ";
        }
      }
     function getInscripcion(){
       loading();
       $.post("cargarDatos.php",
               {action:'get_inscripcion',hash:'<?php echo $hash ?>'},
               function(data){
                 dismiss_load();
                 try {
                   //caso error
                   //console.log(data);
                   obj=JSON.parse(data);
                   if(obj.estado='0'){
                     alert(obj.text);
                   }
                 } catch (e) {
                   //caso correcto
                   $('#inscripcion_lista .content_mail_send').html(data);
                   $('#inscripcion_lista').show(300);
                 }
               });

     }
     function descargar_img() {

       html2canvas(document.querySelector("#canvaIMG")).then(canvas => {
            var link = document.createElement('a');
            link.download = 'Comprobante_<?php echo $hash ?> .png';
            link.href = canvas.toDataURL()
            link.click();
        });
     }

     document.getElementById("send-mail-pdf").addEventListener("click",descargar_img);

    function descargar_img() {
        document.querySelector("#send-mail-pdf").innerHTML="Loading...";
       html2canvas(document.querySelector("#content-proof")).then(canvas => {
           /* var link = document.createElement('a');
            link.download = 'Comprobante_.png';
            link.href = canvas.toDataURL()
            link.click();*/

            //Submit the form manually

            var data = {
                  'img': canvas.toDataURL("image/png"),
                  //'var1': 'value1',
                  //'var2': 'value2',
              };
            jQuery.post("http://localhost:18000/wp-admin/admin-ajax.php", data, function (response) {
               // Contenido de la función de callback, que se lanza cuando tenemos la respuesta..
               console.log(response);
               document.querySelector("#send-mail-pdf").innerHTML="Send Mail";
           });
        });
     }


    </script>
  </head>
  <body>
    <backGrey id="inscripcion_lista"  style="display:none">
      <div class="content_mail_send" id="canvaIMG">

      </div>
    </backGrey>
    <div class="container"
    <?php if(isset($capilla->imagen) && strlen($capilla->imagen)>2 && file_exists('./img/capillas/'.$capilla->imagen)){
      $file='./img/capillas/'.$capilla->imagen;
      ?>
      style="background-image:url(<?php echo $file ?>)"
      <?php
    } ?> onclick="viewContent('content-1')">

      <div id="content-1" class="transp">
        <br>
        <h3 style="margin-top: 10px;"><?php echo $enlace->cantidad ?> cupos para misa.</h3>
        <p style="margin-left: auto; margin-right: auto;">Capilla: <?php echo $capilla->nombre ?> <br>Fecha: <?php echo $dias[$turno->dia].' '.$turno->fecha.' '.$turno->hora.'hs.' ?> </p>
        <br>
        <div class="scrollcontent">
            <table>
              <tbody>
                <?php
                for ($i=0; $i < $enlace->cantidad; $i++) {
                  ?>
                  <tr class="cupo<?php echo ($i+1); ?>">
                    <td colspan="2" style="text-align:center">Cupo <?php echo ($i+1); ?>

                      <?php
                      if($i>0){
                        ?><img src="img/remove_icon.png" style="height:15px;float:right;" onclick="remove(<?php echo ($i+1); ?>)" title="Resignar cupo"><?php
                      }
                       ?> </td>
                  </tr>
                  <tr class="cupo<?php echo ($i+1); ?>">
                    <td class="label">Apellido y nombre:</td><td><input style="text-transform: capitalize;" autocapitalize="words" type="email" class="valid nombre" name="nombres[]"  value="" placeholder="Indique apellido y nombre..."></td>
                  </tr>
                  <tr class="cupo<?php echo ($i+1); ?>">
                    <td class="label">Teléfono:</td><td><input type="number" class="valid telefono" name="telefonos[]" value="" placeholder="Indique teléfono de contacto..." onkeypress='return event.charCode >= 48 && event.charCode <= 57'></td>
                  </tr>
                  <tr class="cupo<?php echo ($i+1); ?>">
                    <td class="label">Email:</td><td><input type="email" class="valid email" name="emails[]" value="<?php echo $enlace->email ?>" placeholder="Indique email de contacto..."></td>
                  </tr>

                  <?php
                }
                 ?>
                 <tr>
                   <td class="label"></td> <td>
                     <a href="#" onclick="goAnotarme();return false;" class="btn">Enviar datos</a>
                     </td>
                 </tr>
              </tbody>
            </table>
        </div>

      </div>

    </div>
  </body>
</html>
