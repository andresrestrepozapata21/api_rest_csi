<?php
//include("conexion.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

require 'src/PHPMailer.php';
require 'src/Exception.php';
require 'src/SMTP.php';

$html = "AQUI EL CONTENIDO DEL MENSAJE";


function enviar_correo_confirmacion($destinatario1, $nombre, $apellido, $cedula)
{
    //HTML del email
    $mensaje = file_get_contents("https://apicsi.csisecurity.co/controllers/correos/template_correo_recordatorio.html");
    $mensaje = str_replace("%nombre%", $nombre, $mensaje);
    $mensaje = str_replace("%apellido%", $apellido, $mensaje);
    $mensaje = str_replace("%cedula%", $cedula, $mensaje);

    //PHP Mailer
    $mail = new PHPMailer;
    $mail->isSMTP();
    $mail->SMTPDebug = SMTP::DEBUG_OFF;
    $mail->Host = '167.114.11.22';
    $mail->Port = 587;
    $mail->SMTPSecure = 'TLS';
    $mail->SMTPAuth = true;
    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );
    $mail->Username = "alertas@csisecurity.co";
    $mail->Password = "R&1e2;g&7r=b";
    $mail->CharSet = 'UTF-8';
    $mail->setFrom('alertas@csisecurity.co', 'Administrador CSI');
    $mail->addReplyTo('alertas@csisecurity.co', 'Administrador CSI');
    $mail->addAddress($destinatario1, 'Envío de Correo');
    $mail->Subject = $nombre . 'Notificaciones CSI';
    $mail->msgHTML($mensaje, __DIR__);
    $mail->AltBody = $nombre . ' Seguridad CSI';;
    //$mail->addAttachment('images/empleado.png');
    //$mail->addAttachment('images/independiente.png');
    //$mail->addAttachment('images/rentista.png');

    if (!$mail->send()) {
        return "Mailer Error: " . $mail->ErrorInfo;
    } else {
        return "Message sent!";
    }
}
