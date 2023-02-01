<?php
/*=======================================
Mostrar Errores
=======================================*/

ini_set('display_errors', 1);
ini_set("log_errors", 1);
ini_set("error_log", "C:/xampp/htdocs/api_rest_csi/php_error_log.txt");

/*=======================================
CORS
=======================================*/
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requested-with,  Content-Type, Accept');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Content-type: application/json; chartset=utf-8');


/*=======================================
Requerimientos
=======================================*/

require_once "controllers/routes.controller.php";

$index = new RoutesController();
$index -> index();