<?php
/**
 * @author Pedro Gabriel
*  Valida a requisição feita
*  Não estabelecida as regras lançará exceção e acabará execução
 * Carrega os objetos excenciais do projeto
 * @method Post
 * @param matricula (int), senha (int)
 * @return core variable
 */
function __autoload($class_name) {
    require_once $class_name . '.php';
}

class Core
{

    private $mat;
    private $pass;

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

    public function getMat()
    {
        return $this->mat;
    }

    public function getPass()
    {
        return $this->pass;
    }
}


$core = new Core();

try{

    $core->verifyMethod();
    $core->verifyPostParams();

    $request = new Request();
    $request->login($core->getMat(), $core->getPass());

} catch (Exception $e) {
    return;
}