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
  $db->query("DELETE FROM `links` where id=$id");
  ?>
	<script>location.href="<?php echo  rewriteGet('index.php',array('id','action'),array('',''));?>";</script>
	<?php
}
function edit(){
  global $db;
  $id=$_GET['id'];
  if($id){
    $turno=$db->get_row("SELECT * FROM links where id=$id");
    if($turno){
      ?>
      <div id="encabezado" style="background:#e4c03d; color:#fff">
      	<div style="color:#fff">Editar Link</div>

        <a href="<?php echo  rewriteGet('index.php',array('action','page','id'),array('','','')); ?>"> Volver</a>
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

	$db->query("UPDATE `links` set `href`='$href where id=$id");
  $db->query("UPDATE `links` set `nombre`='$nombre' where id=$id");
  $db->query("UPDATE `links` set `activo`='$activo' where id=$id");

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

	$db->query("INSERT INTO `links`(`href`, `nombre`, `activo`) VALUES ('$href','$nombre','$activo')");

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
  	<div style="color:#fff">Agregar Link</div>

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
        Enlace
      </td>
      <td class="intro">
        <input class="text" type="text" name="href" value="<?php if(isset($t)) echo $t->href;  ?>">
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
  $mostrar = 10;
  if(isset($_GET['page'])){
  $desde = $_GET['page']*$mostrar;
  }else{
  $desde = 0;
  }
  $turnos=$db->get_results("SELECT * from links LIMIT $desde,$mostrar");
  $cantidad = $db->get_var("SELECT count(*) from links");
?>
<script type="text/javascript">
function confirmar(message){
if (confirm("¿SEGURO DESEA BORRAR EL REGISTRO?")){
location.href=message;
return " ";}}
</script>
<div id="encabezado" style="background:#e4c03d; color:#fff">
	<div style="color:#fff">Lista de Links</div>

  <a href="<?php echo  rewriteGet('index.php',array('action','page','id'),array('add','','')); ?>"> Agregar Link</a>
</div>
<table class="table" width="100%"  cellpadding="10" cellspacing="0">
  <tr class="thead">
    <td>
      Nombre
    </td>
    <td>
      Enlace
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
        <?php echo $value->href ?>
      </td>
      <td align="center">
          <a href="#" onclick="toggletActive(<?php echo $value->id ?>,this,'links');return false;">
            <?php if($value->activo==1) echo "Desactivar";
                  else  echo "Activar"; ?>
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
