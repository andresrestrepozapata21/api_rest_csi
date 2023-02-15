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

        } else if ($table == "getUserTypes") {

            $table = "tipos_usuarios";
            $select = "id_tipo_usuario, descricion_tipo_usuario";
            include "services/get.data.php";

        } else if ($table == "getPlans") {

            $table = "planes";
            $select = "*";
            include "services/get.data.php";

        } else if ($table == "getServices") {

            $table = "servicios";
            $select = "*";
            include "services/get.data.php";

        } else if ($table == "getZones") {

            $table = "zonas";
            $select = "*";
            include "services/get.data.php";

        } else if ($table == "getAlerts") {

            $table = "alertas";
            $select = "*";
            include "services/get.data.php";

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

        } else if ($table == "LoginAdmin") {

            $table = "administradores";
            $suffix  = "administrador";
            include "services/post.Login.php";

        } else if ($table == "addContact") {

            $userToken = "usuarios_clientes";
            include "services/post.contact.php";

        } else if ($table == "createTypeUser") {

            $userToken = "administradores";
            include "services/post.typeUserrecord.php";

        } else if ($table == "uploadDocumentsCustomer") {

            $file1 = $_FILES['file1'];
            $file2 = $_FILES['file2'];
            $file3 = $_FILES['file3'];
            $id = $_POST['id'];
            $table = "usuarios_clientes";
            $suffix  = "usuario_cliente";
            include "services/post.uploadDocuments.php";

        } else if ($table == "uploadDocumentsAgent") {

            $file1 = $_FILES['file1'];
            $file2 = $_FILES['file2'];
            $file3 = $_FILES['file3'];
            $id = $_POST['id'];
            $table = "usuarios_agentes";
            $suffix  = "usuario_agente";
            include "services/post.uploadDocuments.php";

        } else if ($table == "loadPerfilePictureCustomer") { 

            $file = $_FILES['file'];
            $id = $_POST['id'];
            $table = "usuarios_clientes";
            $suffix  = "usuario_cliente";
            include "services/post.loadPerfilePicture.php";

        } else if ($table == "loadPerfilePictureAgent") { 

            $file = $_FILES['file'];
            $id = $_POST['id'];
            $table = "usuarios_agentes";
            $suffix  = "usuario_agente";
            include "services/post.loadPerfilePicture.php";

        } else if ($table == "activatePlan") {

            $userToken = "usuarios_clientes";
            $table = "planes_comprados";
            include "services/post.activatePlan.php";
            
        } else if ($table == "homePage") {

            $userToken = "usuarios_clientes";   
            include "services/get.homePage.php";

        } else if ($table == "serviceRecord") {

            $userToken = "administradores";
            $table = "servicios";
            $file = $_FILES['file'];
            $data = '{"token":"'.$_POST["token"].'","descripcion_servicio":"'.$_POST["descripcion_servicio"].'","puntos_servicio":'.$_POST["puntos_servicio"].'}';
            $data = json_decode($data);
            include "services/post.php";

        } else if ($table == "planRecord") {

            $userToken = "administradores";
            $table = "planes";
            $file = $_FILES['file'];
            $data = '{"token":"'.$_POST["token"].'","tipo_plan":"'.$_POST["tipo_plan"].'","precio_plan":'.$_POST["precio_plan"].',"descripcion_plan":"'.$_POST["descripcion_plan"].'"}';
            $data = json_decode($data);
            include "services/post.php";

        } else if ($table == "planImageUpdate") {

            $file = $_FILES['file'];
            $id = $_POST['id'];
            $token = $_POST['token'];
            $table = "planes";
            $suffix  = "plan";
            $userToken = "administradores";
            $ruta = "src/images_plans/";
            include "services/put.updateImage.php";

        } else if ($table == "serviceImageUpdate") {

            $file = $_FILES['file'];
            $id = $_POST['id'];
            $token = $_POST['token'];
            $table = "servicios";
            $suffix  = "servicio";
            $userToken = "administradores";
            $ruta = "src/images_services/";
            include "services/put.updateImage.php";

        }else if ($table == "getPlan") {

            $userToken = "usuarios_clientes";
            $table = "planes";
            $select = "*";
            $id = "id_plan";
            include "services/get.dataFilter.php";

        } else if ($table == "validateExistingPlan") {

            $userToken = "usuarios_clientes";
            $table = "planes_comprados";
            include "services/get.dataFilter.php";

        } else if ($table == "getPasswordAgent") {

            $table = "usuarios_agentes";
            include "services/get.password.php";
            
        } else if ($table == "getPasswordCustomer") {

            $table = "usuarios_clientes";
            include "services/get.password.php";

        } else if ($table == "getContacts") {

            $userToken = "usuarios_clientes";
            $table = "contactos";
            $select = "*";
            $id = "fk_id_usuario_cliente_contacto";
            include "services/get.dataFilter.php";

        } else if ($table == "servicesPerZoneRecord") {

            $userToken = "administradores";
            $table = "servicos_por_zona";
            $select = "*";
            $id = "fk_id_usuario_cliente_contacto";
            include "services/post.php";

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

            $userToken = "usuarios_clientes";
            $table = "usuarios_clientes";
            $suffix = "usuario_cliente";
            $select = "id_usuario_cliente";
            include "services/put.toUpdate.php";

        } else if ($table == "putAgent") {

            $userToken = "usuarios_agentes";
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

            $userToken = "administradores";
            $table = "tipos_usuarios";
            $suffix = "tipo_usuario";
            $select = "id_tipo_usuario";
            include "services/put.toUpdate.php";

        } else if ($table == "planUpdate") {

            $userToken = "administradores";
            $table = "planes";
            $suffix = "plan";
            $select = "id_plan";
            include "services/put.toUpdate.php";

        } else if ($table == "contactUpdate") {

            $userToken = "usuarios_clientes";
            $table = "contactos";
            $suffix = "contacto";
            $select = "id_contacto";
            include "services/put.toUpdate.php";

        } else if ($table == "putCustomerNumberPhone") {

            $table = "usuarios_clientes";
            $suffix = "usuario_cliente";
            $select = "id_usuario_cliente";
            include "services/put.toUpdateNumberPhone.php";

        } else if ($table == "putCustomerActivate") {

            $userToken = "administradores";
            $table = "usuarios_clientes";
            $suffix = "usuario_cliente";
            $select = "id_usuario_cliente";
            include "services/put.toActivete.php";

        } else if ($table == "putAgentActivate") {

            $userToken = "administradores";
            $table = "usuarios_agentes";
            $suffix = "usuario_agente";
            $select = "id_usuario_agente";
            include "services/put.toActivete.php";

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

            $userToken = "administradores";
            $table = "tipos_usuarios";
            $nameId = "id_tipo_usuario";
            $id = $data->id_tipo_usuario;
            include "services/delete.php";

        } else if($table == "contactDelete"){
            
            $userToken = "usuarios_clientes";
            $table = "contactos";
            $nameId = "id_contacto";
            $id = $data->id_contacto;
            include "services/delete.php";
            
        } else if($table == "deleteCustomer"){
            
            $userToken = "administradores";
            $table = "usuarios_clientes";
            $nameId = "id_usuario_cliente";
            $id = $data->id_usuario_cliente;
            include "services/delete.php";
            
        } else if($table == "deleteAgent"){
            
            $userToken = "administradores";
            $table = "usuarios_agentes";
            $nameId = "id_usuario_agente";
            $id = $data->id_usuario_agente;
            include "services/delete.php";
            
        } else if($table == "deletePlan"){
            
            $userToken = "administradores";
            $table = "planes";
            $nameId = "id_plan";
            $id = $data->id_plan;
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
