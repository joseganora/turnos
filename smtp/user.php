<?

/*---------SMTP INCLUDES----------*/

require_once('./smtp/class.phpmailer.php');
include("./smtp/class.smtp.php");



function notify($html,$email,$nombre,$cuenta){

    $mail = new PHPMailer();  // create a new object
    $mail->IsSMTP(); // enable SMTP
	   $mail->IsHTML(true);
    $mail->SMTPDebug = 0;  // debugging: 1 = errors and messages, 2 = messages only
    $mail->SMTPAuth = true;  // authentication enabled
	   $mail->CharSet="UTF-8";
    $mail->SMTPSecure = 'tls'; // secure transfer enabled REQUIRED for Gmail
    $mail->Host = 'smtp.gmail.com';
    $mail->Port = 587;


    $mail->Username = $cuentas[$cuenta];
    $mail->Password = $pass[$cuenta];
    $mail->SetFrom($cuentas[$cuenta], "Turnos Misa");

    $mail->Subject = "Notificación Turnos Misa " .date('d/m/Y',time());
    $mail->Body = $html;
    $mail->AddAddress($email,$nombre);
    $mail->Send();
}

function notify_custom($html,$email,$nombre,$subject,$cuenta){
    $cuentas=array('turnosmisa@gmail.com','turnosmisa2@gmail.com','turnosmisa3@gmail.com','turnosmisa4@gmail.com');
    $pass=array('','','','');


    $mail = new PHPMailer();  // create a new object
    $mail->IsSMTP(); // enable SMTP
    $mail->IsHTML(true);
    $mail->SMTPDebug = 0;  // debugging: 1 = errors and messages, 2 = messages only
    $mail->SMTPAuth = true;  // authentication enabled
	  $mail->CharSet="UTF-8";
    $mail->SMTPSecure = 'tls'; // secure transfer enabled REQUIRED for Gmail
    $mail->Host = 'smtp.gmail.com';
    $mail->Port = 587;


    $mail->Username = $cuentas[$cuenta];
    $mail->Password = $pass[$cuenta];
    $mail->SetFrom($cuentas[$cuenta], "Turnos Misa");

    $mail->Subject = $subject;
    $mail->Body = $html;
    $mail->AddAddress($email,$nombre);
    $mail->Send();
}




?>
