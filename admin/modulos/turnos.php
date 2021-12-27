<?php
function go(){

if(!isset($_GET['action'])){
  map();
  return ;
}
switch($_GET['action']){
  case '':
    map();
  break;

  case 'map':
    map();
  break;

  case 'edit':
    edit();
    break;
  case 'update':
    update();
    break;
  case 'add':
    add();
    break;
  case 'insert':
    insert();
    break;
  case 'delete':
    delete();
    break;
  case 'viewList':
    viewList();
    break;
  case 'add_fiel':
    add_fiel();
    break;
  case 'update_fiel':
    update_fiel();
    break;
  case 'edit_fiel':
    edit_fiel();
    break;
  case 'insert_fiel':
    insert_fiel();
    break;
  case 'delete_fiel':
    delete_fiel();
    break;


  }


}
function delete(){
  global $db;
  $id=$_GET['id'];
  $count_inscriptos=$db->get_var("SELECT count(*) from turnos_fieles where id_turno=$id");
  if($count_inscriptos==0){
    $db->query("DELETE FROM `turnos` where id=$id");
  }
  ?>
	<script>location.href="<?php echo  rewriteGet('index.php',array('id','action'),array('',''));?>";</script>
	<?php
}
function edit(){
  global $db;
  $id=$_GET['id'];
  if($id){
    $turno=$db->get_row("SELECT * FROM turnos where id=$id");
    if($turno){
      $arrayFecha=explode('-',$turno->fecha);
      $turno->fecha=$arrayFecha[2].'-'.$arrayFecha[1].'-'.$arrayFecha[0];
      ?>
      <div id="encabezado" style="background:#e4c03d; color:#fff">
      	<div style="color:#fff">Editar Turno</div>

        <a href="<?php echo  rewriteGet('index.php',array('action','page','id'),array('','','')); ?>"> <i class="icon-arrow-left"></i> Volver</a>
      </div>
      <form onsubmit="return validar(this);"  class="" action="<?php echo  rewriteGet('index.php',array('action','page','id'),array('update','',$id)); ?>" method="post">
        <?php formulario($turno); ?>
      </form>
      <?php
      die();
    }

  }
  header(rewriteGet('index.php',array('action','page','id'),array('','','')));

}
function update(){
	global $db;
	extract($_POST);
  $id=$_GET['id'];

  $arrayFecha=explode('-',$fecha);
  $arrayHora=explode(':',$hora);
  $timestamp=mktime($arrayHora[0], $arrayHora[1], 0, $arrayFecha[1], $arrayFecha[2], $arrayFecha[0]);
  $fecha=$arrayFecha[2].'-'.$arrayFecha[1].'-'.$arrayFecha[0];
  $dia=date('N',$timestamp);

	$db->query("UPDATE `turnos` set `dia`='$dia' where id=$id");
  $db->query("UPDATE `turnos` set `fecha`='$fecha' where id=$id");
  $db->query("UPDATE `turnos` set `hora`='$hora' where id=$id");
  $db->query("UPDATE `turnos` set `timestamp`='$timestamp' where id=$id");
  $db->query("UPDATE `turnos` set `note`='$note' where id=$id");
  $db->query("UPDATE `turnos` set `activo`='$activo' where id=$id");


	?>
	<pre>
		<?php //print_r($_POST); ?>
	</pre>
	<?php
	?>
	<script>location.href="<?php echo  rewriteGet('index.php',array('id','action'),array('',''));?>";</script>
	<?php
}
function insert(){
	global $db;
	extract($_POST);

  $id_capilla=$_GET['status'];
  /*
  [fecha] => 2020-07-30
    [hora] => 19:00*/
  $arrayFecha=explode('-',$fecha);
  $arrayHora=explode(':',$hora);
  $timestamp=mktime($arrayHora[0], $arrayHora[1], 0, $arrayFecha[1], $arrayFecha[2], $arrayFecha[0]);
  $fecha=$arrayFecha[2].'-'.$arrayFecha[1].'-'.$arrayFecha[0];
  $dia=date('N',$timestamp);
  $hash=getHash_turnos();
	$db->query("INSERT INTO `turnos`(`hash`,`note`, `id_capilla`, `dia`, `fecha`, `hora`, `timestamp`, `activo`) VALUES ('$hash','$note','$id_capilla','$dia','$fecha','$hora','$timestamp','$activo')");

	?>
	<pre>
		<?php // echo print_r($_POST); ?>
	</pre>

	<script>location.href="<?php echo  rewriteGet('index.php',array('id','action'),array('',''));?>";</script>
	<?php
}
function getHash_turnos(){
  global $db;
	$hash=substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 10);
	$exist=$db->get_var("SELECT count(*) from turnos where hash='$hash'");
	if($exist>0){
		$hash=getHash_turnos();
	}
	return $hash;
}
function add(){
  global $db;
  ?>
  <div id="encabezado" style="background:#e4c03d; color:#fff">
  	<div style="color:#fff"><i class="icon-plus-sign"></i> Agregar Turno</div>

    <a href="<?php echo  rewriteGet('index.php',array('action','page','id'),array('','','')); ?>"> <i class="icon-arrow-left"></i> Volver</a>
  </div>
  <form onsubmit="return validar(this);"  class="" action="<?php echo  rewriteGet('index.php',array('action','page','id'),array('insert','','')); ?>" method="post">
    <?php formulario(); ?>
  </form>
  <?php

}
function formulario($t = null){
  global $db;
  ?>
  <table class="tableform" align="center" cellpadding="10" cellspacing="0">
    <tr>
      <td class="label">
        Fecha
      </td>
      <td class="intro">
        <input class="valid" type="date" name="fecha" value="<?php if(isset($t)){
          echo $t->fecha;
        }  ?>">
      </td>
    </tr>
    <tr>
      <td class="label">
        Hora
      </td>
      <td class="intro">
        <input class="valid" type="time" name="hora" value="<?php if(isset($t)) echo $t->hora; ?>">
      </td>
    </tr>
    <tr>
      <td class="label">
        Nota
      </td>
      <td class="intro">
        <input type="text" name="note" value="<?php if(isset($t)) echo $t->note; ?>">
      </td>
    </tr>
    <tr>
      <td class="label">
        Activo
      </td>
      <td class="intro">
        <select class="" name="activo">
          <option value="1" <?php if(isset($t) && $t->activo==1) echo "selected"; ?>>Si</option>
          <option value="0" <?php if(isset($t) && $t->activo==0) echo "selected"; ?>>No</option>
        </select>
      </td>
    </tr>

    <tr><td colspan="3"><input type="submit" class="submit" value="ENVIAR DATOS" /></td></tr>
  </table>
    <?php
}
function add_fiel(){
  global $db;
  ?>
  <div id="encabezado" style="background:#e4c03d; color:#fff">
  	<div style="color:#fff"><i class="icon-plus-sign"></i> Agregar Inscripto</div>

    <a href="<?php echo  rewriteGet('index.php',array('action','page'),array('viewList','')); ?>"> <i class="icon-arrow-left"></i> Volver</a>
  </div>
  <form onsubmit="return validar(this);"  class="" action="<?php echo  rewriteGet('index.php',array('action','page'),array('insert_fiel','')); ?>" method="post">
    <?php formulario_fiel(); ?>
  </form>
  <?php
}
function update_fiel(){
	global $db;
	extract($_POST);
  $id=$_GET['id'];
  $fiel=$db->get_row("SELECT * FROM fieles where id=$id");
  $id_turno=$db->get_var("SELECT id_turno FROM turnos_fieles where id_fiel=$id");

	$db->query("UPDATE `fieles` set `nombre`='$nombre' where id=$id");
  $db->query("UPDATE `fieles` set `mail`='$mail' where id=$id");
  $db->query("UPDATE `fieles` set `telefono`='$telefono' where id=$id");
  $db->query("UPDATE `turnos_fieles` set `cupo_visible`='$cupo_visible' where id_fiel=$id");

	?>
	<script>location.href="<?php echo  rewriteGet('index.php',array('id','action'),array($id_turno,'viewList'));?>";</script>
	<?php
}
function edit_fiel(){
  global $db;
  $id_fiel=$_GET['id'];
  if($id_fiel){

    $id_turno=$db->get_var("SELECT id_turno FROM turnos_fieles where id_fiel=$id_fiel");
    $fiel=$db->get_row("SELECT f.*, tf.cupo_visible FROM fieles f join turnos_fieles tf on tf.id_fiel=f.id where f.id=$id_fiel");

    if($fiel){
      ?>
      <div id="encabezado" style="background:#e4c03d; color:#fff">
        <div style="color:#fff">Editar Inscripción</div>

        <a href="<?php echo  rewriteGet('index.php',array('action','page','id'),array('viewList','',$id_turno)); ?>"> <i class="icon-arrow-left"></i> Volver</a>
      </div>
      <form onsubmit="return validar(this);"  class="" action="<?php echo  rewriteGet('index.php',array('action','page'),array('update_fiel','')); ?>" method="post">
        <?php formulario_fiel($fiel); ?>
      </form>
      <?php
      die();
    }

  }
  header(rewriteGet('index.php',array('action','page'),array('','')));
}
function insert_fiel(){
	global $db;
	extract($_POST);

  $id_turno=$_GET['id'];

	$db->query("INSERT INTO `fieles`(`nombre`, `telefono`, `mail`, `bloqueado`) VALUES ('$nombre','$telefono','$mail','0')");
  $newFiel=$db->get_var("SELECT max(id) FROM fieles where mail='$mail'");
  $db->query("INSERT INTO `turnos_fieles`(`id_fiel`, `id_turno`, `id_enlace`,cupo_visible) VALUES ('$newFiel','$id_turno','0','$cupo_visible')");
	?>

	<script>location.href="<?php echo  rewriteGet('index.php',array('id','action'),array($id_turno,'viewList'));?>";</script>
	<?php
}
function formulario_fiel($t = null){
  global $db;
  ?>
  <table class="tableform" align="center" cellpadding="10" cellspacing="0">
    <tr>
      <td class="label">
        Nombre y apellido
      </td>
      <td class="intro">
        <input type="text" name="nombre" value="<?php if(isset($t)) echo $t->nombre; ?>">
      </td>
    </tr>
    <tr>
      <td class="label">
        Teléfono
      </td>
      <td class="intro">
        <input type="text" name="telefono" value="<?php if(isset($t)) echo $t->telefono; ?>">
      </td>
    </tr>
    <tr>
      <td class="label">
        Email
      </td>
      <td class="intro">
        <input type="text" name="mail" value="<?php if(isset($t)) echo $t->mail; ?>">
      </td>
    </tr>
    <tr>
      <td class="label">
        Visibilizar cupo
      </td>
      <td class="intro">
        <select class="" name="cupo_visible">
          <option value="1" <?php if(isset($t) && $t->cupo_visible=='1') echo "selected"; ?>>Si</option>
          <option value="0" <?php if(isset($t) && $t->cupo_visible=='0') echo "selected"; ?>>No</option>
        </select>
      </td>
    </tr>
    <tr><td colspan="3"><input type="submit" class="submit" value="ENVIAR DATOS" /></td></tr>
  </table>
    <?php
}

