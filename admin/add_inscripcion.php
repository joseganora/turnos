<?php

  include('./core.php');
  extract($_GET);
  $now=time();
  $db->query("INSERT INTO inscriptos_rucas(id_seccion, timestamp, nombre, apellido, nombre_t, apellido_t, telefono, nocontacto, nosintomas, checked) VALUES ('$id_seccion', '$now', '$nombre', '$apellido', '$nombre_t', '$apellido_t', '$telefono', 1, 1, 1)");

 ?>
