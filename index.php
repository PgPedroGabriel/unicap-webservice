<?php
/**
 * @author Pedro Gabriel
 * Projeto criado com a finalidade de acessar o portal do aluno da UNICAP (http://www.unicap.br/PortalGraduacao/) e retornar um JSON com os dados do Aluno,
 * Arquivo inicial do projeto. Irá contruir e importar as funções e exibir o json.
 * @return json
 */

include_once './core/autoload.php';

$core = new Core();
$core->verifyMethod();
$core->verifyPostParams();

$request = new Request();
$request->login($core->getMat(), $core->getPass());
$core->setUserData($request->getUserData());
$core->setMatterData($request->getMatterData());

JsonResult::success($core->getFullData());
