<?php
session_start();

include('core.php');



if(isset($_SESSION['idusuario'])){
  $id=$_GET['id'];
  $table=$_GET['table'];
  $active=$db->get_var("SELECT activo FROM $table where id='$id'");
  if($active=='1'){
    //desactivar
    $db->query("UPDATE $table SET activo=0 where id='$id'");
    $arr = array('estado'=> 1,'action' => 'disactive','val_ant'=>$active);
  }else{
    //Activar
    $db->query("UPDATE $table SET activo=1 where id='$id'");
    $arr = array('estado'=> 1,'action' => 'active','val_ant'=>$active);
  }
}else{
  $arr = array('estado'=>0,'error_id' => 25, 'error_text' => 'Error de autentificación de usuario.');
}
echo json_encode($arr);

 ?>
