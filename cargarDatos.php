<?php
include('./connect.php');
session_start();
$code=getCode();
$dias=array("",'Lunes','Martes','Miercoles','Jueves','Viernes','Sábado','Domingo');


if($_SESSION['marca']===$code){
  extract($_POST);
  $now=time();
  if($action=='anotarme_ruca'){
    $nombre=trim($nombre,"'\"");
    $apellido=trim($apellido,"'\"");
    $nombre_tutor=trim($nombre_tutor,"'\"");
    $apellido_tutor=trim($apellido_tutor,"'\"");

    if(($nosintomas)){
      $nosintomas='1';
    }else{
      $nosintomas='0';
    }
    if(($nocontacto)){
      $nocontacto='1';
    }else{
      $nocontacto='0';
    }
    $id_seccion=$db->get_var("SELECT id from secciones where hash_access='$hash_access'");
    $db->query("INSERT INTO inscriptos_rucas(id_seccion, timestamp, nombre, apellido, nombre_t, apellido_t,telefono,nosintomas,nocontacto) VALUES ('$id_seccion', '$now', '$nombre', '$apellido', '$nombre_tutor', '$apellido_tutor','$telefono','$nosintomas','$nocontacto')");
    $arr = array('estado'=>1,'text' => "¡Recibimos tu mensaje! Pronto te contactaremos.");
    echo json_encode($arr);
  }
  if($action=='addSolicitud'){
    $db->query("INSERT INTO solicitudes (nombre,apellido,telefono,mail,nombre_capilla,provincia,ciudad,timestamp) values ('$nombre','$apellido','$telefono','$mail','$nombre_capilla','$provincia','$ciudad','$now')");
    $arr = array('estado'=>1,'text' => "¡Recibimos tu mensaje! Pronto te contactaremos.");
    echo json_encode($arr);
  }
  if($action=='get_ciudades'){

    ?><option value="">Seleccione ciudad</option><?php
    $_SESSION['provincia']=$provincia;
    $prov=$db->get_results("SELECT ciudad from capillas where provincia='$provincia' and activo=1 group by ciudad order by ciudad ");
    foreach ($prov as $key => $value) {
      ?>
      <option value="<?php echo $provincia.'_'.$value->ciudad ?>"><?php echo $value->ciudad ?></option>
      <?php
    }
    ?>
    <script type="text/javascript">
      $('#btn-rucas').hide(200);
    </script>
    <?php
  }
  if($action=='get_ciudades_rucas'){
    ?><option value="">Seleccione ciudad</option><?php
    $_SESSION['provincia']=$provincia;
    $prov=$db->get_col("SELECT ciudad from rucas where provincia='$provincia' and activo=1 group by ciudad order by ciudad ");
    foreach ($prov as $key => $ciudad) {
      ?>
      <option value="<?php echo $provincia.'_'.$ciudad ?>"><?php echo $ciudad ?></option>
      <?php
    }
  }
  if($action=="get_rucas"){
    $arrayProv_ciudad=explode('_',$ciudad);
    $provincia=$arrayProv_ciudad[0];
    $ciudad=$arrayProv_ciudad[1];

    if($ciudad){
      $_SESSION['ciudad']=$ciudad;
      $rucas=$db->get_results("SELECT * from rucas where ciudad='$ciudad' and provincia='$provincia'");
      if(isset($rucas)&&count($rucas)>0){
        ?>
        <tbody>
          <?php
          foreach ($rucas as $key => $value) {
            ?>
              <tr class="tr-capillas" onclick="goRuca('<?php echo $value->hash_access ?>')" style="cursor:pointer">
                <td style="padding:15px 25px;"><?php echo $value->nombre ?></td>
              </tr>
            <?php
          }
           ?>
         </tbody>

        <?php
      }else{
        ?>
        <p> <i>No hay rucas para esta provincia/ciudad</i> </p>
        <?php
      }
    }

  }
  if($action=='get_capillas'){

    ?><tbody><?php
    $arrayProv_ciudad=explode('_',$ciudad);
    $provincia=$arrayProv_ciudad[0];
    $ciudad=$arrayProv_ciudad[1];
    if($ciudad){
      $_SESSION['ciudad']=$ciudad;
      $now=time();
      $capillas=$db->get_results("SELECT c.*, (SELECT count(*) from turnos where id_capilla=c.id and timestamp>$now) turnos from capillas c where c.provincia='$provincia' and c.ciudad='$ciudad' and c.activo=1 order by c.cupos desc ");
      foreach ($capillas as $key => $value) {
        ?>
          <tr class="tr-capillas" onclick="goCapilla(<?php echo $value->id ?>)"  style="cursor:pointer">
            <td><?php echo $value->nombre ?></td><td><?php echo $value->turnos ?> turno<?php if($value->turnos>1) echo 's'; ?></td><td><?php echo $value->cupos ?> personas</td>
          </tr>
        <?php
      }
      ?>  </tbody>
      <?php
      if(isset($ciudad)&&isset($provincia)){
        $rucas=$db->get_results("SELECT * from rucas where ciudad='$ciudad' and provincia='$provincia'");
        if(count($rucas)>0){

          ?>
          <script type="text/javascript">
            $('#btn-rucas').attr("href","rucas.php?provincia=<?php echo $provincia ?>&ciudad=<?php echo $ciudad ?>")
            $('#btn-rucas').show(200);
          </script>
          <?php
        }else{
          ?>
          <script type="text/javascript">
            $('#btn-rucas').hide(200);
          </script>
          <?php
        }
      }
    }else{
      ?>
      <script type="text/javascript">
        $('#btn-rucas').hide(200);
      </script>
      <?php
    }




  }
  if($action=='cargarReserva'){
    include("./captcha.php");

    $email=strtolower($email);
    $cantidad=intval($cantidad);
    $_SESSION['mail']=$email;
    //verifico el captcha
    if(strtolower($capt)!=strtolower($captcha[$keycapt])){
      $arr = array('estado'=>0,'error_id' => 123, 'text' => 'Captcha Incorrecto.'.$capt.'!='.$captcha[$keycapt]);
      unset($_SESSION['captchakey']);
      echo json_encode($arr);
      die();
    }
    //verifico cantidad de lugares solicitados
    if(!is_numeric($cantidad) || $cantidad<1 || $cantidad>5){
      $arr = array('estado'=>0,'error_id' => 178, 'text' => 'Cantidad de lugares solicitado no es válida.');
      echo json_encode($arr);
      die();
    }
    //verifico los cupos
    $turno=$db->get_row("SELECT * from turnos where id=$id_turno");
    $capilla=$db->get_row("SELECT * from capillas where id=$turno->id_capilla");
    $cuposLibres=$db->get_var("SELECT (SELECT cupos from capillas where id=$capilla->id)-count(*) from turnos_fieles where id_turno=$turno->id and cupo_visible=1");
    if($cuposLibres<1){
      $arr = array('estado'=>0,'error_id' => 179, 'text' => 'No hay más cupos para esta misa.');
      echo json_encode($arr);
      die();
    }
    //verifico si hay enlaces 30 min antes
    $hace30=$now-1800;
    $count_enlaces=$db->get_var("SELECT count(*) from enlaces where email='$email' and timestamp>$hace30");
    if($count_enlaces>0){
      $arr = array('estado'=>0,'error_id' => 125, 'text' => 'Solo puedes solicitar cupos cada 30 min. Espera un tiempo y vuelve a intentarlo.');
      echo json_encode($arr);
      die();
    }

    $turno=$db->get_row("SELECT * from turnos where id=$id_turno");
    $capilla=$db->get_row("SELECT * from capillas where id=$turno->id_capilla");
    //Verifico si ya conozco al usuario
    $count_exist=$db->get_var("SELECT count(*) FROM enlaces WHERE email='$email' and usado=1");
    if($count_exist>0){
      //existe
    }
    //todo correcto. Creo el enlace

    $hash=getHash();
    $cuenta=get_cuenta_mail_index(4);
    $db->query("INSERT INTO enlaces(id_turno,email, cantidad, hash, timestamp,cuenta_mail) VALUES ('$id_turno','$email',$cantidad,'$hash','$now','$cuenta')");

    //enviar mail
    include("./smtp/user.php");
    $dia=$dias[$turno->dia];
    $dia_corto=substr($dias[$turno->dia],0,4);
    $fecha_corta=str_replace('-','/',substr($turno->fecha,0,5));
    $cuerpo="<h3>¡Falta un paso más!</h3>";
    $cuerpo.="<p>Solicitaste $cantidad cupos para la capilla <b>\"$capilla->nombre\"</b>, misa del <b>$dia $turno->fecha $turno->hora hs</b>.</p>";
    $cuerpo.="<p>Para finalizar la inscripción a la misa, haz clic en el link y completa los datos solicitados.</p><a style=\"display: block;text-decoration: none;text-align: center;color: #ffffff;border: solid 1px #dedede;background: #101010c4;padding: 8px;border-radius: 10px;margin: 5px;max-width: 300px;margin-left: auto;margin-right: auto;\" href='".$_SERVER['SERVER_NAME']."/turnos/fin_inscripcion.php?hash=$hash'>Link de inscripción</a>";
    $dominio=$_SERVER['SERVER_NAME'];
    if(substr($dominio, 0, 4)!="www."){
      $dominio='www.'.$dominio;
    }
    $cuerpo.="<p>Link escrito: http://".$dominio."/turnos/fin_inscripcion.php?hash=$hash</p>";
    $cuerpo.="<h3>¡Importante!</h3>";
    $cuerpo.="<p>Si completaste los pasos de la inscripción a la misa y deseas <b>cancelar el turno</b>, ingresa al link de inscripción anteriormente mencionado, y haz clic en las <b>\"X\"</b> correspondiente a tu reserva.</p>";
    $cuerpo.="<p>Por cualquier consulta, puedes comunicarte al e-mail: secretaria@fastacordoba.org
</p><br><p>".date('d/m/Y H:i')."</p>";


    notify_custom($cuerpo,$email,'',"LINK - MISA $dia_corto $fecha_corta $turno->hora hs",$cuenta);
    $arr = array('estado'=>1,'error_id' => 0, 'text' => 'Link enviado al correo.');
    unset($_SESSION['mail']);
    echo json_encode($arr);
  }

  if($action=='get_inscripcion'){
    if(!isset($hash)){
      $arr = array('estado'=>0,'error_id' => 126, 'text' => 'No hay hash');
      echo json_encode($arr);
      die();
    }
    $enlace=$db->get_row("SELECT * from enlaces where hash='$hash'");
    if(!isset($enlace)){
      $arr = array('estado'=>0,'error_id' => 127, 'text' => 'Hash incorrecto');
      echo json_encode($arr);
      die();
    }
    if($enlace->usado==0){
      $arr = array('estado'=>0,'error_id' => 129, 'text' => 'Enlace no utilizado');
      echo json_encode($arr);
      die();
    }
    $turno=$db->get_row("SELECT * from turnos where id=$enlace->id_turno");
    $capilla=$db->get_row("SELECT * from capillas where id=$turno->id_capilla");
    $dias=array("",'Lunes','Martes','Miercoles','Jueves','Viernes','Sábado','Domingo');

    $inscripciones=$db->get_results("SELECT f.* from turnos_fieles tf join fieles f on f.id=tf.id_fiel where tf.id_enlace=$enlace->id");
    $s='';


    if(count($inscripciones)>0){
      if(count($inscripciones)>1) $s='s';
      ?>
      <style media="screen">
        .table-reserva{
          font-size:14px;
          font-size: 13px;
          table-layout: fixed;
        }
        .table-reserva td{
          text-shadow: none;
          border-bottom: solid 1px #ccc;
          word-wrap: break-word;
        }
      </style>

        <h4 style="text-align:center">¡Cupos reservados!</h4>
        <p>Reservaste <?php count($inscripciones) ?> cupo<?php echo $s ?> para la misa del <b><?php echo $dias[$turno->dia].' '.str_replace('-','/',$turno->fecha) ?></b> a las <b><?php echo $turno->hora ?>h.</b> En la capilla <b>"<?php echo $capilla->nombre ?>".</b> </p>
        <p style="color:#ff841a;font-size:14px">Estos cupos son INTRANFERIBLES. Deben asistir únicamente las personas indicadas en los datos 10 o 15 minutos antes de la misa para la acreditación. En caso de no asistir se ruega cancelar el turno.</p>
        <p>Detalle de cupos:</p>
        <table class="table-reserva" style="">
          <tr style="background:#000;color:#fff">
            <td>Nombre</td><td>Telefono/Mail</td><td style="width: 38px;">Cancelar</td>
          </tr>
          <?php
          foreach ($inscripciones as $key => $value) {
            ?>
            <tr>
              <td><?php echo $value->nombre ?></td>
              <td><?php echo $value->telefono ?><br><?php echo $value->mail ?></td>
              <td align="center"><a
                onclick="confirmar('?hash=<?php echo $hash ?>&eliminar=<?php echo $value->id ?>')" style="text-decoration:none;color:#000;font-weight: bold;cursor: pointer;">X</a></td>
            </tr>
            <?php
          }
           ?>
        </table>
        <br>


      <!--<a href="#" onclick="downloadCanvas('canvaIMG', 'comprobante.png');return false;" class="btn" style="background: #101010e0;text-align: center;">Descargar</a>-->
      <a href="#" onclick="descargar_img();return false;" class="btn" style="background: #101010e0;text-align: center;">Descargar</a>
      <a href="./index.php" class="btn" style="background: #101010e0;text-align: center;">Cerrar</a>
      <?php
    }else{
      ?>
      <script type="text/javascript">
        location.reload();
      </script>
      <?php
    }


  }

}else{
  $arr = array('estado'=>0,'error_id' => 25, 'text' => 'Marca de sesion no encontrada.');
  echo json_encode($arr);
}
function getHash(){
  global $db;
	$hash=substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 30);
	$exist=$db->get_var("SELECT count(*) from enlaces where hash='$hash'");
	if($exist){
		$hash=getHash();
	}
	return $hash;
}
 ?>
