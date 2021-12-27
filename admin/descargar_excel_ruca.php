<?php
session_start();

include('core.php');



if(isset($_SESSION['idusuario'])){
  $id_ruca=$_GET['ruca'];
  $id_usuario=$_SESSION['idusuario'];
  $count_permiso=$db->get_var("SELECT count(*) from rucas_usuarios where id_usuario=$id_usuario and id_ruca=$id_ruca");
  if(!($count_permiso>0)){
    ?> <p>No tienes permisos</p> <?php
    die();
  }

  $semana=$_GET['semana'];
  $inscriptos=$db->get_results("SELECT *,(SELECT nombre from secciones where id=inscriptos_rucas.id_seccion) seccion from inscriptos_rucas where id_seccion in (SELECT id from secciones where id_ruca=$id_ruca) and FROM_UNIXTIME(timestamp, '%V-%Y')='$semana'");
  $ruca=$db->get_row("SELECT * from rucas where id=$id_ruca");

  header("Pragma: public");
  header("Expires: 0");
  $sinEspacios=str_replace(' ','',$ruca->nombre);
  $sinEspacios=str_replace('/','',$sinEspacios);
  $dia=getDias()[$turno->dia];
  $filename = "Lista.$sinEspacios.$semana.xls";

  header("Content-type: application/x-msdownload");
  header("Content-Disposition: attachment; filename=$filename");
  header("Pragma: no-cache");
  header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
  ?>
  <table border="1">
    <tr>
      <td colspan="6" style="text-align:center">
        <?php echo utf8_decode($ruca->nombre) ?>. Semana <?php echo "$semana" ?>.
      </td>
    </tr>
    <tr>
      <td>
        #
      </td>
      <td>
        Fecha y hora
      </td>
      <td>
        <?php echo utf8_decode('Sección') ?>
      </td>
      <td>
        Miliciano
      </td>
      <td>
        Tutor
      </td>
      <td>
        <?php echo utf8_decode('Teléfono') ?>
      </td>
    </tr>
    <?php
    $num=0;
    foreach ($inscriptos as $key => $value) {
      $num++;
      ?>
      <tr>
        <td>
          <?php echo $num ?>
        </td>
        <td>
          <?php echo date("d/m/Y H:i",$value->timestamp) ?>
        </td>
        <td>
          <?php echo utf8_decode($value->seccion) ?>
        </td>
        <td>
            <?php echo utf8_decode($value->apellido.' '.$value->nombre) ?>
        </td>
        <td>
          <?php echo utf8_decode($value->apellido_t.' '.$value->nombre_t) ?>
        </td>
        <td>
          <?php echo $value->telefono ?>
        </td>
      </tr>
      <?php
    }
     ?>
  </table>
  <?php
}else{
  header('location: login.php');
}
 ?>
