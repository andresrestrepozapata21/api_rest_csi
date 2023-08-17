<?php
//seteo la zona horaria
date_default_timezone_set('America/Bogota');
//requiro los scripts que necesito
require_once "models/get.filter.model.php";
require_once "models/connection.php";

//nombro la clase
class GetController
{

    /*=============================================
    este metodo es reutilizable para obtener informacion, es solo validar que datos me esta entregando el Servicio y como quedaria armado el modelo
    =============================================*/
    public function getData($table, $select, $data, $id)
    {

        $response = GetModel::getDataFilter($table, $select, $id, $data->$id);

        $return = new GetController();
        $return->fncResponse($response);
    }

    /*=============================================
    metodo para obtener los datos de los viajes solo viajes
    =============================================*/
    public function getDataTrip($table, $select, $data, $id)
    {

        $response = GetModel::getDataFilterTrips($table, $select, $id, $data->$id);

        $return = new GetController();
        $return->fncResponse($response);
    }

    /*=============================================
    metodo para traer todos los datos del viaje con paradas y fotos
    =============================================*/
    public function getTrip($table, $select, $data, $id)
    {
        $conexion = Connection::conexionAlternativa();

        $response_viaje = GetModel::getTrip($table, $select, $id, $data->$id);

        /*=============================================
        Consultamos los contactos de emergencia que esten en la zona (bomberos, guardia civil, etc...)
        =============================================*/
        $sql_paradas = "SELECT * FROM paradas WHERE fk_id_viaje_parada=" . $data->$id;
        $consulta_parada = mysqli_query($conexion, $sql_paradas);

        $filas_paradas = array();

        if ($consulta_parada) {
            while ($valor = mysqli_fetch_assoc($consulta_parada)) {
                $filas_paradas[] = $valor;
            }
        }

        /*=============================================
        Consultamos los contactos de emergencia que esten en la zona (bomberos, guardia civil, etc...)
        =============================================*/
        $sql_fotos = "SELECT * FROM registros_fotograficos_viajes WHERE fk_id_viaje_registro_fotografico_viaje =" . $data->$id;
        $consulta_fotos = mysqli_query($conexion, $sql_fotos);

        $filas_fotos = array();

        if ($consulta_fotos) {
            while ($valor = mysqli_fetch_assoc($consulta_fotos)) {
                $filas_fotos[] = $valor;
            }
        }

        $response = array(
            'informacion_viaje' => $response_viaje,
            'paradas_viaje' => $filas_paradas,
            'fotos_viaje' => $filas_fotos,
        );

        $return = new GetController();
        $return->fncResponse($response);
    }

    /*=============================================
    metodo para los planes existentes en base de datos
    =============================================*/
    public function getDataPlanExistente($table, $data)
    {

        $response = GetModel::getDataFilterPlanExistente($table, $data);

        $return = new GetController();
        $return->fncResponse($response);
    }

    /*=============================================
    metodo para traer los establecimientos de la zona
    =============================================*/
    public function getCodeZone($data)
    {
        $codigo_ingresado = $data->codigo_zona;
        $conexion = Connection::conexionAlternativa();

        $sentencia_listar = "SELECT * FROM codigos_zonas WHERE fk_id_zona_codigo_zona = $data->id_zona";
        $resultado_listado = mysqli_query($conexion, $sentencia_listar);

        $codigo_valido = false;
        while ($valor = mysqli_fetch_assoc($resultado_listado)) {
            if ($codigo_ingresado == $valor["codigo_zona"]) {
                $fecha_fin_codigo_zona = new DateTime($valor["vencimiento_codigo_zona"]);
                $timestamp = $fecha_fin_codigo_zona->format("U");
                $time = time();
                if ($time < $timestamp) {

                    $fecha = date('Y-m-d H:i:s');
                    $sentencia_plan_codigo_zona = "INSERT INTO `planes_comprados`(`activo_plan_comprado`, `fk_id_plan_plan_comprado`, `fk_id_usuario_cliente_plan_comprado`, `date_created_plan_comprado`) VALUES (1,19,$data->id_usuario_cliente,'$fecha')";
                    $consulta_plan_codigo_zona = mysqli_query($conexion, $sentencia_plan_codigo_zona);

                    $codigo_valido = true;
                }
            }
        }

        if ($codigo_valido) {
            $response = array(
                'codigo' => 3
            );

            $return = new GetController();
            $return->fncResponse($response);
        } else {
            $response = array(
                'code' => 19
            );
            $return = new GetController();
            $return->fncResponse($response);
        }
    }

