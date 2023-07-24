<?php
date_default_timezone_set('America/Bogota');
require_once "server/conexion.php";

$id_usuario = $_GET['id_usuario'];

$sentencia = "SELECT * FROM usuarios_clientes WHERE id_usuario_cliente=$id_usuario";
$consulta = mysqli_query($conexion, $sentencia);
$datos = mysqli_fetch_assoc($consulta);

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
            <h4>Validador QR Plan Usuario</h4>
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
                        <?php
                        //Sentencia para comprobar que el usuario tenga un plan diferente al plan pratrocinado que el "19" y tambien que dicho plan este activo
                        $sentencia_1 = "SELECT * FROM planes_comprados WHERE fk_id_usuario_cliente_plan_comprado=$id_usuario AND activo_plan_comprado = 1 AND fk_id_plan_plan_comprado != 19";
                        $consulta_1 = mysqli_query($conexion, $sentencia_1);
                        //valido si la consulta me trajo algo
                        if (mysqli_num_rows($consulta_1) > 0) {
                        ?>
                            <p>El usuario actualmente <b class="si">SI</b> cuenta con CSI, el cual le permite acceder a descuentos especiales.</p>
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
                <?php
                        } else { ?>
                    <p>El usuario actualmente <b class="no">NO</b> cuenta con un plan CSI. Invitalo a que adquiera alguno de nuestros planes y tenga todos los beneficios de ser un CSI.</p>
                <?php }
                ?>
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