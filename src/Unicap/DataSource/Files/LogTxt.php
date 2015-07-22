<?php

namespace Unicap\DataSource\Files;

use Unicap\DataSource\Interfaces\FileInterface;
use Unicap\DataSource\Constants\Txt;
Use Unicap\DataSource\Exceptions\FileException;

class LogTxt implements FileInterface
{

    private $_file;
    private $_fileName;
    private $_content;

    public function __construct($fileName)
    {
        $this->_fileName = $fileName;
        $this->_content = "";
        $this->hasPath();
    }

    public function hasPath(){

        if(!is_dir(Txt::$path))
            throw new FileException("Sem pasta de arquivo", 101);
        if(!is_writable(Txt::$path))
            throw new FileException("Permissão de escrita inválida", 102);
    }

    public function flush($fileName = "Log")
    {

        $this->_file = fopen(Txt::$path.$this->_fileName.Txt::$type, 'a');

        fwrite($this->_file, $this->_content."\n");

        fclose($this->_file);

        return;
    }
    public function putContent($text){
        $this->_content = $text."\n";
        return $this;
    }
    public function getContent(){

    }
}