    /*=============================================
    metodo para las pocisiones de los clientes o agentes cercanos a la zona en la que se esta, agentes o clientes depende de que nombre de tabla se este ingresando, este metodo es reutilizado
    =============================================*/
    public function getClosePosition($table, $suffix, $table2, $suffix2, $data)
    {
        $latitud = $data->latitud;
        $longitud = $data->longitud;

        /*=============================================
        Consultamos las posiciones cercanas
        =============================================*/
        $conexion = Connection::conexionAlternativa();
        //$sentencia_listar = "SELECT id_$suffix, id_dispositivo_$suffix, id_$suffix2, cedula_$suffix2, nombre_$suffix2, apellido_$suffix2, telefono_$suffix2, email, date_created_$suffix,latitud_$suffix, longitud_$suffix, activo_$suffix2 FROM $table p INNER JOIN $table2 u ON u.id_$suffix2=p.fk_id_$suffix2" . "_$suffix WHERE activo_$suffix2 = 1";
        $sentencia_listar = "SELECT id_$suffix, id_dispositivo_$suffix, id_$suffix2, cedula_$suffix2, nombre_$suffix2, apellido_$suffix2, telefono_$suffix2, email, foto_perfil_$suffix2, lastlogin_$suffix2 ,date_created_$suffix, latitud_$suffix, longitud_$suffix, activo_$suffix2 FROM $table p INNER JOIN $table2 u ON u.id_$suffix2=p.fk_id_$suffix2" . "_$suffix WHERE activo_$suffix2 = 1";
        $resultado_listado = mysqli_query($conexion, $sentencia_listar);
        $filasPosiciones = array();

        while ($valor = mysqli_fetch_assoc($resultado_listado)) {

            $distancia = GetController::distance($valor["latitud_$suffix"], $valor["longitud_$suffix"], $latitud, $longitud, "K");

            if ($distancia <= 100) {
                if (round($distancia * 1000) <= 1000) {

                    $valor["distancia"] = '' . round(($distancia * 1000)) . '';
                    $valor["minutos"] = '' . GetController::contarMinutos(date('Y-m-d H:i:s'), $valor["date_created_$suffix"]) . '';

                    if ($valor["minutos"] <= 60) {
                        $filasPosiciones[] = $valor;
                    }
                }
            }
        }

        $response = array(
            'posiciones_cercanas' => $filasPosiciones
        );

        $return = new GetController();
        $return->fncResponse($response);
    }

