<?php
/**
* @author Pedro Gabriel
* @return Classe abstrata com métodos estaticos para erro ou sucesso em Json
*/
abstract class JsonResult
{

    static function error($message = "Parametros inválidos."){
        $result = new stdClass();
        $result->status = false;
        $result->message = $message;
        $result->data = array();
        self::printJson($result);
    }

    static function success($data, $message = "Sucesso"){
       $result = new stdClass();
       $result->status = true;
       $result->message = $message;
       $result->data = $data;
       self::printJson($result);
    }

    static function printJson($result){
        header('Content-type: application/json; charset=utf-8');
        echo json_encode($result);
        exit(0);
    }
}