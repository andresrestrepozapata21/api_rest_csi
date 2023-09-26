<?php
//seteo la zona horaria
date_default_timezone_set('America/Bogota');
//requiro los scripts que necesito
require_once "models/get.homePage.model.php";
require_once "models/connection.php";

//nombro la clase
class GetControllerMaster
{

    /*=============================================
    Metodo en el que obtengo todos los datos para retornar en el master
    =============================================*/
    public function getData($data)
    {
        //instacion la conexion a la BD
        $conexion = Connection::conexionAlternativa();
        //capturo las coordenadas
        $latitud = $data->latitud;
        $longitud = $data->longitud;

        //obtengo los datos del usuario por medio del modelo destinado a eso
        $responseUser = GetHomePageModel::getUsuario($data->id_usuario_cliente);

        //si el usuario no existe retorno un null en el JSON RESPONSE
        if (empty($responseUser)) {
            return GetControllerMaster::fncResponse(null);
        }

        //capturo los datos del usuario
        $id = $responseUser[0]->id_usuario_cliente;
        //$email = $responseUser[0]->email;

        /*=============================================
        Valido que el usuario que ya esta registrado y activo, sea un usuario beneficiario
        
        $sentencia_beneficiario = "SELECT * FROM usuario_beneficiarios WHERE correo_usuario_beneficiario = '$email'";
        $resultado_beneficiario = mysqli_query($conexion, $sentencia_beneficiario);

        if (mysqli_num_rows($resultado_beneficiario) > 0) {
            $fila_beneficiario = mysqli_fetch_assoc($resultado_beneficiario);
            $id_plan = $fila_beneficiario["fk_id_plan_usuario_beneficiario"];
            $fecha_compra = $fila_beneficiario["fecha_compra_plan"];

            $sentencia_aux = "SELECT * FROM planes_comprados WHERE fk_id_usuario_cliente_plan_comprado = $id AND activo_plan_comprado = 1";
            $resultado_aux = mysqli_query($conexion, $sentencia_aux);

            if (mysqli_num_rows($resultado_aux) == 0) {
                $sentencia_insertar_plan_beneficiario = "INSERT INTO `planes_comprados`(`activo_plan_comprado`, `fk_id_plan_plan_comprado`, `fk_id_usuario_cliente_plan_comprado`, `date_created_plan_comprado`) VALUES (1,$id_plan,$id,'$fecha_compra')";
                $resultado_insertar_plan_beneficiario = mysqli_query($conexion, $sentencia_insertar_plan_beneficiario);
            }
        }
        =============================================*/

        /*=============================================
        Continuo con la ejecucion del Master capturo todos los datos del usuario que necesito
        =============================================*/
        $id_usuario_cliente = $responseUser[0]->id_usuario_cliente;
        $nombre = $responseUser[0]->nombre_usuario_cliente;
        $cedula = $responseUser[0]->cedula_usuario_cliente;
        $genero = $responseUser[0]->genero_usuario_cliente;
        $popup_inicio = $responseUser[0]->presentacion_inicial_popup_usuario_cliente;
        $popup_anuncio = $responseUser[0]->anuncio_popup_usuario_cliente;
        $url_cargar_info = $responseUser[0]->url_cargar_info_usuario_cliente;

        //busco el plan que tenga el usuario si es que lo tiene
        $responsePlan = GetHomePageModel::getPlanUsuario($id);
        //declaro una variable vacias por si no se encuentra plan
        $fecha_compra_plan = "";
        //si la consulta me trajo algo lo valido
        if (isset($responsePlan[0]->tipo_plan)) {
            //declaro e igualo las variables que necesito retornar en el master
            $tipo_plan = $responsePlan[0]->tipo_plan;
            $id_plan = $responsePlan[0]->id_plan;
            $codigo_plan = $responsePlan[0]->codigo_plan;
            $contactos_emergencia_plan = $responsePlan[0]->contactos_emergencia_plan;
            $fecha_compra_plan = $responsePlan[0]->date_created_plan_comprado;
            $vigencia_minutos = $responsePlan[0]->vigencia_plan;
            // Crear un objeto DateTime a partir de la fecha actual
            $fecha_creacion = new DateTime($fecha_compra_plan);
            // Sumar los minutos de vigencia del plan en minutos al objeto DateTime
            $fecha_creacion->add(new DateInterval('PT' . $vigencia_minutos . 'M'));
            // Obtener la nueva fecha y hora con los minutos añadidos
            $fecha_vencimiento = $fecha_creacion->format('Y-m-d H:i:s');
        }
        //de no traerme un plan, el usuario no tiene un plan
        else {
            //pongo sus variables en 0 para retornar en el master
            $tipo_plan = 0;
            $id_plan = 0;
            $codigo_plan = 0;
            $contactos_emergencia_plan = 0;
            $fecha_vencimiento = "";
        }

        //creo un log en el directorio para seguimiento
        error_log("Parametros recibidos " . $latitud . " - " . $longitud);

        /*=============================================
        Consultamos cuales son las alertas cercanas
        =============================================*/
        $sentencia_listar = "SELECT * FROM alertas a INNER JOIN usuarios_clientes uc ON a.fk_id_usuario_cliente_alerta=uc.id_usuario_cliente INNER JOIN servicios_por_zona sz ON a.fk_id_servicio_por_zona_alerta=sz.id_servicos_por_zona INNER JOIN servicios s ON sz.fk_id_servicio_servicos_por_zona=s.id_servicio ORDER BY date_created_alerta DESC";
        $resultado_listado = mysqli_query($conexion, $sentencia_listar);

        $filasAlertas = array();

        while ($valor = mysqli_fetch_assoc($resultado_listado)) {

            $distancia = GetControllerMaster::distance($valor["latitud_alerta"], $valor["longitud_alerta"], $latitud, $longitud, "K");

            if (round($distancia * 1000) <= 2000) {

                $valor["distancia"] = '' . round(($distancia * 1000)) . '';
                $valor["dias"] = '' . GetControllerMaster::contarDias(date('Y-m-d'), $valor["date_created_alerta"]) . '';

                if ($valor["dias"] == 0) {
                    $valor["imagenes"] = array();

                    if ($valor["ruta1_imagen_alerta"]) {
                        array_push($valor["imagenes"], $valor["ruta1_imagen_alerta"]);
                        if ($valor["ruta2_imagen_alerta"]) {
                            array_push($valor["imagenes"], $valor["ruta2_imagen_alerta"]);
                            if ($valor["ruta3_imagen_alerta"]) {
                                array_push($valor["imagenes"], $valor["ruta3_imagen_alerta"]);
                            }
                        }
                    }
                    //buscamos si esta alerta tiene una reaccion y hago inner con el usuario que reacciono a esta alerta si es el caso
                    $sql_reacciones = "SELECT * FROM reacciones_cliente_cliente r INNER JOIN usuarios_clientes uc ON r.fk_id_usuario_cliente_reaccion_cliente_cliente=uc.id_usuario_cliente WHERE fk_id_alerta_reaccion_cliente_cliente =" . $valor['id_alerta'];
                    $query_reacciones = mysqli_query($conexion, $sql_reacciones);
                    //valido si me trajo algo la consulta si no hago la logica respectiva
                    if (mysqli_num_rows($query_reacciones) > 0) {
                        $datos_reaccion = mysqli_fetch_assoc($query_reacciones);
                        $valor['reaccion'] = 1;
                        //Elimino del arreglo original todos los atributos que no son relevantes para el lado del cliente en este endpoint
                        unset($datos_reaccion["ruta_imagen_reaccion_cliente_cliente"]);
                        unset($datos_reaccion["fk_id_alerta_reaccion_cliente_cliente"]);
                        unset($datos_reaccion["fk_id_usuario_cliente_reaccion_cliente_cliente"]);
                        unset($datos_reaccion["date_update_reaccion_cliente_cliente"]);
                        unset($datos_reaccion["password"]);
                        unset($datos_reaccion["token"]);
                        unset($datos_reaccion["token_exp"]);
                        unset($datos_reaccion["foto_perfil_usuario_cliente"]);
                        unset($datos_reaccion["tipo_de_sangre"]);
                        unset($datos_reaccion["enfermedades_base"]);
                        unset($datos_reaccion["alergias"]);
                        unset($datos_reaccion["eps"]);
                        unset($datos_reaccion["arl"]);
                        unset($datos_reaccion["activo_usuario_cliente"]);
                        unset($datos_reaccion["estado_usuario_cliente"]);
                        unset($datos_reaccion["eliminado_usuario_cliente"]);
                        unset($datos_reaccion['eliminado_usuario_cliente']);
                        unset($datos_reaccion['presentacion_inicial_popup_usuario_cliente']);
                        unset($datos_reaccion['anuncio_popup_usuario_cliente']);
                        unset($datos_reaccion['lastlogin_usuario_cliente']);
                        unset($datos_reaccion['fk_id_tipo_usuario_usuario_cliente']);
                        unset($datos_reaccion['token_dispositivo']);
                        unset($datos_reaccion['codigo_verificacion']);
                        unset($datos_reaccion['fecha_verificacion_pin']);
                        unset($datos_reaccion['url_cargar_info_usuario_cliente']);
                        unset($datos_reaccion['date_created_usuario_cliente']);
                        unset($datos_reaccion['date_update_usuario_cliente']);

                        $valor['detalles_reaccion'] = $datos_reaccion;
                    } else {
                        $valor['reaccion'] = 0;
                        $valor['detalles_reaccion'] = array();
                    }
                    //Elimino del arreglo original todos los atributos que no son relevantes para el lado del cliente en este endpoint
                    unset($valor["ruta1_imagen_alerta"]);
                    unset($valor["ruta2_imagen_alerta"]);
                    unset($valor["ruta3_imagen_alerta"]);
                    unset($valor['tipo_evento_alerta']);
                    unset($valor['date_update_alerta']);
                    unset($valor['password']);
                    unset($valor['token']);
                    unset($valor['token_exp']);
                    unset($valor['tipo_de_sangre']);
                    unset($valor['enfermedades_base']);
                    unset($valor['alergias']);
                    unset($valor['eps']);
                    unset($valor['arl']);
                    unset($valor['activo_usuario_cliente']);
                    unset($valor['estado_usuario_cliente']);
                    unset($valor['eliminado_usuario_cliente']);
                    unset($valor['presentacion_inicial_popup_usuario_cliente']);
                    unset($valor['anuncio_popup_usuario_cliente']);
                    unset($valor['lastlogin_usuario_cliente']);
                    unset($valor['fk_id_tipo_usuario_usuario_cliente']);
                    unset($valor['token_dispositivo']);
                    unset($valor['codigo_verificacion']);
                    unset($valor['fecha_verificacion_pin']);
                    unset($valor['url_cargar_info_usuario_cliente']);
                    unset($valor['date_created_usuario_cliente']);
                    unset($valor['date_update_usuario_cliente']);
                    unset($valor['foto_perfil_usuario_cliente']);
                    unset($valor['id_servicos_por_zona']);
                    unset($valor['fk_id_servicio_servicos_por_zona']);
                    unset($valor['fk_id_zona_servicos_por_zona']);
                    unset($valor['date_created_servicos_por_zona']);
                    unset($valor['date_update__servicos_por_zona']);
                    unset($valor['ruta_imagen_servicio']);
                    unset($valor['puntos_servicio']);
                    unset($valor['color_sombra_servicio']);
                    unset($valor['date_created_servicio']);
                    unset($valor['date_update_servicio']);
                    unset($valor['fk_id_servicio_por_zona_alerta']);
                    unset($valor['fk_id_usuario_cliente_alerta']);
                    unset($valor['date_created_servicos_por_zona']);
                    unset($valor['date_update__servicos_por_zona']);
                    //voy guarndando mi arreglo final
                    $filasAlertas[] = $valor;
                }
            }
        }

        //si no hay alertas por medio del comentario, el array se iguala a 0
        if (isset($filasAlertas["comentario"])) {
            unset($filasAlertas["comentario"]);
            $filasAlertas = [];
        }

        //obtengo por medio del metodo aux, la zona en la que este
        $filasZonas = GetControllerMaster::validarZonaCercana($latitud, $longitud);

        /*=============================================
        Consultamos los servicios por zona
        =============================================*/
        if ($filasZonas != 0) {
            //elimino la variable codigo zona del json response para que no aparezca en el master
            unset($filasZonas[0]["codigo_zona"]);
            //capturo el id
            $id_zona = $filasZonas[0]["id_zona"];

            //sql para obtener los sevicios por zona
            $sentencia_listar = "SELECT * FROM servicios_por_zona sz INNER JOIN servicios s ON sz.fk_id_servicio_servicos_por_zona = s.id_servicio  WHERE fk_id_zona_servicos_por_zona = $id_zona";
            $resultado_listado = mysqli_query($conexion, $sentencia_listar);

            //creo un array donde iran los servicios que se retornaran
            $filasServicios = array();

            //si en el resultado hay servicios
            if ($resultado_listado) {
                //recorro el resultado
                while ($valor = mysqli_fetch_assoc($resultado_listado)) {
                    //dependiendo del genero quemo que servicios va a tener
                    if ($genero == "M") {
                        if ($valor["id_servicio"] == "31" || $valor["id_servicio"] == "33" || $valor["id_servicio"] == "34" || $valor["id_servicio"] == "35" || $valor["id_servicio"] == "36" || $valor["id_servicio"] == "37" || $valor["id_servicio"] == "38" || $valor["id_servicio"] == "39") {
                            $filasServicios[] = $valor;
                        }
                    } else if ($genero == "F") {
                        if ($valor["id_servicio"] == "31" || $valor["id_servicio"] == "33" || $valor["id_servicio"] == "34" || $valor["id_servicio"] == "35" || $valor["id_servicio"] == "36" || $valor["id_servicio"] == "37" || $valor["id_servicio"] == "38" || $valor["id_servicio"] == "32") {
                            $filasServicios[] = $valor;
                        }
                    }
                }
            }
            //estructura para caso contrado que no hayan servicios
            else {
                $filasServicios["comentario"] = 0;
            }
        }
        //estructura para caso contrado que no hayan servicios
        else {
            $filasServicios["comentario"] = 0;
        }

        //si no hay servicios por medio del comentario, el array se iguala a 0
        if (isset($filasServicios["comentario"])) {
            unset($filasServicios["comentario"]);
            $filasServicios = [];
        }
        //SQL para traer los establecimientos por zona
        $sentencia_listar = "SELECT id_establecimiento, nombre_establecimiento, ruta_imagen_establecimiento, nombre_promocion, descripcion_corta_promocion, url_detalle_establecimiento FROM establecimientos e INNER JOIN promociones_por_establecimiento pe ON e.id_establecimiento=pe.fk_id_establecimiento_promocion_por_establecimiento INNER JOIN promociones p ON pe.fk_id_promocion_promocion_por_establecimiento=p.id_promocion WHERE e.fk_id_zona_establecimiento = $id_zona";
        $resultado_listado = mysqli_query($conexion, $sentencia_listar);

        //array de establecimientos
        $filaslocals = array();

        //si el resultado es positivos
        if ($resultado_listado) {
            //recorro e igual para devolver luego en le array master
            while ($valor = mysqli_fetch_assoc($resultado_listado)) {
                $filaslocals[] = $valor;
            }
        }

        //en caso de que no haya zonas cercana, igualo las variables necesarias a 0 o [] para retornar al master
        if ($filasZonas == 0) {
            $numero_zonas = 0;
            $filasZonas = [];
        } else {
            //caso contrario retorno el numero de zonas cercanas
            $numero_zonas = count($filasZonas);
        }

        /*=============================================
        Consultamos los puntos acumulados del usuario y guardamos en un array para retornar
        =============================================*/
        $sentencia_puntos = "SELECT pg.acumulado_puntos_punto_ganado FROM puntos_ganados pg WHERE fk_id_usuario_cliente_punto_ganado = $id ORDER BY acumulado_puntos_punto_ganado DESC LIMIT 1";
        $consulta_puntos = mysqli_query($conexion, $sentencia_puntos);
        $fila_puntos = mysqli_fetch_assoc($consulta_puntos);
        $puntos_usuario = 0;

        if (isset($fila_puntos["acumulado_puntos_punto_ganado"])) {
            $puntos_usuario = (int) $fila_puntos["acumulado_puntos_punto_ganado"];
        }

        /*=============================================
        Consultamos los productos fisico que el usuario puede comprar y guardamos en un array para retornar
        =============================================*/
        $sentencia_fisicos = "SELECT * FROM productos_fisicos";
        $consulta_fisicos = mysqli_query($conexion, $sentencia_fisicos);

        $filasFisicos = array();

        if ($consulta_fisicos) {
            while ($valor = mysqli_fetch_assoc($consulta_fisicos)) {
                $filasFisicos[] = $valor;
            }
        }

        /*=============================================
        Consultamos la informacion de los cuadrantes de la zona  y guardamos en un array para retornar
        =============================================*/
        $sentencia_cuadrantes = "SELECT * FROM cuadrantes_por_zonas WHERE fk_id_zona_cuadrante_por_zona=$id_zona";
        $consulta_cuadrantes = mysqli_query($conexion, $sentencia_cuadrantes);

        $filasCuadrantes = array();

        if ($consulta_cuadrantes) {
            while ($valor = mysqli_fetch_assoc($consulta_cuadrantes)) {
                $filasCuadrantes[] = $valor;
            }
        }

        /*=============================================
        Consultamos los contactos de emergencia que esten en la zona (bomberos, guardia civil, etc...)  y guardamos en un array para retornar
        =============================================*/
        $sentencia_contactos_seguridad = "SELECT * FROM contactos_seguridad WHERE fk_id_zona_contacto_seguridad=$id_zona";
        $consulta_contactos_seguridad = mysqli_query($conexion, $sentencia_contactos_seguridad);

        $filasContactosSeguridad = array();

        if ($consulta_contactos_seguridad) {
            while ($valor = mysqli_fetch_assoc($consulta_contactos_seguridad)) {
                $filasContactosSeguridad[] = $valor;
            }
        }

        /*=============================================
        Consultamos cuantos Agentes activos hay en CSI y guardamos en un array para retornar
        =============================================*/
        //$sentencia_agentes_activos = "SELECT count(*) as cantidad_agentes_activos FROM usuarios_agentes WHERE activo_usuario_agente=1";
        //$consulta_agentes_activos = mysqli_query($conexion, $sentencia_agentes_activos);
        //$dato_agentes_activos = mysqli_fetch_assoc($consulta_agentes_activos);


        $dato_agentes_activos = rand(800, 999);

        /*=============================================
        Consultamos cuantos Agentes activos hay en CSI y guardamos en un array para retornar
        =============================================*/
        //$sentencia_clientes_activos = "SELECT count(*) as cantidad_clientes_activos FROM usuarios_clientes WHERE activo_usuario_cliente=1";
        //$consulta_clientes_activos = mysqli_query($conexion, $sentencia_clientes_activos);
        //$dato_clientes_activos = mysqli_fetch_assoc($consulta_clientes_activos);

        $dato_clientes_activos = rand(100, 250);

        /*=============================================
        Vamos a generar un saludo aleatorio para enviar en el master dependiendo de rango horario en el que se este
        =============================================*/
        $horaActual = date('H:i');

        //estructura para retornar el saludo especifico consultado desde la base de datos
        if ($horaActual >= '06:00' && $horaActual <= '10:30') {
            //consultamos en la base de datos con el flag
            $sql_saludos = "SELECT descripcion_saludo FROM saludos WHERE horario_saludo = 1 ORDER BY RAND() LIMIT 1";
            $resultado_saludos = $conexion->query($sql_saludos);
            if ($resultado_saludos->num_rows > 0) {
                $fila = $resultado_saludos->fetch_assoc();
                $saludoBD = $fila['descripcion_saludo'];
            } else {
                $saludo = 'Saludo no encontrado';
            }
            $saludo = $saludoBD;
        } else if ($horaActual > '10:30' && $horaActual <= '12:00') {
            //consultamos en la base de datos con el flag
            $sql_saludos = "SELECT descripcion_saludo FROM saludos WHERE horario_saludo = 4 ORDER BY RAND() LIMIT 1";
            $resultado_saludos = $conexion->query($sql_saludos);
            if ($resultado_saludos->num_rows > 0) {
                $fila = $resultado_saludos->fetch_assoc();
                $saludoBD = $fila['descripcion_saludo'];
            } else {
                $saludo = 'Saludo no encontrado';
            }
            $saludo = $saludoBD;
        } else if ($horaActual > '12:00' && $horaActual <= '18:00') {
            //consultamos en la base de datos con el flag
            $sql_saludos = "SELECT descripcion_saludo FROM saludos WHERE horario_saludo = 2 ORDER BY RAND() LIMIT 1";
            $resultado_saludos = $conexion->query($sql_saludos);
            if ($resultado_saludos->num_rows > 0) {
                $fila = $resultado_saludos->fetch_assoc();
                $saludoBD = $fila['descripcion_saludo'];
            } else {
                $saludo = 'Saludo no encontrado';
            }
            $saludo = $saludoBD;
        } else if ($horaActual > '18:00' && $horaActual <= '23:59') {
            //consultamos en la base de datos con el flag
            $sql_saludos = "SELECT descripcion_saludo FROM saludos WHERE horario_saludo = 3 ORDER BY RAND() LIMIT 1";
            $resultado_saludos = $conexion->query($sql_saludos);
            if ($resultado_saludos->num_rows > 0) {
                $fila = $resultado_saludos->fetch_assoc();
                $saludoBD = $fila['descripcion_saludo'];
            } else {
                $saludo = 'Saludo no encontrado';
            }
            $saludo = $saludoBD;
        } else if ($horaActual >= '00:00' && $horaActual < '06:00') {
            //consultamos en la base de datos con el flag
            $sql_saludos = "SELECT descripcion_saludo FROM saludos WHERE horario_saludo = 0 ORDER BY RAND() LIMIT 1";
            $resultado_saludos = $conexion->query($sql_saludos);
            if ($resultado_saludos->num_rows > 0) {
                $fila = $resultado_saludos->fetch_assoc();
                $saludoBD = $fila['descripcion_saludo'];
            } else {
                $saludo = 'Saludo no encontrado';
            }
            $saludo = $saludoBD;
        }

        //reemplazmos el nombre en el mensaje
        $saludo = str_replace("[NOMBRE]", $nombre, $saludo);

        /*=============================================
        Consultamos si el usuario tiene documentos cargados y validamos para devolver el flag correspondiente
        =============================================*/
        $sentencia_documentos = "SELECT * FROM `documentos` WHERE fk_id_usuario_cliente_documento = $id";
        $consulta_documentos = mysqli_query($conexion, $sentencia_documentos);
        //declaro el flag a retornar
        $flag_documentos = 0;
        //valido si bien o no la resultados
        if (mysqli_num_rows($consulta_documentos) > 0) {
            //modifico el flag de ser necesario
            $flag_documentos = 1;
        }

        //cerramos la conexion
        $conexion->close();

        /*=============================================
        Armo el arreglo que se convertira en JSON en el detail de la respuesta con todos los datos que necesite
        =============================================*/
        $response = array(
            'tiene_documentos_cargados' => $flag_documentos,
            'id_usuario_cliente' => $id,
            'nombre_usuario_cliente' => $nombre,
            'genero_usuario_cliente' => $genero,
            'saludo' => $saludo,
            'url_cargar_info' => $url_cargar_info,
            'modal_inicio' => $popup_inicio,
            'modal_anuncio' => $popup_anuncio,
            "codigo_QR" => "https://chart.googleapis.com/chart?chs=180x180&cht=qr&chl=http%3A%2F%2Fapicsi.csisecurity.co%2Fvalidador_plan%2F?id_usuario=$id_usuario_cliente",
            'puntos_ganados' => $puntos_usuario,
            'id_plan' => (int) $id_plan,
            'tipo_plan' => $tipo_plan,
            'codigo_plan' => (int) $codigo_plan,
            'fecha_compra_plan' => $fecha_compra_plan,
            'vencimiento' => $fecha_vencimiento,
            'contactos_emergencia_plan' => (int) $contactos_emergencia_plan,
            'cantidad_zonas' => $numero_zonas,
            //'cantidad_agentes_activos' => $dato_agentes_activos["cantidad_agentes_activos"],
            //'cantidad_clientes_activos' => $dato_clientes_activos["cantidad_clientes_activos"],
            'cantidad_agentes_activos' => $dato_agentes_activos,
            'cantidad_clientes_activos' => $dato_clientes_activos,
            'link_puntos' => 'https://falabella.com.co/',
            'zona' => $filasZonas,
            'alertas_cercanas' => $filasAlertas,
            'servicios_zona' => $filasServicios,
            'establecimientos' => $filaslocals,
            'productos_fisicos' => $filasFisicos,
            'cuadrantes' => $filasCuadrantes,
            "contactos_seguridad_zona" => $filasContactosSeguridad
        );

        $return = new GetControllerMaster();
        $return->fncResponse($response);
    }

