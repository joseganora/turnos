<?php
session_start();

include('core.php');



if(isset($_SESSION['idusuario'])){
  $id_turno=$_GET['id_turno'];
  $inscriptos=$db->get_results("SELECT f.*,tf.timestamp from turnos_fieles tf join fieles f on tf.id_fiel=f.id where tf.id_turno=$id_turno order by f.nombre");
  $turno=$db->get_row("SELECT * from turnos where id=$id_turno");
  $capilla=$db->get_row("SELECT * from capillas where id=$turno->id_capilla");

  header("Pragma: public");
  header("Expires: 0");
  $sinEspacios=str_replace(' ','',$capilla->nombre);
  $sinEspacios=str_replace('/','',$sinEspacios);
  $dia=getDias()[$turno->dia];
  $filename = "Lista.$sinEspacios.$dia.$turno->fecha.$turno->hora.xls";

  header("Content-type: application/x-msdownload");
  header("Content-Disposition: attachment; filename=$filename");
  header("Pragma: no-cache");
  header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
  ?>

  <table border="1">
    <tr>
      <td colspan="5" style="text-align:center">
        <?php echo utf8_decode($capilla->nombre) ?> <?php echo utf8_decode($dia.' '.$turno->fecha.' '.$turno->hora.' h.'); ?>
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
        Nombre y apellido
      </td>
      <td>
        <?php echo utf8_decode('Teléfono') ?>
      </td>
      <td >
        Email
      </td>
    </tr>
    <?php
    $num=0;
    foreach ($inscriptos as $key => $value) {
      $num++;
      $arrayDT=explode(' ',$value->timestamp);
      $fecha=$arrayDT[0];
      $fechaArry=explode('-',$fecha);
      $fecha=$fechaArry[2].'/'.$fechaArry[1].'/'.$fechaArry[0];
      $hora=$arrayDT[1];
      $hora=substr($hora,0,-3);
      ?>
      <tr>
        <td>
          <?php echo $num ?>
        </td>
        <td>
          <?php echo $fecha.' '.$hora ?>
        </td>
        <td>
          <?php echo utf8_decode($value->nombre) ?>
        </td>
        <td>
          <?php echo $value->telefono ?>
        </td>
        <td >
          <?php echo utf8_decode($value->mail); ?>
        </td>
      </tr>
      <?php
    }
     ?>
  </table>
  <?php
}
 ?>
