<?php
/**
 * @author Pedro Gabriel
 * Carrega os objetos excenciais do projeto
 */
function __autoload($class_name) {
    require_once $class_name . '.php';
}