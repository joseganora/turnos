<?php
/*---------EZSQL---------*/
// Motrar todos los errores de PHP
error_reporting(1);
// Motrar todos los errores de PHP
error_reporting(E_ALL);
// Motrar todos los errores de PHP
ini_set('error_reporting', E_ALL);

function s($variable) {
$s = Array();
$s['dbhost'] = "localhost"; // MySQL host
$s['dbname'] = "c1890273_cbaf"; // Database name
$s['dbuname'] = "c1890273_cbaf"; // Database Username
$s['dbpass'] = "49konuseZE";// Database password
return $s[$variable];
}

include_once "ezsql/ez_sql_core.php";
include_once "ezsql/ez_sql_mysqli.php";

$db = new ezSQL_mysqli(s('dbuname'),s('dbpass'),s('dbname'),s('dbhost'));
$db->query("SET NAMES 'utf8'");


/*---------EZSQL---------*/

/*--------------------------FUNCIONES GENERALES---------------------------*/




function dame($tabla,$id,$valor){
  global $db;
  $d = $db->get_row("SELECT * FROM ".$tabla." where id = '$id' ORDER by id");
  return $d->$valor;
}

function calcEdad($timestamp){
  $valor=(time()-$timestamp)/60/60/24/360;
  return floor($valor);
}








function colorToModule($num){

$paleta = array(
'cc0000','009900','0066cc','ff6600','666600','20977f','915aab','ab8d5a','1BC2EB','D31BEB'
);

return $paleta[$num%10];

}







function paginacion($cantidad,$paginacion){
?>
<div class="pager">
<?php
$numeropaginas = ceil($cantidad / $paginacion);
if($_GET['page']!=''){$page = $_GET['page'] ;}else{$page = 0 ;}
?>
<div class="info"><?php echo $cantidad ;?> Elementos encontrados en <?php echo $numeropaginas ;?> página/s.</div>

<?php if($page > 0){?>
<a href="<?php echo rewriteGet('index.php',array('page'),array($page - 1)); ?>">Anterior</a>
<?php } ?>

<?php

for($i = 1; $i <= $numeropaginas; $i++){
//dibujar cada pagina..
if($i-1 == $page){
?>
<div class="current"><?php echo $i; ?></div>
<?php
}else{
?>
<a href="<?php echo rewriteGet('index.php',array('page'),array($i -1) ); ?>"><?php echo $i ?></a>
<?php
}
}
?>

<?php if($page < $numeropaginas -1){?>
<a href="<?php echo rewriteGet('index.php',array('page'),array($page + 1)); ?>">Siguiente</a>
<?php } ?>

</div>
<?php
}









/*------SUPER GET-----*/
function rewriteGet($destino,$arraykeys,$arrayvalues){
	$stringget = $destino.'?';
	$oldgetarray = array();
	foreach($_GET as $clave => $valor){
		$oldgetarray[]=$clave;
		//si la clave existe en el arraykeys
		if(in_array($clave,$arraykeys)){
			//compruebo que exista un valor para esa clave
		if($arrayvalues[array_search($clave,$arraykeys)]!=''){
				$stringget .= $clave.'='.$arrayvalues[array_search($clave,$arraykeys)].'&';
		}
		}else{
			$stringget .= $clave.'='.$valor.'&';
		}
  }
  //sumo claves y valores nuevos que no estaban en el anterior.
	foreach($arraykeys as $k => $v){
		if(!in_array($v,$oldgetarray)){
			//siempre y cuando exista un valor
			if($arrayvalues[$k]){
			$stringget .= $v.'='.$arrayvalues[$k].'&';
			}
			}
	}
	return substr($stringget,0,strlen($stringget)-1);
}
function contenidos(){
  if(isset($_GET['modulo'])){
    $modulo=strtolower($_GET['modulo']);
    include("modulos/$modulo.php");
		go();
  }
}
function menu(){
  global $db;
  ?>
  <div class="level">
    <?php
    if($_SESSION['permisos']==0){
      ?>
      <a class="btn_bottom <?php if($_GET['modulo']=='solicitudes') echo 'select' ?>" href="<?php echo rewriteGet('index.php',array('modulo','status','page','id','action'),array('solicitudes','','','','')) ?>">Solicitudes</a>
      <a class="btn_bottom <?php if($_GET['modulo']=='capillas') echo 'select' ?>" href="<?php echo rewriteGet('index.php',array('modulo','status','page','id','action'),array('capillas','','','','')) ?>">Capillas</a>
      <a class="btn_bottom <?php if($_GET['modulo']=='usuarios') echo 'select' ?>" href="<?php echo rewriteGet('index.php',array('modulo','status','page','id','action'),array('usuarios','','','','')) ?>">Usuarios</a>
      <a class="btn_bottom <?php if($_GET['modulo']=='rucas') echo 'select' ?>" href="<?php echo rewriteGet('index.php',array('modulo','status','page','id','action'),array('rucas','','','','')) ?>">Rucas</a>
      <?php
    }
    if($_SESSION['permisos']==1){
      $id=$_SESSION['idusuario'];
      $relaciones=$db->get_results("SELECT c.* FROM capillas_usuarios uc join capillas c on uc.id_capilla=c.id  where uc.id_usuario=$id");
      foreach ($relaciones as $key => $value) {
        ?>
        <a class="btn_bottom <?php if($_GET['modulo']=='turnos' && $_GET['status']==$value->id) echo 'select' ?>" href="<?php echo rewriteGet('index.php',array('modulo','status','page','id','action'),array('turnos',$value->id,'','','')) ?>">
          Turnos <b><?php echo $value->nombre ?></b>
        </a>
        <?php
      }
      //rucas
      $relaciones=$db->get_results("SELECT c.* FROM rucas_usuarios uc join rucas c on uc.id_ruca=c.id  where uc.id_usuario=$id");
      foreach ($relaciones as $key => $value) {
        ?>
        <a class="btn_bottom <?php if($_GET['modulo']=='secciones' && $_GET['status']==$value->id) echo 'select' ?>" href="<?php echo rewriteGet('index.php',array('modulo','status','page','id','action','seccion'),array('secciones',$value->id,'','','','')) ?>">
          <b><?php echo $value->nombre ?></b>
        </a>
        <?php
      }
    }
     ?>
  </div>
  <?php
}



function head(){
?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script src="https://code.jquery.com/jquery-1.10.1.min.js"></script>
<link href='https://fonts.googleapis.com/css?family=Varela' rel='stylesheet' type='text/css'>
<link href="css/styles.css" rel='stylesheet' type='text/css' />
<script src="js/functions.js"></script>
<title>Turnos de misa</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
<link href="//netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.css" rel="stylesheet">


<?php
}

function getDias(){
  return array("","Lunes","Martes","Miércoles","Jueves","Viernes","Sábado","Domingo");
}
 ?>
