<?php

namespace Prosperia\Stor;

use Prosperia\Stor as Stor;

class StorToFile implements IStorWriter
{
    private $stor = null;
    private $handle = null;
    
    public function __construct(Stor $stor, $file)
    {
        $this->stor = $stor;
        $this->handle = fopen($file, "w+b");
    }
    
    public function write()
    {
        fwrite($this->handle, $this->stor->getOriginalFilename() . "\x01");
        fwrite($this->handle, $this->stor->getType() . "\x02");
        fwrite($this->handle, $this->stor->getSize() . "\x03");
        fwrite($this->handle, $this->stor->getHash() . "\x04");
        
        fwrite($this->handle, $this->stor->getContent());
    }
    
    public function __destruct()
    {
        fclose($this->handle);
    }
}