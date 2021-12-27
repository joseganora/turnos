<?php

  include('./core.php');
  $id=$_GET['id'];
  $check=$_GET['value'];

if(isset($_GET['action'])){
  switch ($_GET['action']) {
    case 'rucas':
      if($check=='true'){
        $check=1;
      }
      if($check=='false'){
        $check=0;
      }
      $db->query("UPDATE inscriptos_rucas set checked=$check where id=$id");
      break;
    case 'delete_rucas':
      $db->query("DELETE FROM inscriptos_rucas where id=$id");
    default:
      // code...
      break;
  }
}else{
  if($check=='true'){
    $check=1;
  }
  if($check=='false'){
    $check=0;
  }
  $db->query("UPDATE turnos_fieles set checked=$check where id=$id");
}


 ?>
