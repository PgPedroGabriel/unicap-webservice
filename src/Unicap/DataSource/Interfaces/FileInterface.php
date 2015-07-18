<?php

namespace Unicap\DataSource\Interfaces;

interface FileInterface
{

    public function hasPath();
    public function create();
    public function putContent($string);
    public function getContent();
}