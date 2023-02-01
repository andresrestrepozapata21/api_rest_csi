<?php
include_once 'config/cors.php';
include_once 'config/dbh.php';
require __DIR__ . '/../vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $latitud = $_GET["latitud"];
    $longitud = $_GET["longitud"];
    $id_usuario = $_GET["id_usuario"];
    error_log("Parametros recibidos".$latitud." - ".$longitud);
    $sentencia_listar = "select * from coordenadas inner join nombreServicios on nombreServicios.id_nombre=coordenadas.tipo_evento order by fecha desc";
    $resultado_listado = mysqli_query($conn, $sentencia_listar);
    $filas = array();
    while ($valor = mysqli_fetch_assoc($resultado_listado)) {
        $distancia = distance($valor["latitud"], $valor["longitud"], $latitud, $longitud, "K");
        if ($distancia <= 100) { //Envía al cliente las alertas que son inferiores a 500mts
            $valor["distancia"] = '' . round(($distancia * 1000)) . '';
            $valor["dias"] = '' . contarDias(date('Y-m-d'), $valor["fecha"]) . '';
            //Contamos las reacciones de la alerta
            $contar_reacciones_consulta = "select count(*) as total from reacciones where fk_id_coordenadas = ".$valor["id_coordenadas"];
            $resultado_reacciones = mysqli_query($conn,$contar_reacciones_consulta);
            $filas_reacciones = mysqli_fetch_assoc($resultado_reacciones);
            $valor["reacciones"] = '' . $filas_reacciones["total"] . '';
            


            $filas[] = $valor;
            //error_log(distance($valor["latitud"], $valor["longitud"], $latitud, $longitud, "K"));
            //error_log(print_r($valor));
        }
    }
    // But for this demo let return decoded data
    http_response_code(200);
    //echo json_encode(array('sql'=> $sentencia_listar));
    echo json_encode($filas);
} else {
    http_response_code(401);
    echo json_encode(array('message' => 'Petición incorrecta'));
}


function distance($lat1, $lon1, $lat2, $lon2, $unit)
{

    $theta = $lon1 - $lon2;
    $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
    $dist = acos($dist);
    $dist = rad2deg($dist);
    $miles = $dist * 60 * 1.1515;
    $unit = strtoupper($unit);

    if ($unit == "K") {
        return ($miles * 1.609344);
    } else if ($unit == "N") {
        return ($miles * 0.8684);
    } else {
        return $miles;
    }
}

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
