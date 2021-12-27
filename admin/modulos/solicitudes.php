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

  case 'confirmar':
    confirmar();
    break;
  case 'update':
    update();
    break;
  case 'delete':
    delete();
    break;

  }


}
function delete(){
  global $db;
  $id=$_GET['id'];
  $db->query("DELETE FROM `solicitudes` where id=$id");
  ?>
	<script>location.href="<?php echo  rewriteGet('index.php',array('id','action'),array('',''));?>";</script>
	<?php
}
function confirmar(){
  global $db;
  $id=$_GET['id'];
  if($id){
    $turno=$db->get_row("SELECT * FROM solicitudes where confirmado=0 and id=$id");
    if($turno){
      ?>
      <div id="encabezado" style="background:#e4c03d; color:#fff">
      	<div style="color:#fff">Confirmar solicitud</div>

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
  $now=time();

  //creo el usuario
  $idNewUsuario=$db->get_var("SELECT `auto_increment` FROM INFORMATION_SCHEMA.TABLES WHERE table_name = 'usuarios'");
  $db->query("INSERT INTO `usuarios`(`nombre`, `mail`, `telefono`, `password`, `permisos`) VALUES ('$nombre','$mail','$telefono','$password','1')");
  $idNewCapilla=$db->get_var("SELECT `auto_increment` FROM INFORMATION_SCHEMA.TABLES WHERE table_name = 'capillas'");
  $coord=explode(",",$coordenadas);
  if(count($coord)>1){
    $lat=$coord[0];
    $lon=$coord[1];
  }else{
    $lat=0;
    $lon=0;
  }

  //creo la capilla
  $db->query("INSERT INTO `capillas`(`nombre`, `cupos`, `imagen`, `provincia`, `ciudad`, `direccion`, `lat_x`, `lon_y`) VALUES ('$nombre_capilla','$cupos','none','$provincia','$ciudad','$direccion','$lat','$lon')");
  $db->query("UPDATE `solicitudes` set `confirmado`='$now' where id=$id");

  //creo relacion
  $db->query("INSERT INTO `capillas`(id_usuario,id_capilla) VALUES ('$idNewUsuario','$idNewCapilla')");

  $db->query("UPDATE `solicitudes` set `confirmado`='$now' where id=$id");
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
      <td colspan="2">Usuario</td>
    </tr>
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
        <input class="text valid" type="text" name="mail" value="<?php echo $t->mail;  ?>">
      </td>
    </tr>
    <tr>
      <td class="label">
        Teléfono
      </td>
      <td class="intro">
        <input class="text valid" type="text" name="telefono" value="<?php echo $t->telefono;  ?>">
      </td>
    </tr>
    <tr>
      <td class="label">
        Contraseña
      </td>
      <td class="intro">
        <input class="text valid" type="text" name="password" value="<?php echo $t->password;  ?>">
      </td>
    </tr>

    <tr>
      <td colspan="2">Capilla</td>
    </tr>

    <tr>
      <td class="label">
        Nombre
      </td>
      <td class="intro">
        <input class="text valid" type="text" name="nombre_capilla" value="<?php echo $c->nombre_capilla;  ?>">
      </td>
    </tr>

    <tr>
      <td class="label">
        Cupos
      </td>
      <td class="intro">
        <input class="text valid" type="number" name="cupos" value="">
      </td>
    </tr>

    <tr>
      <td class="label">
        Provincia
      </td>
      <td class="intro">
        <input class="text valid" type="text" name="provincia" value="<?php echo $c->provincia;  ?>">
      </td>
    </tr>

    <tr>
      <td class="label">
        Ciudad
      </td>
      <td class="intro">
        <input class="text valid" type="text" name="ciudad" value="<?php echo $c->ciudad;  ?>">
      </td>
    </tr>

    <tr>
      <td class="label">
        Direccion
      </td>
      <td class="intro">
        <input class="text valid" type="text" name="direccion" value="">
      </td>
    </tr>

    <tr>
      <td class="label">
        Coordenadas
      </td>
      <td class="intro">
        <input class="text valid" type="text" name="coordenadas" value="">
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

  if(!isset($_GET['status'])){
    $filtro='confirmado=0';
  }elseif($_GET['status']=='confirmadas'){
    $filtro='confirmado>0';
  }
  ?>
  <script type="text/javascript">
  function confirmar(message){
  if (confirm("¿SEGURO DESEA BORRAR EL REGISTRO?")){
  location.href=message;
  return " ";}}
  </script>
  <div id="encabezado" style="background:#e4c03d; color:#fff">
  	<div style="color:#fff">Lista de Solicitudes</div>
    <a href="#">Solicitudes</a>
  </div>
  <ul id="solapas_estados">

    <li style="border-bottom-color: #c00; border-bottom-style: solid; border-bottom-width:5px;<?php if(!isset($_GET['status'])){ ?> background:#000;<?php } ?>">
    <a href="<?php echo  rewriteGet('index.php',array('status','page'),array('','')); ?>">
    Pendientes
    </a>
    </li>
    <li style="border-bottom-color: #c00; border-bottom-style: solid; border-bottom-width:5px;<?php if($_GET['status']=='confirmadas'){ ?> background:#000;<?php } ?>">
    <a href="<?php echo  rewriteGet('index.php',array('status','page'),array('confirmadas','')); ?>">
    Confirmadas
    </a>
    </li>
  </ul>
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
        Capilla
      </td>
      <td>
        Provincia
      </td>
      <td>
        Ciudad
      </td>
      <td align="center">
        <?php
        if(!isset($_GET['status'])){
          echo 'Agregar';
        }
         ?>

      </td>
      <?php
      if(!isset($_GET['status'])){
        echo '<td align="center">
          Borrar
        </td>';
      }
       ?>

    </tr>
    <?php
    $solicitudes=$db->get_results("SELECT * from solicitudes where $filtro order by timestamp");
    foreach ($solicitudes as $key => $value) {

      ?>
      <tr>
        <td>
          <?php echo date('d-m-Y H:i',$value->timestamp) ?>
        </td>
        <td>
          <?php echo $value->nombre.' '.$value->apellido ?>
        </td>
        <td>
          <a href="mailto:<?php echo $value->mail ?>"><?php echo $value->mail ?></a>
        </td>
        <td>
          <a target="_blank" href="https://wa.me/54<?php echo $value->telefono ?>"><?php echo $value->telefono ?></a>
        </td>
        <td>
          <?php echo $value->nombre_capilla ?>
        </td>
        <td>
          <?php echo $value->provincia ?>
        </td>
        <td>
          <?php echo $value->ciudad ?>
        </td>

        <td align="center">
          <a href="<?php echo rewriteGet('index.php',array('action','id'),array('confirmar',$value->id)) ?>">Inscribir</a>
        </td>

          <?php
          if($value->confirmado==0){
            ?>
            <td align="center">
            <a href="#" onclick="confirmar('<?php echo rewriteGet('index.php',array('action','id'),array('delete',$value->id)) ?>');return false;">Borrar</a>
            </td>
            <?php
          }
           ?>


      </tr>
      <?php
    }
     ?>
  </table>
  <?php
  paginacion($cantidad,$mostrar);
}
 ?>
