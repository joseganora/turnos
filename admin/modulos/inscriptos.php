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
  case 'editar':
    editar();
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
  $id_turno=$_GET['id_turno'];
  $db->query("DELETE FROM `adoradores` where id=$id");
  $db->query("DELETE FROM `adoradores_turnos` where id_adorador=$id and id_turno=$id_turno");
  ?>
	<script>location.href="<?php echo  rewriteGet('index.php',array('id','id_turno','action'),array('','',''));?>";</script>
	<?php
}
function add(){
  global $db;
  ?>
  <div id="encabezado" style="background:#e4c03d; color:#fff">
  	<div style="color:#fff">Agregar Adorador</div>
    <a href="<?php echo  rewriteGet('index.php',array('action','page','id'),array('','','')); ?>"> Volver</a>
  </div>
  <form onsubmit="return validar(this);"  class="" action="<?php echo  rewriteGet('index.php',array('action','page'),array('insert','')); ?>" method="post">
    <?php formulario($turno); ?>
  </form>
  <?php
}
function insert(){
	global $db;
	extract($_POST);
  $now=time();

  $idNewUsuario=$db->get_var("SELECT `auto_increment` FROM INFORMATION_SCHEMA.TABLES WHERE table_name = 'adoradores'");
  $db->query("INSERT INTO `adoradores`( `nombre`, `telefono`, `mail`, `id_grupo`,id_solicitud, `timestamp`) VALUES ('$nombre','$telefono','$mail','$id_grupo','0','$now')");
  $db->query("INSERT INTO `adoradores_turnos`( `id_adorador`, `id_turno`, `timestamp`) VALUES ('$idNewUsuario','$id_turno','$now')");

	?>
	<pre>
		<?php //print_r($_POST); ?>
	</pre>
	<?php
	?>
	<script>location.href="<?php echo  rewriteGet('index.php',array('id','action'),array('',''));?>";</script>
	<?php
}
function editar(){
  global $db;
  $id=$_GET['id'];

  if($id){
    $insc=$db->get_row("SELECT a.*, atu.id_turno FROM adoradores_turnos atu  join adoradores a on a.id=atu.id_adorador where atu.id=$id");

    if($insc){
      ?>
      <div id="encabezado" style="background:#e4c03d; color:#fff">
      	<div style="color:#fff">Editar Inscripción</div>

        <a href="<?php echo rewriteGet('index.php',array('action','page','id'),array('','','')); ?>"> Volver</a>
      </div>
      <form onsubmit="return validar(this);"  class="" action="<?php echo  rewriteGet('index.php',array('action','page','id'),array('update','',$id)); ?>" method="post">
        <?php formulario($insc); ?>
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
  $now=time();

  $insc=$db->get_row("SELECT * FROM adoradores_turnos where id=$id");

  $db->query("UPDATE `adoradores_turnos` set `id_turno`='$id_turno' where id=$id");
  $db->query("UPDATE `adoradores_turnos` set `timestamp`='$now' where id=$id");

  $db->query("UPDATE `adoradores` set `nombre`='$nombre' where id=$insc->id_adorador");
  $db->query("UPDATE `adoradores` set `telefono`='$telefono' where id=$insc->id_adorador");
  $db->query("UPDATE `adoradores` set `mail`='$mail' where id=$insc->id_adorador");
  $db->query("UPDATE `adoradores` set `id_grupo`='$id_grupo' where id=$insc->id_adorador");
  $db->query("UPDATE `adoradores` set `timestamp`='$now' where id=$insc->id_adorador");




	?>
	<pre>
		<?php //print_r($_POST); ?>
	</pre>
	<?php
	?>
	<script>location.href="<?php echo  rewriteGet('index.php',array('id','action'),array('',''));?>";</script>
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
        <input class="text valid" type="text" name="nombre" value="<?php echo $t->nombre;  ?>">
      </td>
    </tr>
    <tr>
      <td class="label">
        Mail
      </td>
      <td class="intro">
        <input class="text" type="text" name="mail" value="<?php echo $t->mail;  ?>">
      </td>
    </tr>
    <tr>
      <td class="label">
        Telefono
      </td>
      <td class="intro">
        <input class="text valid" type="text" name="telefono" value="<?php echo $t->telefono;  ?>">
      </td>
    </tr>
    <tr>
      <td class="label">
        Grupo
      </td>
      <td class="intro">
        <?php
        $grupos=$db->get_results("SELECT * from grupos where activo=1");
         ?>
        <select class="valid" name="id_grupo">
          <option value="">Elige un grupo</option>
          <?php
          foreach ($grupos as $key => $value) {
            ?>
            <option value="<?php echo $value->id ?>" <?php if($t->id_grupo==$value->id) echo 'selected'; ?>><?php echo $value->nombre ?></option>
            <?php
          }
           ?>
           <option value="0">Otro</option>
        </select>
      </td>
    </tr>
    <tr>
      <td class="label">
        Turno
      </td>
      <td class="intro">
        <?php
        $turnos=$db->get_results("SELECT * from turnos where activo=1");
        $dias=getDias();
         ?>
        <select class="valid" name="id_turno">
          <option value="">Elige un turno</option>
          <?php
          $band=true;
          foreach ($turnos as $key => $value) {
            ?>
            <option value="<?php echo $value->id ?>"  <?php if(isset($t)){ if($t->id_turno==$value->id) echo 'selected'; }else{ if(isset($_GET['status'])){ if($_GET['status']==$value->id) echo 'selected'; }else{
              if($_GET['dia']==$value->dia && $band){ echo 'selected';$band=false; }
            } } ?>>
              <?php echo $dias[$value->dia].' '.$value->hora ?>
            </option>
            <?php
          }
           ?>
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
  $dias=getDias();



?>
<script type="text/javascript">
function confirmar(message){
if (confirm("¿SEGURO DESEA BORRAR EL REGISTRO?")){
location.href=message;
return " ";}}
function selectDia(tar){
  var dia=$(tar).val();
  location.href="<?php echo rewriteGet('index.php',array(),array()); ?>"+"&dia="+dia;
}
</script>
<div id="encabezado" style="background:#e4c03d; color:#fff">
	<div style="color:#fff">Lista de Inscriptos</div>
  <a href="#">Dias: </a>
  <?php
  $dias_db=$db->get_results("SELECT dia from turnos where activo=1 group by dia order by dia");
  $dia_select=$dias_db[0]->dia;
  foreach ($dias_db as $key => $value) {
    ?>
    <select onchange="selectDia(this)" name="">
      <option value="<?php echo $value->dia ?>" <?php if($value->dia==$_GET['dia']){ echo 'selected';$dia_select=$value->dia; }  ?>><?php echo $dias[$value->dia]; ?></option>
    </select>
    <?php
  }
   ?>
   <a href="<?php echo  rewriteGet('index.php',array('action'),array('add')); ?>">Agregar adorador</a>

</div>
<ul id="solapas_estados">

  <?php
  $horas_db=$db->get_results("SELECT id,hora from turnos where dia='$dia_select' and activo=1 order by orden");
  foreach ($horas_db as $key => $value) {
    ?>
    <li style="border-bottom-color: #c00; border-bottom-style: solid; border-bottom-width:5px;<?php if((!isset($_GET['status']) && $key==0) || $_GET['status']==$value->id ){ echo 'background:#000;';$turno_select=$value->id; } ?>">
    <a href="<?php echo  rewriteGet('index.php',array('status','page'),array($value->id,'')); ?>">
    <?php echo $value->hora ?>
    </a>
    </li>
    <?php
  }
  echo "</ul>";
  $turnos=$db->get_results("SELECT a.*, g.nombre grupo, atu.id_turno,atu.activo, atu.id id_atu
    from adoradores a join adoradores_turnos atu on a.id=atu.id_adorador left join grupos g on a.id_grupo=g.id
     where atu.id_turno=$turno_select
    order by timestamp
    LIMIT $desde,$mostrar");
  $cantidad = $db->get_var("SELECT count(*) from adoradores a join grupos g on a.id_grupo=g.id join adoradores_turnos at on a.id=at.id_adorador
   where at.id_turno=$turno_select");
  ?>
<table class="table" width="100%"  cellpadding="10" cellspacing="0">
  <tr class="thead">
    <td>
      Time
    </td>
    <td>
      Nombre
    </td>
    <td>
      Mail
    </td>
    <td>
      Teléfono
    </td>
    <td>
      Grupo
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
    <tr id="atu<?php echo $value->id_atu ?>" class="<?php if($value->activo==0) echo "desactivado"; ?>">
      <td>
        <?php echo date('d-m-Y H:i',$value->timestamp) ?>
      </td>
      <td>
        <?php echo $value->nombre ?>
      </td>
      <td>
        <?php echo $value->mail ?>
      </td>
      <td>
        <?php echo $value->telefono ?>
      </td>
      <td>
        <?php if($value->grupo) echo $value->grupo;
              else echo "Otro"; ?>
      </td>
      <td align="center">
          <a href="#" onclick="toggletActive(<?php echo $value->id_atu ?>,this,'adoradores_turnos');return false;">
            <?php if($value->activo==1) echo "Desactivar";
                  else  echo "Activar"; ?>
            </a>
      </td>
      <td align="center">
          <a href="<?php echo rewriteGet('index.php',array('action','id'),array('editar',$value->id_atu)) ?>">Editar</a>
      </td>
      <td align="center">
        <a href="#" onclick="confirmar('<?php echo rewriteGet('index.php',array('action','id','id_turno'),array('delete',$value->id,$value->id_turno)) ?>');return false;">Borrar</a>
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
