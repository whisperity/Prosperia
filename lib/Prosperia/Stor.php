<?php

namespace Prosperia;

use Prosperia\Stor\IStorLoader as IStorLoader;

class Stor
{
    private $originalFilename = null;
    private $type = null;
    private $size = null;
    private $hash = null;
    private $content = null;
    
    public function __construct(IStorLoader $loader)
    {
        $loader->load();
        $data = $loader->fetch();
        
        $this->originalFilename = $data['originalFilename'];
        $this->type = $data['type'];
        $this->size = $data['size'];
        $this->hash = $data['hash'];
        $this->content = $data['content'];
    }
    
    public function getOriginalFilename()
    {
        return $this->originalFilename;
    }
    
    public function getType()
    {
        return $this->type;
    }
    
    public function getSize()
    {
        return $this->size;
    }
    
    public function getHash()
    {
        return $this->hash;
    }
    
    public function getContent()
    {
        return $this->content;
    }
}