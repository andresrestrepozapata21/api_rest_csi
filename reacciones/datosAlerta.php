<?php
date_default_timezone_set('America/Bogota');
require_once "server/conexion.php";
//capturo el id de la alerta
$id_alerta = $_GET['id'];
//consulto los datos de la alerta
$sentencia = "SELECT * FROM alertas a INNER JOIN usuarios_clientes uc ON a.fk_id_usuario_cliente_alerta=uc.id_usuario_cliente INNER JOIN servicios_por_zona sz ON a.fk_id_servicio_por_zona_alerta=sz.id_servicos_por_zona INNER JOIN servicios s ON sz.fk_id_servicio_servicos_por_zona=s.id_servicio WHERE a.id_alerta = $id_alerta ORDER BY date_created_alerta DESC";
$consulta = mysqli_query($conexion, $sentencia);
$datos = mysqli_fetch_assoc($consulta);
//capturo la foto
if ($datos["foto_perfil_usuario_cliente"]) {
    $foto_perfil = $datos["foto_perfil_usuario_cliente"];
} else {
    $foto_perfil = "";
}

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

    <!-- fontawesome -->
    <script src="https://kit.fontawesome.com/0a0c61a66b.js" crossorigin="anonymous"></script>
</head>

<body>
    <main class="main">
        <header class="header">
            <h4>Información de la alerta</h4>
        </header>
        <section class="content">
            <article class="logo">
                <img src="assets/images/logoSimboloCSI.png" alt="">
            </article>
            <article class="enlace">
                <h3 class="titulo_servicio">Hola!</h3>
                <div class="info_alerta">
                    <div class="comentario">
                        <i class="fa-regular fa-comment-dots"></i>
                        <p>Esta es la informacion de la alerta que quieres atender, incluyendo su ubicación e información del usuario.</p>
                    </div>
                    <div class="info_usuario">
                        <table>
                            <tr>
                                <th>Servicio Alerta:</th>
                                <td><?php echo $datos["descripcion_servicio"] ?></td>
                            </tr>
                            <tr>
                                <th>Comentario Alerta:</th>
                                <td><?php echo $datos["comentario_alerta"] ?></td>
                            </tr>
                            <tr>
                                <th>Fecha Alerta:</th>
                                <td><?php echo $datos["date_created_alerta"] ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="ubicacion">
                        <i class="fa-solid fa-location-dot"></i>
                        <p><a href="https://maps.google.com/?q=<?php echo $datos["latitud_alerta"] . ',' . $datos["longitud_alerta"] ?>">Ver en Google Maps </a></p>
                    </div>
                    <div class="momento">
                        <i class="fa-regular fa-clock"></i>
                        <p><?php
                            //Contamos los días
                            $dias = '' . contarDias(date('Y-m-d'), $datos["date_created_alerta"]) . '';
                            $texto_dias = '';
                            if ($dias > 0) {
                                $texto_dias = 'hace ' . $dias . ' dias';
                            } else {
                                $texto_dias = 'en este momento...';
                            }
                            echo $texto_dias;
                            ?></p>
                    </div>
                    <!-- HTML para probar toda la info del usuario-->
                    <div class="perfil">
                        <?php
                        if ($foto_perfil != "") {
                        ?>
                            <img src="../<?php echo $foto_perfil ?>" alt="">
                        <?php } ?>
                    </div>
                    <div class="info_usuario">
                        <table>
                            <tr>
                                <th>Nombres:</th>
                                <td><?php echo $datos["nombre_usuario_cliente"] ?></td>
                            </tr>
                            <tr>
                                <th>Apellidos:</th>
                                <td><?php echo $datos["apellido_usuario_cliente"] ?></td>
                            </tr>
                            <tr>
                                <th>Teléfono:</th>
                                <td><?php echo $datos["telefono_usuario_cliente"] ?></td>
                            </tr>
                            <tr>
                                <th>Correo</th>
                                <td><?php echo $datos["email"] ?></td>
                            </tr>
                            <tr>
                                <th>Dirección:</th>
                                <td><?php echo $datos["direccion_usuario_cliente"] ?></td>
                            </tr>
                        </table>
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
<?php
function contarDias($fecha1, $fecha2)
{
    $startTimeStamp = strtotime($fecha1);
    $endTimeStamp = strtotime($fecha2);
    $timeDiff = abs($endTimeStamp - $startTimeStamp);
    $numberDays = $timeDiff / 86400;  // 86400 seconds in one day
    // and you might want to convert to integer
    $numberDays = intval($numberDays);
    return $numberDays;
}
?>