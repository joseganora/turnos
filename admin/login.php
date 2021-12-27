<?php
// Motrar todos los errores de PHP
error_reporting(-1);


// Motrar todos los errores de PHP
error_reporting(E_ALL);

// Motrar todos los errores de PHP
ini_set('error_reporting', E_ALL);

include('core.php');
session_start();
if($_GET['f']=='quit'){
session_destroy();
header('location:login.php');
}
extract($_POST);
if($user && $password){
//$user=	mysqli_real_escape_string($user);
//$password=	mysqli_real_escape_string($password);
$u = $db->get_row("SELECT * from usuarios where mail = '$user' and password = '$password'");
	if($u){
		if($u->activo){
			$_SESSION['idusuario']=$u->id;
			$_SESSION['permisos']=$u->permisos;
			$_SESSION['mail']=$u->mail;

			if($_SESSION['hcode']){
				$code=$_SESSION['hcode'];
				if(isset($_SESSION['ruca'])){
					unset($_SESSION['ruca']);
					unset($_SESSION['hcode']);
					header('location:list_online_rucas.php?hcode='.$code);
				}else{
					header('location:list_online.php?hcode='.$code);
				}

			}else{
				header('location:index.php');
			}

		}else{
			?>
			<script type="text/javascript">
				alert("¡Usuario Inactivo!");
			</script>
			<?php
		}


	}else{
		?>
		<script type="text/javascript">
			alert("¡Usuario o contraseña incorrectos!");
		</script>
		<?php
	}
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php head(); ?>

</head>

<body>
<form id="login_form" method="post" action="login.php" >
	<table style="margin:auto;margin-top:20px">
		<tr>
			<th colspan="2" style="padding-bottom:20px">
				Ingreso administrador
			</th>
		</tr>
		<tr>
			<td>
				Email:
			</td>
			<td>
				<input name="user" type="text">
			</td>
		</tr>
		<tr>
			<td>
				Contraseña:
			</td>
			<td>
				<input name="password" type="password">
			</td>
		</tr>
		<tr>
			<td colspan="2" align="center">
				<input type="submit" value="Enviar" class="go" />
			</td>
		</tr>
	</table>

</form>
</body>
</html>
