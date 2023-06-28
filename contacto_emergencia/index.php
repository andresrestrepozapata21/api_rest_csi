<?php
date_default_timezone_set('America/Bogota');
include("server/conexion.php");
$id_alerta = $_GET["id_alerta"];

$sentencia = "SELECT * FROM alertas a INNER JOIN usuarios_clientes uc ON a.fk_id_usuario_cliente_alerta=uc.id_usuario_cliente INNER JOIN servicios_por_zona sz ON a.fk_id_servicio_por_zona_alerta = sz.id_servicos_por_zona INNER JOIN servicios s ON sz.fk_id_servicio_servicos_por_zona = s.id_servicio WHERE id_alerta = $id_alerta";
$consulta = mysqli_query($conexion, $sentencia);
$datos = mysqli_fetch_assoc($consulta);
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
            <h4>Información Alerta Contacto Emergencia</h4>
        </header>
        <section class="content">
            <article class="logo">
                <img src="assets/images/logoSimboloCSI.png" alt="">
            </article>
            <article class="enlace">
                <h3 class="titulo_servicio"><?php echo $datos["descripcion_servicio"] ?></h3>
                <div class="info_alerta">
                    <div class="comentario">
                        <i class="fa-regular fa-comment-dots"></i>
                        <p>"<?php echo $datos["comentario_alerta"] ?>"</p>
                    </div>
                    <div class="ubicacion">
                        <i class="fa-solid fa-location-dot"></i>
                        <p><a href="https://maps.google.com/?q=<?php echo $datos["latitud_alerta"] . ',' . $datos["longitud_alerta"] ?>">Ver en Google Maps </a></p>
                    </div>
                    <div class="info_usuario">
                        <i class="fa-solid fa-child-reaching"></i>
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
                    <div class="imagenes">
                    <?php
                    if ($datos["ruta1_imagen_alerta"] != "") { ?>
                        <img src="../<?php echo $datos["ruta1_imagen_alerta"] ?>" alt="">
                    <?php
                    }
                    if ($datos["ruta2_imagen_alerta"] != "") { ?>
                        <img src="../<?php echo $datos["ruta2_imagen_alerta"] ?>" alt="">
                    <?php
                    }
                    if ($datos["ruta3_imagen_alerta"] != "") { ?>
                        <img src="../<?php echo $datos["ruta3_imagen_alerta"] ?>" alt="">
                    <?php
                    }
                    ?>
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