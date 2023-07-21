<?php
date_default_timezone_set('America/Bogota');
require_once "server/conexion.php";
//capturo el id del usuario
$id_usuario = $_GET['id'];
//busco kis datos del usuario
$sentencia = "SELECT * FROM usuarios_clientes WHERE id_usuario_cliente=$id_usuario";
$consulta = mysqli_query($conexion, $sentencia);
$datos = mysqli_fetch_assoc($consulta);
//capturo la foto de perfil
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
            <h4>Información del Usuario Reacción</h4>
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
                        <p>Este usuario es el que quiere ayudarte y va a atender tu emergencia</p>
                    </div>
                    <!-- HTML para probar toda la info del usuario-->
                    <div class="perfil">
                        <img src="../<?php echo $foto_perfil ?>" alt="">
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