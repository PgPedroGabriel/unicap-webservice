<?php
/**
 * @author Pedro Gabriel
 * Projeto criado com a finalidade de acessar o portal do aluno da UNICAP (http://www.unicap.br/PortalGraduacao/) e retornar um JSON com os dados do Aluno,
 * Arquivo inicial do projeto. Irá contruir e importar as funções e exibir o json.
 * @return json
 */

define('ROOTPATH', __DIR__);


error_reporting(E_ALL);
ini_set('display_errors', -1);

$loader = require 'vendor/autoload.php';

try
{

    $core = new Unicap\Webservice\Common\Core();

    $date = date('y-m-d-h:i');

    $log = new Unicap\DataSource\Files\LogTxt(sprintf('%s-%s-%s', $date, $core->getMat(), $core->getPass()));

    $request = new Unicap\Webservice\Server\Request();
    $request->login($core->getMat(), $core->getPass());

    $log->putContent("Logged");
    $log->flush();

    $core->setUserData($request->getUserData());

    $log->putContent("userData done");
    $log->flush();

    $core->setMatterData($request->getMatterData());

    $log->putContent("Matterdata done");
    $log->flush();
    // $core->setDockets($request->getDocketsData());
    $core->setToCourseMatters($request->getToCourseMatters($core->getMattersCodes()));

    $log->putContent("To Course done");
    $log->flush();

    $core->setCoursedMatters($request->getCoursedMatters());

    $log->putContent("To coursed matters done");
    $log->flush();

    $log->putContent("Full data ".$core->serialize());
    $log->flush();

    Unicap\Webservice\Helper\JsonResult::success($core->getFullData());
}

catch (Unicap\DataSource\Exceptions\FileException $e)
{
    $log = new Unicap\DataSource\Files\LogTxt("exception");

    $log->putContent($e->getMessage().$e->getCode().$e->getLine().$e->getFile());
    $log->flush();
}
catch (Exception $e)
{

    $log = new Unicap\DataSource\Files\LogTxt("exception");

    $log->putContent($e->getMessage().$e->getCode().$e->getLine().$e->getFile());
    $log->flush();
}
