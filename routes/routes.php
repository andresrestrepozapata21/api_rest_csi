<?php

$routesArray = explode("/", $_SERVER['REQUEST_URI']);
$routesArray = array_filter($routesArray);

/*=============================================
Dejo estas lineas comentadas, son las que se usaron para hacer pruebas unitarias en caso de errores
=============================================*/
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

    //capturo la palabra que se ingreso en la URL despues del /
    $table = explode("?", $routesArray[1])[0];
    //Capturo los datos que vienen en el JSON Request en caso de tenerlo
    $data = json_decode(file_get_contents("php://input"));

    /*=============================================
    Peticiones GET, dependendo de la palabra que se ingrese despues del / en la URL llama el servicio correspondiente en el folder services
    algunos utilizan variables $table, $suffix necesarias para validar y/o armar la SQL posteriormente en el model
    =============================================*/
    if ($_SERVER['REQUEST_METHOD'] == "GET") {
        //Estructra condicional para validar la palabra ingresada en la URL
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
        } else if ($table == "getPopup") {

            $table = "anuncios_popup";
            $select = "*";
            include "services/get.data.php";
        } else {
            //Retorno el caso de error
            $json  = array(

                'status' => 400,
                'result' => 6
            );

            echo json_encode($json, http_response_code($json["status"]));
            return;
        }
    }

    /*=============================================
    Peticiones POST, dependendo de la palabra que se ingrese despues del / en la URL llama el servicio correspondiente en el folder services
    algunos utilizan variables $table, $suffix necesarias para validar y/o armar la SQL posteriormente en el model
    =============================================*/
    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        //Estructra condicional para validar la palabra ingresada en la URL
        //Valido los endpoint que no necesitan recepcion como FormData por medio del metodo $_POST
        if ($table == "customerRecord") {

            $table = "usuarios_clientes";
            $suffix  = "usuario_cliente";
            include "services/post.record.php";
        } else if ($table == "customerLogin") {

            $table = "usuarios_clientes";
            $suffix  = "usuario_cliente";
            include "services/post.Login.php";
        } else if ($table == "LoginAdmin") {

            $table = "administradores";
            $suffix  = "administrador";
            include "services/post.Login.php";
        } else if ($table == "addContact") {

            $userToken = "usuarios_clientes";
            include "services/post.contact.php";
        } else if ($table == "tripRecord") {

            $userToken = "usuarios_clientes";
            $table = "viajes";
            $suffix  = "viaje";
            include "services/post.php";
        } else if ($table == "createTypeUser") {

            $userToken = "administradores";
            $table = "tipos_usuarios";
            $suffix = "tipo_usuario";
            include "services/post.php";
        } else if ($table == "uploadDocumentsCustomer") {

            $file1 = $_FILES['file1'];
            $file2 = $_FILES['file2'];
            $file3 = $_FILES['file3'];
            $id = $_POST['id'];
            $table = "usuarios_clientes";
            $suffix  = "usuario_cliente";
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
        } else if ($table == "master") {

            $userToken = "usuarios_clientes";
            include "services/get.homePage.php";
        } else if ($table == "masterChatico") {

            $userToken = "usuarios_clientes";
            $table = "masterChatico";
            $user = $data->user;
            $password = $data->password;
            include "services/post_chatico.php";
        } else if ($table == "getPlan") {

            $userToken = "usuarios_clientes";
            $table = "planes";
            $select = "*";
            $id = "id_plan";
            include "services/get.dataFilter.php";
        } else if ($table == "getUserTrips") {

            $userToken = "usuarios_clientes";
            $table = "viajes";
            $select = "*";
            $id = "fk_id_usuario_cliente_viaje";
            include "services/get.dataFilter.php";
        } else if ($table == "getTripStop") {

            $userToken = "usuarios_clientes";
            $table = "paradas";
            $select = "*";
            $id = "fk_id_viaje_parada";
            include "services/get.dataFilter.php";
        } else if ($table == "getTripPictures") {

            $userToken = "usuarios_clientes";
            $table = "registros_fotograficos_viajes";
            $select = "*";
            $id = "fk_id_viaje_registro_fotografico_viaje";
            include "services/get.dataFilter.php";
        } else if ($table == "getTrip") {

            $userToken = "usuarios_clientes";
            $table = "viaje";
            $select = "*";
            $id = "id_viaje";
            include "services/get.dataFilter.php";
        } else if ($table == "getLocal") {

            $userToken = "usuarios_clientes";
            $table = "establecimientos";
            include "services/get.dataFilter.php";
        } else if ($table == "getPhysicalProduct") {

            $userToken = "usuarios_clientes";
            $table = "productos_fisicos";
            $select = "*";
            $id = "id_producto_fisico";
            include "services/get.dataFilter.php";
        } else if ($table == "getServicesPerZone") {

            $userToken = "usuarios_clientes";
            $table = "servicios_por_zona";

            include "services/get.dataFilter.php";
        } else if ($table == "getZone") {

            $userToken = "usuarios_clientes";
            $table = "zona";

            include "services/get.dataFilter.php";
        } else if ($table == "validateExistingPlan") {

            $userToken = "usuarios_clientes";
            $table = "planes_comprados";
            include "services/get.dataFilter.php";
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
        } else if ($table == "reactionAgentAlert") {

            $userToken = "usuarios_agentes";
            $table = "reacciones_agentes";
            $suffix = "reaccion_agente";
            include "services/post.php";
        } else if ($table == "reactionCustomerAlert") {

            $userToken = "usuarios_clientes";
            $table = "reacciones_cliente_cliente";
            $suffix = "reaccion_cliente_cliente";
            include "services/post.php";
        } else if ($table == "checkZoneCode") {

            $userToken = "usuarios_clientes";
            $table = "zonas";
            include "services/get.dataFilter.php";
        } else if ($table == "positionCustomerRecord") {

            $userToken = "usuarios_clientes";
            $table = "posiciones_clientes";
            $suffix = "posicion_cliente";
            $id = "id_posicion_cliente";
            include "services/post.php";
        } else if ($table == "positionCustomerRecordWithoutZone") {

            $userToken = "usuarios_clientes";
            $table = "posiciones_sin_cobertura";
            $suffix = "posicion_sin_cobertura";
            $id = "id_posicion_sin_cobertura";
            include "services/post.php";
        } else if ($table == "getCloseCustomers") {

            $userToken = "usuarios_clientes";
            $table = "posiciones_clientes";
            $suffix = "posicion_cliente";
            $table2 = "usuarios_clientes";
            $suffix2 = "usuario_cliente";
            include "services/post.closePosition.php";
        } else if ($table == "getCloseAgents") {

            $userToken = "usuarios_agentes";
            $table = "posiciones_agentes";
            $suffix = "posicion_agente";
            $table2 = "usuarios_agentes";
            $suffix2 = "usuario_agente";
            include "services/post.closePosition.php";
        }  else if ($table == "getCloseAgentsChief") {

            $userToken = "usuarios_clientes";
            $table = "posiciones_agentes";
            $suffix = "posicion_agente";
            $table2 = "usuarios_agentes";
            $suffix2 = "usuario_agente";
            include "services/post.closePositionChief.php";
        } else if ($table == "contactDelete") {

            $userToken = "usuarios_clientes";
            $table = "contactos";
            $nameId = "id_contacto";
            $id = $data->id_contacto;
            include "services/delete.php";
        } else if ($table == "putUpdateCustomerDeviceToken") {

            $table = "usuarios_clientes";
            $suffix = "usuario_cliente";
            $select = "id_usuario_cliente";
            include "services/put.toUpdateDeviceToken.php";
        } else if ($table == "putUpdateAgentDeviceToken") {

            $table = "usuarios_agentes";
            $suffix = "usuario_agente";
            $select = "id_usuario_agente";
            include "services/put.toUpdateDeviceToken.php";
        } else if ($table == "TripUpdate") {

            $userToken = "usuarios_clientes";
            $table = "viajes";
            $suffix = "viaje";
            $select = "id_viaje";
            include "services/put.toUpdate.php";
        } else if ($table == "getAlertsCostumer") {

            $userToken = "usuarios_clientes";
            $table = "alertas";

            include "services/get.dataFilter.php";
        } else if ($table == "getCostumerData") {

            $userToken = "usuarios_clientes";
            $table = "usuarios_clientes";
            $select = "cedula_usuario_cliente,nombre_usuario_cliente,apellido_usuario_cliente,telefono_usuario_cliente,direccion_usuario_cliente,genero_usuario_cliente,email,password,foto_perfil_usuario_cliente,tipo_de_sangre,enfermedades_base,alergias,eps,arl,date_created_usuario_cliente";
            $id = "id_usuario_cliente";

            include "services/get.dataFilter.php";
        } else if ($table == "getAlertsZone") {

            $userToken = "usuarios_clientes";
            $table = "alertasZona";

            include "services/get.dataFilter.php";
        } else if ($table == "resendMessageCustomer") {

            $table = "usuarios_clientes";
            $suffix = "usuario_cliente";
            $select = "id_usuario_cliente";
            include "services/put.resendMessage.php";
        } else if ($table == "valideCostumerChatico") {

            $table = "usuarios_clientes";
            $user = $data->user;
            $password = $data->password;

            include "services/post_chatico.php";
        } else if ($table == "getPointsUser") {

            $userToken = "usuarios_clientes";
            $table = "puntos_ganados";
            include "services/get.dataFilter.php";
        } else if ($table == "deleteCustomer") {

            $userToken = "usuarios_clientes";
            $table = "usuarios_clientes";
            $nameId = "id_usuario_cliente";
            $id = $data->id_usuario_cliente;
            $suffix = "usuario_cliente";
            include "services/delete.php";
        }  else if ($table == "expirationPlanUser") {

            $userToken = "usuarios_clientes";
            $table = "planes_comprados_por_usuario";
            include "services/get.dataFilter.php";
        } else if ($table == "customerGotDocuments") {

            $userToken = "usuarios_clientes";
            $table = "documentos";
            include "services/get.dataFilter.php";
        } else if ($table == "ignoreAlert") {

            $userToken = "usuarios_clientes";
            $table = "ignoraciones";
            $suffix  = "usuario_cliente";
            include "services/post.php";
        }

        //Hay algunos casos donde utilizo la recepcion de un FormData() por tanto se capturan los datos por medio del metodo POST, luego armo el JSON normal para enviarlo al Servicio
        else if ($table == "stopRecord") {

            $userToken = "usuarios_clientes";
            $table = "paradas";
            $file = $_FILES['file'];
            $data = '{"token":"' . $_POST["token"] . '","descripcion_parada":"' . $_POST["descripcion_parada"] . '","latitud_parada":"' . $_POST["latitud_parada"] . '","longitud_parada":"' . $_POST["longitud_parada"] . '","fk_id_viaje_parada":"' . $_POST["fk_id_viaje_parada"] . '"}';
            $data = json_decode($data);
            include "services/post.php";
        } else if ($table == "alertRecord") {

            $userToken = "usuarios_clientes";
            $table = "alertas";
            $file1 = $_FILES['file1'];
            $file2 = $_FILES['file2'];
            $file3 = $_FILES['file3'];
            $file = array();
            array_push($file, $file1, $file2, $file3);
            $video = $_FILES['videoFile'];
            $data = '{"token":"' . $_POST["token"] . '","latitud_alerta":' . $_POST["latitud_alerta"] . ',"longitud_alerta":' . $_POST["longitud_alerta"] . ',"comentario_alerta":"' . $_POST["comentario_alerta"] . '","notificar_contactos":' . $_POST["notificar_contactos"] . ',"fk_id_usuario_cliente_alerta":' . $_POST["fk_id_usuario_cliente_alerta"] . ',"fk_id_servicio_por_zona_alerta":' . $_POST["fk_id_servicio_por_zona_alerta"] . '}';
            $data = json_decode($data);

            include "services/post.php";
        } else if ($table == "planUpdate") {

            $userToken = "administradores";
            $table = "planes";
            $suffix = "plan";
            $select = "id_plan";
            $id = $_POST["id_plan"];
            $file = $_FILES['file'];
            $data = '{"token":"' . $_POST["token"] . '","id_plan":"' . $_POST["id_plan"] . '","tipo_plan":"' . $_POST["tipo_plan"] . '","precio_plan":"' . $_POST["precio_plan"] . '","descripcion_plan":"' . $_POST["descripcion_plan"] . '"}';
            $data = json_decode($data);
            $ruta = "src/images_plans/";
            include "services/put.toUpdate.php";
        } else if ($table == "serviceUpdate") {

            $userToken = "administradores";
            $table = "servicios";
            $suffix = "servicio";
            $select = "id_servicio";
            $id = $_POST["id_servicio"];
            $file = $_FILES['file'];
            $data = '{"token":"' . $_POST["token"] . '","id_servicio":"' . $_POST["id_servicio"] . '","descripcion_servicio":"' . $_POST["descripcion_servicio"] . '","puntos_servicio":"' . $_POST["puntos_servicio"] . '","color_sombra_servicio":"' . $_POST["color_sombra_servicio"] . '"}';
            $data = json_decode($data);
            $ruta = "src/images_services/";
            include "services/put.toUpdate.php";
        } else if ($table == "stopUpdate") {

            $userToken = "usuarios_clientes";
            $table = "paradas";
            $suffix = "parada";
            $select = "id_parada";
            $id = $_POST["id_parada"];
            $file = $_FILES['file'];
            $data = '{"token":"' . $_POST["token"] . '","id_parada":"' . $_POST["id_parada"] . '","descripcion_parada":"' . $_POST["descripcion_parada"] . '","place_id_parada":"' . $_POST["place_id_parada"] . '","fk_id_viaje_parada":"' . $_POST["fk_id_viaje_parada"] . '"}';
            $data = json_decode($data);
            $ruta = "src/images_stops/";
            include "services/put.toUpdate.php";
        } else if ($table == "serviceRecord") {

            $userToken = "administradores";
            $table = "servicios";
            $file = $_FILES['file'];
            $data = '{"token":"' . $_POST["token"] . '","descripcion_servicio":"' . $_POST["descripcion_servicio"] . '","puntos_servicio":' . $_POST["puntos_servicio"] . ',"color_sombra_servicio":"' . $_POST["color_sombra_servicio"] . '"}';
            $data = json_decode($data);
            include "services/post.php";
        } else if ($table == "planRecord") {

            $userToken = "administradores";
            $table = "planes";
            $file = $_FILES['file'];
            $data = '{"token":"' . $_POST["token"] . '","tipo_plan":"' . $_POST["tipo_plan"] . '","precio_plan":' . $_POST["precio_plan"] . ',"descripcion_plan":"' . $_POST["descripcion_plan"] . '"}';
            $data = json_decode($data);
            include "services/post.php";
        } else if ($table == "tripPictureRecord") {

            $userToken = "usuarios_clientes";
            $table = "registros_fotograficos_viajes";
            $file = $_FILES['file'];
            $data = '{"token":"' . $_POST["token"] . '","fk_id_viaje_registro_fotografico_viaje":' . $_POST["fk_id_viaje_registro_fotografico_viaje"] . '}';
            $data = json_decode($data);
            include "services/post.php";
        } else if ($table == "putCustomer") {

            $userToken = "usuarios_clientes";
            $table = "usuarios_clientes";
            $suffix = "usuario_cliente";
            $select = "id_usuario_cliente";
            $id = $_POST["id_usuario_cliente"];
            $file = $_FILES['file'];
            $data = '{"token":"' . $_POST["token"] . '","id_usuario_cliente":"' . $_POST["id_usuario_cliente"] . '","Enfermedades_base":"' . $_POST["Enfermedades_base"] . '","nombre_usuario_cliente":"' . $_POST["nombre_usuario_cliente"] . '","apellido_usuario_cliente":"' . $_POST["apellido_usuario_cliente"] . '","telefono_usuario_cliente":' . $_POST["telefono_usuario_cliente"] . ',"cedula_usuario_cliente":"' . $_POST["cedula_usuario_cliente"] . '","tipo_de_sangre":"' . $_POST["tipo_de_sangre"] . '","direccion_usuario_cliente":"' . $_POST["direccion_usuario_cliente"] . '","email":"' . $_POST["email"] . '","arl":"' . $_POST["arl"] . '","password":' . $_POST["password"] . ',"alergias":"' . $_POST["alergias"] . '","eps":"' . $_POST["eps"] . '"}';
            $data = json_decode($data);
            $ruta = "src/perfile_pictures/clients/" . $id . "/";

            include "services/put.toUpdate.php";
        } else if ($table == "putAgent") {

            $userToken = "usuarios_agentes";
            $table = "usuarios_agentes";
            $suffix = "usuario_agente";
            $select = "id_usuario_agente";
            $id = $_POST["id_usuario_agente"];
            $file = $_FILES['file'];
            $data = '{"token":"' . $_POST["token"] . '","id_usuario_agente":"' . $_POST["id_usuario_agente"] . '","Enfermedades_base":"' . $_POST["Enfermedades_base"] . '","nombre_usuario_agente":"' . $_POST["nombre_usuario_agente"] . '","apellido_usuario_agente":"' . $_POST["apellido_usuario_agente"] . '","telefono_usuario_agente":' . $_POST["telefono_usuario_agente"] . ',"cedula_usuario_agente":"' . $_POST["cedula_usuario_agente"] . '","tipo_de_sangre":"' . $_POST["tipo_de_sangre"] . '","direccion_usuario_agente":"' . $_POST["direccion_usuario_agente"] . '","email":"' . $_POST["email"] . '","arl":"' . $_POST["arl"] . '","password":' . $_POST["password"] . ',"alergias":"' . $_POST["alergias"] . '","eps":"' . $_POST["eps"] . '","fk_id_tipo_usuario_usuario_agente":"' . $_POST["fk_id_tipo_usuario_usuario_agente"] . '"}';
            $data = json_decode($data);
            $ruta = "src/perfile_pictures/agents/" . $id . "/";

            include "services/put.toUpdate.php";
        } else {
            //Retorno el caso de error
            $json  = array(

                'status' => 400,
                'result' => 6
            );

            echo json_encode($json, http_response_code($json["status"]));
            return;
        }
    }

    /*=============================================
    Peticiones PUT, dependendo de la palabra que se ingrese despues del / en la URL llama el servicio correspondiente en el folder services
    algunos utilizan variables $table, $suffix necesarias para validar y/o armar la SQL posteriormente en el model
    =============================================*/
    if ($_SERVER['REQUEST_METHOD'] == "PUT") {
        //Estructra condicional para validar la palabra ingresada en la URL
        if ($table == "checkAccountCustomer") {

            $table = "usuarios_clientes";
            $suffix = "usuario_cliente";
            $select = "id_usuario_cliente";
            include "services/put.checkAccount.php";
        } else if ($table == "typeUserUpdate") {

            $userToken = "administradores";
            $table = "tipos_usuarios";
            $suffix = "tipo_usuario";
            $select = "id_tipo_usuario";
            include "services/put.toUpdate.php";
        } else if ($table == "contactUpdate") {

            $userToken = "usuarios_clientes";
            $table = "contactos";
            $suffix = "contacto";
            $select = "id_contacto";
            include "services/put.toUpdate.php";
        } else if ($table == "putCustomerData") {

            $table = "usuarios_clientes";
            $suffix = "usuario_cliente";
            $select = "id_usuario_cliente";
            include "services/put.toUpdateCustomerData.php";
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
            //Retorno el caso de error
            $json  = array(

                'status' => 400,
                'result' => 6
            );

            echo json_encode($json, http_response_code($json["status"]));
            return;
        }
    }

    /*=============================================
    Peticiones DELETE, dependendo de la palabra que se ingrese despues del / en la URL llama el servicio correspondiente en el folder services
    algunos utilizan variables $table, $suffix necesarias para validar y/o armar la SQL posteriormente en el model
    =============================================*/
    if ($_SERVER['REQUEST_METHOD'] == "DELETE") {
        //Estructra condicional para validar la palabra ingresada en la URL
        if ($table == "deleteTypeUser") {

            $userToken = "administradores";
            $table = "tipos_usuarios";
            $nameId = "id_tipo_usuario";
            $id = $data->id_tipo_usuario;
            include "services/delete.php";
        } else if ($table == "stopDelete") {

            $userToken = "usuarios_clientes";
            $table = "paradas";
            $nameId = "id_parada";
            $id = $data->id_parada;
            include "services/delete.php";
        } else if ($table == "deleteAgent") {

            $userToken = "administradores";
            $table = "usuarios_agentes";
            $nameId = "id_usuario_agente";
            $id = $data->id_usuario_agente;
            $suffix = "usuario_agente";
            include "services/delete.php";
        } else if ($table == "deletePlan") {

            $userToken = "administradores";
            $table = "planes";
            $nameId = "id_plan";
            $id = $data->id_plan;
            $suffix = "plan";
            include "services/delete.php";
        } else if ($table == "deleteService") {

            $userToken = "administradores";
            $table = "servicios";
            $nameId = "id_servicio";
            $id = $data->id_servicio;
            $suffix = "servicio";
            include "services/delete.php";
        } else if ($table == "deleteTripPicture") {

            $userToken = "usuarios_clientes";
            $table = "registros_fotograficos_viajes";
            $nameId = "id_registro_fotografico_viaje";
            $id = $data->id_registro_fotografico_viaje;
            $suffix = "registro_fotografico_viaje";
            include "services/delete.php";
        } else {
            //Retorno el caso de error
            $json  = array(

                'status' => 400,
                'result' => 6
            );

            echo json_encode($json, http_response_code($json["status"]));
            return;
        }
    }
}
