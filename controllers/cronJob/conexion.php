<?php
$db_host = "localhost";
$db_user = "mipgenlinea_csi";
$db_pass = "+avU~Yj(]FjZ";
$db_name = "mipgenlinea_csi";
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if (!$conn->set_charset("utf8")) {
    printf("", $conn->error);
} else {
   printf("", $conn->character_set_name());
}
if ($conn->connect_errno) {
    printf("Falló la conexión 2022: %s\n", $mysqli->connect_error);
    exit();
}
?>
