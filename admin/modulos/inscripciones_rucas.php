<?php



function go(){

if(!isset($_GET['action'])){
  viewList();
  return ;
}
switch($_GET['action']){
  case '':
    viewList();
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
  default: viewList();

  }


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
  $id_semana=$db->get_var("SELECT FROM_UNIXTIME(timestamp, '%V-%Y') FROM inscriptos_rucas where id=$id");
  if(isset($nocontacto)){
    $nocontacto='1';
  }else{
    $nocontacto='0';
  }
  if(isset($nosintomas)){
    $nosintomas='1';
  }else{
    $nosintomas='0';
  }
	$db->query("UPDATE inscriptos_rucas set id_seccion='$id_seccion' where id=$id");
  $db->query("UPDATE inscriptos_rucas set nombre='$nombre' where id=$id");
  $db->query("UPDATE inscriptos_rucas set apellido='$apellido' where id=$id");
  $db->query("UPDATE inscriptos_rucas set nombre_t='$nombre_t' where id=$id");
  $db->query("UPDATE inscriptos_rucas set apellido_t='$apellido_t' where id=$id");
  $db->query("UPDATE inscriptos_rucas set telefono='$telefono' where id=$id");
  $db->query("UPDATE inscriptos_rucas set nocontacto='$nocontacto' where id=$id");
  $db->query("UPDATE inscriptos_rucas set nosintomas='$nosintomas' where id=$id");

	?>
	<script>location.href="<?php echo  rewriteGet('index.php',array('id','action'),array($id_semana,'viewList'));?>";</script>
	<?php
}
function edit_fiel(){
  global $db;
  $id=$_GET['id'];
  if($id){

    $id_semana=$db->get_var("SELECT FROM_UNIXTIME(timestamp, '%V-%Y') FROM inscriptos_rucas where id=$id");
    $insc=$db->get_row("SELECT * FROM inscriptos_rucas where id=$id");

    if($insc){
      ?>
      <div id="encabezado" style="background:#e4c03d; color:#fff">
        <div style="color:#fff">Editar Inscripción</div>

        <a href="<?php echo  rewriteGet('index.php',array('action','page','id'),array('viewList','',$id_semana)); ?>"> <i class="icon-arrow-left"></i> Volver</a>
      </div>
      <form onsubmit="return validar(this);"  class="" action="<?php echo  rewriteGet('index.php',array('action','page'),array('update_fiel','')); ?>" method="post">
        <?php formulario_fiel($insc); ?>
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
  $semana=$_GET['id'];
  $week=explode('-',$semana)[0];
  $year=explode('-',$semana)[1];
  $timestamp=$_GET['id'];
  $pointer_day=mktime(0,0,0,1,4,2021);
  while(date('W',$pointer_day)<$week || date('Y',$pointer_day)!=$year){
    $pointer_day+=86400;
  }
  $pointer_day+=86400;
  if(isset($nocontacto)){
    $nocontacto='1';
  }else{
    $nocontacto='0';
  }
  if(isset($nosintomas)){
    $nosintomas='1';
  }else{
    $nosintomas='0';
  }
  $db->query("INSERT INTO inscriptos_rucas(id_seccion, timestamp, nombre, apellido, nombre_t, apellido_t, telefono, nocontacto, nosintomas) VALUES ('$id_seccion', '$pointer_day', '$nombre', '$apellido', '$nombre_t', '$apellido_t', '$telefono', '$nocontacto', '$nosintomas')");
	?>
	<script>location.href="<?php echo  rewriteGet('index.php',array('id','action','seccion'),array($semana,'viewList',$id_seccion));?>";</script>
	<?php
}
function formulario_fiel($t = null){
  global $db;
  $id_ruca=$_GET['status'];
  if(isset($t)){
    $id_seccion=$t->id_seccion;
  }else{
    $id_seccion=$_GET['seccion'];
    if(!isset($id_seccion)){
      $id_seccion=$db->get_var("SELECT id from secciones where id_ruca=$id_ruca order by orden limit 1");
    }
  }

  ?>
  <table class="tableform" align="center" cellpadding="10" cellspacing="0">
    <tr>
      <td class="label">Sección</td>
      <td class="intro">
        <select class="" name="id_seccion">
          <?php
          $secciones=$db->get_results("SELECT * from secciones where id_ruca=$id_ruca order by orden ");
          foreach ($secciones as $key => $value) {
            ?>
            <option value="<?php echo $value->id ?>" <?php if($value->id==$id_seccion) echo "selected"; ?>><?php echo $value->nombre ?></option>
            <?php
          }
           ?>
        </select>
      </td>
    </tr>
    <tr>
      <td colspan="2" align="center" style="font-weight: bold;">
        Miliciano
      </td>
    </tr>
    <tr>
      <td class="label">
        Nombre
      </td>
      <td class="intro">
        <input type="text" name="nombre" value="<?php if(isset($t)) echo $t->nombre; ?>">
      </td>
    </tr>
    <tr>
      <td class="label">
        Apellido
      </td>
      <td class="intro">
        <input type="text" name="apellido" value="<?php if(isset($t)) echo $t->apellido; ?>">
      </td>
    </tr>
    <tr>
      <td colspan="2" align="center" style="font-weight: bold;">
        Tutor
      </td>
    </tr>
    <tr>
      <td class="label">
        Nombre
      </td>
      <td class="intro">
        <input type="text" name="nombre_t" value="<?php if(isset($t)) echo $t->nombre_t; ?>">
      </td>
    </tr>
    <tr>
      <td class="label">
        Apellido
      </td>
      <td class="intro">
        <input type="text" name="apellido_t" value="<?php if(isset($t)) echo $t->apellido_t; ?>">
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
        No Contacto
      </td>
      <td class="intro">
        <input type="checkbox" name="nocontacto" <?php if(isset($t) && $t->nocontacto==1) echo 'checked'; ?>>
      </td>
    </tr>
    <tr>
      <td class="label">
        No Sintomas
      </td>
      <td class="intro">
        <input type="checkbox" name="nosintomas" <?php if(isset($t) && $t->nosintomas==1) echo 'checked'; ?>>
      </td>
    </tr>
    <tr><td colspan="3"><input type="submit" class="submit" value="ENVIAR DATOS" /></td></tr>
  </table>
    <?php
}
function delete_fiel(){
  global $db;
  $id=$_GET['id'];
  $semana=$db->get_var("SELECT FROM_UNIXTIME(timestamp, '%V-%Y') FROM inscriptos_rucas where id=$id");
  $db->query("DELETE FROM inscriptos_rucas where id=$id");
  ?>
	<script>location.href="<?php echo  rewriteGet('index.php',array('id','action'),array($semana,'viewList'));?>";</script>
	<?php
}
function viewList(){
  global $db;
  $id_ruca=$_GET['status'];
  $id_seccion=$_GET['seccion'];
  if(!isset($id_seccion)){
    $id_ruca=$_GET['status'];
    $id_seccion=$db->get_var("SELECT id from secciones where id_ruca=$id_ruca order by orden limit 1");
  }
  $semana=$_GET['id'];
  $week=explode('-',$semana)[0];
  $year=explode('-',$semana)[1];
  $id_usuario=$_SESSION['idusuario'];
  $count_permiso=$db->get_var("SELECT count(*) from rucas_usuarios where id_usuario=$id_usuario and id_ruca=$id_ruca");
  if($count_permiso>0){
    //PAGINACION
    $mostrar = 20;
    if(isset($_GET['page'])){
    $desde = $_GET['page']*$mostrar;
    }else{
    $desde = 0;
    }
    $ruca=$db->get_row("SELECT * from rucas where id=$id_ruca");
    $seccion=$db->get_row("SELECT * from secciones where id=$id_seccion");
    $dias=getDias();
    ?>
    <script type="text/javascript">
    function confirmar(message){
    if (confirm("¿SEGURO DESEA BORRAR EL REGISTRO?")){
    location.href=message;
    return " ";}}

    </script>
  <div id="encabezado" style="background:#e4c03d; color:#fff">
  	<div style="color:#fff"><b>Ruca <?php if(strlen($seccion->nombre)>25){echo substr($seccion->nombre,0,20).'[...]';}else{echo $seccion->nombre;} ?>. Semana <?php echo $week ?> del año <?php echo $year ?></b> </div>

    <a style="display:none" href="descargar_excel.php?id_turno=<?php echo $seccion->id ?>" target="_blank"> <i class="icon-download-alt"></i> Descargar</a>
    <a style="display:none" href="list_online.php?hcode=<?php echo $seccion->hash_access ?>" target="_blank"> <i class="icon-check"></i> Link de asistencia</a>

    <a href="<?php echo  rewriteGet('index.php',array('action','page'),array('add_fiel','')); ?>"> <i class="icon-plus-sign"></i> Agregar</a>
    <a href="<?php echo  rewriteGet('index.php',array('modulo','action','page','id'),array('secciones','','','')); ?>"> <i class="icon-arrow-left"></i> Volver</a>
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
        Miliciano
      </td>
      <td>
        Tutor
      </td>
      <td >
        Teléfono
      </td>

      <td align="center">
        No contacto estrecho
      </td>
      <td align="center">
        Sin Síntomas
      </td>
      <td align="center">
        Asistencia
      </td>
      <td align="center">
        Editar
      </td>
      <td align="center">
        Borrar
      </td>
    </tr>
    <?php
    $inscriptos=$db->get_results("SELECT * from inscriptos_rucas where FROM_UNIXTIME(timestamp, '%V-%Y')='$semana' and id_seccion=$seccion->id order by timestamp LIMIT $desde,$mostrar");
    $cantidad = $db->get_var("SELECT count(*) from inscriptos_rucas where FROM_UNIXTIME(timestamp, '%V-%Y')='$semana' and id_seccion=$seccion->id");
    $num=$_GET['page']*$mostrar;
    foreach ($inscriptos as $key => $value) {
      $num++;
      ?>
      <tr>
        <td>
          <?php echo $num ?>
        </td>
        <td>
          <?php echo date('d/m/Y',$value->timestamp) ?>
        </td>
        <td>
          <?php echo $value->apellido.', '.$value->nombre ?>
        </td>
        <td>
          <?php echo $value->apellido_t.', '.$value->nombre_t ?>
        </td>
        <td>
          <a style="text-decoration: underline;" target="_blank" href="https://wa.me/54<?php echo $value->telefono ?>">
            <?php echo $value->telefono ?>
          </a>
        </td>
        <td align="center">
          <?php
          if($value->nocontacto){
            ?>
            <i class="icon-check"></i>
            <?php
          }else{
            ?>
            <i class="icon-check-empty"></i>
            <?php
          }
           ?>
        </td>
        <td align="center">

          <?php
          if($value->nosintomas){
            ?>
            <i class="icon-check"></i>
            <?php
          }else{
            ?>
            <i class="icon-check-empty"></i>
            <?php
          }
           ?>
        </td>
        <td align="center">
          <?php
          if($value->checked==='1'){
            echo '<i title="Presente" style="color:#00a200" class="icon-check"></i>';
          }else{
            echo '<i title="Ausente" style="color:#ea0000" class="icon-remove"></i>';
          }
           ?>
        </td>
        <td align="center">
          <a href="<?php echo rewriteGet('index.php',array('action','id'),array('edit_fiel',$value->id)) ?>"><i class="icon-edit"></i></a>
        </td>
        <td align="center">
          <a style="cursor:pointer" onclick="confirmar('<?php echo rewriteGet('index.php',array('action','id'),array('delete_fiel',$value->id)) ?>');return false;"><i class="icon-trash"></i></a>
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
