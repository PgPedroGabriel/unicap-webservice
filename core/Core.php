<?php

/**
 * @author Pedro Gabriel
*  Valida a requisição feita
*  Não estabelecida as regras lançará exceção e acabará execução
 * @method POST
 * @param matricula (int), senha (int)
 * @return core object
 */

class Core
{

    private $mat;
    private $pass;
    private $userData;
    private $matterData;
    private $dockets;
    private $docketVia = null;

    /**
    * Verify if is a post method
    * trhow new exception if not is a post method.
    */
    public function verifyMethod(){
        if($_SERVER['REQUEST_METHOD'] != "POST")
            JsonResult::error("Metodo de requisição inválido");
    }


    /**
    * Verify if the post params is in rules.
    * trhow new exception if not in rules.
    */
    public function verifyPostParams(){

        $mat = @$_POST['matricula'];
        $pass = @$_POST['senha'];

        if(empty($mat) || empty($pass))
            JsonResult::error();
        else{

            $matIsNumber = (int) $mat;
            $passIsNumber = (int) $pass;

            if(!$matIsNumber || !$passIsNumber)
                JsonResult::error();

            $matHas9 = strlen($mat);

            if($matHas9 != 9)
                JsonResult::error();

            $this->mat = $mat;
            $this->pass = $pass;
        }
    }

    public function verifyPostParamsDocket()
    {
        self::verifyPostParams();

        $docketVia = @$_POST['docket_via'];

        if(empty($docketVia))
            JsonResult::error();
        else{

            $docketVia = (int)$docketVia;

            if($docketVia < 1 || $docketVia > 12)
                JsonResult::error("Numero da parcela inválido");

            $this->_setDocketVia($docketVia);
            return;
        }
    }

    public function getMat()
    {
        return $this->mat;
    }

    public function getPass()
    {
        return $this->pass;
    }

    public function addData($index, $data)
    {
        $this->data[$index] = $data;
    }

    public function setUserData($data)
    {
        $this->userData = $data;
    }

    public function setMatterData($data)
    {
        $this->matterData = $data;
    }

    public function setDockets($value='')
    {
        $this->dockets = $value;
        return $this;
    }

    public function getDocketVia()
    {
        return $this->docketVia;
    }

    private function _setDocketVia($value = null)
    {
        if(!empty($value) && $value > 1 && $value < 13)
            $this->docketVia = $value;

        return $this;
    }

    public function getDockets()
    {
        return $this->dockets;
    }

    public function getFullData()
    {
        return array('userData' => $this->userData, 'matterData' => $this->matterData, 'docketsData' => $this->dockets) ;
    }
}