<?php
//include("conexion.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

require 'src/PHPMailer.php';
require 'src/Exception.php';
require 'src/SMTP.php';

$html = "AQUI EL CONTENIDO DEL MENSAJE";


function enviar_correo_confirmacion($destinatario1, $nombre, $servicio)
{
    //HTML del email
    $mensaje = file_get_contents("https://pruebas.mipgenlinea.com/controllers/correos/template_correo_recordatorio.html");
    $mensaje = str_replace("%nombre%", $nombre, $mensaje);
    $mensaje = str_replace("%servicio%", $servicio, $mensaje);

    $correo1 = $destinatario1;

    //PHP Mailer
    $mail = new PHPMailer;
    $mail->isSMTP();
    $mail->SMTPDebug = SMTP::DEBUG_OFF;
    $mail->Host = 'email-smtp.us-east-2.amazonaws.com';
    $mail->Port = 587;
    $mail->SMTPSecure = 'tls';
    $mail->SMTPAuth = true;
    $mail->Username = "AKIAVHKQVZ4UQMHKXDHZ";
    $mail->Password = "BIdvprThst2JBj9orUTftvHoVxMj3kIiJ6eKB7jDTIkO";
    $mail->CharSet = 'UTF-8';
    $mail->setFrom('alertas@mipgenlinea.com', 'Administrador CSI');
    $mail->addReplyTo('alertas@mipgenlinea.com', 'Administrador CSI');
    $mail->addAddress($correo1, 'Envío de Correo');
    $mail->Subject = $nombre . ' Alerta CSI';
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