function delete_fiel(){
  global $db;
  $id=$_GET['id'];
  $id_turno=$db->get_var("SELECT id_turno FROM turnos_fieles where id_fiel=$id");
  $db->query("DELETE FROM `turnos_fieles` where id_fiel=$id and id_turno=$id_turno");
  //$db->query("DELETE FROM `fieles` where id=$id");
  ?>
	<script>location.href="<?php echo  rewriteGet('index.php',array('id','action'),array($id_turno,'viewList'));?>";</script>
	<?php
}
function map(){
  global $db;
  $id_capilla=$_GET['status'];
  $id_usuario=$_SESSION['idusuario'];
  $count_permiso=$db->get_var("SELECT count(*) from capillas_usuarios where id_usuario=$id_usuario and id_capilla=$id_capilla");
  if($count_permiso>0){
    //PAGINACION
    $mostrar = 10;
    if(isset($_GET['page'])){
    $desde = $_GET['page']*$mostrar;
    }else{
    $desde = 0;
    }
    $capilla=$db->get_row("SELECT * from capillas where id=$id_capilla");
    $dias=getDias();
    ?>
    <script type="text/javascript">
    function confirmar(message){
    if (confirm("¿SEGURO DESEA BORRAR EL REGISTRO?")){
    location.href=message;
    return " ";}}
    </script>
  <div id="encabezado" style="background:#e4c03d; color:#fff">
  	<div style="color:#fff">Lista de Turnos</div>

    <a href="<?php echo  rewriteGet('index.php',array('action','page','id'),array('add','','')); ?>"> <i class="icon-plus-sign"></i> Agregar Turno</a>
  </div>
  <ul id="solapas_estados">

    <?php
    foreach ($dias as $key => $value) {
      if($key>0){
        $count_turnos=$db->get_var("SELECT count(*) FROM turnos where id_capilla=$id_capilla and dia=$key and activo=1");
        ?>
        <li style="border-bottom-color: <?php if($count_turnos==0) echo '#c00'; else echo '#0c0' ?>; border-bottom-style: solid; border-bottom-width:5px;<?php if((!isset($_GET['dia']) && $key==1) || $_GET['dia']==$key ){ echo 'background:#000;';$dia_select=$key; } ?>">
        <a href="<?php echo  rewriteGet('index.php',array('dia','page'),array($key,'')); ?>">
        <?php echo $value ?>
        </a>
        </li>
        <?php
      }

    }
    $turnos=$db->get_results("SELECT *,(SELECT count(*) from turnos_fieles where id_turno=turnos.id) inscriptos, (SELECT cupos from capillas where id=turnos.id_capilla)-(SELECT count(*) from turnos_fieles where id_turno=turnos.id and cupo_visible=1) cupos from turnos where id_capilla=$id_capilla and dia=$dia_select order by timestamp desc LIMIT $desde,$mostrar");
    $cantidad = $db->get_var("SELECT count(*) from turnos where id_capilla=$id_capilla and dia=$dia_select");

    ?>
    </ul>
  <table class="table" width="100%"  cellpadding="10" cellspacing="0">
    <tr class="thead">
      <td>
        Fecha
      </td>
      <td>
        Hora
      </td>
      <td>
        Nota
      </td>
      <td align="center">
        Inscriptos
      </td>
      <td align="center">
        Cupos libres
      </td>
      <td align="center">
        Ver Lista
      </td>
      <td align="center" style="width: 50px;">
        Activo
      </td>
      <td align="center" style="width: 50px;">
        Editar
      </td>
      <td align="center" style="width: 50px;">
        Borrar
      </td>
    </tr>
    <?php
    foreach ($turnos as $key => $value) {
      ?>
      <tr id="atu<?php echo $value->id ?>"  <?php if(!$value->activo) echo 'class="desactivado"' ?> >
        <td>
          <?php echo $value->fecha ?>
        </td>
        <td>
          <?php echo $value->hora ?>
        </td>
        <td>
          <?php echo $value->note ?>
        </td>
        <td align="center">
          <?php echo $value->inscriptos; ?>
        </td>
        <td align="center">
          <?php echo $value->cupos; ?>
        </td>
        <td align="center">
          <a href="<?php echo rewriteGet('index.php',array('action','id'),array('viewList',$value->id)); ?>"> <i class="icon-list-alt"></i></a>
        </td>
        <td align="center">
          <a href="#" onclick="toggletActive(<?php echo $value->id ?>,this,'turnos');return false;">
            <?php if($value->activo==1) echo '<i style="color:#063" class="icon-ok-sign"></i>';
                  else  echo '<i style="color:#900" class="icon-remove-sign"></i>'; ?>
            </a>
        </td>
        <td align="center">
          <a href="<?php echo rewriteGet('index.php',array('action','id'),array('edit',$value->id)) ?>"><i class="icon-edit"></i></a>
        </td>
        <td align="center">
          <a href="#" onclick="confirmar('<?php echo rewriteGet('index.php',array('action','id'),array('delete',$value->id)) ?>');return false;"><i class="icon-trash"></i></a>
        </td>
      </tr>
      <?php
    }
     ?>
  </table>
    <?php
    paginacion($cantidad,$mostrar);
  }else{
    session_destroy();
    ?>
    <script type="text/javascript">
      alert('<?php echo $id_capilla ?>/<?php echo $id_usuario ?>');
      location.reload();
    </script>
    <?php

  }

}
function viewList(){
  global $db;
  $id_turno=$_GET['id'];
  $id_usuario=$_SESSION['idusuario'];
  $id_capilla=$db->get_var("SELECT id_capilla from turnos where id=$id_turno");
  $count_permiso=$db->get_var("SELECT count(*) from capillas_usuarios where id_usuario=$id_usuario and id_capilla=$id_capilla");
  if($count_permiso>0){
    //PAGINACION
    $mostrar = 20;
    if(isset($_GET['page'])){
    $desde = $_GET['page']*$mostrar;
    }else{
    $desde = 0;
    }
    $capilla=$db->get_row("SELECT * from capillas where id=$id_capilla");
    $turno=$db->get_row("SELECT * from turnos where id=$id_turno");
    $dias=getDias();
    ?>
    <script type="text/javascript">
    function confirmar(message){
    if (confirm("¿SEGURO DESEA BORRAR EL REGISTRO?")){
    location.href=message;
    return " ";}}

    </script>
  <div id="encabezado" style="background:#e4c03d; color:#fff">
  	<div style="color:#fff">Lista de inscriptos. <b><?php echo $dias[$turno->dia].' '.$turno->fecha ?>. <?php echo $capilla->nombre ?></b> </div>
    <a href="descargar_excel.php?id_turno=<?php echo $turno->id ?>" target="_blank"> <i class="icon-download-alt"></i> Descargar</a>
    <a href="list_online.php?hcode=<?php echo $turno->hash ?>" target="_blank"> <i class="icon-check"></i> Link de asistencia</a>

    <a href="<?php echo  rewriteGet('index.php',array('action','page','id'),array('add_fiel','',$id_turno)); ?>"> <i class="icon-plus-sign"></i> Agregar</a>
    <a href="<?php echo  rewriteGet('index.php',array('action','page','id'),array('','','')); ?>"> <i class="icon-arrow-left"></i> Volver</a>
  </div>

  <table class="table" width="100%"  cellpadding="10" cellspacing="0">
    <tr class="thead">
      <td style="width: 10px;">
        #
      </td>
      <td>
        Fecha y hora
      </td>
      <td>
        Nombre y apellido
      </td>
      <td>
        Teléfono
      </td>
      <td >
        Email
      </td>
      <?php
      $now=time();
      if($turno->timestamp<$now){
        ?>
        <td align="center">
          Asistencia
        </td>
        <?php
      }
       ?>

      <td align="center">
        Editar
      </td>
      <td align="center">
        Borrar
      </td>
    </tr>
    <?php
    $inscriptos=$db->get_results("SELECT f.*,tf.timestamp,tf.checked from turnos_fieles tf join fieles f on tf.id_fiel=f.id where tf.id_turno=$id_turno order by tf.timestamp LIMIT $desde,$mostrar");
    $cantidad = $db->get_var("SELECT count(*) from turnos_fieles tf join fieles f on tf.id_fiel=f.id where tf.id_turno=$id_turno");
    $num=$_GET['page']*$mostrar;
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
          <?php echo $value->nombre ?>
        </td>
        <td>
          <a target="_blank" href="https://wa.me/54<?php echo $value->telefono ?>">
            <?php echo $value->telefono ?>
          </a>
        </td>
        <td >
          <?php echo $value->mail; ?>
        </td>
        <?php
        if($turno->timestamp<$now){
          ?>
          <td align="center">
            <?php
            if($value->checked==='1'){
              echo '<i title="Presente" style="color:#00a200" class="icon-check"></i>';
            }else{
              echo '<i title="Ausente" style="color:#ea0000" class="icon-remove"></i>';
            }
             ?>
          </td>
          <?php
        }
         ?>
        <td align="center">
          <a href="<?php echo rewriteGet('index.php',array('action','id'),array('edit_fiel',$value->id)) ?>"><i class="icon-edit"></i></a>
        </td>
        <td align="center">
          <a href="#" onclick="confirmar('<?php echo rewriteGet('index.php',array('action','id'),array('delete_fiel',$value->id)) ?>');return false;"><i class="icon-trash"></i></a>
        </td>
      </tr>
      <?php
    }
     ?>
  </table>
    <?php
    paginacion($cantidad,$mostrar);
  }else{
    session_destroy();
    ?>
    <script type="text/javascript">
      alert('No tenes permisos para ver esta lista. <?php echo $id_capilla ?>/<?php echo $id_usuario ?>');
      location.reload();
    </script>
    <?php

  }

}
 ?>