    /*=============================================
    metodo para las pocisiones de los clientes o agentes cercanos a la zona en la que se esta, agentes o clientes depende de que nombre de tabla se este ingresando, este metodo es reutilizado
    =============================================*/
    public function getClosePositionChief($table, $suffix, $table2, $suffix2, $data)
    {
        //intancia de la conexion
        $conexion = Connection::conexionAlternativa();

        $latitud = $data->latitud;
        $longitud = $data->longitud;
        $count = 50;

        $result = array();

        for ($i = 0; $i < $count; $i++) {
            // Consultar un nombre aleatorio de la tabla nombres_agentes_fake
            $consultaNombre = "SELECT nombre FROM nombres_agentes_fake ORDER BY RAND() LIMIT 1";
            $resultadoNombre = mysqli_query($conexion, $consultaNombre);

            // Verificar si se encontró un resultado para el nombre
            if (mysqli_num_rows($resultadoNombre) > 0) {
                $filaNombre = mysqli_fetch_assoc($resultadoNombre);
                $nombreAleatorio = $filaNombre['nombre'];
            }
            // Consultar un apellido aleatorio de la tabla nombres_agentes_fake
            $consultaApellido = "SELECT apellido FROM nombres_agentes_fake ORDER BY RAND() LIMIT 1";
            $resultadoApellido = mysqli_query($conexion,$consultaApellido);
            // Verificar si se encontró un resultado para el apellido
            if (mysqli_num_rows($resultadoApellido) > 0) {
                $filaApellido = mysqli_fetch_assoc($resultadoApellido);
                $apellidoAleatorio = $filaApellido['apellido'];
            }
            //defino mis coordenadas aleatorias con un rango de 5 KM
            $randomLat = $latitud + (mt_rand(-900, 900) / 100000);
            $randomLng = $longitud + (mt_rand(-900, 900) / 100000);
            //defino las coordenadas en un array coordinate
            $coordinate = array(
                'lat' => $randomLat,
                'lng' => $randomLng,
            );
            //defino las variables adicionales que necestio
            $title = 'Agente CSI '. $nombreAleatorio . ' ' . $apellidoAleatorio;
            $snippet = 'Vigilando Zona';
            $iconUrl = '/assets/pointer.png';
            $iconSize = array(
                'width' => 48,
                'height' => 55,
            );
            //armo mi objeto en la respectiva iteracion
            $object = array(
                'coordinate' => $coordinate,
                'title' => $title,
                'snippet' => $snippet,
                'iconUrl' => $iconUrl,
                'iconSize' => $iconSize,
            );
            //en cada iteracin voy guardando cada objeto en un array principal y este es el que se retorna
            $result[] = $object;
        }
        //instacion mi class get controller y llamo mi petodo para retornar la respueta en el JSON
        $return = new GetController();
        $return->fncResponse($result);
    }

    /*=============================================
    metodo para obtener establecimiento
    =============================================*/
    public function getLocal($data)
    {

        /*=============================================
        Consultamos en que zona estamos
        =============================================*/
        $conexion = Connection::conexionAlternativa();
        $sentencia_listar = "SELECT id_establecimiento, nombre_establecimiento, ruta_imagen_establecimiento, nombre_promocion, descripcion_promocion, puntos_promocion FROM establecimientos e INNER JOIN promociones_por_establecimiento pe ON e.id_establecimiento=pe.fk_id_establecimiento_promocion_por_establecimiento INNER JOIN promociones p ON pe.fk_id_promocion_promocion_por_establecimiento=p.id_promocion WHERE e.id_establecimiento = $data->id_establecimiento LIMIT 1";

        $resultado_listado = mysqli_query($conexion, $sentencia_listar);

        $response = mysqli_fetch_assoc($resultado_listado);

        $return = new GetController();
        $return->fncResponse($response);
    }

    /*=============================================
    metodo para obtener servicios por zona
    =============================================*/
    public function getServicesPerZone($data)
    {
        //Consultamos en que zona estamos
        $conexion = Connection::conexionAlternativa();
        $sentencia_listar = "SELECT id_servicos_por_zona, id_servicio, descripcion_servicio, ruta_imagen_servicio, puntos_servicio, color_sombra_servicio FROM servicios_por_zona sz INNER JOIN servicios s ON sz.fk_id_servicio_servicos_por_zona = s.id_servicio  WHERE fk_id_zona_servicos_por_zona = $data->id_zona";
        $resultado_listado = mysqli_query($conexion, $sentencia_listar);

        $filasServicios = array();

        while ($valor = mysqli_fetch_assoc($resultado_listado)) {
            $filasServicios[] = $valor;
        }

        $response = $filasServicios;

        $return = new GetController();
        $return->fncResponse($response);
    }