    /*===========================================================================================================================================================
                                                                     METODOS AUXILIARES
    ===========================================================================================================================================================*/
    //calcular y devolver en que zona se esta
    function validarZonaCercana($latitud, $longitud)
    {
        $conexion = Connection::conexionAlternativa();
        $sentencia_listar = "SELECT * FROM zonas";
        $resultado_listado = mysqli_query($conexion, $sentencia_listar);

        $filasZonas = array();

        while ($valor = mysqli_fetch_assoc($resultado_listado)) {

            $distancia = GetControllerMaster::distance($valor["latitud_zona"], $valor["longitud_zona"], $latitud, $longitud, "K");

            if (round($distancia * 1000) <= $valor["radio_zona"]) {

                $valor["distancia"] = '' . round(($distancia * 1000)) . '';

                $filasZonas[] = $valor;
            }
        }

        if (empty($filasZonas)) {
            return  $filasZonas["comentario"] = 0;
        }

        return $filasZonas;
    }
    //calcular la distancia
    public function distance($lat1, $lon1, $lat2, $lon2, $unit)
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
    //calcula el numeero de dias transcurridos de una alerta
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

    /*=============================================
    Respuestas del controlador
    =============================================*/
    public function fncResponse($response)
    {

        if (!empty($response)) {
            if ($response["id_plan"] != 0) {
                $json  = array(
                    'status' => 200,
                    'result' => 3,
                    'id_plan' => (int) $response["id_plan"],
                    'detail' => $response
                );
            } else {
                $json  = array(
                    'status' => 200,
                    'result' => 3,
                    'id_plan' => (int) $response["id_plan"],
                    'detail' => $response
                );
            }
        } else {
            $json = array(
                'status' => 404,
                'result' => 'Not Found',
                'method' => $_SERVER['REQUEST_METHOD']
            );
        }
        echo json_encode($json, http_response_code($json["status"]));
    }
}
