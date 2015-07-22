<?php

namespace Unicap\DataSource\Interfaces;

interface FileInterface
{

    public function hasPath();
    public function flush();
    public function putContent($string);
    public function getContent();
}