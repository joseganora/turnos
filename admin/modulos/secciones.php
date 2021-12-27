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
  case 'desactivar':
    desactivar();
    break;
  case 'activar':
    activar();
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

function map(){
  global $db;
  $id_ruca=$_GET['status'];
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
    ?>
    <script type="text/javascript">
      function confirmar(message){
      if (confirm("¿SEGURO DESEA BORRAR EL REGISTRO?")){
      location.href=message;
      return " ";}}
    </script>
  <div id="encabezado" style="background:#e4c03d; color:#fff">
    <?php
    $week=date('W-Y');
    $inscriptos_semana=$db->get_var("SELECT  count(1) from inscriptos_rucas where id_seccion in (SELECT id from secciones where id_ruca=$id_ruca) and FROM_UNIXTIME(timestamp, '%V-%Y')='$week' ");
     ?>
  	<div style="color:#fff">Lista de secciones. Total semana <?php echo $week ?>: <b><?php echo $inscriptos_semana ?></b>
      <a href="./descargar_excel_ruca.php?semana=<?php echo $week; ?>&ruca=<?php echo $id_ruca ?>" target="_blank"><i class="icon-download-alt"></i></a>
      <a href="./list_online_rucas.php?hcode=<?php echo $ruca->hash_access ?>" target="_blank"><i class="icon-check"></i></a> </div>

    <a href="<?php echo  rewriteGet('index.php',array('modulo','action','page','id'),array('tags','map_tags','','')); ?>"> <i class="icon-tags"></i> Tags</a>
    <a href="<?php echo  rewriteGet('index.php',array('action','page','id'),array('add','','')); ?>"> <i class="icon-plus-sign"></i> Agregar</a>
    <a href="<?php echo  rewriteGet('index.php',array('action','page','id'),array('edit','',$_GET['seccion'])); ?>"> <i class="icon-edit"></i> Editar</a>
    <a onclick="confirmar('<?php echo  rewriteGet('index.php',array('action','page','id'),array('delete','',$_GET['seccion'])); ?>')"> <i class="icon-trash"></i> Borrar</a>
  </div>

  <ul id="solapas_estados">

    <?php
    $secciones=$db->get_results("SELECT * from secciones where id_ruca=$id_ruca order by orden");
    foreach ($secciones as $key => $value) {
        ?>
        <li style="border-bottom-color: #fff; border-bottom-style: solid; border-bottom-width:5px;<?php if((!isset($_GET['seccion']) && $key==0) || $_GET['seccion']==$value->id ){ echo 'background:#000;';$seccion_select=$value->id; } ?>">
        <a href="<?php echo  rewriteGet('index.php',array('seccion'),array($value->id)); ?>">
        <?php echo $value->nombre ?>
        </a>
        </li>
        <?php
    }
    ?>
  </ul>
  <table class="table" width="100%"  cellpadding="10" cellspacing="0">
    <tr class="thead">
      <td>
        Semana
      </td>
      <td>
        Nota
      </td>
      <td align="center">
        Inscriptos
      </td>
      <td align="center">
        Ver Lista
      </td>
    </tr>
    <?php
    $inscriptos_semana=$db->get_results("SELECT FROM_UNIXTIME(timestamp, '%V-%Y') semana,min(timestamp) primero,count(1) cantidad from inscriptos_rucas where id_seccion=$seccion_select group by FROM_UNIXTIME(timestamp, '%V-%Y') LIMIT $desde,$mostrar");
    $cantidad=count($db->get_results("SELECT FROM_UNIXTIME(timestamp, '%V-%Y') semana,min(timestamp) primero,count(1) cantidad from inscriptos_rucas where id_seccion=$seccion_select group by FROM_UNIXTIME(timestamp, '%V-%Y')"));
    if($inscriptos_semana)
    foreach ($inscriptos_semana as $key => $value) {

      ?>
      <tr>
        <td>
          <?php echo $value->semana ?>
        </td>
        <?php
        $nota=$db->get_row("SELECT * from nota_secciones where timestamp_inicio<$value->primero and timestamp_fin>$value->primero and id_seccion=$seccion_select");
        $color='#000';
        if(isset($nota)){
          $color='#f79f00';
         $nota_str= $nota->nota;
        }else{
         if(date('N',$value->primero)<6){
           $next_sat=strtotime("next Saturday",$value->primero);
         }else{
           if(date('N',$value->primero)>6){
             $next_sat=strtotime("last Saturday");
           }else{
             $next_sat=$value->primero;
           }
         }
          $meses = array('','Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre');
          $nota_str='Sábado '.date('j',$next_sat).' de '.$meses[date('n',$next_sat)];
         }
         ?>
        <td style="color:<?php echo $color ?>">
          <?php

            echo $nota_str; ?>
        </td>
        <td align="center">
          <?php
          echo $value->cantidad; ?>
        </td>
        <td align="center">
          <a href="<?php echo rewriteGet('index.php',array('modulo','action','id'),array('inscripciones_rucas','viewList',$value->semana)); ?>"> <i class="icon-list-alt"></i></a>
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
function delete(){
  global $db;
  $id=$_GET['id'];
  if(!isset($id)){
    $id_ruca=$_GET['status'];
    $id=$db->get_var("SELECT id from secciones where id_ruca=$id_ruca order by orden limit 1");
  }
  $count_inscriptos=$db->get_var("SELECT count(*) from inscriptos_rucas where id_seccion=$id");
  if($count_inscriptos==0){
    $db->query("DELETE FROM secciones where id=$id");
    ?>
  	<script>location.href="<?php echo  rewriteGet('index.php',array('id','action','seccion'),array('','',''));?>";</script>
  	<?php
  }else{
    ?>
  	<script>
    var resp=confirm("No podemos borrar esta sección porque tiene inscripciones. ¿Quieres que la desactivemos?");
    if(resp){
      location.href="<?php echo  rewriteGet('index.php',array('id','action'),array($id,'desactivar'));?>";
    }
    </script>
  	<?php
  }
}
function desactivar(){
  global $db;
  $id=$_GET['id'];
  if(!isset($id)){
    $id_ruca=$_GET['status'];
    $id=$db->get_var("SELECT id from secciones where id_ruca=$id_ruca order by orden limit 1");
  }
  $db->query("UPDATE secciones SET activo=0 where id=$id");
}
function activar(){
  global $db;
  $id=$_GET['id'];
  if(!isset($id)){
    $id_ruca=$_GET['status'];
    $id=$db->get_var("SELECT id from secciones where id_ruca=$id_ruca order by orden limit 1");
  }
  $db->query("UPDATE secciones SET activo=1 where id=$id");
}
function edit(){
  global $db;
  $id=$_GET['id'];
  if(!isset($id)){
    $id_ruca=$_GET['status'];
    $seccion=$db->get_row("SELECT * from secciones where id_ruca=$id_ruca order by orden limit 1");
  }else{
    $seccion=$db->get_row("SELECT * FROM secciones where id=$id");
  }
    if($seccion){
      ?>
      <div id="encabezado" style="background:#e4c03d; color:#fff">
      	<div style="color:#fff">Editar Sección</div>

        <a href="<?php echo  rewriteGet('index.php',array('action','page','id'),array('','','')); ?>"> <i class="icon-arrow-left"></i> Volver</a>
      </div>
      <form onsubmit="return validar(this);"  class="" action="<?php echo  rewriteGet('index.php',array('action','page','id'),array('update','',$seccion->id)); ?>" method="post">
        <?php formulario($seccion); ?>
      </form>
      <?php
      die();
    }else{
      header(rewriteGet('index.php',array('action','page','id'),array('','','')));
    }


}
function update(){
	global $db;
	extract($_POST);
  $id=$_GET['id'];

	$db->query("UPDATE secciones set nombre='$nombre' where id=$id");
  $db->query("UPDATE secciones set orden='$orden' where id=$id");
  $db->query("UPDATE secciones set activo='$activo' where id=$id");

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

  $id_ruca=$_GET['status'];
  /*
  [fecha] => 2020-07-30
    [hora] => 19:00*/
  $hash_access=getHash_secciones();



	$db->query("INSERT INTO secciones(hash_access,nombre, orden, activo, id_ruca) VALUES ('$hash_access','$nombre','$orden','$activo','$id_ruca')");
	?>
	<pre>
		<?php // echo print_r($_POST); ?>
	</pre>

	<script>location.href="<?php echo  rewriteGet('index.php',array('id','action'),array('',''));?>";</script>
	<?php
}
function getHash_secciones(){
  global $db;
	$hash_access=substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 10);
	$exist=$db->get_var("SELECT count(*) from secciones where hash_access='$hash_access'");
	if($exist>0){
		$hash_access=getHash_secciones();
	}
	return $hash_access;
}
function add(){
  global $db;
  ?>
  <div id="encabezado" style="background:#e4c03d; color:#fff">
  	<div style="color:#fff"><i class="icon-plus-sign"></i> Agregar Sección</div>

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
        Nombre
      </td>
      <td class="intro">
        <input type="text" name="nombre" value="<?php if(isset($t)) echo $t->nombre; ?>">
      </td>
    </tr>
    <tr>
      <td class="label">
        Orden
      </td>
      <td class="intro">
        <input type="number" name="orden" value="<?php if(isset($t)) echo $t->orden; ?>">
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

 ?>
