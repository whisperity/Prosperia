<?php

namespace Prosperia;

use Prosperia\Stor as Stor;

class Thumbnail
{
    const CHUNK_SIZE = 4096;
    const DEFAULT_WIDTH = 100;
    
    private $width = 0;
    private $type = null;
    private $content = null;
    private $filename = null;
    private $thumbnailString = null;
    
    public function __construct(Stor $stor, $width = null)
    {
        $this->type = $stor->getType();
        $this->content = $stor->getContent();
        
        $this->filename = "var/temp".generateRandomChars(4, "0123456789");
        
        if (!isset($width))
        {
            $this->width = self::DEFAULT_WIDTH;
        }
        else
        {
            $this->width = $width;
        }
        
        $this->make();
    }
    
    private function make()
    {
        list($width_orig, $height_orig) = @getimagesizefromstring($this->content);
        $height = ($this->width / $width_orig) * $height_orig;
        
        $thumbnail = imagecreatetruecolor($this->width, $height);
        $image = imagecreatefromstring($this->content);
        imagecopyresampled($thumbnail, $image, 0, 0, 0, 0, $this->width, $height, $width_orig, $height_orig);
        
        switch ($this->type)
        {
            case "image/jpeg":
                imagejpeg($thumbnail, $this->filename);
                break;
            case "image/png":
                imagepng($thumbnail, $this->filename);
                break;
            case "image/gif":
                imagegif($thumbnail, $this->filename);
                break;
        }
        
        $handle = fopen($this->filename, "rb");
        $thumbstring = null;
        while (!feof($handle))
        {
            $thumbstring .= fread($handle, self::CHUNK_SIZE);
        }
        
        $this->thumbnailString = $thumbstring;
        
        fclose($handle);
        imagedestroy($thumbnail);
        unlink($this->filename);
        
        return $thumbstring;
    }
    
    public function html()
    {
        return "<img src=\"data:".$this->type.";base64," .
            base64_encode($this->thumbnailString) . "\" />";
    }
    
    public function raw()
    {
        return $this->thumbnailString;
    }
    
    public function size()
    {
        return strlen($this->thumbnailString);
    }
}