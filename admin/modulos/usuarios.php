<?php
/*
error_reporting(E_ALL);
ini_set('display_errors', '1');
*/
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
  $db->query("DELETE FROM `capillas_usuarios` where id_usuario=$id");
  $db->query("DELETE FROM `usuarios` where id=$id");
  ?>
	<script>location.href="<?php echo  rewriteGet('index.php',array('id','action'),array('',''));?>";</script>
	<?php
}
function edit(){
  global $db;
  $id=$_GET['id'];
  if($id){
    $user=$db->get_row("SELECT * FROM usuarios where id=$id");
    if($user){
      ?>
      <div id="encabezado" style="background:#e4c03d; color:#fff">
      	<div style="color:#fff">Editar Usuario - <?php echo $user->nombre ?></div>
        <a href="#" onclick="addCapilla();return false;">Agregar Capilla</a>
        <a href="<?php echo  rewriteGet('index.php',array('action','page','id'),array('','','')); ?>"> Volver</a>
      </div>
      <form onsubmit="return validar(this);"  class="" action="<?php echo  rewriteGet('index.php',array('action','page','id'),array('update','',$id)); ?>" method="post">
        <?php formulario($user); ?>
      </form>
      <?php
      die();
    }

  }
  ?>
	<script>location.href="<?php echo  rewriteGet('index.php',array('id','action'),array('',''));?>";</script>
	<?php

}
function update(){
	global $db;
	extract($_POST);
  $id=$_GET['id'];

  if(!$id_responsable) $id_responsable=0;
	$db->query("UPDATE `usuarios` set `nombre`='$nombre' where id=$id");
  $db->query("UPDATE `usuarios` set `mail`='$mail' where id=$id");
  $db->query("UPDATE `usuarios` set `telefono`='$telefono' where id=$id");
  $db->query("UPDATE `usuarios` set `password`='$password' where id=$id");
  $db->query("UPDATE `usuarios` set `activo`='$activo' where id=$id");
  $db->query("DELETE FROM `capillas_usuarios` where id_usuario=$id");
  foreach ($idCapillas as $key => $value) {
    $db->query("INSERT INTO `capillas_usuarios`(`id_usuario`, `id_capilla`) VALUES ('$id','$value')");
  }

	?>
	<script>location.href="<?php echo  rewriteGet('index.php',array('id','action'),array('',''));?>";</script>
	<?php
}
function insert(){
	global $db;
	extract($_POST);
  $id_usuario=$_SESSION['idusuario'];
  $now=time();

	$db->query("INSERT INTO `usuarios`(`nombre`, `mail`, `telefono`, `password`, `permisos`, `activo`,id_usuario,timestamp) VALUES ('$nombre','$mail','$telefono','$password','1','$activo','$id_usuario','$now')");
  $idNew=$db->get_var("SELECT max(id) from usuarios");
  foreach ($idCapillas as $key => $value) {
    $db->query("INSERT INTO `capillas_usuarios`(`id_usuario`, `id_capilla`) VALUES ('$idNew','$value')");
  }

	?>
  <pre>
		<?php //echo print_r($_POST); ?>
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
  	<div style="color:#fff">Agregar Usuario</div>
    <a href="#" onclick="addCapilla();return false;">Agregar Capilla</a>
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
  <script type="text/javascript">
  function addCapilla(){
    var newTr=$('tr.capilla_class').last().clone();
    newTr.insertAfter($('tr.capilla_class').last());
  }
  function close_tag(tar){
    $(tar).closest('tr').remove();
  }
  </script>
  <table class="tableform" align="center" cellpadding="10" cellspacing="0">
    <tr>
      <td class="label">
        Nombre
      </td>
      <td class="intro">
        <input class="valid" type="text" name="nombre" value="<?php if(isset($t)) echo $t->nombre; ?>">
      </td>
    </tr>
    <tr>
      <td class="label">
        Mail
      </td>
      <td class="intro">
        <input class="valid" type="text" name="mail" value="<?php if(isset($t)) echo $t->mail; ?>">
      </td>
    </tr>
    <tr>
      <td class="label">
        Teléfono
      </td>
      <td class="intro">
        <input class="valid" type="text" name="telefono" value="<?php if(isset($t)) echo $t->telefono; ?>">
      </td>
    </tr>
    <tr>
      <td class="label">
        Password
      </td>
      <td class="intro">
        <input class="valid" type="text" name="password" value="<?php if(isset($t)) echo $t->password; ?>">
      </td>
    </tr>
    <?php
    $id_usuario=$_SESSION['idusuario'];
    if(isset($t)){

      $mis_capillas=$db->get_results("SELECT * from capillas_usuarios where id_usuario=$t->id");
      $capillas=$db->get_results("SELECT * from capillas where id_usuario=$id_usuario order by nombre");
      foreach ($mis_capillas as $k => $v) {
        ?>
        <tr class="capilla_class">
          <td class="label">
            Capilla
          </td>
          <td class="intro">
            <select class="" name="idCapillas[]">
              <option value="" >Elige una capilla</option>
              <?php
              foreach ($capillas as $key => $value) {
               ?>
               <option value="<?php echo $value->id; ?>" <?php if(isset($t) && $v->id_capilla==$value->id) echo "selected"; ?>><?php echo $value->nombre; ?></option>
               <?php
              }
              ?>
            </select>
          </td>

        </tr>
        <?php
      }
    }else{
      ?>
      <tr  class="capilla_class">
        <td class="label">
          Capilla
        </td>
        <td class="intro">
          <select class="" name="idCapillas[]">
            <option value="" >Elige una capilla</option>
            <?php $capillas=$db->get_results("SELECT * from capillas where id_usuario=$id_usuario order by nombre");
            foreach ($capillas as $key => $value) {
             ?>
             <option value="<?php echo $value->id; ?>"><?php echo $value->nombre; ?></option>
             <?php
            }
            ?>
          </select>
        </td>

      </tr>
      <?php
    }
     ?>
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
  $id_usuario=$_SESSION['idusuario'];
  $turnos=$db->get_results("SELECT * from usuarios where id_usuario=$id_usuario LIMIT $desde,$mostrar");
  $cantidad = $db->get_var("SELECT count(*) from usuarios where id_usuario=$id_usuario");
  ?>
  <script type="text/javascript">
    function confirmar(message){
    if (confirm("¿SEGURO DESEA BORRAR EL REGISTRO?")){
    location.href=message;
    return " ";}}
  </script>
  <div id="encabezado" style="background:#e4c03d; color:#fff">
  	<div style="color:#fff">Lista de Usuarios</div>

    <a href="<?php echo  rewriteGet('index.php',array('action','page','id'),array('add','','')); ?>"> Agregar Usuario</a>
  </div>
<table class="table" width="100%"  cellpadding="10" cellspacing="0">
  <tr class="thead">
    <td>
      Nombre
    </td>
    <td>
      Mail
    </td>
    <td>
      Telefono
    </td>
    <td>
      Capillas
    </td>
    <td align="center">
      Activar/Descativar
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
    <tr id="atu<?php echo $value->id ?>"  <?php if(!$value->activo) echo 'class="desactivado"' ?> >
      <td>
        <?php echo $value->nombre ?>
      </td>
      <td>
        <?php echo $value->mail ?>
      </td>
      <td>
        <?php if($value->telefono) echo '<a target="_blank" href="https://wa.me/549'.$value->telefono.'">'.$value->telefono.'</a>'; else echo '-'; ?>
      </td>
      <td>
        <?php
        $capillas=$db->get_results("SELECT c.* from capillas_usuarios cu join capillas c on cu.id_capilla=c.id where cu.id_usuario=$value->id order by nombre");
        foreach ($capillas as $k => $v) {
          echo $v->nombre.'<br>';
        }
        ?>
      </td>

      <td align="center">
        <a href="#" onclick="toggletActive(<?php echo $value->id ?>,this,'usuarios');return false;">
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
