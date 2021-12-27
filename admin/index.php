<?php

session_start();
if(!$_SESSION['idusuario']){
header('location:login.php');
}
//error_reporting(E_ALL ^ E_NOTICE);

include('core.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php head(); ?>
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
</head>

<body>


<div id="header">
<div id="userbox">
<?php echo  $_SESSION['nombre']?> &nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp; <a href="login.php?f=quit">Abandonar Sesión &nbsp;<i class="icon-remove closeme"></i></a>
</div>

<?php
menu();
?>
</div>



<div id="contenidos">
<?php
contenidos();
?>
</div>

</body>
</html>
