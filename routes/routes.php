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

            $table = "arl_vigentes";
            $select = "nombre_arl";
            include "services/get.arlAndEps.php";

        }else if($table=="getEps"){

            $table = "eps_vigentes";
            $select = "nombre_eps";
            include "services/get.arlAndEps.php";

        }else if($table=="getPasswordAgent"){

            $table = "usuarios_agentes";
            include "services/get.password.php";

        }else if($table=="getPasswordCustomer"){

            $table = "usuarios_clientes";
            include "services/get.password.php";

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

            $table = "usuarios_clientes";
            $suffix  = "usuario_cliente";

            include "services/post.record.php";  

        }else if($table=="agentRecord"){

            $table = "usuarios_agentes";
            $suffix  = "usuario_agente";

            include "services/post.record.php";  

        }else if($table=="customerLogin"){

            $table = "usuarios_clientes";
            $suffix  = "usuario_cliente";

            include "services/post.Login.php";   

        }else if($table=="agentLogin"){

            $table = "usuarios_agentes";
            $suffix  = "usuario_agente";

            include "services/post.Login.php";    

        }else if($table=="addContract"){

            include "services/post.contact.php";   

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

            $table = "usuarios_clientes";
            $suffix = "usuario_cliente";
            $select = "id_usuario_cliente";

            include "services/put.toUpdate.php";

        }else if($table=="putAgent"){

            $table = "usuarios_agentes";
            $suffix = "usuario_agente";
            $select = "id_usuario_agente";

            include "services/put.toUpdate.php";

        }else if($table=="resendMessageCustomer"){

            $table = "usuarios_clientes";
            $suffix = "usuario_cliente";
            $select = "id_usuario_cliente";

            include "services/put.resendMessage.php";

        }else if($table=="resendMessageAgent"){

            $table = "usuarios_agentes";
            $suffix = "usuario_agente";
            $select = "id_usuario_agente";

            include "services/put.resendMessage.php";

        }else if($table=="activateAccountCustomer"){

            $table = "usuarios_clientes";
            $suffix = "usuario_cliente";
            $select = "id_usuario_cliente";

            include "services/put.activateAccount.php";

        }else if($table=="activateAccountAgent"){

            $table = "usuarios_agentes";
            $suffix = "usuario_agente";
            $select = "id_usuario_agente";

            include "services/put.activateAccount.php";

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
