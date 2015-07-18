<?php


namespace Unicap\DataSource\Exceptions;

class FileException Extends \Exception
{
    public function __construct($message, $code)
    {
        $this->code = $code;
        $this->message = $message;
    }
}