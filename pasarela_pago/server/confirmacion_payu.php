<?php
date_default_timezone_set("America/Bogota");
include("conexion.php");
include("utilidad_enviar_correo.php");

/////////////////////////////////Data////////////////////////////////////// 
$file = fopen("data.txt", "a");

fwrite($file, "#-------------------------------------------------------\n");
fwrite($file, "# Request\n");
fwrite($file, "#-------------------------------------------------------\n");
foreach ($_POST as $id => $responseValue) {
    fwrite($file, "/---" . $id . " => " . $responseValue . "---/\n");
}
fclose($file);

/////////////////////////////////////////////////////////////////////////

$date = $_POST['date'];
$shipping_city = $_POST['shipping_city'];
$sign = $_POST['sign'];
$operation_date = $_POST['operation_date'];
$payment_method  = $_POST['payment_method'];
$transaction_id = $_POST['transaction_id'];
$transaction_date  = $_POST['transaction_date'];
$test = $_POST['test'];
$exchange_rate = $_POST['exchange_rate'];
$ip  = $_POST['ip'];
$reference_pol = $_POST['reference_pol'];
$cc_holder  = $_POST['cc_holder'];
$pse_bank  = $_POST['pse_bank'];
$transaction_type = $_POST['transaction_type'];
$state_pol = $_POST['state_pol'];
$billing_city = $_POST['billing_city'];
$phone = $_POST['phone'];
$cus = $_POST['cus'];
$description = $_POST['description'];
$merchant_id  = $_POST['merchant_id'];
$authorization_code = $_POST['authorization_code'];
$value = $_POST['value'];
$transaction_bank_id = $_POST['transaction_bank_id'];
$billing_country = $_POST['billing_country'];
$cardType  = $_POST['cardType'];
$payment_method_name = $_POST['payment_method_name'];
$email_buyer  = $_POST['email_buyer'];
$payment_method_id = $_POST['payment_method_id'];
$response_message_pol  = $_POST['response_message_pol'];
$account_id  = $_POST['account_id'];
$bank_referenced_code  = $_POST['bank_referenced_code'];
$reference_sale  = $_POST['reference_sale'];
$additional_value  = $_POST['additional_value'];
$fecha = date('Y-m-d H:i:s');



//Valida la petición, busca el state_pol = 1 para poder agregar el producto a las compras del cliente
/*
1. Busca la referencia de la transaccion en peticiones, captura todos los datos, para asociar esa petición a la compra
2. Mira el state_pol = 4 para autorizar el producto
3. Agrega en la tabla planes_comprados los datos del cliente y del plan
*/
$sentencia_busqueda = "SELECT * FROM peticiones_compra WHERE referenceCode LIKE '$reference_sale' ";
$resultado_busqueda = mysqli_query($conexion, $sentencia_busqueda);
if (mysqli_num_rows($resultado_busqueda) > 0) {
    if ($state_pol == 4) {
        //Inserta en la tabla historial_compras los datos del cliente
        $fila = mysqli_fetch_assoc($resultado_busqueda);
        $fk_id_usuario_cliente_plan_comprado = $fila["id_usuario"];
        $fk_id_plan_plan_comprado = $fila["id_plan"];
        $sentencia_compra = "INSERT INTO `planes_comprados`(`activo_plan_comprado`, `fk_id_plan_plan_comprado`, `fk_id_usuario_cliente_plan_comprado`, `date_created_plan_comprado`) VALUES (1,$fk_id_plan_plan_comprado,$fk_id_usuario_cliente_plan_comprado,'$fecha')";
        $resultado_compra = mysqli_query($conexion, $sentencia_compra);
        if (!$resultado_compra) {
            echo "Error insertando compra " . mysqli_error($conexion) . " -" . $sentencia_compra;
        }
    }
}

$sentencia = "INSERT INTO payu_confirmaciones(date,shipping_city,sign,operation_date,payment_method,transaction_id,transaction_date,test,exchange_rate,ip,reference_pol,cc_holder,pse_bank,transaction_type,state_pol,billing_city,phone,cus,description,merchant_id,authorization_code,value,transaction_bank_id,billing_countrycardType,email_buyer,payment_method_id,response_message_pol,account_id,bank_referenced_code,reference_sale,additional_value,fecha)values ('$date','$shipping_city','$sign','$operation_date','$payment_method','$transaction_id','$transaction_date','$test','$exchange_rate','$ip','$reference_pol','$cc_holder','$pse_bank','$transaction_type','$state_pol','$billing_city','$phone','$cus','$description','$merchant_id','$authorization_code','$value','$transaction_bank_id','$billing_country','$cardType','$payment_method_name','$email_buyer','$payment_method_id','$response_message_pol','$account_id','$bank_referenced_code','$reference_sale','$additional_value','$fecha')";
$resultado = mysqli_query($conexion, $sentencia);
if (!$resultado) {
    echo "Error insertando confirmacion " . mysqli_error($conexion) . " - " . $sentencia;
}

//Con el reference_sale sacamos los datos del usuario
$sentencia_usuario = "SELECT * FROM usuarios_clientes WHERE id_usuario_cliente=$fk_id_usuario_cliente_plan_comprado";
$resultado_usuario = mysqli_query($conexion, $sentencia_usuario);
$fila_usuario = mysqli_fetch_assoc($resultado_usuario);
$correo_usuario = $fila_usuario["email"];
$telefono = $fila_usuario["telefono_usuario_cliente"];
$nombre = $fila_usuario["nombre_usuario_cliente"];
$apellido = $fila_usuario["apellido_usuario_cliente"];
$nombre_completo = $nombre . " " . $apellido;

$resultado_correo = enviar_correo_confirmacion($correo_usuario, $nombre_completo);
file_put_contents('log_correos_' . date("j.n.Y") . '.txt', '[' . date('Y-m-d H:i:s') . ']' . " ID_usuario_cliente: " . $fk_id_usuario_cliente_plan_comprado . " correo enviado a -> " . $correo_usuario . " Resultado: " . $resultado_correo . "\n\r", FILE_APPEND);
