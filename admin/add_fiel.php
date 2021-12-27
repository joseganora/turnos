<?php

  include('./core.php');
  $id_turno=$_GET['id_turno'];
  $nombre=$_GET['nombre'];
  $telefono=$_GET['telefono'];
  $mail=$_GET['mail'];

  $db->query("INSERT INTO `fieles`(`nombre`, `telefono`, `mail`, `bloqueado`) VALUES ('$nombre','$telefono','$mail','0')");

  $newFiel=$db->get_var("SELECT max(id) FROM fieles where mail='$mail'");

  $db->query("INSERT INTO `turnos_fieles`(`id_fiel`, `id_turno`, `id_enlace`,cupo_visible,checked) VALUES ('$newFiel','$id_turno','0','1','1')");

 ?>
