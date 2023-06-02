<?php
//include("conexion.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

require '../src/PHPMailer.php';
require '../src/Exception.php';
require '../src/SMTP.php';

$html = "AQUI EL CONTENIDO DEL MENSAJE";


function enviar_correo_confirmacion($destinatario1, $nombre_completo)
{
    //HTML del email
    $mensaje = file_get_contents("template_correo.html");

    //Trae la información del contratista para capturar el correo
    $correo1 = $destinatario1;

    //PHP Mailer
    $mail = new PHPMailer;
    $mail->isSMTP();
    $mail->SMTPDebug = SMTP::DEBUG_LOWLEVEL;
    $mail->Host = 'email-smtp.us-east-2.amazonaws.com';
    $mail->Port = 587;
    $mail->SMTPSecure = 'tls';
    $mail->SMTPAuth = true;
    $mail->Username = "AKIAVHKQVZ4UZADJC5IV";
    $mail->Password = "BLlaf3qmSV4BfliI0CiOl1xVUU4Z85XsFS7rbOygG0sG";
    $mail->CharSet = 'UTF-8';
    $mail->setFrom('alertas@mipgenlinea.com', 'Administrador CSI');
    $mail->addReplyTo('alertas@mipgenlinea.com', 'Administrador CSI');
    $mail->addAddress($correo1, 'Envío de Correo');
    $mail->Subject = $nombre_completo . ' Alerta CSI';
    $mail->msgHTML($mensaje, __DIR__);
    $mail->AltBody = $nombre_completo . ' Seguridad CSI';;
    //$mail->addAttachment('images/empleado.png');
    //$mail->addAttachment('images/independiente.png');
    //$mail->addAttachment('images/rentista.png');

    if (!$mail->send()) {
        echo "Mailer Error: " . $mail->ErrorInfo;
    } else {
        echo "Message sent!";
    }
}
