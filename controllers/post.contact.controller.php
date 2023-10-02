<?php
//requiro los scripts que necesito
require_once "models/connection.php";
require_once "models/post.contact.model.php";
require_once "models/get.filter.model.php";

//nombro la clase
class PostController
{
    /*=============================================
    Peticion post para crear un contacto de emergencia al cliente
    =============================================*/
    static public function postRegister($data)
    {

        //Sanitizamos el telefono del usuario para que se normalice si tiene caracteres especiales como "(",")","-" o espacios en blanco
        $contacto_contacto = $data->telefono_contacto;
        $contacto_contacto = str_replace("(", "", $contacto_contacto);
        $contacto_contacto = str_replace(")", "", $contacto_contacto);
        $contacto_contacto = str_replace("-", "", $contacto_contacto);
        $contacto_contacto = str_replace(" ", "", $contacto_contacto);
        $contacto_contacto = PostController::validarYCorregirNumero($contacto_contacto);
        $data->telefono_contacto = $contacto_contacto;
        /*=============================================
        Validamos que el telefono No exista en base de datos
        =============================================*/
        //instacion la conexion a la BD
        $conexion = Connection::conexionAlternativa();
        /*=============================================
        Consultamos cuales son las alertas cercanas
        =============================================*/
        $sentencia_existente = "SELECT * FROM `contactos` WHERE telefono_contacto LIKE '$data->telefono_contacto' AND fk_id_usuario_cliente_contacto  = $data->fk_id_usuario_cliente_contacto";
        $resultado_existente = mysqli_query($conexion, $sentencia_existente);
        //si el telefono efectivamente no esta en base de datos podemos registrarlo
        if ($resultado_existente && mysqli_num_rows($resultado_existente) < 1) {
            //llamo el modelo para registrar
            $response = PostModel::postData($data);
            //retorno el JSON response
            $return = new PostController();
            $return->fncResponse($response);
        } else {
            //en caso contrario devuelvo el codigo 10 que significa que el telefono ya existe
            $response = array(
                "code" => 10
            );
            $return = new PostController();
            $return->fncResponse($response);
        }
    }

    /*=============================================
                METODOS AUXILIARES
    =============================================*/
    function validarYCorregirNumero($numero)
    {
        // Expresión regular para validar si el número empieza con un indicativo
        $pattern = "/^\+\d{1,4}/";

        if (!preg_match($pattern, $numero)) {
            // Si no tiene indicativo, agregamos el de Colombia
            $numero = '+57' . $numero;
        }

        return $numero;
    }

    /*=============================================
    Respuestas del controlador
    =============================================*/
    public function fncResponse($response)
    {
        if (!empty($response)) {
            if ($response['code'] == 3) {
                $json  = array(

                    'status' => 200,
                    'result' => $response["code"],
                    'method' => $_SERVER['REQUEST_METHOD']
                );
            } else {
                $json = array(
                    'status' => 200,
                    'result' => $response['code'],
                    'method' => $_SERVER['REQUEST_METHOD']
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
