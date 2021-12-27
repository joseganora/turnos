<?php



function go(){

if(!isset($_GET['action'])){
  map_tags();
  return ;
}
switch($_GET['action']){
  case '':
    map_tags();
  break;

  case 'map_tags':
    map_tags();
    break;
  case 'edit_tag':
    edit_tag();
    break;
  case 'add_tag':
    add_tag();
    break;
  case 'delete_tag':
    delete_tag();
    break;
  case 'update_tag':
    update_tag();
    break;
  case 'insert_tag':
    insert_tag();
    break;
  default:
    map_tags();
    break;

  }


}


function map_tags(){
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
  <div id="encabezado" style="background:#800000; color:#fff">
  	<div style="color:#fff">Lista de Tags <i class="icon-tags"></i></div>

    <a href="<?php echo  rewriteGet('index.php',array('action','page','id'),array('add_tag','','')); ?>"> <i class="icon-plus-sign"></i> Agregar Tag</a>
    <a href="<?php echo  rewriteGet('index.php',array('modulo','action','page','id'),array('secciones','','','')); ?>"> <i class="icon-arrow-left"></i> Volver</a>
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
        Desde
      </td>
      <td>
        Hasta
      </td>
      <td align="center">
        Nota
      </td>
      <td align="center">
        Bloquear inscripcion
      </td>
      <td align="center">
        Editar
      </td>
      <td align="center">
        Borrar
      </td>
    </tr>
    <?php
    $tags=$db->get_results("SELECT * from nota_secciones where id_seccion=$seccion_select order by timestamp_inicio");
    if($tags)
    foreach ($tags as $key => $value) {
      ?>
      <tr>
        <td>
          <?php echo date('d-m-Y',$value->timestamp_inicio) ?>
        </td>
        <td>
          <?php echo date('d-m-Y',$value->timestamp_fin) ?>
        </td>
        <td align="center">
          <?php echo $value->nota ?>
        </td>
        <td align="center">
          <?php if($value->bloquear) echo 'SI'; else echo "NO"; ?>
        </td>
        <td align="center">
          <a href="<?php echo rewriteGet('index.php',array('action','id'),array('edit_tag',$value->id)); ?>"> <i class="icon-edit"></i></a>
        </td>
        <td align="center">
          <a style="cursor:pointer" onclick="confirmar('<?php echo rewriteGet('index.php',array('action','id'),array('delete_tag',$value->id)); ?>')"> <i class="icon-trash"></i></a>
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
function add_tag(){
  global $db;
    ?>
    <div id="encabezado" style="background:#800000; color:#fff">
    	<div style="color:#fff">Editar Tag</div>

      <a href="<?php echo  rewriteGet('index.php',array('action','page','id'),array('map_tags','','')); ?>"> <i class="icon-arrow-left"></i> Volver</a>
    </div>
    <form onsubmit="return validar(this);"  class="" action="<?php echo  rewriteGet('index.php',array('action','page','id'),array('insert_tag','','')); ?>" method="post">
      <?php formulario_tag(null); ?>
    </form>
    <?php
}
function edit_tag(){
  global $db;
  $id=$_GET['id'];
  $tag=$db->get_row("SELECT * FROM nota_secciones where id=$id");

  if($tag){
    ?>
    <div id="encabezado" style="background:#800000; color:#fff">
    	<div style="color:#fff">Editar Tag</div>

      <a href="<?php echo  rewriteGet('index.php',array('action','page','id'),array('map_tags','','')); ?>"> <i class="icon-arrow-left"></i> Volver</a>
    </div>
    <form onsubmit="return validar(this);"  class="" action="<?php echo  rewriteGet('index.php',array('action','page','id'),array('update_tag','',$tag->id)); ?>" method="post">
      <?php formulario_tag($tag); ?>
    </form>
    <?php
  }else{
    ?>
  	<script>location.href="<?php echo  rewriteGet('index.php',array('action','page','id'),array('','',''));?>";</script>
  	<?php
  }
}
function delete_tag(){
  global $db;
  $id=$_GET['id'];
  $db->query("DELETE FROM nota_secciones where id=$id");
  ?>
	<script>location.href="<?php echo  rewriteGet('index.php',array('id','action'),array('','map_tags'));?>";</script>
	<?php
}
function update_tag(){
  global $db;
	extract($_POST);
  $id=$_GET['id'];
  $arrayFecha=explode('-',$desde);
  $timestamp_inicio=mktime(0,0, 0, $arrayFecha[1], $arrayFecha[2], $arrayFecha[0]);
  $arrayFecha=explode('-',$hasta);
  $timestamp_fin=mktime(23,59,59, $arrayFecha[1], $arrayFecha[2], $arrayFecha[0]);
  $nota=trim($nota,"'\"<>");



	$db->query("UPDATE nota_secciones set id_seccion='$id_seccion' where id=$id");
  $db->query("UPDATE nota_secciones set nota='$nota' where id=$id");
  $db->query("UPDATE nota_secciones set bloquear=$bloquear where id=$id");
  $db->query("UPDATE nota_secciones set timestamp_fin='$timestamp_fin' where id=$id");
  $db->query("UPDATE nota_secciones set timestamp_inicio='$timestamp_inicio' where id=$id");

	?>
	<pre>
		<?php //print_r($_POST); ?>
	</pre>
	<?php
	?>
	<script>location.href="<?php echo  rewriteGet('index.php',array('id','action','seccion'),array('','map_tags',$id_seccion));?>";</script>
	<?php
}
function insert_tag(){
  /*  id_seccion
    nota
    desde
    hasta
    activo*/
  global $db;
	extract($_POST);

  $arrayFecha=explode('-',$desde);
  $timestamp_inicio=mktime(0,0, 0, $arrayFecha[1], $arrayFecha[2], $arrayFecha[0]);
  $arrayFecha=explode('-',$hasta);
  $timestamp_fin=mktime(23,59,59, $arrayFecha[1], $arrayFecha[2], $arrayFecha[0]);
  $nota=trim($nota,"'\"<>");
	$db->query("INSERT INTO nota_secciones(id_seccion, nota, timestamp_inicio, timestamp_fin, bloquear) VALUES ('$id_seccion', '$nota', '$timestamp_inicio', '$timestamp_fin', $bloquear)");
	?>
	<pre>
		<?php // echo print_r($_POST); ?>
	</pre>

	<script>location.href="<?php echo  rewriteGet('index.php',array('id','action','seccion'),array('','map_tags',$id_seccion));?>";</script>
  <?php
}

function formulario_tag($tag){
  global $db;
  $id_ruca=$_GET['status'];
  $id_seccion=$_GET['seccion'];
  if(isset($tag)){
    $id_seccion=$tag->id_seccion;
  }
  ?>
  <table class="tableform" align="center" cellpadding="10" cellspacing="0">
    <tr>
      <td class="label">
        Sección
      </td>
      <td class="intro">
        <select class="" name="id_seccion">
          <?php
          $secciones=$db->get_results("SELECT * from secciones where id_ruca=$id_ruca");
          foreach ($secciones as $key => $value) {
            ?>
            <option value="<?php echo $value->id ?>" <?php if($id_seccion==$value->id) echo "selected"; ?>><?php echo $value->nombre ?></option>
            <?php
          }
           ?>
        </select>
      </td>
    </tr>
    <tr>
      <td class="label">
        Nota
      </td>
      <td class="intro">
        <input type="text" name="nota" value="<?php if(isset($tag)) echo $tag->nota; ?>" placeholder="Escribe lo que quieres que vean los milicianos...">
      </td>
    </tr>
    <tr>
      <td class="label">
        Desde
      </td>
      <td class="intro">
        <input style="width: 130px;" type="date" name="desde" value="<?php if(isset($tag)) echo date('Y-m-d',$tag->timestamp_inicio); ?>">
      </td>
    </tr>
    <tr>
      <td class="label">
        Hasta
      </td>
      <td class="intro">
        <input style="width: 130px;"  type="date" name="hasta" value="<?php if(isset($tag)) echo date('Y-m-d',$tag->timestamp_fin); ?>">
      </td>
    </tr>

    <tr>
      <td class="label">
        Bloquear Inscripción
      </td>
      <td class="intro">
        <select class="" name="bloquear">
          <option value="0" <?php if(isset($tag) && $tag->bloquear==0) echo "selected"; ?>>No</option>
          <option value="1" <?php if(isset($tag) && $tag->bloquear==1) echo "selected"; ?>>Si</option>
        </select>
      </td>
    </tr>

    <tr><td colspan="3"><input type="submit" class="submit" value="ENVIAR DATOS" /></td></tr>
  </table>
  <?php
}

 ?>
