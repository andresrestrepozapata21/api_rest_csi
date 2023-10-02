<?php
date_default_timezone_set('America/Bogota');
require_once "server/conexion.php";
//capturo el id de la reaccion que vienen en la URL que le llego al usuario via SMS
$id_reaccion = $_GET['id_reaccion'];
//busco la reaccion con sus respectivos datos
$sentencia_reaccion = "SELECT * FROM reacciones_cliente_cliente WHERE id_reaccion_cliente_cliente=$id_reaccion";
$consulta_reaccion = mysqli_query($conexion, $sentencia_reaccion);
$datos_reaccion = mysqli_fetch_assoc($consulta_reaccion);
//capturo los datos de la reaccion que necesito
$fk_id_alerta = $datos_reaccion["fk_id_alerta_reaccion_cliente_cliente"];
$fk_id_usuario_cliente = $datos_reaccion["fk_id_usuario_cliente_reaccion_cliente_cliente"];

//Busco los datos de la alerta, info de la alerta, de la victima
$sentencia = "SELECT * FROM alertas a INNER JOIN usuarios_clientes uc ON a.fk_id_usuario_cliente_alerta =uc.id_usuario_cliente WHERE a.id_alerta = $fk_id_alerta";
$resultado = mysqli_query($conexion, $sentencia);
$fila = mysqli_fetch_assoc($resultado);
//capturo los datos de la alerta y del usuario victima que necesite
$id_alerta = $fila["id_alerta"];
$nombre_cliente_victima = $fila["nombre_usuario_cliente"];
$apellido_cliente_victima = $fila["apellido_usuario_cliente"];
$telefono_cliente_victima = $fila["telefono_usuario_cliente"];
//busca los datos del usuario que reacciono
$sentencia_usuario_reaccion = "SELECT * FROM usuarios_clientes WHERE id_usuario_cliente=$fk_id_usuario_cliente";
$resultado_usuarios_reaccion = mysqli_query($conexion, $sentencia_usuario_reaccion);
$filas_usuario_reaccion = mysqli_fetch_assoc($resultado_usuarios_reaccion);
//Capturo los datos del usuario que esta reaccionando
$id_usuario_cliente_reaccion = $filas_usuario_reaccion["id_usuario_cliente"];
$nombre_usuario_cliente_reaccion = $filas_usuario_reaccion["nombre_usuario_cliente"];
$apellido_usuario_cliente_reaccion = $filas_usuario_reaccion["apellido_usuario_cliente"];
$telefono_usuario_cliente_reaccion = $filas_usuario_reaccion["telefono_usuario_cliente"];
$email_usuario_cliente_reaccion = $filas_usuario_reaccion["email"];
$direccion_usuario_cliente_reaccion = $filas_usuario_reaccion["direccion_usuario_cliente"];
//capturo la foto de perfil del usuario que reacciono
if ($filas_usuario_reaccion["foto_perfil_usuario_cliente"]) {
    $foto_perfil = $filas_usuario_reaccion["foto_perfil_usuario_cliente"];
} else {
    $foto_perfil = "";
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['btn'])) {
    $id_reaccion = $_POST["id_reaccion"];
    $sentencia_0 = "UPDATE reacciones_cliente_cliente SET confirmacionVictima= 1 WHERE id_reaccion_cliente_cliente = $id_reaccion";
    $consulta_0 = mysqli_query($conexion, $sentencia_0);
    if ($consulta_0) {
        header("Location: avisoConfirmado.php");
    } else {
        header("Location: avisoError.php");
    }
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
            <h4>Confirma que has sido atendido por un usuario de CSI</h4>
        </header>
        <section class="content">
            <article class="logo">
                <img src="assets/images/logoSimboloCSI.png" alt="">
            </article>
            <article class="enlace">
                <h3 class="titulo_servicio">Hola! <?php echo $nombre_cliente_victima ?></h3>
                <div class="info_alerta">
                    <div class="comentario">
                        <i class="fa-regular fa-comment-dots"></i>
                        <p>El usuario CSI que reacciono a una de tus alertas es:</p>
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
                                <td><?php echo $nombre_usuario_cliente_reaccion ?></td>
                            </tr>
                            <tr>
                                <th>Apellidos:</th>
                                <td><?php echo $apellido_usuario_cliente_reaccion ?></td>
                            </tr>
                            <tr>
                                <th>Teléfono:</th>
                                <td><?php echo $telefono_usuario_cliente_reaccion ?></td>
                            </tr>
                            <tr>
                                <th>Correo</th>
                                <td><?php echo $email_usuario_cliente_reaccion ?></td>
                            </tr>
                            <tr>
                                <th>Dirección:</th>
                                <td><?php echo $direccion_usuario_cliente_reaccion ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="content_btn">
                        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
                            <input type="hidden" name="id_reaccion" value="<?php echo $id_reaccion ?>">
                            <button type="submit" class="btn" id="btn" name="btn">Confirmar</button>
                        </form>
                    </div>
                </div>
            </article>
            <footer>
                <div class="footer">
                    <h3>CSI Seguridad es una Marca de BE SOLUTIONS SAS</h3>
                </div>
                <div class="footer_1">
                    <a href="https://csisecurity.co/tc/index.html">
                        <h3>Términos y Condiciones Política de Privacidad de Datos_reaccion</h3>
                    </a>
                </div>
            </footer>
        </section>
    </main>
</body>

</html>