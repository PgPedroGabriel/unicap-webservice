<?php
/**
 * @author Pedro Gabriel
 * Retorna o boleto bancário do aluno.
 * @return PDF or Json with error
 */

include_once './core/autoload.php';
error_reporting(E_ALL);

$core = new Core();
$core->verifyMethod();
$core->verifyPostParamsDocket();

$request = new Request();
$request->login($core->getMat(), $core->getPass());
$request->downloadDocket($core->getDocketVia());