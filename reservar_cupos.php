<?php


$hash=$_POST['hash'];

if(!isset($hash)){
  $arr = array('estado'=>0,'error_id' => 126, 'text' => 'No hay hash');
  echo json_encode($arr);
  die();
}
include('./connect.php');
$enlace=$db->get_row("SELECT * from enlaces where hash='$hash'");
if(!isset($enlace)){
  $arr = array('estado'=>0,'error_id' => 127, 'text' => 'Hash incorrecto');
  echo json_encode($arr);
  die();
}
$now30=time()-1800;
if($enlace->timestamp<$now30){
  $arr = array('estado'=>0,'error_id' => 128, 'text' => 'Fuera de tiempo. Tienes 30 minutos para inscribirte luego de solicitar los cupos');
  echo json_encode($arr);
  die();
}
if($enlace->usado=='1'){
  $arr = array('estado'=>0,'error_id' => 129, 'text' => 'Link utilizado anteriormente.');
  echo json_encode($arr);
  die();
}
$time_turno=$db->get_var("SELECT timestamp from turnos where id=$enlace->id_turno");
$now_2horas=time()+3600;
if($now_2horas>$time_turno){
  $arr = array('estado'=>0,'error_id' => 129, 'text' => 'No puedes inscribirte. Solo podes guardar cupos dos horas antes de la misa');
  echo json_encode($arr);
  die();
}
//reviso cupos
$turno=$db->get_row("SELECT * from turnos where id=$enlace->id_turno");
$capilla=$db->get_row("SELECT * from capillas where id=$turno->id_capilla");
$cuposLibres=$db->get_var("SELECT (SELECT cupos from capillas where id=$capilla->id)-count(*) from turnos_fieles where id_turno=$turno->id and cupo_visible=1");
if(count($_POST['nombres'])>$cuposLibres){
  if($cuposLibres<1){
    $db->query("UPDATE `enlaces` set usado='1' where hash='$hash'");
    $arr = array('estado'=>0,'error_id' => 131, 'text' => 'No hay más cupos para esta misa.');
    echo json_encode($arr);
  }else{
    $s='';
    if($cuposLibres>1) $s='s';
    $arr = array('estado'=>0,'error_id' => 130, 'text' => 'Exceso de cupos. Solo hay '.$cuposLibres.' cupo'.$s.' libre'.$s.'.');
    echo json_encode($arr);
  }

  die();
}


$arrayId=array();
for ($i=0; $i < $enlace->cantidad; $i++) {
  if(isset($_POST['nombres']) && strlen($_POST['nombres'][$i])>2){
    $nombre=$_POST['nombres'][$i];
    $nombre=strtolower($nombre);
    $nombre=ucwords($nombre);
    $mail=strtolower($_POST['emails'][$i]);
    $tel=$_POST['telefonos'][$i];
    $db->query("INSERT INTO `fieles`( `nombre`, `telefono`, `mail`, `bloqueado`) VALUES ('$nombre','$tel','$mail','0')");
    $newId=$db->get_var("SELECT max(id) from fieles where mail='$mail'");
    array_push($arrayId,$newId);
  }
}
foreach ($arrayId as $key => $value) {
  $db->query("INSERT INTO `turnos_fieles`( `id_fiel`, `id_turno`,`id_enlace`) VALUES ('$value','$enlace->id_turno','$enlace->id')");
}

$db->query("UPDATE `enlaces` set usado='1' where hash='$hash'");

$arr = array('estado'=>1,'error_id' => 0, 'text' => 'Éxito.');
echo json_encode($arr);