    /*=============================================
    metodo para obtener alertas por usuario cliente
    =============================================*/
    public function getAlertsCostumer($data)
    {
        /*=============================================
        Consultamos las alertas que tienes la foranea del usuario
        =============================================*/
        $conexion = Connection::conexionAlternativa();
        $sentencia_listar = "SELECT a.id_alerta, a.latitud_alerta, a.longitud_alerta, a.estado_alerta, a.comentario_alerta, a.ruta1_imagen_alerta, a.ruta2_imagen_alerta, a.ruta3_imagen_alerta, a.ruta_video_alerta, a.date_created_alerta, sz.id_servicos_por_zona, s.id_servicio, s.descripcion_servicio FROM alertas a INNER JOIN servicios_por_zona sz ON a.fk_id_servicio_por_zona_alerta=sz.id_servicos_por_zona INNER JOIN servicios s ON sz.fk_id_servicio_servicos_por_zona=s.id_servicio WHERE a.fk_id_usuario_cliente_alerta = $data->fk_id_usuario_cliente_alerta ORDER BY a.date_created_alerta DESC";
        $resultado_listado = mysqli_query($conexion, $sentencia_listar);

        $filasAlertasCostumer = array();
        while ($valor = mysqli_fetch_assoc($resultado_listado)) {
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
            unset($valor["ruta1_imagen_alerta"]);
            unset($valor["ruta2_imagen_alerta"]);
            unset($valor["ruta3_imagen_alerta"]);
            $filasAlertasCostumer[] = $valor;
        }

        $response = $filasAlertasCostumer;

        $return = new GetController();
        $return->fncResponse($response);
    }

