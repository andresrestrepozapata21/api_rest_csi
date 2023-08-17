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
        $response = GetModel::getDataFilter("contactos", "id_contacto, email_contacto, telefono_contacto", "telefono_contacto", $data->telefono_contacto);

        //si el telefono efectivamente no esta en base de datos podemos registrarlo
        if (empty($response)) {
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
