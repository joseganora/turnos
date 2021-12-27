<?php
include('./connect.php');
session_start();
include("./captcha.php");


$keyCaptcha=array_rand($captcha,1);

$_SESSION['marca']=getCode();

$hash=$_GET['h'];
if(strlen($hash)!=10){
  header('location: index.php');
  die();
}
$id_turno=$db->get_var("SELECT id from turnos where hash='$hash'");
if($id_turno){
  $turno=$db->get_row("SELECT * from turnos where id=$id_turno");
  $capilla=$db->get_row("SELECT * from capillas where id=$turno->id_capilla");
  $now_2horas=time()+3600;
  if($now_2horas>$turno->timestamp){
    header("location: turnos.php?cap=".$capilla->id);
    die();
  }
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

        function validateEmail(email) {
          const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
          return re.test(String(email).toLowerCase());
        }
        function goAnotarme(){
          loading();
          if(validar()){
            var cantidad=parseInt($('#cantidad').val());
            $.post('./cargarDatos.php',{
              action:'cargarReserva',
              email:$('#email').val(),
              cantidad:cantidad,
              capt:$('#captcha').val(),
              keycapt:'<?php echo $keyCaptcha ?>',
              id_turno:'<?php echo $id_turno ?>'
            },function(data){
              var obj_res=JSON.parse(data);
              if(obj_res.estado==0){
                alert(obj_res.text);
                if(obj_res.error_id==123){
                  location.reload();
                }
              }
              dismiss_load();
              if(obj_res.estado==1){

                $('#correo_ing').text($('#email').val());

                $('#enlace_enviado').show(300);
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

              error += ' '+$(this).attr('name')+'. ';
              $(this).addClass('required');
              }else{
                if(this.name=='email'){
                  if(!validateEmail(this.value)){
                    error += ' Formato de email incorrecto.';
                    $(this).addClass('required');
                  }else{
                    $(this).removeClass('required');
                  }
                }else{
                  if(this.name=='cantidad'){
                    var cantidad=parseInt(this.value);
                    if(!Number.isInteger(cantidad)){
                      error += ' No incluyas letras o números en la cantidad.';
                      $(this).addClass('required');
                    }else{
                      if(cantidad>6){
                        error += ' No puedes solicitar mas de 5 lugares.';
                        $(this).addClass('required');
                      }else{
                        if(cantidad<1){
                          error += ' Debes solicitar al menos un lugar.';
                          $(this).addClass('required');
                        }else{
                          $(this).removeClass('required');
                        }
                      }
                    }
                  }else{
                    $(this).removeClass('required');
                  }

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
      </script>
    </head>
    <body>
      <div class="container"
      <?php if(isset($capilla->imagen) && strlen($capilla->imagen)>2 && file_exists('./img/capillas/'.$capilla->imagen)){
        $file='./img/capillas/'.$capilla->imagen;
        ?>
        style="background-image:url(<?php echo $file ?>)"
        <?php


      } ?> onclick="viewContent('content-1')">
        <backGrey id="enlace_enviado" style="display:none">
          <div class="content_mail_send">
            <h4>Todavia NO estas inscripto, falta el último paso.</h4>
            <p>Te enviamos un correo a <b><em id="correo_ing"></em></b> donde encontrarás un enlace para cargar los datos y reservar tus lugares. Tenés solo 30 minutos para completarlos.</p>
            <img src="./img/mail_send.png" style="height:50px">
            <p style="color: #ff4700;">Advertencia: La disponibilidad de cupos está sujeta a la demora en completar los datos y la demanda.</p>

            <a href="./index.php" class="btn" style="background: #101010e0;">Cerrar</a>
          </div>
        </backGrey>
        <div id="content-1" class="transp">
          <br>
          <?php $dias=array("",'lunes','martes','miercoles','jueves','viernes','sábado','domingo'); ?>
          <h3 style="margin-top: 10px;">Solicitar lugar para la misa del  <?php echo $dias[$turno->dia].' '.str_replace('-','/',$turno->fecha); ?> <?php echo $turno->hora ?>h. en la capilla "<?php echo $capilla->nombre ?>"</h3>
          <br>
          <div class="scrollcontent">
            <div style="margin-top:5px;margin-bottom:5px;"><b>Primer Paso.</b> Completá los siguientes datos.</div>
            <table>
              <tbody>
                <tr>
                  <td class="label">Email:</td><td><input type="email" class="valid" name="email" id="email" value="<?php if(isset($_SESSION['mail'])) echo $_SESSION['mail']; ?>" placeholder="Indique un mail..."></td>
                </tr>
                <tr>
                  <td class="label">Cantidad:</td><td><input type="number" class="valid" min="1" max="5" name="cantidad" id="cantidad" value="" placeholder="Cantidad..." onkeypress='return event.charCode >= 48 && event.charCode <= 57'></td>
                </tr>
                <tr>
                  <td class="label"></td><td><img style="border: solid 1px #000;" src="captchaimg/<?php echo  $keyCaptcha; ?>.jpg"></td>
                </tr>
                <tr>
                  <td class="label"></td><td> <input type="text" id="captcha" name="captcha" class="valid" value="" placeholder="Escribe lo que ves en la imagen..."></td>
                </tr>
                <tr>
                  <td class="label"></td> <td>
                    <a href="#" onclick="goAnotarme();return false;" class="btn">Solicitar cupos</a>
                    </td>
                </tr>
                <tr>
                  <td colspan="2"><a href="./turnos.php?cap=<?php echo $capilla->id ?>" class="btn">Volver</a></td>
                </tr>
              </tbody>
            </table>

          </div>

        </div>

      </div>
    </body>
  </html>
<?php
}else{
  header('location: index.php');
}
 ?>
