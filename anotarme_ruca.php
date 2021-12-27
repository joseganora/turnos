<?php
include('./connect.php');
session_start();
include("./captcha.php");


$keyCaptcha=array_rand($captcha,1);

$_SESSION['marca']=getCode();

$hash=$_GET['h'];
if(strlen($hash)!=10 || strpos($hash_ruca,' ')!==false){
  header('location: index.php');
  die();
}
$seccion=$db->get_row("SELECT * from secciones where hash_access='$hash'");
if($seccion){
  $ruca=$db->get_row("SELECT * from rucas where id=$seccion->id_ruca");
  $now=time();
  $nota=$db->get_row("SELECT * from nota_secciones where timestamp_inicio<$now and timestamp_fin>$now and id_seccion=$seccion->id");
  if(isset($nota)){
   $nota_str= $nota->nota;
  }else{
   if(date('N')<6){
     $next_sat=strtotime("next Saturday");
   }else{
     if(date('N')>6){
       $next_sat=strtotime("last Saturday");
     }else{
       $next_sat=time();
     }
   }

   $meses = array('','Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre');
   $nota_str='Sábado '.date('j',$next_sat).' de '.$meses[date('n',$next_sat)];
  }
    ?>
  <!DOCTYPE html>
  <html>
    <head>
      <meta charset="utf-8">
      <title>Ruca <?php echo $ruca->nombre ?> - Burbuja <?php echo $seccion->nombre ?></title>
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

        function otro(){
          location.reload();
        }
        function goAnotarme(){
          loading();
          if(validar()){
            var nocontacto='0';
            if($('#nocontacto').prop('checked')){
              nocontacto='1';
            }
            var nosintomas='0';
            if($('#nosintomas').prop('checked')){
              nosintomas='1';
            }
            var cantidad=parseInt($('#cantidad').val());
            $.post('./cargarDatos.php',{
              action:'anotarme_ruca',
              apellido:$('#apellido').val(),
              nombre:$('#nombre').val(),
              burbuja:$('#burbuja').val(),
              apellido_tutor:$('#apellido_tutor').val(),
              nombre_tutor:$('#nombre_tutor').val(),
              telefono:$('#telefono').val(),
              nocontacto:nocontacto,
              nosintomas:nosintomas,
              capt:$('#captcha').val(),
              keycapt:'<?php echo $keyCaptcha ?>',
              hash_access:'<?php echo $_GET['h'] ?>'
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
                comprobante=$('#nombre').val().split(' ').join('_');
                $('#comprobante_miliciano').text($('#apellido').val()+' '+$('#nombre').val());
                if($('#burbuja').val()){
                  $('#comprobante_burbuja').text($('#burbuja').val());
                }else{
                  $('#comprobante_burbuja').closest('tr').hide();
                }
                $('#comprobante_tutor').text($('#apellido_tutor').val()+' '+$('#nombre_tutor').val());
                $('#comprobante_telefono').text($('#telefono').val());
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
              $(this).removeClass('required');
            }
            });
            if(!$('#conformidad-protocolo').prop("checked")){
              error+=' Conformidad con el protocolo.';
              $('#conformidad-protocolo').addClass('required');
            }
            if(error){

              alert('Por favor complete los siguientes campos: '+error);
              $('.required:first').focus();
              return false;
              }else{
                return true;
              }
        }
        $( document ).ready(function() {
          viewContent('content-1');
        });
        function print() {

          html2canvas(document.querySelector("#canvaIMG")).then(canvas => {
               var link = document.createElement('a');
               link.download = 'Comprobante_'+comprobante+'_<?php echo date('dmyHi') ?>.png';
               link.href = canvas.toDataURL()
               link.click();
           });
        }
        var comprobante='';
      </script>
    </head>
    <body>
      <div class="container"
      <?php if(isset($ruca->imagen) && strlen($ruca->imagen)>2 && file_exists('./img/rucas/'.$ruca->imagen)){
        $file='./img/rucas/'.$ruca->imagen;
        ?>
        style="background-image:url(<?php echo $file ?>)"
        <?php
      } ?> onclick="viewContent('content-1')">
        <backGrey id="enlace_enviado" style="display:none">
          <div class="content_mail_send">
            <div id="canvaIMG" style="padding: 0px 3px;">
              <h4>Ya estas inscripto.</h4>
              <p style="text-align:center">Te inscribiste en la sección <?php echo $seccion->nombre ?> de ruca <?php echo $ruca->nombre ?>.<br>
                <span style="padding: 15px;display: inline-block;border: solid 1px;margin-top: 5px;font-weight: bold;"><?php echo $nota_str ?>.</span>  </p>
              <img src="./img/check.png" style="height:50px">
              <table class="comprobante">
                <tr>
                  <td colspan="2" style="background:#111;color:#fff">Datos de inscripción</td>
                </tr>
                <tr>
                  <td style="text-shadow: none">Miliciano:</td>
                  <td style="text-shadow: none" id="comprobante_miliciano"></td>
                </tr>
                <tr>
                  <td style="text-shadow: none">Burbuja:</td>
                  <td style="text-shadow: none" id="comprobante_burbuja"></td>
                </tr>
                <tr>
                  <td style="text-shadow: none">Tutor:</td>
                  <td style="text-shadow: none" id="comprobante_tutor"></td>
                </tr>
                <tr>
                  <td style="text-shadow: none">Telefono:</td>
                  <td style="text-shadow: none" id="comprobante_telefono"></td>
                </tr>
              </table>
            </div>

            <a onclick="print()" class="btn" style="background: #101010e0;cursor:pointer">Descargar</a>
            <a onclick="otro()" class="btn" style="background: #101010e0;cursor:pointer">Anotar Otro</a>
          </div>
        </backGrey>
        <div id="content-1" class="transp">
          <br>
          <h3 style="margin-top: 10px;">Inscribirme para la actividad de <?php echo $seccion->nombre ?> de ruca <?php echo $ruca->nombre ?></h3>


           <div class="" style="display: inline-block;padding: 3px 15px;background: #ffffffed;color: #000;border-radius: 5px;text-shadow: none;font-size: 13px;margin: auto;">
             <?php echo $nota_str ?>
           </div>
          <br>
          <div class="scrollcontent">
            <table>
              <tbody>
                <tr>
                  <td colspan="2">Miliciano</td>
                </tr>
                <tr>
                  <td class="label">Apellido:</td><td><input type="text" class="valid " name="apellido" id="apellido" value="<?php if(isset($_SESSION['apellido'])) echo $_SESSION['apellido']; ?>" placeholder="Indique apellido...">
                     </td>
                </tr>
                <tr>
                  <td class="label">Nombre:</td><td><input type="text" class="valid " name="nombre" id="nombre" value="<?php if(isset($_SESSION['nombre'])) echo $_SESSION['nombre']; ?>" placeholder="Indique nombre...">
                    </td>
                </tr>
                <tr>
                  <td class="label">Burbuja:</td><td><input type="text" class="" name="burbuja" id="burbuja" value="<?php if(isset($_SESSION['burbuja'])) echo $_SESSION['burbuja']; ?>" placeholder="Burbuja del cole (Solo para alumnos de Fasta)">
                    </td>
                </tr>
                <tr>
                  <td colspan="2">Padre, madre o tutor</td>
                </tr>
                <tr>
                  <td class="label">Apellido:</td><td><input type="text" class="valid " name="apellido_tutor" id="apellido_tutor" value="<?php if(isset($_SESSION['apellido_tutor'])) echo $_SESSION['apellido_tutor']; ?>" placeholder="Indique apellido...">
                    </td>
                </tr>
                <tr>
                  <td class="label">Nombre:</td><td><input type="text" class="valid " name="nombre_tutor" id="nombre_tutor" value="<?php if(isset($_SESSION['nombre'])) echo $_SESSION['nombre']; ?>" placeholder="Indique nombre...">
                    </td>
                </tr>
                <tr>
                  <td class="label">Teléfono:</td><td><input type="number" class="valid " name="telefono" id="telefono" value="<?php if(isset($_SESSION['telefono'])) echo $_SESSION['telefono']; ?>" placeholder="Indique teléfono..." onkeypress='return event.charCode >= 48 && event.charCode <= 57'>
                    </td>
                </tr>
                <tr>
                  <td class="label">Declaración Jurada:</td>
                  <td align="left"><input type="checkbox" name="nocontacto" id="nocontacto" style="display: inline-block;width: 20px;height: 20px;"> Declaro no haber estado en contacto con personas sospechosas o confirmadas de covid19, en los últimos 14 días</td>
                </tr>
                <tr>
                  <td class="label"></td>
                  <td align="left"><input type="checkbox" name="nosintomas" id="nosintomas" style="display: inline-block;width: 20px;height: 20px;"> Declaro no haber desarrollado en los últimos 5 días, algún episodio febril con temperatura superior a 37,5 grados, tenido cefalea, diarrea, pérdida del olfato o gusto, tos o dificultad respiratoria.</td>
                </tr>
                <tr>
                  <td class="label"></td>
                  <td align="left">Protocolo: <a style="color: #f5deb3;" target="_blank" href="./protocolos/protocolo_<?php echo $ruca->id ?>.pdf">Leer protocolo</a> </td>
                </tr>
                <tr>
                  <td class="label"></td>
                  <td align="left"><input type="checkbox" class="valid" id="conformidad-protocolo" style="display: inline-block;width: 20px;height: 20px;"> Habiendo tomado conocimiento del protocolo, yo padre/madre/tutor autorizo a mi hijo/a a participar de la actividad de ruca en la fecha anteriormente mencionada.</td>
                </tr>
                <tr>
                  <td class="label"></td><td><img style="border: solid 1px #000;" src="captchaimg/<?php echo  $keyCaptcha; ?>.jpg"></td>
                </tr>
                <tr>
                  <td class="label"></td><td> <input type="text" id="captcha" name="captcha" class="valid" value="" placeholder="Escribe lo que ves en la imagen..."></td>
                </tr>
                <tr>
                  <td colspan="2">
                    <a href="#" onclick="goAnotarme();return false;" class="btn">Enviar</a>
                  </td>
                </tr>
                <tr>
                  <td colspan="2"><a href="secciones_ruca.php?h=<?php echo $ruca->hash_access ?>" class="btn">Volver</a></td>
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
