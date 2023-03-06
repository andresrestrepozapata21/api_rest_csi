<?php
date_default_timezone_set('America/Bogota');
include_once 'conexion.php';
//include_once 'cors.php';


//Busca las alertas que tengan confirmacionAgente=0 y confirmacion_cliente_reaccion_agente=0, 
//que tengan notificacionesAgente < 3 y notificacion_cliente_reaccion_agente < 3 para enviar recordatorios

$sentencia = 'SELECT *, ua.token_dispositivo as token_dispositivo_agente, uc.token_dispositivo as token_dispositivo_cliente FROM reacciones_agentes ra INNER JOIN alertas a on ra.fk_id_alerta_reaccion_agente=a.id_alerta INNER JOIN usuarios_clientes uc ON a.fk_id_usuario_cliente_alerta=uc.id_usuario_cliente INNER JOIN usuarios_agentes ua ON ra.fk_id_usuario_agente_reaccion_agente=ua.id_usuario_agente WHERE (ra.confirmacion_agente_reaccion_agente = 0 or ra.confirmacion_cliente_reaccion_agente = 0)';

$resultado = mysqli_query($conn, $sentencia);

while ($filas = mysqli_fetch_assoc($resultado)) {

    //Enviar SMS al cliente, para preguntarle si el agente asisitió y aumentar el campo notificacion_cliente_reaccion_agente ++

    if ($filas['notificacion_cliente_reaccion_agente'] < 3 && $filas['confirmacion_cliente_reaccion_agente'] < 1) {
        $telefono = $filas["telefono_usuario_cliente"];
        $mensaje = 'Por favor confirma asistencia del Agente ' . $filas["nombre_usuario_agente"] . " " . $filas["apellido_usuario_agente"] . " que dijo que te iba a asistir, ingresa en este link https://csi.mipgenlinea.com/CSI-api-reaccion/confirmacionCliente.php?id_alerta=" . $filas["id_alerta"];
        $url = 'http://api.mipgenlinea.com/serviceSMS.php';
        $data = array(
            "usuario" => "smsFoxUser",
            "password" => "rhjIMEI3*",
            "telefono" => "+57" . $telefono,
            "mensaje" => $mensaje,
            "aplicacion" => "SMS Test Unitario",
        );
        $json = json_encode($data);
        $header = array('Content-Type: application/json');
        $result = CallAPI($url, $json, $header);
        file_put_contents('./log_' . date("j.n.Y") . '.txt', '[' . date('Y-m-d H:i:s') . ']' . "SMS API -> $result\n\r", FILE_APPEND);


        //Enviar PUSH notification al cliente
        // API access key from Google API's Console
        define('API_ACCESS_KEY', 'key=AAAA2NHxz-Q:APA91bEtlZR2cBYFQEpOtveMP4I3cpbcWJC_A25JPYsqSNvA6KBnDU62NFzY0XNb9NQV8eNRNh7S70yO4-zvfLM8QRdMNa3Vr04JhFq93aCE7mpLfSBnTcTbDoGa76EcQy6PJjiQ4XIJ');
        $token_dispositivo = $filas["token_dispositivo_cliente"];
        $msg = array(
            'to' => $token_dispositivo,
            'notification' => array('body' => 'Por favor confirma asistencia del Agente ' . $filas["nombre_usuario_agente"] . ' ' . $filas["apellido_usuario_agente"] . ' que te notificó que te iba a asistir en tu emergencia. Recuerda que su número es ' . $filas["telefono_usuario_agente"] . ' fue enviado un SMS a tu número con las instrucciones para confirmar', 'title' => 'Confirma Asistencia')
        );
        $headers = array(
            'Authorization:' . API_ACCESS_KEY,
            'Content-Type: application/json'
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($msg));
        $result = curl_exec($ch);
        curl_close($ch);
        file_put_contents('./log_alertas_reacciones_' . date("j.n.Y") . '.txt', "\r\n[" . date('Y-m-d H:i:s') . ']' . "PUSH Notification enviada a la alerta " . $filas["id_alerta"] . " Al cliente " . $filas["nombre_usuario_cliente"] . " " . $filas["apellido_usuario_cliente"], FILE_APPEND);

        //Actualiza contadores
        $sentencia_updateClientes = "UPDATE reacciones_agentes SET notificacion_cliente_reaccion_agente = notificacion_cliente_reaccion_agente + 1 WHERE fk_id_alerta_reaccion_agente = " . $filas["id_alerta"];
        $resultado_updateClientes = mysqli_query($conn, $sentencia_updateClientes);
        error_log($sentencia_updateClientes);
    }


    //Enviar SMS al Agente, para preguntarle si fue a donde el cliente y aumentar el campo notificacionesAgente ++
    if ($filas['notificacion_agente_reaccion_agente'] < 3 && $filas['confirmacion_agente_reaccion_agente'] < 1) {
        $telefono = $filas["telefono_usuario_agente"];
        $mensaje = 'Hola ' . $filas["nombre_usuario_agente"] . " recuerda que " . $filas["nombre_usuario_cliente"] . " espera tu asistencia, confirma si pudiste asistirlo, ingresa a este link https://csi.mipgenlinea.com/CSI-api-reaccion/confirmarAgente.php?id_alerta=" . $filas["id_alerta"];
        $url = 'http://api.mipgenlinea.com/serviceSMS.php';
        $data = array(
            "usuario" => "smsFoxUser",
            "password" => "rhjIMEI3*",
            "telefono" => "+57" . $telefono,
            "mensaje" => $mensaje,
            "aplicacion" => "SMS Test Unitario",
        );
        $json = json_encode($data);
        $header = array('Content-Type: application/json');
        $result = CallAPI($url, $json, $header);
        file_put_contents('./log_' . date("j.n.Y") . '.txt', '[' . date('Y-m-d H:i:s') . ']' . "SMS API -> $result\n\r", FILE_APPEND);



        //Enviar PUSH notification al agente

        // API access key from Google API's Console
        define('API_ACCESS_KEY2', 'key=AAAA2NHxz-Q:APA91bEtlZR2cBYFQEpOtveMP4I3cpbcWJC_A25JPYsqSNvA6KBnDU62NFzY0XNb9NQV8eNRNh7S70yO4-zvfLM8QRdMNa3Vr04JhFq93aCE7mpLfSBnTcTbDoGa76EcQy6PJjiQ4XIJ');
        $token_dispositivo = $filas["token_dispositivo_agente"];
        $msg = array(
            'to' => $token_dispositivo,
            'notification' => array('body' => 'Por favor confirma asistencia al cliente, te esta esperando ' . $filas["nombre_usuario_agente"] . ' ' . $filas["apellido_usuario_agente"] . '. Le notificaste que asistirías en su ayuda. Recuerda que su número es ' . $filas["telefono_usuario_cliente"] . ' fue enviado un SMS a tu número con las instrucciones para confirmar', 'title' => 'URG. Confirma Asistencia')
        );
        $headers = array(
            'Authorization:' . API_ACCESS_KEY2,
            'Content-Type: application/json'
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($msg));
        $result = curl_exec($ch);
        curl_close($ch);
        file_put_contents('./log_alertas_reacciones_' . date("j.n.Y") . '.txt', "\r\n[" . date('Y-m-d H:i:s') . ']' . "PUSH Notification enviada a la alerta " . $filas["id_alerta"] . " Al Agente " . $filas["nombre_usuario_agente"] . " " . $filas["apellido_usuario_gente"], FILE_APPEND);


        //Actualiza contadores
        $sentencia_updateAgentes = "UPDATE reacciones_agentes SET notificacion_agente_reaccion_agente = notificacion_agente_reaccion_agente + 1 WHERE fk_id_alerta_reaccion_agente = " . $filas["id_alerta"];
        $resultado_updateAgentes = mysqli_query($conn, $sentencia_updateAgentes);
        error_log($resultado_updateAgentes);
    }
}

/*=============================================
METODOS AUXILIARES
=============================================*/
function CallAPI($url, $json, $header)
{
    $curl = curl_init();

    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $json);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

    $response = curl_exec($curl);
    $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    curl_close($curl);

    return $response;
}
