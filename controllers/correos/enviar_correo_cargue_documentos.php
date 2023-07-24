<?php

include("utilidad_enviar_correo.php");

//$email = "arz.950203@gmail.com";
//$nombre = "andres";
//$servicio = "alarma";

//$result = enviar_correo_confirmacion($email, $nombre, $servicio);
//echo $result;

function enviar_correo($destinatario1, $nombre, $apellido, $cedula){
    $result = enviar_correo_confirmacion($destinatario1, $nombre, $apellido, $cedula);
    return $result;
}





