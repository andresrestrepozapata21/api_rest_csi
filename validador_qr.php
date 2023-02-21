<?php
require_once "models/connection.php";

$cedula = $_GET['cedula'];

$conexion = Connection::conexionAlternativa();
$sentencia = "SELECT * FROM usuarios_clientes WHERE cedula_usuario_cliente='$cedula'";
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
    <link rel="shortcut icon" href="assets/img/pie_canciones.png" type="image/x-icon">

    <link rel="stylesheet" href="assets/css/validar_qr.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css" rel="stylesheet">
</head>

<body>
    <div class="contenedorPpal">
        <div class="cuerpo">
            <div class="cuerpo_derecha">
                <div class="contenedor_derecha_limit">
                    <div class="bienvenida">
                        <h1>¡HOLA!</h1>
                        <p>Este código <b>QR</b></p>
                    </div>
                    <div class="navegaciones">
                        <div class="contenedor_navegaciones">
                            <?php
                            if ($datos) {
                            ?>
                                <h2>¡SI!, es usuario de CSI</h2>
                            <?php } else { ?>
                                <h2>¡NO!, es usuario de CSI</h2>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>