    /*=============================================
    metodo para obtener alertas por zona agregando la distancia y los dias transcurridos, si esta alerta lleva mas de un dia no se retorna
    =============================================*/
    public function getAlertsZone($data)
    {
        //Guardamos el id de la zona
        $latitud = $data->latitud;
        $longitud = $data->longitud;

        /*=============================================
        Consultamos cuales son las alertas cercanas
        =============================================*/
        $conexion = Connection::conexionAlternativa();
        $sentencia_listar = "SELECT * FROM alertas a INNER JOIN usuarios_clientes uc ON a.fk_id_usuario_cliente_alerta=uc.id_usuario_cliente INNER JOIN servicios_por_zona sz ON a.fk_id_servicio_por_zona_alerta=sz.id_servicos_por_zona INNER JOIN servicios s ON sz.fk_id_servicio_servicos_por_zona=s.id_servicio ORDER BY date_created_alerta DESC";
        $resultado_listado = mysqli_query($conexion, $sentencia_listar);

        $filasAlertas = array();

        while ($valor = mysqli_fetch_assoc($resultado_listado)) {

            $distancia = GetController::distance($valor["latitud_alerta"], $valor["longitud_alerta"], $latitud, $longitud, "K");

            if (round($distancia * 1000) <= 2000) {

                $valor["distancia"] = '' . round(($distancia * 1000)) . '';
                $valor["dias"] = '' . GetController::contarDias(date('Y-m-d'), $valor["date_created_alerta"]) . '';

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

        if (empty($filasAlertas)) {
            $response = array(
                'code' => 26
            );
        } else {
            $response = $filasAlertas;
        }

        $return = new GetController();
        $return->fncResponse($response);
    }

    /*=============================================
    Consultamos si es usuario activo o no con base al nuemo de telefono
    =============================================*/
    public function getValideCostumer($table, $data)
    {
        //conexion a la base de datos
        $conexion = Connection::conexionAlternativa();

        $sql = "SELECT * FROM $table WHERE telefono_usuario_cliente='$data->phone_number' AND activo_usuario_cliente = 1";
        $query = mysqli_query($conexion, $sql);

        if (mysqli_num_rows($query) > 0) {
            $datos = mysqli_fetch_assoc($query);
            $id_usuario_cliente = $datos["id_usuario_cliente"];
            $token_vigente = $datos["token"];
            /*=============================================
            Armo el arreglo que se convertira en JSON en el detail de la respuesta
            =============================================*/
            $response = array(
                "flag_be" => 1,
                'id_usuario_cliente' => (int) $id_usuario_cliente,
                'token' => $token_vigente
            );
        } else {
            $response = array(
                "flag_be" => 0
            );
        }
        $return = new GetController();
        $return->fncResponse($response);
    }

    /*=============================================
    metodo para obtener la zona en la que se esta co base a sus coordenadas y que servicios se tiene para esta zona
    =============================================*/
    public function getZone($data)
    {
        $latitud = $data->latitud;
        $longitud = $data->longitud;

        $filasZonas = GetController::validarZonaCercana($latitud, $longitud);

        if (!isset($filasZonas["comentario"])) {

            $id_zona = $filasZonas[0]["id_zona"];

            $conexion = Connection::conexionAlternativa();
            $sentencia_listar = "SELECT id_servicos_por_zona, id_servicio, descripcion_servicio, ruta_imagen_servicio, puntos_servicio, color_sombra_servicio FROM servicios_por_zona sz INNER JOIN servicios s ON sz.fk_id_servicio_servicos_por_zona = s.id_servicio  WHERE fk_id_zona_servicos_por_zona = $id_zona";
            $resultado_listado = mysqli_query($conexion, $sentencia_listar);

            $filasServicios = array();

            if ($resultado_listado) {
                while ($valor = mysqli_fetch_assoc($resultado_listado)) {
                    $filasServicios[] = $valor;
                }
            } else {
                $filasServicios["comentario"] = 0;
            }
        } else {
            $filasServicios["comentario"] = 0;
        }

        $response = array(
            'zona' => $filasZonas,
            'servicios_zona' => $filasServicios,
        );

        $return = new GetController();
        $return->fncResponse($response);
    }

    /*=============================================
    metodo para obtener los puntos acumulados del usuario por usar las alertas
    =============================================*/
    public function getPointsUser($table, $data)
    {
        $conexion = Connection::conexionAlternativa();
        /*=============================================
        Consultamos los puntos acumulados del usuario
        =============================================*/
        $sentencia_puntos = "SELECT pg.acumulado_puntos_punto_ganado FROM $table pg WHERE fk_id_usuario_cliente_punto_ganado = $data->id_usuario ORDER BY acumulado_puntos_punto_ganado DESC LIMIT 1";
        $consulta_puntos = mysqli_query($conexion, $sentencia_puntos);
        $fila_puntos = mysqli_fetch_assoc($consulta_puntos);
        $puntos_usuario = 0;

        if (isset($fila_puntos["acumulado_puntos_punto_ganado"])) {
            $puntos_usuario = (int) $fila_puntos["acumulado_puntos_punto_ganado"];
        }

        /*=============================================
        Armo el arreglo que se convertira en JSON en el detail de la respuesta
        =============================================*/
        $response = array(
            'puntos_ganados' => $puntos_usuario
        );

        $return = new GetController();
        $return->fncResponse($response);
    }

    /*=============================================
    metodo para obtener el vencimiento del plan del usuario
    =============================================*/
    public function expirationPlanUser($data)
    {
        $conexion = Connection::conexionAlternativa();
        /*=============================================
        Consultamos los puntos acumulados del usuario
        =============================================*/
        $sentencia = "SELECT * FROM planes_comprados pc INNER JOIN planes p ON p.id_plan=pc.fk_id_plan_plan_comprado WHERE fk_id_usuario_cliente_plan_comprado = $data->id_usuario_cliente AND activo_plan_comprado = 1";
        $consulta = mysqli_query($conexion, $sentencia);
        $puntos_usuario = 0;

        if (mysqli_num_rows($consulta) > 0) {
            $fila = mysqli_fetch_assoc($consulta);
            //capturo las variables que necesito
            $fecha_compra_plan = $fila["date_created_plan_comprado"];
            $vigencia_minutos = $fila["vigencia_plan"];
            // Crear un objeto DateTime a partir de la fecha actual
            $fecha_creacion = new DateTime($fecha_compra_plan);
            // Sumar los minutos de vigencia del plan en minutos al objeto DateTime
            $fecha_creacion->add(new DateInterval('PT' . $vigencia_minutos . 'M'));
            // Obtener la nueva fecha y hora con los minutos añadidos
            $fecha_vencimiento = $fecha_creacion->format('Y-m-d H:i:s');

            /*=============================================
            Armo el arreglo que se convertira en JSON en el detail de la respuesta
            =============================================*/
            $response = array(
                'fecha_vencimiento' => $fecha_vencimiento
            );

            $return = new GetController();
            $return->fncResponse($response);
        } else {
            $response = array(
                'code' => 16
            );

            $return = new GetController();
            $return->fncResponse($response);
        }
    }

    /*=============================================
    metodo para obtener el vencimiento del plan del usuario
    =============================================*/
    public function validateDocumentsCustomer($data)
    {
        //Instancio la conexcion a la base de datos
        $conexion = Connection::conexionAlternativa();
        //capturo el ID del usuario que viene en la peticion
        $id = $data->fk_id_usuario_cliente_alerta;
        /*=============================================
        Consultamos los documentos de ese usuario
        =============================================*/
        $sentencia_documentos = "SELECT * FROM `documentos` WHERE fk_id_usuario_cliente_documento = $id";
        $consulta_documentos = mysqli_query($conexion, $sentencia_documentos);
        //declaro el flag a retornar
        $flag_documentos = 0;
        //valido si bien o no la resultados y modifico el flag de ser necesario (valido si el usurio tiene documentos cargados)
        if (mysqli_num_rows($consulta_documentos) > 0) {
            //modifico el flag de ser necesario
            $flag_documentos = 1;
        }
        //Defino la respuesta
        $response = array(
            'tiene_documentos_cargados' => $flag_documentos
        );
        //Llamo el metodo que va a retornar el JSON RESPONSE
        $return = new GetController();
        $return->fncResponse($response);
    }

    /*===========================================================================================================================================================
                                                                     METODOS AUXILIARES
    ===========================================================================================================================================================*/
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
    //contar los munitos transcurridos de una alerta
    function contarMinutos($fecha1, $fecha2)
    {
        $startTimeStamp = strtotime($fecha1);
        $endTimeStamp = strtotime($fecha2);
        $timeDiff = abs($endTimeStamp - $startTimeStamp);
        $numberDays = $timeDiff / 60;  // 60 seconds in one minute
        // and you might want to convert to integer
        $numberDays = intval($numberDays);
        return $numberDays;
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
    //calcular y devolver en que zona se esta
    function validarZonaCercana($latitud, $longitud)
    {
        $conexion = Connection::conexionAlternativa();
        $sentencia_listar = "SELECT * FROM zonas";
        $resultado_listado = mysqli_query($conexion, $sentencia_listar);

        $filasZonas = array();

        while ($valor = mysqli_fetch_assoc($resultado_listado)) {

            $distancia = GetController::distance($valor["latitud_zona"], $valor["longitud_zona"], $latitud, $longitud, "K");

            if ($distancia <= 100) {
                if (round($distancia * 1000) <= $valor["radio_zona"]) {

                    $valor["distancia"] = '' . round(($distancia * 1000)) . '';

                    $filasZonas[] = $valor;
                }
            }
        }

        if (empty($filasZonas)) {
            return  $filasZonas["comentario"] = 0;
        }

        return $filasZonas;
    }

    /*=============================================
    Respuestas del controlador
    =============================================*/
    public function fncResponse($response)
    {

        if (!empty($response)) {
            if (isset($response["code"])) {
                $json  = array(
                    'status' => 200,
                    'result' => $response["code"],
                );
            } else {
                $json  = array(
                    'status' => 200,
                    'result' => 3,
                    'detail' => $response
                );
            }
        } else {
            $json = array(
                'status' => 200,
                'result' => 4,
                'method' => $_SERVER['REQUEST_METHOD']
            );
        }
        echo json_encode($json, http_response_code($json["status"]));
    }
}
