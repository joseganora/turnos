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

  }


}
function delete(){
  global $db;
  $id=$_GET['id'];
  $db->query("DELETE FROM `rucas` where id=$id");
  ?>
	<script>location.href="<?php echo  rewriteGet('index.php',array('id','action'),array('',''));?>";</script>
	<?php
}
function edit(){
  global $db;
  $id=$_GET['id'];
  if($id){
    $capilla=$db->get_row("SELECT * FROM rucas where id=$id");
    if($capilla){
      ?>
      <div id="encabezado" style="background:#e4c03d; color:#fff">
      	<div style="color:#fff">Editar Ruca - <?php echo $capilla->nombre ?></div>

        <a href="<?php echo  rewriteGet('index.php',array('action','page','id'),array('','','')); ?>"> Volver</a>
      </div>
      <form onsubmit="return validar(this);"  class="" action="<?php echo  rewriteGet('index.php',array('action','page','id'),array('update','',$id)); ?>" method="post">
        <?php formulario($capilla); ?>
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

	$db->query("UPDATE `rucas` set `nombre`='$nombre' where id=$id");
  $db->query("UPDATE `rucas` set `imagen`='$imagen' where id=$id");
  $db->query("UPDATE `rucas` set `provincia`='$provincia' where id=$id");
  $db->query("UPDATE `rucas` set `ciudad`='$ciudad' where id=$id");
  $db->query("UPDATE `rucas` set `activo`='$activo' where id=$id");

	?>
	<pre>
		<?php //print_r($_POST); ?>
	</pre>
	<?php
	?>
	<script>location.href="<?php echo  rewriteGet('index.php',array('id','action'),array('',''));?>";</script>
	<?php
}
function get_hash_ruca(){
  global $db;
	$hash_access=substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 10);
	$exist=$db->get_var("SELECT count(*) from rucas where hash_access='$hash_access'");
	if($exist>0){
		$hash_access=get_hash_ruca();
	}
	return $hash_access;
}
function insert(){
	global $db;
	extract($_POST);
  $id_usuario=$_SESSION['idusuario'];
  $now=time();
  $new_hash=get_hash_ruca();
	$db->query("INSERT INTO `rucas`(`nombre`, `imagen`, `provincia`, `ciudad`, `activo`,id_usuario,timestamp,hash_access)
  VALUES ('$nombre','$imagen','$provincia','$ciudad','$activo',$id_usuario,$now,'$new_hash')");

	?>
	<pre>
		<?php //print_r($_POST); ?>
	</pre>
	<?php
	?>
	<script>location.href="<?php echo  rewriteGet('index.php',array('id','action'),array('',''));?>";</script>
	<?php
}
function add(){
  global $db;
  ?>
  <div id="encabezado" style="background:#e4c03d; color:#fff">
  	<div style="color:#fff">Agregar Ruca</div>

    <a href="<?php echo  rewriteGet('index.php',array('action','page','id'),array('','','')); ?>"> Volver</a>
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
        Nombre
      </td>
      <td class="intro">
        <input class="text" type="text" name="nombre" maxchar="100" value="<?php if(isset($t)) echo $t->nombre;  ?>">
      </td>
    </tr>
    <tr>
      <td class="label">
        imagen
      </td>
      <td class="intro">
        <input class="text" type="text" name="imagen" maxchar="100" value="<?php if(isset($t)) echo $t->imagen;  ?>">
      </td>
    </tr>
    <tr>
      <td class="label">
        Provincia
      </td>
      <td class="intro">
        <select class="" name="provincia">
          <option value="Buenos Aires" <?php if(isset($t) && $t->provincia=='Buenos Aires') echo 'selected';  ?>>Buenos Aires</option>
          <option value="Catamarca" <?php if(isset($t) && $t->provincia=='Catamarca') echo 'selected';  ?>>Catamarca</option>
          <option value="Chaco" <?php if(isset($t) && $t->provincia=='Chaco') echo 'selected';  ?>>Chaco</option>
          <option value="Chubut" <?php if(isset($t) && $t->provincia=='Chubut') echo 'selected';  ?>>Chubut</option>
          <option value="C??rdoba" <?php if(isset($t) && $t->provincia=='C??rdoba') echo 'selected';  ?>>C??rdoba</option>
          <option value="Corrientes" <?php if(isset($t) && $t->provincia=='Corrientes') echo 'selected';  ?>>Corrientes</option>
          <option value="Entre R??os" <?php if(isset($t) && $t->provincia=='Entre R??os') echo 'selected';  ?>>Entre R??os</option>
          <option value="Formosa" <?php if(isset($t) && $t->provincia=='Formosa') echo 'selected';  ?>>Formosa</option>
          <option value="Jujuy" <?php if(isset($t) && $t->provincia=='Jujuy') echo 'selected';  ?>>Jujuy</option>
          <option value="La Pampa" <?php if(isset($t) && $t->provincia=='La Pampa') echo 'selected';  ?>>La Pampa</option>
          <option value="La Rioja" <?php if(isset($t) && $t->provincia=='La Rioja') echo 'selected';  ?>>La Rioja</option>
          <option value="Mendoza" <?php if(isset($t) && $t->provincia=='Mendoza') echo 'selected';  ?>>Mendoza</option>
          <option value="Misiones" <?php if(isset($t) && $t->provincia=='Misiones') echo 'selected';  ?>>Misiones</option>
          <option value="Neuqu??n" <?php if(isset($t) && $t->provincia=='Neuqu??n') echo 'selected';  ?>>Neuqu??n</option>
          <option value="R??o Negro" <?php if(isset($t) && $t->provincia=='R??o Negro') echo 'selected';  ?>>R??o Negro</option>
          <option value="Salta" <?php if(isset($t) && $t->provincia=='Salta') echo 'selected';  ?>>Salta</option>
          <option value="San Juan" <?php if(isset($t) && $t->provincia=='San Juan') echo 'selected';  ?>>San Juan</option>
          <option value="San Luis" <?php if(isset($t) && $t->provincia=='San Luis') echo 'selected';  ?>>San Luis</option>
          <option value="Santa Cruz" <?php if(isset($t) && $t->provincia=='Santa Cruz') echo 'selected';  ?>>Santa Cruz</option>
          <option value="Santa Fe" <?php if(isset($t) && $t->provincia=='Santa Fe') echo 'selected';  ?>>Santa Fe</option>
          <option value="Santiago del Estero" <?php if(isset($t) && $t->provincia=='Santiago del Estero') echo 'selected';  ?>>Santiago del Estero</option>
          <option value="Tucum??n" <?php if(isset($t) && $t->provincia=='Tucum??n') echo 'selected';  ?>>Tucum??n</option>
        </select>
      </td>
    </tr>
    <tr>
      <td class="label">
        Ciudad
      </td>
      <td class="intro">
        <input class="text" type="text" name="ciudad" maxchar="100" value="<?php if(isset($t)) echo $t->ciudad;  ?>">
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
function map(){
  global $db;
  //PAGINACION
  $id_usuario=$_SESSION['idusuario'];
  $mostrar = 10;
  if(isset($_GET['page'])){
  $desde = $_GET['page']*$mostrar;
  }else{
  $desde = 0;
  }
  $turnos=$db->get_results("SELECT * from rucas where id_usuario=$id_usuario LIMIT $desde,$mostrar");
  $cantidad = $db->get_var("SELECT count(*)  from rucas where id_usuario=$id_usuario");
?>
<script type="text/javascript">
function confirmar(message){
if (confirm("??SEGURO DESEA BORRAR EL REGISTRO?")){
location.href=message;
return " ";}}
</script>
<div id="encabezado" style="background:#e4c03d; color:#fff">
	<div style="color:#fff">Lista de Rucas</div>

  <a href="<?php echo  rewriteGet('index.php',array('action','page','id'),array('add','','')); ?>"> Agregar Ruca</a>
</div>
<table class="table" width="100%"  cellpadding="10" cellspacing="0">
  <tr class="thead">
    <td>
      Nombre
    </td>
    <td>
      Cupos
    </td>
    <td>
      Provincia
    </td>
    <td>
      Ciudad
    </td>
    <td>
      Direccion
    </td>
    <td align="center">
      Activar/desactivar
    </td>
    <td align="center">
      Editar
    </td>
    <td align="center">
      Borrar
    </td>
  </tr>
  <?php
  foreach ($turnos as $key => $value) {
    ?>
    <tr <?php if(!$value->activo) echo 'class="desactivado"' ?> id="atu<?php echo $value->id ?>">
      <td>
        <?php echo $value->nombre ?>
      </td>
      <td>
        <?php echo $value->cupos ?>
      </td>
      <td>
        <?php echo $value->provincia ?>
      </td>
      <td>
        <?php echo $value->ciudad ?>
      </td>
      <td>
        <?php echo $value->direccion ?>
      </td>
      <td align="center">
          <a href="#" onclick="toggletActive(<?php echo $value->id ?>,this,'links');return false;">
            <?php if($value->activo==1) echo '<i style="color:#063" class="icon-ok-sign"></i>';
                  else  echo '<i style="color:#900" class="icon-remove-sign"></i>'; ?>
            </a>
      </td>
      <td align="center">
        <a href="<?php echo rewriteGet('index.php',array('action','id'),array('edit',$value->id)) ?>">Editar</a>
      </td>
      <td align="center">
        <a href="#" onclick="confirmar('<?php echo rewriteGet('index.php',array('action','id'),array('delete',$value->id)) ?>');return false;">Borrar</a>
      </td>
    </tr>
    <?php
  }
   ?>
</table>
  <?php
  paginacion($cantidad,$mostrar);
}
 ?>
