<?php
/**
 * @author Pedro Gabriel
 * Projeto criado com a finalidade de acessar o portal do aluno da UNICAP (http://www.unicap.br/PortalGraduacao/) e retornar um JSON com os dados do Aluno,
 * Arquivo inicial do projeto. Irá contruir e importar as funções e exibir o json.
 * @return json
 */

define('ROOTPATH', __DIR__);


$loader = require 'vendor/autoload.php';

try
{



    $core = new Unicap\Webservice\Common\Core();

    $core->verifyMethod();
    $core->verifyPostParams();

    $request = new Unicap\Webservice\Server\Request();
    $request->login($core->getMat(), $core->getPass());

    $core->setUserData($request->getUserData());
    $core->setMatterData($request->getMatterData());
    // $core->setDockets($request->getDocketsData());
    $core->setToCourseMatters($request->getToCourseMatters($core->getMattersCodes()));
    $core->setCoursedMatters($request->getCoursedMatters());


    Unicap\Webservice\Helper\JsonResult::success($core->getFullData());
}
catch (Unicap\DataSource\Exceptions\FileException $e)
{
}
catch (Exception $e)
{

    $log = new Unicap\DataSource\Files\LogTxt("exception");

    $log->putContent($e->getMessage().$e->getCode().$e->getLine().$e->getFile());
    $log->create();
}
