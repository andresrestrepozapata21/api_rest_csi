<?php
date_default_timezone_set('America/Bogota');
include("server/conexion.php");
$id_usuario_cliente = $_GET["id_usuario"];
$id_plan = $_GET["id_plan"];
//$id_usuario_cliente = 47;
//$id_plan = 2;

//Rutina para buscar el correo del usuario
$sql_cliente = "SELECT email FROM usuarios_clientes WHERE id_usuario_cliente = $id_usuario_cliente";
$consulta_cliente = mysqli_query($conexion, $sql_cliente);
$datos_cliente = mysqli_fetch_assoc($consulta_cliente);
$correo_usuario = $datos_cliente["email"];

//Rutina para buscar el nombre del plan
$sql_plan = "SELECT precio_plan, tipo_plan FROM planes WHERE id_plan = $id_plan";
$consulta_plan = mysqli_query($conexion, $sql_plan);
$datos_plan = mysqli_fetch_assoc($consulta_plan);
$tipo_plan = $datos_plan["tipo_plan"];
$precio_plan = $datos_plan["precio_plan"];

//Cuenta Produccion Diego A.
$apiKey = "N4AFPXABK2cG3mzlJ0nofwq7m4";
$merchantId = "993803";
$accountId = "1002356";

//Datos adiciones que se necesitan
$tax = "0";
$taxReturnBase = "0";
$currency = "COP";
$test = "0";
$buyerEmail = $correo_usuario;

//URLs especificas para el flujo del landing de LAP
//$responseUrl = "http://localhost/api_rest_csi/pasarela_pago/respuesta_payu_csi.php";
//$confirmationUrl = "http://localhost/api_rest_csi/pasarela_pago/confirmacion_payu.php";
$responseUrl = "https://apicsi.csisecurity.co/pasarela_pago/respuesta_payu_csi.php";
$confirmationUrl = "https://apicsi.csisecurity.co/pasarela_pago/server/confirmacion_payu.php";

//URL Sandbox o Produccion
//$url_pagos = "https://sandbox.checkout.payulatam.com/ppp-web-gateway-payu/";
$url_pagos = "https://checkout.payulatam.com/ppp-web-gateway-payu/";

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CSI</title>

    <!-- CSS -->
    <link rel="stylesheet" href="assets/css/styles.css">
</head>

