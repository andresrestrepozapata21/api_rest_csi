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
    $mensaje = file_get_contents("https://apicsi.mipgenlinea.com/controllers/correos/template_correo_recordatorio.html");
    $mensaje = str_replace("%nombre%", $nombre, $mensaje);
    $mensaje = str_replace("%apellido%", $apellido, $mensaje);
    $mensaje = str_replace("%cedula%", $cedula, $mensaje);

    //PHP Mailer
    $mail = new PHPMailer;
    $mail->isSMTP();
    $mail->SMTPDebug = SMTP::DEBUG_OFF;
    $mail->Host = 'mail.mipgenlinea.com';
    $mail->Port = 587;
    $mail->SMTPSecure = 'tls';
    $mail->SMTPAuth = true;
    $mail->Username = "alertas@mipgenlinea.com";
    $mail->Password = "Juryzu57!";
    $mail->CharSet = 'UTF-8';
    $mail->setFrom('alertas@mipgenlinea.com', 'Alertas CSI');
    $mail->addReplyTo('alertas@mipgenlinea.com', 'Alertas CSI');
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
