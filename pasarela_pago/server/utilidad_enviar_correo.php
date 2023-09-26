<?php
//include("conexion.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

require 'src/PHPMailer.php';
require 'src/Exception.php';
require 'src/SMTP.php';

$html = "AQUI EL CONTENIDO DEL MENSAJE";

function enviar_correo_confirmacion($destinatario1, $nombre_completo)
{
    //HTML del email
    $mensaje = file_get_contents("template_correo.html");
    $mensaje = str_replace("%nombre%", $nombre_completo, $mensaje);
    //correo de destino
    $correo1 = $destinatario1;
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
    $mail->addAddress($correo1, 'Envío de Correo');
    $mail->Subject = 'Gracias por tu compra, ya cuentas con CSI SECURITY';
    $mail->msgHTML($mensaje, __DIR__);
    $mail->AltBody = ' Seguridad CSI';
    //valido y retorno la respuesta
    if (!$mail->send()) {
        //echo "Mailer Error: " . $mail->ErrorInfo;
        return "error";
    } else {
        //echo "Solicitud Enviada";
        return "Solicitud Enviada";
    }
}
