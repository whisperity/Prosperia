<?php

namespace Prosperia\Stor;

class StorFromFile implements IStorLoader
{
    private $handle = null;
    private $stordata = array();
    
    public function __construct($file)
    {
        $this->handle = fopen($file, "rb");
    }
    
    public function fetch()
    {
        return $this->stordata;
    }
    
    private function fread_until_delimiter($handle, $delimiter)
    {
        $buffer = null;
        $char = null;
        
        while ($char != $delimiter)
        {
            $char = fread($handle, 1);
            $buffer .= $char;
        }
        
        return substr($buffer, 0, strlen($buffer) - 1);
    }
    
    public function load()
    {
        $this->stordata = array(
            'originalFilename'  =>  $this->fread_until_delimiter($this->handle, "\x01"),
            'type'  =>  $this->fread_until_delimiter($this->handle, "\x02"),
            'size'  =>  $this->fread_until_delimiter($this->handle, "\x03"),
            'hash'  =>  $this->fread_until_delimiter($this->handle, "\x04"),
            'secretKey'    =>  $this->fread_until_delimiter($this->handle, "\x05")
        );
        
        $this->stordata['content'] = fread($this->handle, (int)$this->stordata['size']);
    }
    
    public function __destruct()
    {
        fclose($this->handle);
    }
}