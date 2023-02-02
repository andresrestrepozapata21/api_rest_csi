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

        if ($table == "getArl") {

            $table = "arl_vigentes";
            $select = "id_arl,nombre_arl";
            include "services/get.data.php";
        } else if ($table == "getEps") {

            $table = "eps_vigentes";
            $select = "id_eps,nombre_eps";
            include "services/get.data.php";
        } else if ($table == "getPasswordAgent") {

            $table = "usuarios_agentes";
            include "services/get.password.php";
        } else if ($table == "getPasswordCustomer") {

            $table = "usuarios_clientes";
            include "services/get.password.php";
        } else if ($table == "getUserTypes") {

            $table = "tipos_usuarios";
            $select = "id_tipo_usuario, descricion_tipo_usuario";
            include "services/get.data.php";
        } else if ($table == "getPlans") {

            $table = "planes";
            $select = "*";
            include "services/get.data.php";
        } else if ($table == "getPlan") {

            $table = "planes";
            $select = "*";
            $id_plan = "id_plan";
            include "services/get.dataFilter.php";
        } else if ($table == "validateExistingPlan") {

            $table = "planes_comprados";
            include "services/get.dataFilter.php";
        } else {
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

        if ($table == "customerRecord") {

            $table = "usuarios_clientes";
            $suffix  = "usuario_cliente";

            include "services/post.record.php";
        } else if ($table == "agentRecord") {

            $table = "usuarios_agentes";
            $suffix  = "usuario_agente";

            include "services/post.record.php";
        } else if ($table == "customerLogin") {

            $table = "usuarios_clientes";
            $suffix  = "usuario_cliente";

            include "services/post.Login.php";
        } else if ($table == "agentLogin") {

            $table = "usuarios_agentes";
            $suffix  = "usuario_agente";

            include "services/post.Login.php";
        } else if ($table == "addContract") {

            include "services/post.contact.php";
        } else if ($table == "createTypeUser") {

            include "services/post.typeUserrecord.php";
        } else if ($table == "uploadDocumentsCustomer") {

            $file = $_FILES['file'];
            $id = $_POST['id'];
            $table = "usuarios_clientes";
            $suffix  = "usuario_cliente";

            include "services/post.uploadDocumentsCustomer.php";
        } else if ($table == "uploadDocumentsAgent") {

            $file = $_FILES['file'];
            $id = $_POST['id'];
            $table = "usuarios_agentes";
            $suffix  = "usuario_agente";

            include "services/post.uploadDocumentsAgent.php";
        } else if ($table == "activatePlan") {

            $table = "planes_comprados";

            include "services/post.activatePlan.php";
        } else if ($table == "homePage") {

            include "services/get.homePage.php";
        }else {
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

        if ($table == "putCustomer") {

            $table = "usuarios_clientes";
            $suffix = "usuario_cliente";
            $select = "id_usuario_cliente";

            include "services/put.toUpdate.php";
        } else if ($table == "putAgent") {

            $table = "usuarios_agentes";
            $suffix = "usuario_agente";
            $select = "id_usuario_agente";

            include "services/put.toUpdate.php";
        } else if ($table == "resendMessageCustomer") {

            $table = "usuarios_clientes";
            $suffix = "usuario_cliente";
            $select = "id_usuario_cliente";

            include "services/put.resendMessage.php";
        } else if ($table == "resendMessageAgent") {

            $table = "usuarios_agentes";
            $suffix = "usuario_agente";
            $select = "id_usuario_agente";

            include "services/put.resendMessage.php";
        } else if ($table == "checkAccountCustomer") {

            $table = "usuarios_clientes";
            $suffix = "usuario_cliente";
            $select = "id_usuario_cliente";

            include "services/put.checkAccount.php";
        } else if ($table == "checkAccountAgent") {

            $table = "usuarios_agentes";
            $suffix = "usuario_agente";
            $select = "id_usuario_agente";

            include "services/put.checkAccount.php";
        } else if ($table == "typeUserUpdate") {

            $table = "tipos_usuarios";
            $suffix = "tipo_usuario";
            $select = "id_tipo_usuario";

            include "services/put.toUpdate.php";
        } else {
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

        if ($table == "deleteTypeUser") {

            $table = "tipos_usuarios";
            $nameId = "id_tipo_usuario";
            $id = $data->id_tipo_usuario;

            include "services/delete.php";
        } else {
            $json  = array(

                'status' => 400,
                'result' => 6
            );

            echo json_encode($json, http_response_code($json["status"]));

            return;
        }
    }
}
