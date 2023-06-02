<?PHP
date_default_timezone_set("America/Bogota");
include("server/conexion.php");
session_start();

$fecha = date("Y-m-d H:i:s");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CSI Security</title>

    <!-- Estilos personalizados según lo que se requiera para el diseño -->
    <link rel="stylesheet" href="assets/css/respuesta_payu_csi.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css" rel="stylesheet">

    <!-- Meta Pixel Code -->
    <script>
        ! function(f, b, e, v, n, t, s) {
            if (f.fbq) return;
            n = f.fbq = function() {
                n.callMethod ?
                    n.callMethod.apply(n, arguments) : n.queue.push(arguments)
            };
            if (!f._fbq) f._fbq = n;
            n.push = n;
            n.loaded = !0;
            n.version = '2.0';
            n.queue = [];
            t = b.createElement(e);
            t.async = !0;
            t.src = v;
            s = b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t, s)
        }(window, document, 'script',
            'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '803163544037517');
        fbq('track', 'PageView');
    </script>
    <!-- End Meta Pixel Code -->

</head>

<body>
    <main class="main">
        <header class="header">
            <h4>Gracias por adquirir la membresía</h4>
        </header>
        <section class="content">
            <article class="logo">
                <img src="assets/images/logoSimboloCSI.png" alt="">
            </article>
            <article class="enlace">

                <!-- Validamos que la transaccion se realice -->
                <?php
                $ApiKey = "SCQlXfGYbg1JCgKw50E7VbsV6K";
                $merchant_id = $_REQUEST['merchantId'];
                $referenceCode = $_REQUEST['referenceCode'];
                $TX_VALUE = $_REQUEST['TX_VALUE'];
                $new_value = number_format($TX_VALUE, 1, '.', '');
                $currency = $_REQUEST['currency'];
                $transactionState = $_REQUEST['transactionState'];
                $firma_cadena = "$ApiKey~$merchant_id~$referenceCode~$new_value~$currency~$transactionState";
                $firmacreada = md5($firma_cadena);
                $firma = $_REQUEST['signature'];
                $reference_pol = $_REQUEST['reference_pol'];
                $cus = $_REQUEST['cus'];
                $extra1 = $_REQUEST['description'];
                $pseBank = $_REQUEST['pseBank'];
                $lapPaymentMethod = $_REQUEST['lapPaymentMethod'];
                $transactionId = $_REQUEST['transactionId'];

                if ($_REQUEST['transactionState'] == 4) {
                    $estadoTx = "Transacción aprobada";
                    //Saca el id del plan y el id_usuario del referenceCode
                    $partes = explode("|", $referenceCode);
                    $fk_id_usuario_cliente_plan_comprado = $partes[1];
                    $fk_id_plan_plan_comprado = $partes[3];
                    $sentencia_compra = "INSERT INTO `planes_comprados`(`activo_plan_comprado`, `fk_id_plan_plan_comprado`, `fk_id_usuario_cliente_plan_comprado`, `date_created_plan_comprado`) VALUES (1,$fk_id_plan_plan_comprado,$fk_id_usuario_cliente_plan_comprado,'$fecha')";
                    $resultado_compra = mysqli_query($conexion, $sentencia_compra);
                    if (!$resultado_compra) {
                        echo "Error insertando compra " . mysqli_error($conexion) . " -" . $sentencia_compra;
                    }
                } else if ($_REQUEST['transactionState'] == 6) {
                    $estadoTx = "Transacción rechazada";
                } else if ($_REQUEST['transactionState'] == 104) {
                    $estadoTx = "Error";
                } else if ($_REQUEST['transactionState'] == 7) {
                    $estadoTx = "Pago pendiente";
                } else {
                    $estadoTx = $_REQUEST['mensaje'];
                }

                //fin de los textos de validacion
                //Mostramos los mensajes de error segun sea el caso
                switch ($_REQUEST['transactionState']) {
                    case 6:
                        session_destroy();
                ?>
                        <div class="contenedor_gracias">
                            <h2>Transaccion Rechazada!</h2>
                            <p>Parece que hubo un error en tu transacción</p>
                            <p>Por favor valida con tu entidad bancaria</p>
                            <p>Aquí te esperamos para que continúes el proceso</p>
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
                        </div>
                    <?php
                        break;
                    case 104:
                        session_destroy();
                    ?>
                        <div class="contenedor_gracias">
                            <h2>Error!</h2>
                            <p>Parece que hubo un error en tu transacción</p>
                            <p>Por favor valida con tu entidad bancaria</p>
                            <p>Aquí te esperamos para que continúes el proceso</p>
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
                        </div>
                    <?php
                        break;
                    case 4:
                    ?>
                        <div class="contenedor_gracias">
                            <h2>¡Gracias por tu compra!</h2>
                            <p>
                                Tu pago ha sido confirmado con la entidad bancaria y ya puedes hacer uso de la aplicación.
                            </p>
                            <p>
                                Nuestro equipo de servicio al cliente se comunicará contigo para coordinar detalles posteriores.
                            </p>
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
                        </div>
                        <!-- Meta Pixel Code -->
                        <noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=803163544037517&ev=PageView&noscript=1" /></noscript>
                        <!-- End Meta Pixel Code -->
                    <?php
                        break;
                    case 7:
                        session_destroy();
                    ?>
                        <div class="contenedor_gracias">
                            <h2>¡Gracias por tu compra!</h2>
                            <p>
                                Tu pago está siendo confirmado con la entidad bancaria, este proceso puede tomar un par de minutos. Te llegará una notificación al correo registrado una vez el proceso termine.
                            </p>
                            <p>
                                Recuerda nuestra línea de Atención es:
                            </p>
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
                        </div>
                <?php
                        break;
                }
                ?>
                <?php
                if (strtoupper($firma) == strtoupper($firmacreada)) {
                ?>
                    <table>
                        <tr class="claroFila">
                            <td><b>Estado de la transacción:</b></td>
                            <td><?php echo $estadoTx; ?></td>
                        </tr>
                        <!-- <tr>
                                    <td><b>ID de la transacción:</b></td>
                                    <td><?php //echo $transactionId; 
                                        ?></td>
                                </tr> -->
                        <tr>
                            <td><b>Referencia de venta:</b></td>
                            <td><?php echo $reference_pol; ?></td>
                        </tr>
                        <!-- <tr>
                                    <td><b>Referencia de la transacción:</b></td>
                                    <td><?php echo $referenceCode; ?></td>
                                </tr> -->
                        <tr class="claroFila">
                            <?php
                            if ($pseBank != null) {
                            ?>
                        <tr>
                            <td><b>CUS:</b> </td>
                            <td><?php echo $cus; ?> </td>
                        </tr>
                        <tr class="claroFila">
                            <td><b>Banco:</b> </td>
                            <td><?php echo $pseBank; ?> </td>
                        </tr>
                    <?php
                            }
                    ?>
                    <tr class="claroFila">
                        <td><b>Valor total:</b></td>
                        <td>$<?php echo number_format($TX_VALUE); ?></td>
                    </tr>
                    <!-- <tr class="claroFila">
                                <td><b>Moneda:</b></td>
                                <td><?php //echo $currency; 
                                    ?></td>
                            </tr> -->
                    <tr>
                        <td><b>Descripción:</b></td>
                        <td><?php echo ($extra1); ?></td>
                    </tr>
                    <tr class="claroFila">
                        <td><b>Entidad:</b></td>
                        <td><?php echo ($lapPaymentMethod); ?></td>
                    </tr>
                    </table>
                    <?php
                    //Inserta en la tabla de payu_respuestas los datos
                    $fecha = date("Y-m-d H:i:s");
                    $sentencia_rta = "INSERT INTO `payu_respuestas` (`estado`, `id_transaccion`, `referencia_venta`, `referencia_transaccion`, `CUS`, `banco`, `valor_total`, `moneda`, `descripcion`, `entidad`,`firma_cadena`, `fecha`) VALUES ('$estadoTx', '$transactionId', '$reference_pol', '$referenceCode', '$cus', '$pseBank', '" . number_format($TX_VALUE) . "', '$currency', '$extra1', '$lapPaymentMethod','$firma_cadena','$fecha')";
                    $resultado = mysqli_query($conexion, $sentencia_rta);
                    if (!$resultado) {
                        echo "<p>Error SQL: " . mysqli_error($conexion) . " - " . $sentencia_rta . "</p>";
                    }
                } else {
                    ?>
                    <h1><b>Error validando la firma digital. Esta transacción no es válida</b></h1>
                    <?php echo "Firma Creada:" . $firmacreada ?>
                    <br>
                    <?php echo "Firma Signature:" . $firma ?>
                <?php
                }
                ?>

            </article>
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