<body>
    <main class="main">
        <header class="header">
            <h4>Gracias por adquirir la membresía</h4>
        </header>
        <section class="content">
            <?php
            if ($id_plan == 1) { ?>
                <article class="logo">
                    <img src="assets/images/logoSimboloCSI.png" alt="">
                </article>
                <article class="enlace">
                    <form class="form_confirm" method="POST" action="<?php echo $url_pagos ?>">
                        <h4>Esta membresía incluye:</h4>
                        <div class="info">
                            <p>- Asistencia 24 horas</p>
                            <p>- Protección en Zonas donde
                                CSI Opera</p>
                            <p>- Función de Acompañamiento</p>
                            <p>- Red de Apoyo</p>
                            <p>- Servicios y descuentos</p>
                            <p>Adicionales para los afiliados</p>
                        </div>
                        <div class="content_precio">
                            <h2 class="precio">
                                <span class="text">$238.000</span>
                                <span class="line"></span>
                            </h2>
                        </div>
                        <!-- Boton submit para lanzar la pasarela de pago -->
                        <div class="content_btn">
                            <button class="btn" type="submit">
                                Pagar $199.900
                            </button>
                        </div>
                        <!-- CAMPOS PARA LA TRANSACCION ELECTRÓNICA CON PAYU-->
                        <?php
                        //Variables Adcionales para la compra en PAYU
                        $referenceCode = date('Y-m-d H:i:s') . "|" . $id_usuario_cliente . "|" . $tipo_plan . "|" . $id_plan;  //Creamos un valor único de la transacción: Dia, Mes, Año, horas, minutos, segundos, nombre de usuario, nombre de producto
                        $description = "CSI Segurity";
                        $precioTotal = $precio_plan;
                        $fecha = date('Y-m-d H:i:s');
                        ?>
                        <input name="merchantId" type="hidden" value="<?php print($merchantId) ?>">
                        <input name="accountId" type="hidden" value="<?php print($accountId) ?>">
                        <input name="description" type="hidden" value="<?php print($description) ?>">
                        <input name="referenceCode" type="hidden" value="<?php print($referenceCode) ?>">
                        <input name="amount" type="hidden" value="<?php print($precioTotal) ?>">
                        <input name="tax" type="hidden" value="<?php print($tax); ?>">
                        <input name="taxReturnBase" type="hidden" value="<?php print($taxReturnBase); ?>">
                        <input name="currency" type="hidden" value="<?php print($currency) ?>">
                        <input name="signature" type="hidden" value="<?php print(md5($apiKey . "~" . $merchantId . "~" . $referenceCode . "~" . $precioTotal . "~" . $currency)) ?>">
                        <input name="test" type="hidden" value="<?php print($test); ?>">
                        <input name="buyerEmail" type="hidden" value="<?php print($buyerEmail); ?>">
                        <input name="responseUrl" type="hidden" value="<?php print($responseUrl); ?>">
                        <input name="confirmationUrl" type="hidden" value="<?php print($confirmationUrl); ?>">
                        <?php
                        //Crea una petición de compra y la guarda en la base de datos.
                        //esta petición indica que puede haber un carrito abandonado
                        //de todas formas sirve para buscar la signature después y validar la transacción
                        $signature = md5($apiKey . "~" . $merchantId . "~" . $referenceCode . "~" . $precioTotal . "~" . $currency);
                        $fecha = date("Y-m-d H:i:s");
                        $sentencia_intencion = "INSERT INTO `peticiones_compra` (`id_usuario`,`id_plan`, `merchantId`, `accountId`, `description`, `referenceCode`, `precioTotal`, `tax`, `taxReturnBase`, `currency`, `signature`, `test`, `buyerEmail`, `responseUrl`, `confirmationUrl`,`fecha`) VALUES ('$id_usuario_cliente','$id_plan','$merchantId', '$accountId', '$description', '$referenceCode', '$precioTotal', '$tax', '$taxReturnBase', '$currency', '$signature', '$test', '$buyerEmail', '$responseUrl', '$confirmationUrl','$fecha')";
                        $resultado_intencion = mysqli_query($conexion, $sentencia_intencion);
                        if (!$resultado_intencion) {
                            echo "<p>Error en insercion a las peticiones " . mysqli_error($conexion) . " - " . $sentencia_intencion . "</p>";
                        }
                        ?>
                        <!-- <p class="firma">
                            <?php echo ($apiKey . "~" . $merchantId . "~" . $referenceCode . "~" . $precioTotal . "~" . $currency) ?>
                        </p>-->
                    </form>
                </article>
            <?php
            } else if ($id_plan == 2) { ?>
                <article class="logo_1">
                    <img src="assets/images/logoSimboloCSI.png" alt="">
                    <div class="img_premium">
                        <img src="assets/images/panico.png" alt="">
                        <img src="assets/images/angel.png" alt="">
                    </div>
                </article>
                <article class="enlace">
                    <form class="form_confirm" method="POST" action="<?php echo $url_pagos ?>">
                        <h4>Esta membresía incluye:</h4>
                        <div class="info">
                            <p>- Botón de Pánico e intercomunicador <b>Mi Angel!</b></p>
                            <p>- Plan de datos para <b>Mi Angel!</b></p>
                            <p>- Conexión vía telefónica la central, usando el botón</p>
                            <p>- Asistencia 24 horas</p>
                            <p>- Protección en Zonas donde CSI Opera</p>
                            <p>- Función de Acompañamiento</p>
                            <p>- Red de Apoyo</p>
                            <p>- Servicios y descuentos adicionales para los afiliados</p>
                        </div>
                        <div class="content_precio">
                            <h2 class="precio">
                                <span class="text">$598.000</span>
                                <span class="line"></span>
                            </h2>
                        </div>
                        <!-- Boton submit para lanzar la pasarela de pago -->
                        <div class="content_btn">
                            <button class="btn" type="submit">
                                Pagar $478.800
                            </button>
                        </div>
                        <!-- CAMPOS PARA LA TRANSACCION ELECTRÓNICA CON PAYU-->
                        <?php
                        //Variables Adcionales para la compra en PAYU
                        $referenceCode = date('Y-m-d H:i:s') . "|" . $id_usuario_cliente . "|" . $tipo_plan . "|" . $id_plan;  //Creamos un valor único de la transacción: Dia, Mes, Año, horas, minutos, segundos, nombre de usuario, nombre de producto
                        $description = "CSI Segurity";
                        $precioTotal = 478800;
                        $fecha = date('Y-m-d H:i:s');
                        ?>
                        <input name="merchantId" type="hidden" value="<?php print($merchantId) ?>">
                        <input name="accountId" type="hidden" value="<?php print($accountId) ?>">
                        <input name="description" type="hidden" value="<?php print($description) ?>">
                        <input name="referenceCode" type="hidden" value="<?php print($referenceCode) ?>">
                        <input name="amount" type="hidden" value="<?php print($precioTotal) ?>">
                        <input name="tax" type="hidden" value="<?php print($tax); ?>">
                        <input name="taxReturnBase" type="hidden" value="<?php print($taxReturnBase); ?>">
                        <input name="currency" type="hidden" value="<?php print($currency) ?>">
                        <input name="signature" type="hidden" value="<?php print(md5($apiKey . "~" . $merchantId . "~" . $referenceCode . "~" . $precioTotal . "~" . $currency)) ?>">
                        <input name="test" type="hidden" value="<?php print($test); ?>">
                        <input name="buyerEmail" type="hidden" value="<?php print($buyerEmail); ?>">
                        <input name="responseUrl" type="hidden" value="<?php print($responseUrl); ?>">
                        <input name="confirmationUrl" type="hidden" value="<?php print($confirmationUrl); ?>">
                        <?php
                        //Crea una petición de compra y la guarda en la base de datos.
                        //esta petición indica que puede haber un carrito abandonado
                        //de todas formas sirve para buscar la signature después y validar la transacción
                        $signature = md5($apiKey . "~" . $merchantId . "~" . $referenceCode . "~" . $precioTotal . "~" . $currency);
                        $fecha = date("Y-m-d H:i:s");
                        $sentencia_intencion = "INSERT INTO `peticiones_compra` (`id_usuario`,`id_plan`, `merchantId`, `accountId`, `description`, `referenceCode`, `precioTotal`, `tax`, `taxReturnBase`, `currency`, `signature`, `test`, `buyerEmail`, `responseUrl`, `confirmationUrl`,`fecha`) VALUES ('$id_usuario_cliente','$id_plan','$merchantId', '$accountId', '$description', '$referenceCode', '$precioTotal', '$tax', '$taxReturnBase', '$currency', '$signature', '$test', '$buyerEmail', '$responseUrl', '$confirmationUrl','$fecha')";
                        $resultado_intencion = mysqli_query($conexion, $sentencia_intencion);
                        if (!$resultado_intencion) {
                            echo "<p>Error en insercion a las peticiones " . mysqli_error($conexion) . " - " . $sentencia_intencion . "</p>";
                        }
                        ?>
                        <!-- <p class="firma">
                            <?php echo ($apiKey . "~" . $merchantId . "~" . $referenceCode . "~" . $precioTotal . "~" . $currency) ?>
                        </p>-->
                    </form>
                </article>
            <?php
            }
            ?>
            <article class="content_info_payu">
                <img src="assets/images/payu.png" alt="">
                <div class="linea_atencion">
                    <h3>Línea de Atención</h3>
                    <div class="number_wpp">
                        <img src="assets/images/wpp_icon.png" alt="">
                        <h3>3003651804</h3>
                    </div>
                </div>
            </article>
            <footer>
                <div class="footer">
                    <h3>CSI Seguridad es una Marca de BE SOLUTIONS SAS</h3>
                </div>
                <div class="footer_1">
                    <a href="">
                        <h3>Términos y Condiciones Política de Privacidad de Datos</h3>
                    </a>
                </div>
            </footer>
        </section>
    </main>
</body>

</html>