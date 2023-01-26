<?php

$routesArray = explode("/", $_SERVER['REQUEST_URI']);
$routesArray = array_filter($routesArray);

//echo '<pre>'; print_r($routesArray); echo '</pre>';
//return;

/*=============================================
Cuando no se hace ninguna peticion a la API
=============================================*/
if (count($routesArray) == 0) {

    $json  = array(

        'status' => 400,
        'result' => 5
    );

    echo json_encode($json, http_response_code($json["status"]));

    return;
}

/*=============================================
Cuando si se hace una peticion a la API
=============================================*/

if (count($routesArray) == 1 && isset($_SERVER['REQUEST_METHOD'])) {

    $table = explode("?", $routesArray[1])[0];
    $data = json_decode(file_get_contents("php://input"));

    /*=============================================
    Peticiones GET
    =============================================*/
    if ($_SERVER['REQUEST_METHOD'] == "GET") {

        if($table=="getArl"){
            include "services/get.arl.php";
        }else if($table=="getEps"){
            include "services/get.eps.php";
        }else{
            $json  = array(

                'status' => 400,
                'result' => 6
            );
        
            echo json_encode($json, http_response_code($json["status"]));
        
            return;
        }
    }

    /*=============================================
    Peticiones POST
    =============================================*/
    if ($_SERVER['REQUEST_METHOD'] == "POST") {

        if($table=="customerRecord"){
            include "services/post.customerRecord.php";   
        }else if($table=="agentRecord"){
            include "services/post.agentRecord.php";   
        }else if($table=="customerLogin"){
            include "services/post.customerLogin.php";   
        }else if($table=="agentLogin"){
            include "services/post.agentLogin.php";   
        }else{
            $json  = array(

                'status' => 400,
                'result' => 6
            );
        
            echo json_encode($json, http_response_code($json["status"]));
        
            return;
        }
    }

    /*=============================================
    Peticiones PUT
    =============================================*/
    if ($_SERVER['REQUEST_METHOD'] == "PUT") {

        if($table=="putCustomer"){
            include "services/put.customer.php";
        }else if($table=="putAgent"){
            include "services/put.agent.php";
        }else{
            $json  = array(

                'status' => 400,
                'result' => 6
            );
        
            echo json_encode($json, http_response_code($json["status"]));
        
            return;
        }
    }

    /*=============================================
    Peticiones DELETE
    =============================================*/
    if ($_SERVER['REQUEST_METHOD'] == "DELETE") {
        
        echo "DELETE";
    }
}
